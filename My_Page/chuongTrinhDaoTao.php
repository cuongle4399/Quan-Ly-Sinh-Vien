<?php include ("../BackEnd/blockBugLogin.php") ?>
<?php include("../BackEnd/connectSQL.php") ?>

<?php
    $sqlMaNganh = "SELECT ttcn.MaNganh FROM ThongTinCaNhan ttcn WHERE MaSinhVien = '$msv'";
    $resultMaNganh = mysqli_query($conn,$sqlMaNganh);
    if (!$resultMaNganh) {
        die("Lỗi truy vấn MaNganh: " . mysqli_error($conn));
    }

    $maNganh = mysqli_fetch_assoc($resultMaNganh)["MaNganh"];

    $sqlTenNganh = "SELECT TenNganh FROM Nganh WHERE MaNganh='$maNganh'";
    $resultTenNganh = mysqli_query($conn,$sqlTenNganh);
    $tenNganh = mysqli_fetch_assoc($resultTenNganh)["TenNganh"];

    $sql = "SELECT * FROM chuongtrinhdaotao WHERE MaNganh='$maNganh'";
    $resultSql = mysqli_query($conn, $sql);

    $chuongtrinhdaotao = [];
    while ($row = mysqli_fetch_assoc($resultSql)) {
        $chuongtrinhdaotao[] = [
            'MaHocPhan' => $row['MaHocPhan'],
            'TenHocPhan' => $row['TenHocPhan'],
            'SoTinChi' => $row['SoTinChi'],
            'LyThuyet' => $row['LyThuyet'],
            'ThucHanh' => $row['ThucHanh'],
            'TuLuan' => $row['TuLuan'],
            'ThucTap' => $row['ThucTap'],
            'HocPhanHocTruoc' => $row['HocPhanHocTruoc'],
            'HocPhanThayThe' => $row['HocPhanThayThe'],
            'MaNganh' => $row['MaNganh'],
            'HocKy' => $row['HocKy']
        ];
    }

    // Lấy học kỳ chọn
    $hockySelect = '';
    $selectedValueHocKy = '';
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['hocky'])) {
        $selectedValueHocKy = $_GET['hocky'];
        $hockySelect = $_GET['hocky'];
    }

    // Lấy tất số học kỳ của ngành
    $maxHocKy = 0;
    foreach ($chuongtrinhdaotao as $hocPhan) {

        $hocKy = (int)$hocPhan['HocKy'];
        if ($hocKy > $maxHocKy) {
            $maxHocKy = $hocKy;
        }
    }
    
    function layDanhSachHP ($hocky, $chuongtrinhdaotao) {

        $dsHP = [];

        foreach ($chuongtrinhdaotao as $ct) {
            if ($ct['HocKy'] == $hocky) {
                $dsHP[] = $ct;
            }
        }

        return $dsHP;
    }

    function tongSoTinChi($dsHP) {
        $tong = 0;
        foreach ($dsHP as $hocPhan) {
            // Chuyển "SoTinChi" thành số nguyên trước khi cộng
            $soTinChi = (int)$hocPhan['SoTinChi'];
            $tong += $soTinChi;
        }
        return $tong;
    }

    function hienThiHocPhanTheoHocKy($hocky, $dsHP) {
        global $STT;

        $tongTC = tongSoTinChi($dsHP);

        echo '<tr class="table__body-row">';
        echo '<td class="table__body-cell" colspan="10">';
        echo 'Học kỳ ' .  $hocky . ' (' . $tongTC  . ' tín chỉ)';
        echo '</td>';
        echo '</tr>';

        foreach ($dsHP as $hocphan) {
            if ($hocphan['HocKy'] == $hocky) {
                echo '<tr class="table__body-row">';
                echo '<td class="table__body-cell">' . $STT++ . '</td>';
                echo '<td class="table__body-cell">' . htmlspecialchars($hocphan['MaHocPhan']) . '</td>';
                echo '<td class="table__body-cell">' . htmlspecialchars($hocphan['TenHocPhan']) . '</td>';
                echo '<td class="table__body-cell">' . htmlspecialchars($hocphan['SoTinChi']) . '</td>';
                echo '<td class="table__body-cell">' . htmlspecialchars($hocphan['LyThuyet']) . '</td>';
                echo '<td class="table__body-cell">' . htmlspecialchars($hocphan['ThucHanh']) . '</td>';
                echo '<td class="table__body-cell">' . htmlspecialchars($hocphan['TuLuan']) . '</td>';
                echo '<td class="table__body-cell">' . htmlspecialchars($hocphan['ThucTap']) . '</td>';
                echo '<td class="table__body-cell">' . htmlspecialchars($hocphan['HocPhanHocTruoc']) . '</td>';
                echo '<td class="table__body-cell">' . htmlspecialchars($hocphan['HocPhanThayThe']) . '</td>';
                echo '</tr>';
            }
        }
    }
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
                <select style="padding:10px;margin:10px;font-size: 18px;">
                    <option><?= htmlspecialchars($tenNganh) ?></option>
                </select>

                <form method="get" action="">
                    <label for="dsHocKy">Học kỳ:</label>
                    <select name="hocky" id="dsHocKy" style="padding:10px;margin:10px;font-size: 18px;" onchange="this.form.submit()">
                        <option value="" <?= ($hockySelect == '') ? 'selected' : '' ?>>-- Tất cả --</option>
                        <?php for ($i = 1; $i <= $maxHocKy; $i++) { ?>
                            <option value="<?= $i ?>" <?= ($i ==  $hockySelect ? "selected" : "") ?>> <?= "Học kỳ " . $i ?> </option>
                        <?php } ?>
                    </select> 
                </form>
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
                        $STT = 1; 
                        if ($hockySelect == '') { // Hiển thị tẩt cả
                            for ($i = 1; $i <= $maxHocKy; $i++) {
                                $dsHP = layDanhSachHP($i, $chuongtrinhdaotao);
                                hienThiHocPhanTheoHocKy($i, $dsHP);
                            }
                        } else {
                            $dsHP = layDanhSachHP($hockySelect, $chuongtrinhdaotao);
                            hienThiHocPhanTheoHocKy($hockySelect, $dsHP);
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>
