<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../BackEnd/saveLogin.php");
include("../BackEnd/connectSQL.php");

$keyword = '';
if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Trang Chủ</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/tbhocphi.css">
</head>
<body>
    <?php include("../Template_Layout/main/header.php") ?>

    <main class="main">
        <?php include("../My_Page_public/sidebar.php") ?>
 
        <div class="main__content">
            <div class="main__content-title">
                <p>Thông báo học phí</p>
            </div>

            <form method="GET" class="search-form">
                <input type="text" name="keyword" placeholder="Tìm theo mã SV hoặc tên" value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit">Tìm kiếm</button>
            </form>

            <?php if (!empty($keyword)): ?>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Mã Sinh Viên</th>
                            <th>Họ Tên</th>
                            <th>Tên Học Phần</th>
                            <th>Học Phí</th>
                            <th>Đã Đóng</th>
                            <th>Còn Nợ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Escape the keyword to prevent SQL injection
                        $keywordEscaped = mysqli_real_escape_string($conn, $keyword);

                        // Corrected SQL query: Removed erroneous AND in WHERE clause
                        $sql = "
                            SELECT 
                                sv.MaSinhVien,
                                sv.HoTen,
                                lhp.TenLopHocPhan,
                                ctdt.SoTinChi * ng.GiaCua1TinChi AS HocPhi,
                                CASE WHEN cthp.TrangThai = 1 THEN ctdt.SoTinChi * ng.GiaCua1TinChi ELSE 0 END AS SoTienDaDong
                            FROM 
                                ThongTinCaNhan sv
                            JOIN 
                                KetQuaDangKyHocPhan dk ON sv.MaSinhVien = dk.MaSinhVien
                            JOIN 
                                DangKyHocPhan lhp ON dk.MaLopHocPhan = lhp.MaLopHocPhan
                            JOIN 
                                ChuongTrinhDaoTao ctdt ON lhp.MaHocPhan = ctdt.MaHocPhan
                            JOIN 
                                Nganh ng ON sv.MaNganh = ng.MaNganh
                            LEFT JOIN 
                                ChiTietHocPhi cthp ON cthp.MaLopHocPhan = dk.MaLopHocPhan AND cthp.MaSinhVien = dk.MaSinhVien
                            WHERE 
                                sv.MaSinhVien LIKE '%$keywordEscaped%' OR sv.HoTen LIKE '%$keywordEscaped%'
                            ORDER BY 
                                sv.MaSinhVien, lhp.TenLopHocPhan
                        ";

                        $result = mysqli_query($conn, $sql);
                        if (!$result) {
                            echo "<tr><td colspan='6'>Lỗi truy vấn SQL: " . htmlspecialchars(mysqli_error($conn)) . "</td></tr>";
                        } elseif (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $maSinhVien = htmlspecialchars($row['MaSinhVien']);
                                $hoTen = htmlspecialchars($row['HoTen']);
                                $tenLopHocPhan = htmlspecialchars($row['TenLopHocPhan']);
                                $hocPhi = number_format($row['HocPhi'], 0, ',', '.');
                                $daDong = number_format($row['SoTienDaDong'], 0, ',', '.');
                                $conNo = number_format($row['HocPhi'] - $row['SoTienDaDong'], 0, ',', '.');

                                echo "<tr>
                                        <td>$maSinhVien</td>
                                        <td>$hoTen</td>
                                        <td>$tenLopHocPhan</td>
                                        <td>$hocPhi</td>
                                        <td>$daDong</td>
                                        <td>$conNo</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Không có dữ liệu phù hợp.</td></tr>";
                        }

                        // Free the result set
                        mysqli_free_result($result);
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </main>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>