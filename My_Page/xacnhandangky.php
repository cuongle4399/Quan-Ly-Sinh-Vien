<?php
include("../BackEnd/connectSQL.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$msv = $_SESSION['MSV'] ?? '';
$selectedCourse = $_GET['selected_course'] ?? '';

if (empty($msv)) {
    header("Location: dangKyHocPhan.php?error=Vui lòng đăng nhập để đăng ký học phần.");
    exit;
}

if (empty($selectedCourse)) {
    header("Location: dangKyHocPhan.php?error=Vui lòng chọn một học phần để đăng ký.");
    exit;
}

// Debug để kiểm tra $msv và $selectedCourse
// var_dump($msv, $selectedCourse); // Bỏ comment nếu cần kiểm tra giá trị

// Lấy thông tin học phần, bao gồm sĩ số và học phần trước
$sql = "
    SELECT dkhp.MaLopHocPhan, dkhp.TenLopHocPhan, ctdt.SoTinChi, dkhp.GiangVien, dkhp.LichHoc, 
    (SELECT COUNT(*) FROM KetQuaDangKyHocPhan kqdk WHERE kqdk.MaLopHocPhan = dkhp.MaLopHocPhan) as SiSoDK, 
    ctdt.HocPhanHocTruoc
    FROM DangKyHocPhan dkhp
    INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
    INNER JOIN Nganh n ON n.MaNganh = ctdt.MaNganh
    INNER JOIN ThongTinCaNhan ttcn ON n.MaNganh = ttcn.MaNganh
    WHERE dkhp.MaLopHocPhan = '$selectedCourse' AND ttcn.MaSinhVien = '$msv'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: dangKyHocPhan.php?error=Học phần không tồn tại hoặc không thuộc ngành của bạn.");
    exit;
}

$course = $result->fetch_assoc();

// Kiểm tra giới hạn sĩ số
if ($course['SiSoDK'] >= 40) {
    header("Location: dangKyHocPhan.php?error=Học phần {$course['MaLopHocPhan']} đã đủ số lượng đăng ký (40).");
    exit;
}

// Kiểm tra học phần trước và điểm
if (!empty($course['HocPhanHocTruoc'])) {
    $prerequisite = $course['HocPhanHocTruoc'];
    $sqlCheckPrereq = "
        SELECT d.DiemCk
        FROM Diem d
        INNER JOIN DangKyHocPhan dkhp ON d.MaLopHocPhan = dkhp.MaLopHocPhan
        WHERE d.MaSinhVien = '$msv' AND dkhp.MaHocPhan = '$prerequisite'";
    $resultPrereq = $conn->query($sqlCheckPrereq);
    
    if ($resultPrereq->num_rows == 0) {
        header("Location: dangKyHocPhan.php?error=Học phần {$course['MaLopHocPhan']} yêu cầu hoàn thành học phần $prerequisite trước.");
        exit;
    }
    
    $prereqData = $resultPrereq->fetch_assoc();
    if ($prereqData['DiemCk'] < 4.0) {
        header("Location: dangKyHocPhan.php?error=Học phần {$course['MaLopHocPhan']} yêu cầu điểm học phần $prerequisite từ 4.0 trở lên.");
        exit;
    }
}

// Kiểm tra học phần đã ở trong danh sách chờ hoặc đã đăng ký
$alreadyPending = false;
$alreadyRegistered = false;

if (!empty($_SESSION['pending_courses'])) {
    foreach ($_SESSION['pending_courses'] as $pendingCourse) {
        if ($pendingCourse['MaLopHocPhan'] == $course['MaLopHocPhan']) {
            $alreadyPending = true;
            break;
        }
    }
}

$sqlCheck = "SELECT * FROM KetQuaDangKyHocPhan WHERE MaSinhVien = '$msv' AND MaLopHocPhan = '$selectedCourse'";
$resultCheck = $conn->query($sqlCheck);
if ($resultCheck->num_rows > 0) {
    $alreadyRegistered = true;
}

if ($alreadyPending) {
    header("Location: dangKyHocPhan.php?error=Học phần {$course['MaLopHocPhan']} đã có trong hàng chờ.");
    exit;
}

if ($alreadyRegistered) {
    header("Location: dangKyHocPhan.php?error=Học phần {$course['MaLopHocPhan']} đã được đăng ký.");
    exit;
}

// Thêm học phần vào danh sách chờ
$_SESSION['pending_courses'][] = [
    'MaLopHocPhan' => $course['MaLopHocPhan'],
    'TenLopHocPhan' => $course['TenLopHocPhan'],
    'SoTinChi' => $course['SoTinChi'],
    'GiangVien' => $course['GiangVien'],
    'LichHoc' => $course['LichHoc']
];

header("Location: dangKyHocPhan.php?success=Học phần {$course['MaLopHocPhan']} đã được thêm vào hàng chờ.");
exit;

$conn->close();
?>