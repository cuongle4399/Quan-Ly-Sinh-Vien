<?php
include("../BackEnd/blockBugLogin.php");
include("../BackEnd/connectSQL.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CourseRegistration {
    private $conn;
    private $msv;
    private $maHP;

    public function __construct($conn, $msv, $maHP) {
        $this->conn = $conn;
        $this->msv = $conn->real_escape_string($msv);
        $this->maHP = $conn->real_escape_string($maHP);
    }

    public function validateInput() {
        return empty($this->maHP) ? "Mã lớp học phần không hợp lệ." : null;
    }

    public function getCourseDetails() {
        $sql = "
            SELECT dkhp.MaLopHocPhan, dkhp.TenLopHocPhan, ctdt.SoTinChi, dkhp.GiangVien, 
                   dkhp.LichHoc, dkhp.NgayBatDau, dkhp.NgayKetThuc, ctdt.TenHocPhan, 
                   ctdt.MaHocPhan, ctdt.HocPhanHocTruoc
            FROM DangKyHocPhan dkhp
            INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
            WHERE dkhp.MaLopHocPhan = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $this->maHP);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function registerCourse() {
        try {
            // Check for duplicate registration
            $sqlCheckDuplicate = "SELECT MaLopHocPhan FROM KetQuaDangKyHocPhan WHERE MaLopHocPhan = ? AND MaSinhVien = ?";
            $stmtCheckDuplicate = $this->conn->prepare($sqlCheckDuplicate);
            $stmtCheckDuplicate->bind_param("ss", $this->maHP, $this->msv);
            $stmtCheckDuplicate->execute();
            if ($stmtCheckDuplicate->get_result()->num_rows > 0) {
                throw new Exception("Học phần đã được đăng ký trước đó.");
            }

            // Get course details and prerequisite
            $courseDetails = $this->getCourseDetails();
            if (!$courseDetails) {
                throw new Exception("Mã lớp học phần không tồn tại.");
            }
            $hocPhanHocTruoc = $courseDetails['HocPhanHocTruoc'];
            $tenHocPhan = $courseDetails['TenHocPhan'];

            // Check prerequisite if exists
            if (!empty($hocPhanHocTruoc)) {
                $sqlCheckPreReq = "
                    SELECT d.DiemCC, d.DiemCk
                    FROM KetQuaDangKyHocPhan kqdkhp
                    INNER JOIN DangKyHocPhan dkhp ON kqdkhp.MaLopHocPhan = dkhp.MaLopHocPhan
                    INNER JOIN Diem d ON dkhp.MaLopHocPhan = d.MaLopHocPhan
                    WHERE dkhp.MaHocPhan = ? AND kqdkhp.MaSinhVien = ?";
                $stmtCheckPreReq = $this->conn->prepare($sqlCheckPreReq);
                $stmtCheckPreReq->bind_param("ss", $hocPhanHocTruoc, $this->msv);
                $stmtCheckPreReq->execute();
                $resultCheckPreReq = $stmtCheckPreReq->get_result();

                if ($resultCheckPreReq->num_rows == 0) {
                    throw new Exception("Bạn chưa đăng ký học phần học trước ($hocPhanHocTruoc).");
                }

                $hasPassingScore = false;
                while ($rowScore = $resultCheckPreReq->fetch_assoc()) {
                    $diem10 = $rowScore['DiemCC'] * 0.3 + $rowScore['DiemCk'] * 0.7;
                    if ($diem10 >= 4.0) {
                        $hasPassingScore = true;
                        break;
                    }
                }
                if (!$hasPassingScore) {
                    throw new Exception("Bạn chưa đạt điểm học phần học trước ($hocPhanHocTruoc).");
                }
            }

            // Register course
            $now = date("Y-m-d H:i:s");
            $sqlInsert = "INSERT INTO KetQuaDangKyHocPhan (MaLopHocPhan, NgayDangKy, TenHocPhan, MaSinhVien) VALUES (?, ?, ?, ?)";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ssss", $this->maHP, $now, $tenHocPhan, $this->msv);
            $stmtInsert->execute();

            return ["success" => "Đăng ký thành công học phần $this->maHP."];
        } catch (Exception $e) {
            return ["error" => "Đăng ký thất bại: " . $e->getMessage()];
        }
    }
}

$msv = $_SESSION['MSV'] ?? '';
$maHP = $_GET['maHP'] ?? '';
$registration = new CourseRegistration($conn, $msv, $maHP);

$inputError = $registration->validateInput();
if ($inputError) {
    header("Location: dangKyHocPhan.php?error=" . urlencode($inputError));
    exit;
}

$courseDetails = $registration->getCourseDetails();
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maHP']) && $_POST['maHP'] === $maHP) {
    $result = $registration->registerCourse();
    header("Location: dangKyHocPhan.php?" . (isset($result['success']) ? "success=" . urlencode($result['success']) : "error=" . urlencode($result['error'])));
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Chi tiết lớp học phần</title>
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/dangkyhocphan.css">
</head>
<body>
    <?php include("../Template_Layout/main/header.php") ?>
    <div class="content">
        <?php include("../Template_Layout/main/sidebar.php") ?>
        <div class="main-content">
            <div class="panel">
                <div class="panel-heading">
                    <strong>Chi tiết lớp học phần</strong>
                </div>
                <div class="panel-body">
                    <?php if (isset($result['error'])): ?>
                        <div class="message error"><?= htmlspecialchars($result['error']) ?></div>
                    <?php endif; ?>
                    <?php if ($courseDetails): ?>
                        <table>
                            <tr><td><strong>Mã lớp học phần</strong></td><td><?= htmlspecialchars($courseDetails['MaLopHocPhan']) ?></td></tr>
                            <tr><td><strong>Tên lớp học phần</strong></td><td><?= htmlspecialchars($courseDetails['TenLopHocPhan']) ?></td></tr>
                            <tr><td><strong>Số tín chỉ</strong></td><td><?= $courseDetails['SoTinChi'] ?></td></tr>
                            <tr><td><strong>Giảng viên</strong></td><td><?= htmlspecialchars($courseDetails['GiangVien']) ?></td></tr>
                            <tr><td><strong>Lịch học</strong></td><td><?= htmlspecialchars($courseDetails['LichHoc']) ?></td></tr>
                            <tr><td><strong>Ngày bắt đầu</strong></td><td><?= $courseDetails['NgayBatDau'] ?></td></tr>
                            <tr><td><strong>Ngày kết thúc</strong></td><td><?= $courseDetails['NgayKetThuc'] ?></td></tr>
                        </table>
                        <div class="button-group">
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="maHP" value="<?= htmlspecialchars($maHP) ?>">
                                <button type="submit">Xác nhận đăng ký</button>
                            </form>
                            <button onclick="window.location.href='dangKyHocPhan.php'">Thoát</button>
                        </div>
                    <?php else: ?>
                        <p>Không tìm thấy thông tin lớp học phần.</p>
                        <div class="button-group">
                            <button onclick="window.location.href='dangKyHocPhan.php'">Thoát</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>
<?php $conn->close(); ?>