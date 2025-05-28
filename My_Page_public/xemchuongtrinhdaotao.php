<?php 
include("../BackEnd/saveLogin.php");
include("../BackEnd/connectSQL.php");

// Handle form submission
$MaNganh = '';
$selectedValue = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ctdt'])) {
    $selectedValue = $MaNganh = $_POST['ctdt'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem chương trình đào tạo</title>
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/table_chuongTrinhDaoTao.css">
</head>
<body>
    <?php include("../Template_Layout/main/header.php") ?>

    <main class="main">
        <?php include("../My_Page_public/sidebar.php") ?>

        <div class="main__content">
            <div class="main__content-title">
                <p>Thông báo chung</p>
            </div>
            
            <div class="main__content-content">
                <form method="post" action="">
                    <label for="cacctdt">Ngành học:</label>
                    <select name="ctdt" id="cacctdt" style="padding:10px;margin:10px;font-size: 18px;">
                        <?php 
                        $queryNganh = "SELECT * FROM Nganh";
                        $resultNganh = mysqli_query($conn, $queryNganh);
                        while($rowNganh = mysqli_fetch_assoc($resultNganh)):
                            $isSelected = ($rowNganh["MaNganh"] == $selectedValue) ? 'selected' : '';
                        ?>
                            <option value="<?= $rowNganh["MaNganh"] ?>" <?= $isSelected ?>><?= $rowNganh["TenNganh"] ?></option>
                        <?php endwhile ?>
                    </select>
                    <input type="submit" value="Xem CTDT" style="padding:10px;font-size: 18px;">
                </form>

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
                            if ($MaNganh) {
                                $STT = 1;
                                for ($hocKy = 1; $hocKy <= 8; $hocKy++) {
                                    $tongTCCuaHK = "
                                        SELECT SUM(SoTinChi) AS TongSoTinChi 
                                        FROM ChuongTrinhDaoTao 
                                        WHERE HocKy = $hocKy AND MaNganh = '$MaNganh'
                                        GROUP BY HocKy
                                    ";
                                    $tmp = mysqli_query($conn, $tongTCCuaHK);
                                    $soTC = $tmp->fetch_assoc()['TongSoTinChi'] ?? 0;
                            ?>
                                <tr class="table__body-row">
                                    <td class="table__body-cell" colspan="13">
                                        <?= "Học kỳ $hocKy ($soTC tín chỉ)" ?>
                                    </td>
                                </tr>
                                <?php 
                                    $sql = "SELECT * FROM ChuongTrinhDaoTao 
                                           WHERE MaNganh='$MaNganh' AND HocKy = $hocKy";
                                    $result = mysqli_query($conn, $sql);
                                    while ($row = mysqli_fetch_assoc($result)) : 
                                ?>
                                    <tr class="table__body-row">
                                        <td class="table__body-cell"><?= $STT++ ?></td>
                                        <td class="table__body-cell"><?= $row['MaHocPhan'] ?></td>
                                        <td class="table__body-cell"><?= $row['TenHocPhan'] ?></td>
                                        <td class="table__body-cell"><?= $row['SoTinChi'] ?></td>
                                        <td class="table__body-cell"><?= $row['LyThuyet'] ?></td>
                                        <td class="table__body-cell"><?= $row['ThucHanh'] ?></td>
                                        <td class="table__body-cell"><?= $row['TuLuan'] ?></td>
                                        <td class="table__body-cell"><?= $row['ThucTap'] ?></td>
                                        <td class="table__body-cell"><?= $row['HocPhanHocTruoc'] ?></td>
                                        <td class="table__body-cell"><?= $row['HocPhanThayThe'] ?></td>
                                    </tr>
                                <?php 
                                    endwhile;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html> 