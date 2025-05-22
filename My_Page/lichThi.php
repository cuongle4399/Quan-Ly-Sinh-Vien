<?php include ("../BackEnd/blockBugLogin.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/lichthi.css">
    <link rel="stylesheet" href="../css/mainIN.css">
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
            <label for="namhoc">Năm học:</label>
            <select id="namhoc">
                <option>2024-2025</option>
            </select>

            <label for="hocky">Học kỳ:</label>
            <select id="hocky">
                <option>Học kỳ 2</option>
            </select>
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
                include("../BackEnd/connectSQL.php");
                $sql = "
                    SELECT lt.MaLopHocPhan, lt.TenHocPhan, ctdt.SoTinChi, lt.NgayThi, lt.GioThi, lt.ThoiLuong, 
                           lt.PhongThi, lt.LinkPhongThi, lt.LinkNopBai, lt.DiaDiem, lt.GhiChu
                    FROM LichThi lt
                    INNER JOIN DangKyHocPhan dkhp ON lt.MaLopHocPhan = dkhp.MaLopHocPhan
                    INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
                    WHERE lt.MaSinhVien = '$msv'";
                $result = $conn->query($sql);

                if (!$result) {
                    die("Lỗi truy vấn: " . $conn->error);
                }

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) :
            ?>
                        <tr class="lichthi-row">
                            <td class="lichthi-cell"><?= $row['MaLopHocPhan'] ?></td>
                            <td class="lichthi-cell"><?= $row['TenHocPhan'] ?></td>
                            <td class="lichthi-cell"><?= $row['SoTinChi'] ?></td>
                            <td class="lichthi-cell"><?= $row['NgayThi'] ?></td>
                            <td class="lichthi-cell"><?= $row['GioThi'] ?></td>
                            <td class="lichthi-cell"><?= $row['ThoiLuong'] ?></td>
                            <td class="lichthi-cell"><?= $row['PhongThi'] ?></td>
                            <td class="lichthi-cell"><?= $row['LinkPhongThi'] ?></td>
                            <td class="lichthi-cell"><?= $row['LinkNopBai'] ?></td>
                            <td class="lichthi-cell"><?= $row['DiaDiem'] ?></td>
                            <td class="lichthi-cell"><?= $row['GhiChu'] ?></td>
                        </tr>
            <?php 
                    endwhile;
                } else {
            ?>
                    <tr>
                        <td colspan="11" class="no-data">Chưa có lịch thi</td>
                    </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php include("../Template_Layout/main/footer.php"); ?>
</body>
</html>