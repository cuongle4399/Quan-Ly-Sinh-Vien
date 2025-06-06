<?php
include("../BackEnd/blockBugLogin.php");
include("../BackEnd/connectSQL.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$msv = $_SESSION['MSV'] ?? '';

// Lấy giá trị lọc từ GET
$hocKy = isset($_GET['hocky']) ? $_GET['hocky'] : '';
$namHoc = isset($_GET['namhoc']) ? $_GET['namhoc'] : '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/lichthi.css">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Trường đại học Quy Nhơn</title>
</head>
<body>
    <!-- Header -->
    <?php include("../Template_Layout/main/header.php"); ?>

    <!-- Main -->
    <div class="content">
        <!-- Sidebar -->
        <?php include("../Template_Layout/main/sidebar.php"); ?>

        <!-- Content Main -->
        <div class="content__main">
            <div class="title">Lịch thi</div>
            <div class="filters">
                <form method="GET" action="" id="filterForm">
                    <label for="namhoc">Năm học:</label>
                    <select id="namhoc" name="namhoc" onchange="this.form.submit()">
                        <option value="">-- Chọn năm học --</option>
                        <?php
                        // Lấy danh sách năm học từ NgayThi trong LichThi
                        $sqlNamHoc = "
                            SELECT DISTINCT CONCAT(YEAR(NgayThi) - 1, '-', YEAR(NgayThi)) AS NamHoc
                            FROM LichThi
                            WHERE MaSinhVien = ?
                            ORDER BY NamHoc DESC";
                        $stmtNamHoc = $conn->prepare($sqlNamHoc);
                        $stmtNamHoc->bind_param("s", $msv);
                        $stmtNamHoc->execute();
                        $resultNamHoc = $stmtNamHoc->get_result();
                        if ($resultNamHoc->num_rows > 0) {
                            while ($rowNamHoc = $resultNamHoc->fetch_assoc()) {
                                $nh = $rowNamHoc['NamHoc'];
                                $selected = ($namHoc == $nh) ? "selected" : "";
                                echo "<option value='$nh' $selected>$nh</option>";
                            }
                        }
                        $stmtNamHoc->close();
                        ?>
                    </select>

                    <label for="hocky">Học kỳ:</label>
                    <select id="hocky" name="hocky" onchange="this.form.submit()">
                        <option value="">-- Chọn học kỳ --</option>
                        <?php
                        // Lấy danh sách học kỳ từ các học phần mà sinh viên đã đăng ký và có lịch thi
                        $sqlHocKy = "
                            SELECT DISTINCT ctdt.HocKy
                            FROM ChuongTrinhDaoTao ctdt
                            INNER JOIN DangKyHocPhan dkhp ON ctdt.MaHocPhan = dkhp.MaHocPhan
                            INNER JOIN KetQuaDangKyHocPhan kqdk ON dkhp.MaLopHocPhan = kqdk.MaLopHocPhan
                            INNER JOIN LichThi lt ON kqdk.MaLopHocPhan = lt.MaLopHocPhan
                            WHERE kqdk.MaSinhVien = ? AND lt.MaSinhVien = ?
                            ORDER BY ctdt.HocKy";
                        $stmtHocKy = $conn->prepare($sqlHocKy);
                        $stmtHocKy->bind_param("ss", $msv, $msv);
                        $stmtHocKy->execute();
                        $resultHocKy = $stmtHocKy->get_result();
                        if ($resultHocKy->num_rows > 0) {
                            while ($rowHocKy = $resultHocKy->fetch_assoc()) {
                                $hk = $rowHocKy['HocKy'];
                                $selected = ($hocKy == $hk) ? "selected" : "";
                                echo "<option value='$hk' $selected>Học kỳ $hk</option>";
                            }
                        }
                        $stmtHocKy->close();
                        ?>
                    </select>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Mã học phần</th>
                        <th>Tên học phần</th>
                        <th>STC</th>
                        <th>Ngày thi</th>
                        <th>Giờ thi</th>
                        <th>Thời lượng (phút)</th>
                        <th>Phòng thi</th>
                        <th>Link phòng thi</th>
                        <th>Link nộp bài</th>
                        <th>Địa điểm</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                // Truy vấn SQL với bộ lọc học kỳ, năm học và học phần đã đăng ký
                $sql = "
                    SELECT lt.MaLopHocPhan, lt.TenHocPhan, ctdt.SoTinChi, lt.NgayThi, lt.GioThi, lt.ThoiLuong, 
                           lt.PhongThi, lt.LinkPhongThi, lt.LinkNopBai, lt.DiaDiem, lt.GhiChu
                    FROM LichThi lt
                    INNER JOIN DangKyHocPhan dkhp ON lt.MaLopHocPhan = dkhp.MaLopHocPhan
                    INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
                    INNER JOIN KetQuaDangKyHocPhan kqdk ON lt.MaLopHocPhan = kqdk.MaLopHocPhan AND kqdk.MaSinhVien = lt.MaSinhVien
                    WHERE lt.MaSinhVien = ?";

                // Thêm điều kiện lọc HocKy và NamHoc
                $params = [$msv];
                $types = "s";
                if (!empty($hocKy)) {
                    $sql .= " AND ctdt.HocKy = ?";
                    $params[] = $hocKy;
                    $types .= "i";
                }
                if (!empty($namHoc)) {
                    $startYear = explode('-', $namHoc)[0];
                    $sql .= " AND YEAR(lt.NgayThi) = ?";
                    $params[] = (int)$startYear + 1;
                    $types .= "i";
                }

                // Chuẩn bị và thực thi truy vấn
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    die("Lỗi chuẩn bị truy vấn: " . $conn->error);
                }
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) :
                ?>
                        <tr class="lichthi-row">
                            <td class="lichthi-cell"><?php echo htmlspecialchars($row['MaLopHocPhan']); ?></td>
                            <td class="lichthi-cell"><?php echo htmlspecialchars($row['TenHocPhan']); ?></td>
                            <td class="lichthi-cell"><?php echo htmlspecialchars($row['SoTinChi']); ?></td>
                            <td class="lichthi-cell"><?php echo htmlspecialchars($row['NgayThi']); ?></td>
                            <td class="lichthi-cell"><?php echo htmlspecialchars($row['GioThi']); ?></td>
                            <td class="lichthi-cell"><?php echo htmlspecialchars($row['ThoiLuong']); ?></td>
                            <td class="lichthi-cell"><?php echo htmlspecialchars($row['PhongThi']); ?></td>
                            <td class="lichthi-cell"><?php echo htmlspecialchars($row['LinkPhongThi']); ?></td>
                            <td class="lichthi-cell"><?php echo htmlspecialchars($row['LinkNopBai']); ?></td>
                            <td class="lichthi-cell"><?php echo htmlspecialchars($row['DiaDiem']); ?></td>
                            <td class="lichthi-cell"><?php echo htmlspecialchars($row['GhiChu']); ?></td>
                        </tr>
                <?php 
                    endwhile;
                } else {
                ?>
                    <tr>
                        <td colspan="11" class="no-data">Chưa có lịch thi</td>
                    </tr>
                <?php 
                }
                $stmt->close();
                $conn->close();
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php"); ?>
</body>
</html>