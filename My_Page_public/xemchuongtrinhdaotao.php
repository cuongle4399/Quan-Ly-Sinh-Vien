<?php 
include("../BackEnd/saveLogin.php");
include("../BackEnd/connectSQL.php");

// Handle form submission
$MaNganh = '';
$selectedValueMaNganh = '';
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['ctdt'])) {
    $selectedValueMaNganh = $MaNganh = $_GET['ctdt'];
}

// Lấy học kỳ chọn
$hockySelect = '';
$selectedValueHocKy = '';
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['hocky'])) {
    $selectedValueHocKy = $hockySelect = $_GET['hocky'];
}

// Lấy tổng số học kỳ của ngành học
$soHocKy = 0;
if (!empty($MaNganh)) {
    $sqlSoHocKy = "SELECT COUNT(DISTINCT ctdt.HocKy) FROM chuongtrinhdaotao ctdt WHERE ctdt.MaNganh = '$MaNganh'";
    $resultSoHocKy = mysqli_query($conn, $sqlSoHocKy);
    $soHocKy = mysqli_fetch_array($resultSoHocKy)[0] ?? 0;
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
                <form method="GET" action="" >
                    <label for="cacctdt">Ngành học:</label>
                    <select name="ctdt" id="cacctdt" style="padding:10px;margin:10px;font-size: 18px;" onchange="this.form.submit()">
                        <option value="">-- Chọn ngành --</option>
                        <?php 
                        $queryNganh = "SELECT * FROM Nganh";
                        $resultNganh = mysqli_query($conn, $queryNganh);
                        while($rowNganh = mysqli_fetch_assoc($resultNganh)):
                            $isSelected = ($rowNganh["MaNganh"] == $selectedValueMaNganh) ? 'selected' : '';
                        ?>
                            <option value="<?= $rowNganh["MaNganh"] ?>" <?= $isSelected ?>><?= $rowNganh["TenNganh"] ?></option>
                        <?php endwhile ?>
                    </select>

                    <label for="dsHocKy">Học kỳ:</label>
                    <select name="hocky" id="dsHocKy" style="padding:10px;margin:10px;font-size: 18px;" onchange="this.form.submit()">
                        <option value="all" <?=  ($hockySelect == "all") ? 'selected' : ''; ?> >Toàn bộ</option>
                        <?php 
                        $queryHocKy = "SELECT DISTINCT HocKy FROM chuongtrinhdaotao WHERE MaNganh = '$MaNganh' ORDER BY HocKy";
                        $resultHocKy = mysqli_query($conn, $queryHocKy);
                        while($rowHocKy = mysqli_fetch_assoc($resultHocKy)):
                            $isSelected = ($rowHocKy["HocKy"] == $selectedValueHocKy) ? 'selected' : '';
                        ?>
                            <option value="<?= $rowHocKy["HocKy"] ?>" <?= $isSelected ?>><?= "Học kỳ " . $rowHocKy["HocKy"] ?></option>
                        <?php endwhile ?>
                    </select>                    
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
                            <?php if ($MaNganh) {
                                if (!isset($_GET['hocky']) || $hockySelect == 'all') {
                                    // Hiển thị tất cả học kỳ
                                    $STT = 1;
                                    for ($hocKy = 1; $hocKy <= $soHocKy; $hocKy++) {
                                        $tongTCCuaHK = "
                                            SELECT SUM(SoTinChi) AS TongSoTinChi 
                                            FROM ChuongTrinhDaoTao 
                                            WHERE HocKy = $hocKy AND MaNganh = '$MaNganh'
                                            GROUP BY HocKy
                                        ";
                                        $tmp = mysqli_query($conn, $tongTCCuaHK);
                                        $soTC = mysqli_fetch_assoc($tmp)['TongSoTinChi'] ?? 0;
                                        ?>

                                        <tr class="table__body-row">
                                            <td class="table__body-cell" colspan="10">
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
                                } else {
                                    // Hiển thị học kỳ cụ thể
                                    $STT = 1;

                                    $tongTCCuaHK = "
                                        SELECT SUM(SoTinChi) AS TongSoTinChi 
                                        FROM ChuongTrinhDaoTao 
                                        WHERE HocKy = $hockySelect AND MaNganh = '$MaNganh'
                                        GROUP BY HocKy
                                    ";
                                    $tmp = mysqli_query($conn, $tongTCCuaHK);
                                    $soTC = mysqli_fetch_assoc($tmp)['TongSoTinChi'] ?? 0;
                                ?>
                                    <tr class="table__body-row">
                                        <td class="table__body-cell" colspan="10">
                                            <?= "Học kỳ $hockySelect ($soTC tín chỉ)" ?>
                                        </td>
                                    </tr>

                                    <?php 
                                    $sql = "SELECT * FROM ChuongTrinhDaoTao 
                                        WHERE MaNganh='$MaNganh' AND HocKy = $hockySelect";
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
                                    <?php endwhile; 
                                }
                            
                            }?>

                            <?php if (!$MaNganh) { ?>

                                <tr>
                                    <td colspan="10">Vui lòng chọn ngành học!</td>
                                </tr>

                            <?php } ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>