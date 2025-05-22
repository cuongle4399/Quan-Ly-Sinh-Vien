<?php
include("../BackEnd/blockBugLogin.php");
include("../BackEnd/connectSQL.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$msv = $_SESSION['MSV'] ?? '';
$maHP = $_GET['maHP'] ?? '';

if (empty($maHP)) {
    echo "Mã lớp học phần không hợp lệ.";
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['maHP'])) {
    $maHP = trim($_POST['maHP']);
    $msv = trim($msv);

    $conn->begin_transaction();
    try {
        // Check MaSinhVien
        $sqlCheckMSV = "SELECT MaSinhVien FROM ThongTinCaNhan WHERE MaSinhVien = ?";
        $stmtCheckMSV = $conn->prepare($sqlCheckMSV);
        $stmtCheckMSV->bind_param("s", $msv);
        $stmtCheckMSV->execute();
        $resultCheckMSV = $stmtCheckMSV->get_result();
        if ($resultCheckMSV->num_rows == 0) {
            throw new Exception("Mã sinh viên $msv không tồn tại trong hệ thống.");
        }

        // Check MaLopHocPhan
        $sqlCheckMaHP = "SELECT MaLopHocPhan FROM DangKyHocPhan WHERE MaLopHocPhan = ?";
        $stmtCheckMaHP = $conn->prepare($sqlCheckMaHP);
        $stmtCheckMaHP->bind_param("s", $maHP);
        $stmtCheckMaHP->execute();
        $resultCheckMaHP = $stmtCheckMaHP->get_result();
        if ($resultCheckMaHP->num_rows == 0) {
            throw new Exception("Mã lớp học phần $maHP không tồn tại trong hệ thống.");
        }

        // Get TenHocPhan
        $sqlGetTenHP = "SELECT TenHocPhan FROM ChuongTrinhDaoTao WHERE MaHocPhan = (SELECT MaHocPhan FROM DangKyHocPhan WHERE MaLopHocPhan = ?)";
        $stmtTenHP = $conn->prepare($sqlGetTenHP);
        $stmtTenHP->bind_param("s", $maHP);
        $stmtTenHP->execute();
        $resultTenHP = $stmtTenHP->get_result();
        $tenHocPhan = $resultTenHP->fetch_assoc()['TenHocPhan'] ?? 'Tên mặc định';

        // 
        $now = date("Y-m-d H:i:s");
        $sqlInsert = "INSERT INTO KetQuaDangKyHocPhan (MaLopHocPhan, NgayDangKy, TenHocPhan, MaSinhVien) VALUES (?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("ssss", $maHP, $now, $tenHocPhan, $msv);
        $stmtInsert->execute();

        $conn->commit();
        header("Location: dangKyHocPhan.php?success=" . urlencode("Đăng ký thành công $maHP"));
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: dangKyHocPhan.php?error=" . urlencode("Đăng ký thất bại: " . $e->getMessage()));
        exit;
    }
}

// Query course details
$sql = "SELECT dkhp.MaLopHocPhan, dkhp.TenLopHocPhan, ctdt.SoTinChi, dkhp.GiangVien, dkhp.LichHoc, dkhp.NgayBatDau, dkhp.NgayKetThuc
        FROM DangKyHocPhan dkhp
        INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
        WHERE dkhp.MaLopHocPhan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $maHP);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/dangkyhocphan.css">
    <title>Chi tiết lớp học phần</title>
    <style>
        table td { text-align: left; }
    </style>
</head>
<body>
    <?php include("../Template_Layout/main/header.php") ?>
    <div class="content">
        <?php include("../Template_Layout/main/sidebar.php") ?>
        <div class="main-content">
            <div class="panel">
                <div class="panel-heading"><strong>Chi tiết lớp học phần</strong></div>
                <div class="panel-body">
                    <?php if ($row): ?>
                        <table>
                            <tr><td><strong>Mã lớp học phần</strong></td><td><?php echo $row['MaLopHocPhan']; ?></td></tr>
                            <tr><td><strong>Tên lớp học phần</strong></td><td><?php echo $row['TenLopHocPhan']; ?></td></tr>
                            <tr><td><strong>Số tín chỉ</strong></td><td><?php echo $row['SoTinChi']; ?></td></tr>
                            <tr><td><strong>Giảng viên</strong></td><td><?php echo $row['GiangVien']; ?></td></tr>
                            <tr><td><strong>Lịch học</strong></td><td><?php echo $row['LichHoc']; ?></td></tr>
                            <tr><td><strong>Ngày bắt đầu</strong></td><td><?php echo $row['NgayBatDau']; ?></td></tr>
                            <tr><td><strong>Ngày kết thúc</strong></td><td><?php echo $row['NgayKetThuc']; ?></td></tr>
                        </table>
                        <form method="post">
                            <input type="hidden" name="maHP" value="<?php echo $maHP; ?>">
                            <button type="submit">Xác nhận đăng ký</button>
                        </form>
                    <?php else: ?>
                        <p>Không tìm thấy thông tin lớp học phần.</p>
                        <a href="dangKyHocPhan.php">Quay lại</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>
<?php $conn->close(); ?>






