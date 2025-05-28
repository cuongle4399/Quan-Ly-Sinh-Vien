<?php include ("../BackEnd/blockBugLogin.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/ketquaxettotnghiep.css">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Trường đại học Quy Nhơn</title>
</head>
<body>
<?php include("../Template_Layout/main/header.php") ?>
    <div class="content">
    <?php include("../Template_Layout/main/sidebar.php") ?>
        <div class="content__main">
            <!-- Tiêu đề trang -->
                <div class="title">Kết quả tốt nghiệp</div>
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên đợt</th>
                        <th>Tên chương trình đào tạo</th>
                        <th>Ngày xét</th>
                        <th>Kết quả</th>
                        <th>Ghi chú</th>
                        <th>Tổng số tín chỉ</th>
                        <th>TBC tích lũy</th>
                        <th>Xếp loại</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                include("../BackEnd/connectSQL.php");
                $msv = "SV001"; // Giả sử $msv được lấy từ session hoặc tham số
                $sql = "
                    SELECT kqxtn.STT, kqxtn.TenDot, kqxtn.TenChuongTrinhDaoTao, kqxtn.NgayXet, kqxtn.KetQua, 
                           kqxtn.GhiChu, kqxtn.TBCTichLuy, kqxtn.XepLoai,
                           (SELECT SUM(d.SoTinChi) 
                            FROM Diem d 
                            WHERE d.MaSinhVien = kqxtn.MaSinhVien) AS TongSoTinChi
                    FROM KetQuaXetTotNghiep AS kqxtn
                    WHERE kqxtn.MaSinhVien = '$msv'";
                $result = $conn->query($sql);

                if (!$result) {
                    die("Lỗi truy vấn: " . $conn->error);
                }

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) :
            ?>
                        <tr class="ketquaxettotnghiep-row">
                            <td class="ketquaxettotnghiep-cell"><?= $row['STT'] ?></td>
                            <td class="ketquaxettotnghiep-cell"><?= $row['TenDot'] ?></td>
                            <td class="ketquaxettotnghiep-cell"><?= $row['TenChuongTrinhDaoTao'] ?></td>
                            <td class="ketquaxettotnghiep-cell"><?= $row['NgayXet'] ?></td>
                            <td class="ketquaxettotnghiep-cell"><?= $row['KetQua'] ?></td>
                            <td class="ketquaxettotnghiep-cell"><?= $row['GhiChu'] ?></td>
                            <td class="ketquaxettotnghiep-cell"><?= $row['TongSoTinChi'] ?? 'N/A' ?></td>
                            <td class="ketquaxettotnghiep-cell"><?= $row['TBCTichLuy'] ?></td>
                            <td class="ketquaxettotnghiep-cell"><?= $row['XepLoai'] ?></td>
                        </tr>
            <?php 
                    endwhile;
                } else {
            ?>
                    <tr>
                        <td colspan="9" class="no-data">Chưa có kết quả xét tốt nghiệp</td>
                    </tr>
            <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>