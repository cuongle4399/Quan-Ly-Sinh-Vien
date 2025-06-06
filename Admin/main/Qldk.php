<?php
session_start();
include("../../BackEnd/connectSQL.php");
include("../../BackEnd/phantrang.php");

// Initialize queue if not set
if (!isset($_SESSION['hang_cho_hoc_phan'])) {
    $_SESSION['hang_cho_hoc_phan'] = [];
}

// Handle adding to queue
if (isset($_POST['them_vao_hang_cho'])) {
    $_SESSION['hang_cho_hoc_phan'][] = [
        'maHocPhan' => $_POST['maHocPhan'],
        'tenHocPhan' => $_POST['tenHocPhan'],
        'soTinChi' => (int)$_POST['soTinChi'],
        'maNganh' => $_POST['maNganh'],
        'hocKy' => (int)$_POST['hocKy'],
        'namHoc' => $_POST['namHoc'],
        'maLopHocPhan' => $_POST['maLopHocPhan'],
        'tenLopHocPhan' => $_POST['tenLopHocPhan'],
        'ngayBatDau' => $_POST['ngayBatDau'],
        'ngayKetThuc' => $_POST['ngayKetThuc'],
        'giangVien' => $_POST['giangVien'],
        'lichHoc' => $_POST['lichHoc']
    ];
    $_SESSION['success'] = "Đã thêm học phần vào hàng chờ!";
    header("Location: Qldk.php");
    exit();
}

// Handle confirming all in queue
if (isset($_POST['xac_nhan_tat_ca'])) {
    foreach ($_SESSION['hang_cho_hoc_phan'] as $hocPhan) {
        $sql = "INSERT INTO DangKyHocPhan (MaLopHocPhan, MaHocPhan, NgayBatDau, NgayKetThuc, GiangVien, LichHoc, TenLopHocPhan) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $hocPhan['maLopHocPhan'], $hocPhan['maHocPhan'], $hocPhan['ngayBatDau'], $hocPhan['ngayKetThuc'], $hocPhan['giangVien'], $hocPhan['lichHoc'], $hocPhan['tenLopHocPhan']);
        $stmt->execute();
        $stmt->close();
    }
    $_SESSION['hang_cho_hoc_phan'] = [];
    $_SESSION['success'] = "Đã xác nhận tất cả học phần!";
    header("Location: Qldk.php");
    exit();
}

// Handle confirming single course
if (isset($_GET['xac_nhan_hoc_phan']) && isset($_GET['index'])) {
    $index = $_GET['index'];
    if (isset($_SESSION['hang_cho_hoc_phan'][$index])) {
        $hocPhan = $_SESSION['hang_cho_hoc_phan'][$index];
        $sql = "INSERT INTO DangKyHocPhan (MaLopHocPhan, MaHocPhan, NgayBatDau, NgayKetThuc, GiangVien, LichHoc, TenLopHocPhan) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $hocPhan['maLopHocPhan'], $hocPhan['maHocPhan'], $hocPhan['ngayBatDau'], $hocPhan['ngayKetThuc'], $hocPhan['giangVien'], $hocPhan['lichHoc'], $hocPhan['tenLopHocPhan']);
        $stmt->execute();
        $stmt->close();
        unset($_SESSION['hang_cho_hoc_phan'][$index]);
        $_SESSION['hang_cho_hoc_phan'] = array_values($_SESSION['hang_cho_hoc_phan']);
        $_SESSION['success'] = "Đã xác nhận học phần!";
    }
    header("Location: Qldk.php");
    exit();
}

// Handle removing from queue
if (isset($_GET['xoa_hang_cho']) && isset($_GET['index'])) {
    unset($_SESSION['hang_cho_hoc_phan'][$_GET['index']]);
    $_SESSION['hang_cho_hoc_phan'] = array_values($_SESSION['hang_cho_hoc_phan']);
    $_SESSION['success'] = "Đã xóa học phần khỏi hàng chờ!";
    header("Location: Qldk.php");
    exit();
}

// Handle deleting confirmed course
if (isset($_GET['xoa_lop_hoc_phan']) && isset($_GET['maLopHocPhan'])) {
    $sql = "DELETE FROM DangKyHocPhan WHERE MaLopHocPhan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_GET['maLopHocPhan']);
    $stmt->execute();
    $stmt->close();
    $_SESSION['success'] = "Đã xóa lớp học phần!";
    header("Location: Qldk.php");
    exit();
}

// Handle sorting
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'MaLopHocPhan';
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';
$validColumns = ['MaLopHocPhan', 'TenLopHocPhan', 'NgayBatDau', 'NgayKetThuc', 'GiangVien', 'LichHoc'];
if (!in_array($sortColumn, $validColumns)) {
    $sortColumn = 'MaLopHocPhan';
}

// Fetch course data
$pagination = new Pagination($conn, 5);
$sqlHocPhan = "SELECT MaLopHocPhan, TenLopHocPhan, NgayBatDau, NgayKetThuc, GiangVien, LichHoc FROM DangKyHocPhan ORDER BY $sortColumn $sortOrder";
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

            <!-- Form to add course to queue -->
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
                    <input type="number" name="soTinChi" id="soTinChi" placeholder="Số tín chỉ" readonly>
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
                    <input type="text" name="maLopHocPhan" placeholder="Mã lớp học phần" required>
                    <input type="text" name="tenLopHocPhan" placeholder="Tên lớp học phần" required>
                    <input type="text" name="giangVien" placeholder="Giảng viên" required>
                    <input type="datetime-local" name="ngayBatDau" required>
                    <input type="datetime-local" name="ngayKetThuc" required>
                    <input type="text" name="lichHoc" placeholder="Lịch học (VD: Thứ 2, 8h-10h)" required>
                    <button type="submit" name="them_vao_hang_cho">Thêm vào hàng chờ</button>
                </form>
            </div>

            <!-- Display queue -->
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
                                <td>
                                    <a href="?xoa_hang_cho=1&index=<?php echo $index; ?>" class="btn-xoa">Xóa</a>
                                    <a href="?xac_nhan_hoc_phan=1&index=<?php echo $index; ?>" class="btn-xac-nhan">Xác nhận</a>
                                </td>
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

            <!-- Display confirmed courses -->
            <table>
                <thead>
                    <tr>
                        <th class="sortable" data-column="MaLopHocPhan" data-order="<?php echo ($sortColumn === 'MaLopHocPhan' && $sortOrder === 'ASC') ? 'desc' : 'asc'; ?>">Mã lớp HP<?php if ($sortColumn === 'MaLopHocPhan') echo $sortOrder === 'ASC' ? ' ↑' : ' ↓'; ?></th>
                        <th class="sortable" data-column="TenLopHocPhan" data-order="<?php echo ($sortColumn === 'TenLopHocPhan' && $sortOrder === 'ASC') ? 'desc' : 'asc'; ?>">Tên lớp HP<?php if ($sortColumn === 'TenLopHocPhan') echo $sortOrder === 'ASC' ? ' ↑' : ' ↓'; ?></th>
                        <th class="sortable" data-column="NgayBatDau" data-order="<?php echo ($sortColumn === 'NgayBatDau' && $sortOrder === 'ASC') ? 'desc' : 'asc'; ?>">Ngày bắt đầu<?php if ($sortColumn === 'NgayBatDau') echo $sortOrder === 'ASC' ? ' ↑' : ' ↓'; ?></th>
                        <th class="sortable" data-column="NgayKetThuc" data-order="<?php echo ($sortColumn === 'NgayKetThuc' && $sortOrder === 'ASC') ? 'desc' : 'asc'; ?>">Ngày kết thúc<?php if ($sortColumn === 'NgayKetThuc') echo $sortOrder === 'ASC' ? ' ↑' : ' ↓'; ?></th>
                        <th class="sortable" data-column="GiangVien" data-order="<?php echo ($sortColumn === 'GiangVien' && $sortOrder === 'ASC') ? 'desc' : 'asc'; ?>">Giảng viên<?php if ($sortColumn === 'GiangVien') echo $sortOrder === 'ASC' ? ' ↑' : ' ↓'; ?></th>
                        <th class="sortable" data-column="LichHoc" data-order="<?php echo ($sortColumn === 'LichHoc' && $sortOrder === 'ASC') ? 'desc' : 'asc'; ?>">Lịch học<?php if ($sortColumn === 'LichHoc') echo $sortOrder === 'ASC' ? ' ↑' : ' ↓'; ?></th>
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
            <?php echo $pagination->generatePagination($_SERVER['PHP_SELF'] . '?sort=' . urlencode($sortColumn) . '&order=' . urlencode($sortOrder)); ?>
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
                document.getElementById('namHoc').value = '<?php echo date('Y') . '-' . (date('Y') + 1); ?>';
            } else {
                document.getElementById('tenHocPhan').value = '';
                document.getElementById('soTinChi').value = '';
                document.getElementById('maNganh').value = '';
                document.getElementById('hocKy').value = '';
                document.getElementById('namHoc').value = '';
            }
        });

        document.querySelectorAll('.sortable').forEach(th => {
            th.addEventListener('click', function() {
                const column = this.getAttribute('data-column');
                const order = this.getAttribute('data-order');
                window.location.href = `Qldk.php?sort=${column}&order=${order}`;
            });
        });
    </script>
    <?php $conn->close(); ?>
</body>
</html>