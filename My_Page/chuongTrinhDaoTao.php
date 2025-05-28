<?php include ("../BackEnd/blockBugLogin.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/table_chuongTrinhDaoTao.css">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    
    <title>Trường đại học Quy Nhơn</title>
</head>
<body>
<?php include("../Template_Layout/main/header.php") ?>
    <div class = "content">
        <?php include("../Template_Layout/main/sidebar.php") ?>

        <div class="content__main">
            <!-- Tiêu đề trang -->
            <div class="title">
                <div>Chương trình đào tạo</div>
            </div>

            <div class="main__select">
                <label>Chương trình ĐT: </label>
                <select>
                    <option>Công nghệ thông tin</option>
                </select>
            </div>

            <div class="main__table">
                <table class="table">
                    <!-- Header -->
                    <thead class="table__head">
                        <tr class="table__head-row">
                            <th class="table__head-cell" rowspan="2">TT</th>
                            <th class="table__head-cell" rowspan="2">MÃ HỌC PHẦN</th>
                            <th class="table__head-cell" rowspan="2">TÊN HỌC PHẦN</th>
                            <th class="table__head-cell" rowspan="2">SỐ TC</th>
                            <th class="table__head-cell" colspan="7">SỐ TIẾT</th>
                            <th class="table__head-cell" rowspan="2">HỌC PHẦN HỌC TRƯỚC</th>
                            <th class="table__head-cell" rowspan="2">HỌC PHẦN THAY THẾ</th>
                        </tr>

                        <tr class="table__head-row">
                            <th class="table__head-cell">LT</th>
                            <th class="table__head-cell">TH</th>
                            <th class="table__head-cell">TL</th>
                            <th class="table__head-cell">TT</th>
                            <th class="table__head-cell">BTL</th>
                            <th class="table__head-cell">DA</th>
                            <th class="table__head-cell">KL</th>
                        </tr>
                    </thead>

                    <?php include("../BackEnd/connectSQL.php") ?>

                    <?php
                        // Lấy mã ngành của sinh viên hiện tại
                        $sqlMaNganh = "SELECT ttcn.MaNganh FROM ThongTinCaNhan ttcn WHERE MaSinhVien = 'SV001'";
                        $resultMaNganh = $conn->query($sqlMaNganh);
                        $maNganh = $resultMaNganh->fetch_assoc()['MaNganh'];

                        // Lệnh sql truy vấn tổng số tính chỉ của học kỳ cụ thể
 						$tongTCCuaHK = "SELECT ctdt.HocKy , SUM(ctdt.SoTinChi) AS TongSoTinChi 
							FROM ChuongTrinhDaoTao ctdt
							WHERE ctdt.HocKy = ? AND MaNganh = '$maNganh'
							GROUP BY ctdt.HocKy 
                        ";  
                        
                        // Số Thứ Tự
                        $i= 1;
                    ?>

                    <tbody class="table__body">
                        <!-- HỌC KỲ 1 -->

                        <?php
                            $HK = 1;
                            $HK1 = $conn->prepare($tongTCCuaHK);
                            $HK1->bind_param("i", $HK);
                            $HK1->execute();
                            $resultHK1 = $HK1->get_result();
                            $tongSoTCHK1 = $resultHK1->fetch_assoc()['TongSoTinChi'];
                        ?>

                        <tr class="table__body-row">
                            <td class="table__body-cell" colspan="13"><?= "Học kỳ 1 ($tongSoTCHK1 tín chỉ)" ?></td>
                        </tr>


                        <?php
                            $sql = "
                                SELECT ctdt.* FROM ThongTinCaNhan ttcn
                                INNER JOIN Nganh n ON n.MaNganh = ttcn.MaNganh
                                INNER JOIN ChuongTrinhDaoTao ctdt ON ctdt.MaNganh = n.MaNganh
                                WHERE ttcn.MaSinhVien = '" . $msv . "' AND HocKy = 1
                            ";                     

                            $result = $conn->query($sql);
                         ?>

                        <?php
                         while ($row = $result->fetch_assoc()) : ?>
                            
                            <tr class="table__body-row">
                                <td class="table__body-cell"> <?= $i++ ?> </td>
                                <td class="table__body-cell"> <?= $row['MaHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TenHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['SoTinChi'] ?> </td>
                                <td class="table__body-cell"> <?= $row['LyThuyet'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucHanh'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TuLuan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucTap'] ?> </td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"> <?= $row['HocPhanHocTruoc'] ?> </td>
                                <td class="table__body-cell"> <?= $row['HocPhanThayThe'] ?> </td>
                            </tr>

                        <?php endwhile; ?>

                        <!-- HỌC KỲ 2 -->
                        <?php
                            $HK = 2;
                            $HK2 = $conn->prepare($tongTCCuaHK);
                            $HK2->bind_param("i", $HK);
                            $HK2->execute();
                            $resultHK2 = $HK2->get_result();
                            $tongSoTCHK2 = $resultHK2->fetch_assoc()['TongSoTinChi'];
                        ?>

                        <tr class="table__body-row">
                            <td class="table__body-cell" colspan="13"><?= "Học kỳ $HK ($tongSoTCHK2 tín chỉ)" ?></td>
                        </tr>

                        <?php
                            $sql = "
                                SELECT ctdt.* FROM ThongTinCaNhan ttcn
                                INNER JOIN Nganh n ON n.MaNganh = ttcn.MaNganh
                                INNER JOIN ChuongTrinhDaoTao ctdt ON ctdt.MaNganh = n.MaNganh
                                WHERE ttcn.MaSinhVien = '" . $msv . "' AND HocKy = 2
                            ";
                            $result = $conn->query($sql);
                        ?>

                        <?php  
                         while ($row = $result->fetch_assoc()) : ?>
                            
                            <tr class="table__body-row">
                                <td class="table__body-cell"> <?= $i++ ?> </td>
                                <td class="table__body-cell"> <?= $row['MaHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TenHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['SoTinChi'] ?> </td>
                                <td class="table__body-cell"> <?= $row['LyThuyet'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucHanh'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TuLuan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucTap'] ?> </td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"> <?= $row['HocPhanHocTruoc'] ?> </td>
                                <td class="table__body-cell"> <?= $row['HocPhanThayThe'] ?> </td>
                            </tr>

                        <?php endwhile; ?>


                        <!-- HỌC KỲ 3 -->
                        <?php
                            $HK = 3;
                            $HK3 = $conn->prepare($tongTCCuaHK);
                            $HK3->bind_param("i", $HK);
                            $HK3->execute();
                            $resultHK3 = $HK3->get_result();
                            $tongSoTCHK3 = $resultHK3->fetch_assoc()['TongSoTinChi'];
                        ?>

                        <tr class="table__body-row">
                            <td class="table__body-cell" colspan="13"><?= "Học kỳ $HK ($tongSoTCHK3 tín chỉ)" ?></td>
                        </tr>

                        <?php
                            $sql = "
                                SELECT ctdt.* FROM ThongTinCaNhan ttcn
                                INNER JOIN Nganh n ON n.MaNganh = ttcn.MaNganh
                                INNER JOIN ChuongTrinhDaoTao ctdt ON ctdt.MaNganh = n.MaNganh
                                WHERE ttcn.MaSinhVien = '" . $msv . "' AND HocKy = 3
                            ";
                            $result = $conn->query($sql);
                        ?>

                        <?php 
                         while ($row = $result->fetch_assoc()) : ?>
                            
                            <tr class="table__body-row">
                                <td class="table__body-cell"> <?= $i++ ?> </td>
                                <td class="table__body-cell"> <?= $row['MaHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TenHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['SoTinChi'] ?> </td>
                                <td class="table__body-cell"> <?= $row['LyThuyet'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucHanh'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TuLuan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucTap'] ?> </td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"> <?= $row['HocPhanHocTruoc'] ?> </td>
                                <td class="table__body-cell"> <?= $row['HocPhanThayThe'] ?> </td>
                            </tr>

                        <?php endwhile; ?>
                        
                        <!-- HỌC KỲ 4 -->
                        <?php
                            $HK = 4;
                            $HK4 = $conn->prepare($tongTCCuaHK);
                            $HK4->bind_param("i", $HK);
                            $HK4->execute();
                            $resultHK4 = $HK4->get_result();
                            $tongSoTCHK4 = $resultHK4->fetch_assoc()['TongSoTinChi'];
                        ?>

                        <tr class="table__body-row">
                            <td class="table__body-cell" colspan="13"><?= "Học kỳ $HK ($tongSoTCHK4 tín chỉ)" ?></td>
                        </tr>

                        <?php
                            $sql = "
                                SELECT ctdt.* FROM ThongTinCaNhan ttcn
                                INNER JOIN Nganh n ON n.MaNganh = ttcn.MaNganh
                                INNER JOIN ChuongTrinhDaoTao ctdt ON ctdt.MaNganh = n.MaNganh
                                WHERE ttcn.MaSinhVien = '" . $msv . "' AND HocKy = 4
                            ";
                            $result = $conn->query($sql);
                        ?>

                        <?php 
                         while ($row = $result->fetch_assoc()) : ?>
                            
                            <tr class="table__body-row">
                                <td class="table__body-cell"> <?= $i++ ?> </td>
                                <td class="table__body-cell"> <?= $row['MaHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TenHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['SoTinChi'] ?> </td>
                                <td class="table__body-cell"> <?= $row['LyThuyet'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucHanh'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TuLuan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucTap'] ?> </td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"> <?= $row['HocPhanHocTruoc'] ?> </td>
                                <td class="table__body-cell"> <?= $row['HocPhanThayThe'] ?> </td>
                            </tr>

                        <?php endwhile; ?>

                        <!-- HỌC KỲ 5 -->
                        <?php
                            $HK = 5;
                            $HK5 = $conn->prepare($tongTCCuaHK);
                            $HK5->bind_param("i", $HK);
                            $HK5->execute();
                            $resultHK5 = $HK5->get_result();
                            $tongSoTCHK5 = $resultHK5->fetch_assoc()['TongSoTinChi'];
                        ?>

                        <tr class="table__body-row">
                            <td class="table__body-cell" colspan="13"><?= "Học kỳ $HK ($tongSoTCHK5 tín chỉ)" ?></td>
                        </tr>

                        <?php
                            $sql = "
                                SELECT ctdt.* FROM ThongTinCaNhan ttcn
                                INNER JOIN Nganh n ON n.MaNganh = ttcn.MaNganh
                                INNER JOIN ChuongTrinhDaoTao ctdt ON ctdt.MaNganh = n.MaNganh
                                WHERE ttcn.MaSinhVien = '" . $msv . "' AND HocKy = 5
                            ";
                            $result = $conn->query($sql);
                        ?>

                        <?php 
                         while ($row = $result->fetch_assoc()) : ?>
                            
                            <tr class="table__body-row">
                                <td class="table__body-cell"> <?= $i++ ?> </td>
                                <td class="table__body-cell"> <?= $row['MaHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TenHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['SoTinChi'] ?> </td>
                                <td class="table__body-cell"> <?= $row['LyThuyet'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucHanh'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TuLuan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucTap'] ?> </td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"> <?= $row['HocPhanHocTruoc'] ?> </td>
                                <td class="table__body-cell"> <?= $row['HocPhanThayThe'] ?> </td>
                            </tr>

                        <?php endwhile; ?>

                        <!-- HỌC KỲ 6 -->
                        <?php
                            $HK = 6;
                            $HK6 = $conn->prepare($tongTCCuaHK);
                            $HK6->bind_param("i", $HK);
                            $HK6->execute();
                            $resultHK6 = $HK6->get_result();
                            $tongSoTCHK6 = $resultHK6->fetch_assoc()['TongSoTinChi'];
                        ?>

                        <tr class="table__body-row">
                            <td class="table__body-cell" colspan="13"><?= "Học kỳ $HK ($tongSoTCHK6 tín chỉ)" ?></td>
                        </tr>

                        <?php
                            $sql = "
                                SELECT ctdt.* FROM ThongTinCaNhan ttcn
                                INNER JOIN Nganh n ON n.MaNganh = ttcn.MaNganh
                                INNER JOIN ChuongTrinhDaoTao ctdt ON ctdt.MaNganh = n.MaNganh
                                WHERE ttcn.MaSinhVien = '" . $msv . "' AND HocKy = 6
                            ";
                            $result = $conn->query($sql);
                        ?>

                        <?php  
                         while ($row = $result->fetch_assoc()) : ?>
                            
                            <tr class="table__body-row">
                                <td class="table__body-cell"> <?= $i++ ?> </td>
                                <td class="table__body-cell"> <?= $row['MaHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TenHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['SoTinChi'] ?> </td>
                                <td class="table__body-cell"> <?= $row['LyThuyet'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucHanh'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TuLuan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucTap'] ?> </td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"> <?= $row['HocPhanHocTruoc'] ?> </td>
                                <td class="table__body-cell"> <?= $row['HocPhanThayThe'] ?> </td>
                            </tr>

                        <?php endwhile; ?>

                        <!-- HỌC KỲ 7 -->
                        <?php
                            $HK = 7;
                            $HK7 = $conn->prepare($tongTCCuaHK);
                            $HK7->bind_param("i", $HK);
                            $HK7->execute();
                            $resultHK7 = $HK7->get_result();
                            $tongSoTCHK7 = $resultHK7->fetch_assoc()['TongSoTinChi'];
                        ?>

                        <tr class="table__body-row">
                            <td class="table__body-cell" colspan="13"><?= "Học kỳ $HK ($tongSoTCHK7 tín chỉ)" ?></td>
                        </tr>

                        <?php
                            $sql = "
                                SELECT ctdt.* FROM ThongTinCaNhan ttcn
                                INNER JOIN Nganh n ON n.MaNganh = ttcn.MaNganh
                                INNER JOIN ChuongTrinhDaoTao ctdt ON ctdt.MaNganh = n.MaNganh
                                WHERE ttcn.MaSinhVien = '" . $msv . "' AND HocKy = 7
                            ";
                            $result = $conn->query($sql);
                        ?>

                        <?php  
                         while ($row = $result->fetch_assoc()) : ?>
                            
                            <tr class="table__body-row">
                                <td class="table__body-cell"> <?= $i++ ?> </td>
                                <td class="table__body-cell"> <?= $row['MaHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TenHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['SoTinChi'] ?> </td>
                                <td class="table__body-cell"> <?= $row['LyThuyet'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucHanh'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TuLuan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucTap'] ?> </td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"> <?= $row['HocPhanHocTruoc'] ?> </td>
                                <td class="table__body-cell"> <?= $row['HocPhanThayThe'] ?> </td>
                            </tr>

                        <?php endwhile; ?>

                        <!-- HỌC KỲ 8 -->
                        <?php
                            $HK = 8;
                            $HK8 = $conn->prepare($tongTCCuaHK);
                            $HK8->bind_param("i", $HK);
                            $HK8->execute();
                            $resultHK8 = $HK8->get_result();
                            $tongSoTCHK8 = $resultHK8->fetch_assoc()['TongSoTinChi'];
                        ?>

                        <tr class="table__body-row">
                            <td class="table__body-cell" colspan="13"><?= "Học kỳ $HK ($tongSoTCHK8 tín chỉ)" ?></td>
                        </tr>

                        <?php
                            $sql = "
                                SELECT ctdt.* FROM ThongTinCaNhan ttcn
                                INNER JOIN Nganh n ON n.MaNganh = ttcn.MaNganh
                                INNER JOIN ChuongTrinhDaoTao ctdt ON ctdt.MaNganh = n.MaNganh
                                WHERE ttcn.MaSinhVien = '" . $msv . "' AND HocKy = 8
                            ";
                            $result = $conn->query($sql);
                        ?>

                        <?php  
                         while ($row = $result->fetch_assoc()) : ?>
                            
                            <tr class="table__body-row">
                                <td class="table__body-cell"> <?= $i++ ?> </td>
                                <td class="table__body-cell"> <?= $row['MaHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TenHocPhan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['SoTinChi'] ?> </td>
                                <td class="table__body-cell"> <?= $row['LyThuyet'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucHanh'] ?> </td>
                                <td class="table__body-cell"> <?= $row['TuLuan'] ?> </td>
                                <td class="table__body-cell"> <?= $row['ThucTap'] ?> </td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"></td>
                                <td class="table__body-cell"> <?= $row['HocPhanHocTruoc'] ?> </td>
                                <td class="table__body-cell"> <?= $row['HocPhanThayThe'] ?> </td>
                            </tr>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>