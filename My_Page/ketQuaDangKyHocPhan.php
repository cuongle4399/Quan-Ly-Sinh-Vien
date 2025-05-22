<?php include ("../BackEnd/blockBugLogin.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/ketquadangkyhocphan.css">
    <title>Đăng ký học phần</title>
</head>
<body>
<?php include("../Template_Layout/main/header.php") ?>
    <div class="content">
    <?php include("../Template_Layout/main/sidebar.php") ?>
        <div class="main-content">
            <div class="panel">
                <div class="panel-heading"><strong>Đăng ký học phần</strong></div>
                <div class="panel-body">
                    <label>Chương trình đào tạo:</label>
                    <fieldset>
                        <table>
                            <thead>
                                <th>STT</th>
                                <th>Mã lớp học phần</th>
                                <th>Tên học phần</th>
                                <th>STC</th>
                                <th>Ngày đăng ký</th>
                            </thead>
                            <tbody>
                            <?php
            include("../BackEnd/connectSQL.php");

            $sql = "
                SELECT kqdkhp.*, ctdt.SoTinChi FROM KetQuaDangKyHocPhan kqdkhp
                INNER JOIN DangKyHocPhan dkhp ON dkhp.MaLopHocPhan = kqdkhp.MaLopHocPhan
                INNER JOIN ChuongTrinhDaoTao ctdt ON ctdt.MaHocPhan = dkhp.MaHocPhan
				WHERE MaSinhVien = '" . $msv . "'";
            $result = $conn->query($sql);
            $i= 0;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $i++. "</td>";
                    echo "<td>" . $row['MaLopHocPhan'] . "</td>";
                    echo "<td>" . $row['TenHocPhan'] . "</td>";
                    echo "<td>" . $row['SoTinChi'] . "</td>";
                    echo "<td>" . $row['NgayDangKy'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>Không có dữ liệu</td></tr>";
            }

            $conn->close();
        ?>
                            </tbody>
                        </table>
                    </fieldset>
                    
                </div>
            </div>
        </div>  
            
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>
