<?php include ("../BackEnd/blockBugLogin.php") ?>
<?php include("../BackEnd/connectSQL.php") ?>

<?php
    $sqlMaNganh = "SELECT ttcn.MaNganh FROM ThongTinCaNhan ttcn WHERE MaSinhVien = '$msv'";
    $resultMaNganh = mysqli_query($conn,$sqlMaNganh);
    $maNganh = mysqli_fetch_assoc($resultMaNganh)["MaNganh"];

    $sqlTenNganh = "SELECT TenNganh FROM Nganh WHERE MaNganh='$maNganh'";
    $resultTenNganh = mysqli_query($conn,$sqlTenNganh);
    $tenNganh = mysqli_fetch_assoc($resultTenNganh)["TenNganh"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/table_chuongTrinhDaoTao.css">
    <title>Trường đại học Quy Nhơn</title>
</head>
<body>
    <?php include("../Template_Layout/main/header.php") ?>
    <div class="content">
        <?php include("../Template_Layout/main/sidebar.php") ?>
        <div class="content__main">
            <div class="title">
                <div>Chương trình đào tạo</div>
            </div>
            <div class="main__select">
                <label>Chương trình ĐT: </label>
                <select>
                    <option><?= $tenNganh ?></option>
                </select>
            </div>
            <div class="main__table">
                <table class="table">
                    <thead class="table__head">
                        <tr class="table__head-row">
                            <th class="table__head-cell" rowspan="2">TT</th>
                            <th class="table__head-cell" rowspan="2">MÃ HỌC PHẦN</th>
                            <th class="table__head-cell" rowspan="2">TÊN HỌC PHẦN</th>
                            <th class="table__head-cell" rowspan="2">SỐ TC</th>
                            <th class="table__head-cell" colspan="4">SỐ TIẾT</th>
                            <th class="table__head-cell" rowspan="2">HỌC PHẦN HỌC TRƯỚC</th>
                            <th class="table__head-cell" rowspan="2">HỌC PHẦN THAY THẾ</th>
                        </tr>
                        <tr class="table__head-row">
                            <th class="table__head-cell">LT</th>
                            <th class="table__head-cell">TH</th>
                            <th class="table__head-cell">TL</th>
                            <th class="table__head-cell">TT</th>
                        </tr>
                    </thead>

                    <tbody class="table__body">
                        <?php 
                            $hocKy = 0;
                            $STT = 1;
                            while ($hocKy++ < 8) :
                                $tongTCCuaHK = "
                                    SELECT ctdt.HocKy, SUM(ctdt.SoTinChi) AS TongSoTinChi 
                                    FROM ChuongTrinhDaoTao ctdt
                                    WHERE ctdt.HocKy = $hocKy AND ctdt.MaNganh = '$maNganh'
                                    GROUP BY ctdt.HocKy
                                ";
                                $tmp = mysqli_query($conn, $tongTCCuaHK);
                                $soTC = $tmp->fetch_assoc()['TongSoTinChi'];
                                $sql = "
                                    SELECT ctdt.* FROM ThongTinCaNhan ttcn
                                    INNER JOIN Nganh n ON n.MaNganh = ttcn.MaNganh
                                    INNER JOIN ChuongTrinhDaoTao ctdt ON ctdt.MaNganh = n.MaNganh
                                    WHERE ttcn.MaSinhVien = '$msv' AND ctdt.HocKy = $hocKy
                                ";
                                $result = mysqli_query($conn, $sql);
                        ?>
                            <tr class="table__body-row">
                                <td class="table__body-cell" colspan="13">
                                    <?= "Học kỳ $hocKy ($soTC tín chỉ)" ?>
                                </td>
                            </tr>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr class="table__body-row">
                                    <td class="table__body-cell"> <?= $STT++ ?> </td>
                                    <td class="table__body-cell"> <?= $row['MaHocPhan'] ?> </td>
                                    <td class="table__body-cell"> <?= $row['TenHocPhan'] ?> </td>
                                    <td class="table__body-cell"> <?= $row['SoTinChi'] ?> </td>
                                    <td class="table__body-cell"> <?= $row['LyThuyet'] ?> </td>
                                    <td class="table__body-cell"> <?= $row['ThucHanh'] ?> </td>
                                    <td class="table__body-cell"> <?= $row['TuLuan'] ?> </td>
                                    <td class="table__body-cell"> <?= $row['ThucTap'] ?> </td>
                                    <td class="table__body-cell"> <?= $row['HocPhanHocTruoc'] ?> </td>
                                    <td class="table__body-cell"> <?= $row['HocPhanThayThe'] ?> </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>
