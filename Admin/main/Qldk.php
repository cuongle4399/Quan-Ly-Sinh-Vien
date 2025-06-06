<?php
include("../../BackEnd/blockBugLogin.php");
include("../../BackEnd/connectSQL.php");
include("../../BackEnd/phantrang.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Khởi tạo hàng chờ nếu chưa có
if (!isset($_SESSION['hang_cho_hoc_phan'])) {
    $_SESSION['hang_cho_hoc_phan'] = [];
}

// Xử lý thêm vào hàng chờ
if (isset($_POST['them_vao_hang_cho'])) {
    $maHocPhan = $_POST['maHocPhan'];
    $tenHocPhan = $_POST['tenHocPhan'];
    $soTinChi = (int)$_POST['soTinChi'];
    $maNganh = $_POST['maNganh'];
    $hocKy = (int)$_POST['hocKy'];
    $namHoc = $_POST['namHoc'];
    $maLopHocPhan = $_POST['maLopHocPhan'];
    $tenLopHocPhan = $_POST['tenLopHocPhan'];
    $ngayBatDau = $_POST['ngayBatDau'];
    $ngayKetThuc = $_POST['ngayKetThuc'];
    $giangVien = $_POST['giangVien'];
    $lichHoc = $_POST['lichHoc'];

    // Kiểm tra trùng lặp MaLopHocPhan
    $sql = "SELECT * FROM DangKyHocPhan WHERE MaLopHocPhan = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $maLopHocPhan);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $_SESSION['error'] = "Mã lớp học phần đã tồn tại!";
        } else {
            // Thêm vào hàng chờ
            $_SESSION['hang_cho_hoc_phan'][] = [
                'maHocPhan' => $maHocPhan,
                'tenHocPhan' => $tenHocPhan,
                'soTinChi' => $soTinChi,
                'maNganh' => $maNganh,
                'hocKy' => $hocKy,
                'namHoc' => $namHoc,
                'maLopHocPhan' => $maLopHocPhan,
                'tenLopHocPhan' => $tenLopHocPhan,
                'ngayBatDau' => $ngayBatDau,
                'ngayKetThuc' => $ngayKetThuc,
                'giangVien' => $giangVien,
                'lichHoc' => $lichHoc
            ];
            $_SESSION['success'] = "Đã thêm học phần vào hàng chờ!";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Lỗi chuẩn bị truy vấn!";
    }
    header("Location: Qldk.php");
    exit();
}

// Xử lý xác nhận toàn bộ hàng chờ
if (isset($_POST['xac_nhan_tat_ca'])) {
    foreach ($_SESSION['hang_cho_hoc_phan'] as $hocPhan) {
        $maHocPhan = $hocPhan['maHocPhan'];
        $maLopHocPhan = $hocPhan['maLopHocPhan'];
        $tenLopHocPhan = $hocPhan['tenLopHocPhan'];
        $ngayBatDau = $hocPhan['ngayBatDau'];
        $ngayKetThuc = $hocPhan['ngayKetThuc'];
        $giangVien = $hocPhan['giangVien'];
        $lichHoc = $hocPhan['lichHoc'];
        $namHoc = $hocPhan['namHoc'];

        // Thêm lớp học phần vào DangKyHocPhan
        $sql = "INSERT INTO DangKyHocPhan (MaLopHocPhan, MaHocPhan, NgayBatDau, NgayKetThuc, GiangVien, LichHoc, TenLopHocPhan) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssss", $maLopHocPhan, $maHocPhan, $ngayBatDau, $ngayKetThuc, $giangVien, $lichHoc, $tenLopHocPhan);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Đã xác nhận tất cả học phần trong hàng chờ!";
            } else {
                $_SESSION['error'] = "Lỗi khi xác nhận học phần!";
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Lỗi chuẩn bị truy vấn!";
        }
    }
    $_SESSION['hang_cho_hoc_phan'] = [];
    header("Location: Qldk.php");
    exit();
}

// Xử lý xóa khỏi hàng chờ
if (isset($_GET['xoa_hang_cho']) && isset($_GET['index'])) {
    $index = $_GET['index'];
    unset($_SESSION['hang_cho_hoc_phan'][$index]);
    $_SESSION['hang_cho_hoc_phan'] = array_values($_SESSION['hang_cho_hoc_phan']);
    $_SESSION['success'] = "Đã xóa học phần khỏi hàng chờ!";
    header("Location: Qldk.php");
    exit();
}

// Xử lý xóa lớp học phần đã xác nhận
if (isset($_GET['xoa_lop_hoc_phan']) && isset($_GET['maLopHocPhan'])) {
    $maLopHocPhan = $_GET['maLopHocPhan'];
    $sql = "DELETE FROM DangKyHocPhan WHERE MaLopHocPhan = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $maLopHocPhan);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Đã xóa lớp học phần!";
        } else {
            $_SESSION['error'] = "Lỗi khi xóa lớp học phần!";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Lỗi chuẩn bị truy vấn!";
    }
    header("Location: Qldk.php");
    exit();
}

// Lấy danh sách các lớp học phần đã mở
$pagination = new Pagination($conn, 5);
$sqlHocPhan = "SELECT MaLopHocPhan, TenLopHocPhan, NgayBatDau, NgayKetThuc, GiangVien, LichHoc FROM DangKyHocPhan ORDER BY MaLopHocPhan DESC";
$pagination->setQuery($sqlHocPhan);
$resultHocPhan = $pagination->getData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/Qldk.css">
    <title>Quản lý học phần mở</title>
</head>
<body>
    <?php include('header.php'); ?>
    <div class="Content-main">
        <?php include('sidebar.php'); ?>
        <div class="main">
            <h2>Quản lý học phần mở</h2>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="message success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <!-- Form thêm học phần vào hàng chờ -->
            <div class="form-them">
                <h3>Thêm học phần vào hàng chờ</h3>
                <form method="POST" action="">
                    <select name="maHocPhan" id="maHocPhan" required>
                        <option value="">Chọn mã học phần</option>
                        <?php
                        $sql = "SELECT MaHocPhan, TenHocPhan, SoTinChi, MaNganh, HocKy FROM ChuongTrinhDaoTao";
                        $result = $conn->query($sql);
                        $hocPhanData = [];
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaHocPhan']}'>{$row['MaHocPhan']} - {$row['TenHocPhan']}</option>";
                            $hocPhanData[$row['MaHocPhan']] = [
                                'tenHocPhan' => $row['TenHocPhan'],
                                'soTinChi' => $row['SoTinChi'],
                                'maNganh' => $row['MaNganh'],
                                'hocKy' => $row['HocKy']
                            ];
                        }
                        ?>
                    </select>
                    <input type="text" name="tenHocPhan" id="tenHocPhan" placeholder="Tên học phần" readonly>
                    <input type="number" name="soTinChi" id="soTinChi" placeholder="Số tín chỉ" min="0" max="3" readonly>
                    <input type="text" name="maNganh" id="maNganh" placeholder="Mã ngành" readonly>
                    <input type="number" name="hocKy" id="hocKy" placeholder="Học kỳ" readonly>
                    <select name="namHoc" id="namHoc" required>
                        <option value="">Chọn năm học</option>
                        <?php
                        $currentYear = date('Y');
                        for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) {
                            $namHoc = $i . '-' . ($i + 1);
                            echo "<option value='{$namHoc}'>{$namHoc}</option>";
                        }
                        ?>
                    </select>
                    <input type="text" name="maLopHocPhan" placeholder="Mã lớp học phần" required pattern="[A-Z0-9]+" maxlength="20">
                    <input type="text" name="tenLopHocPhan" placeholder="Tên lớp học phần" required maxlength="50">
                    <input type="text" name="giangVien" placeholder="Giảng viên" required maxlength="50">
                    <input type="datetime-local" name="ngayBatDau" required>
                    <input type="datetime-local" name="ngayKetThuc" required>
                    <input type="text" name="lichHoc" placeholder="Lịch học (VD: Thứ 2, 8h-10h)" required maxlength="50">
                    <button type="submit" name="them_vao_hang_cho">Thêm vào hàng chờ</button>
                </form>
            </div>

            <!-- Hiển thị hàng chờ học phần -->
            <div class="hang-cho">
                <h3>Hàng chờ học phần</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Mã HP</th>
                            <th>Tên HP</th>
                            <th>Học kỳ</th>
                            <th>Mã lớp HP</th>
                            <th>Tên lớp HP</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Giảng viên</th>
                            <th>Lịch học</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['hang_cho_hoc_phan'] as $index => $hocPhan): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($hocPhan['maHocPhan']); ?></td>
                                <td><?php echo htmlspecialchars($hocPhan['tenHocPhan']); ?></td>
                                <td><?php echo htmlspecialchars($hocPhan['hocKy']); ?></td>
                                <td><?php echo htmlspecialchars($hocPhan['maLopHocPhan']); ?></td>
                                <td><?php echo htmlspecialchars($hocPhan['tenLopHocPhan']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($hocPhan['ngayBatDau'])); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($hocPhan['ngayKetThuc'])); ?></td>
                                <td><?php echo htmlspecialchars($hocPhan['giangVien']); ?></td>
                                <td><?php echo htmlspecialchars($hocPhan['lichHoc']); ?></td>
                                <td><a href="?xoa_hang_cho=1&index=<?php echo $index; ?>" class="btn-xoa">Xóa</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (!empty($_SESSION['hang_cho_hoc_phan'])): ?>
                    <form method="POST" action="">
                        <button type="submit" name="xac_nhan_tat_ca">Xác nhận tất cả</button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Danh sách các lớp học phần đã mở -->
            <table>
                <thead>
                    <tr>
                        <th>Mã lớp HP</th>
                        <th>Tên lớp HP</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Giảng viên</th>
                        <th>Lịch học</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultHocPhan->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['MaLopHocPhan']); ?></td>
                            <td><?php echo htmlspecialchars($row['TenLopHocPhan']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['NgayBatDau'])); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['NgayKetThuc'])); ?></td>
                            <td><?php echo htmlspecialchars($row['GiangVien']); ?></td>
                            <td><?php echo htmlspecialchars($row['LichHoc']); ?></td>
                            <td><a href="?xoa_lop_hoc_phan=1&maLopHocPhan=<?php echo urlencode($row['MaLopHocPhan']); ?>" class="btn-xoa">Xóa</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php echo $pagination->generatePagination($_SERVER['PHP_SELF']); ?>
        </div>
    </div>

    <script>
        const hocPhanData = <?php echo json_encode($hocPhanData); ?>;
        document.getElementById('maHocPhan').addEventListener('change', function() {
            const selectedMaHocPhan = this.value;
            if (selectedMaHocPhan && hocPhanData[selectedMaHocPhan]) {
                document.getElementById('tenHocPhan').value = hocPhanData[selectedMaHocPhan].tenHocPhan;
                document.getElementById('soTinChi').value = hocPhanData[selectedMaHocPhan].soTinChi;
                document.getElementById('maNganh').value = hocPhanData[selectedMaHocPhan].maNganh;
                document.getElementById('hocKy').value = hocPhanData[selectedMaHocPhan].hocKy;
                const currentYear = <?php echo date('Y'); ?>;
                document.getElementById('namHoc').value = `${currentYear}-${currentYear + 1}`;
            } else {
                document.getElementById('tenHocPhan').value = '';
                document.getElementById('soTinChi').value = '';
                document.getElementById('maNganh').value = '';
                document.getElementById('hocKy').value = '';
                document.getElementById('namHoc').value = '';
            }
        });
    </script>
    <?php $conn->close(); ?>
</body>
</html>