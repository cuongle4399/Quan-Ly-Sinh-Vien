<?php include ("../BackEnd/blockBugLogin.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Trường đại học Quy Nhơn</title>
</head>
<body>
<?php include("../Template_Layout/main/header.php") ?>
    <div class = "content">
    <?php include("../Template_Layout/main/sidebar.php") ?>
    <div class="content__main">
            <div class="mailbox__nav">
                <div class="mailbox__nav-item">Thời khóa biểu</div>
            </div>

            <div class="content-main__select">
                <div class="select-item">
                    <label>Năm học:</label>
                    <select>
                        <option>2024-2025</option>
                    </select>
                </div>


                <div class="select-item">
                    <label>Học kỳ:</label>
                    <select>
                        <option>Học kỳ 2</option>
                    </select>
                </div>


                <div class="select-item">
                    <label>Tuần:</label>
                    <select>
                        <option>37(05/05/2025)</option>
                    </select>
                </div>

                <div class="select-item">
                    <select>
                        <option>Học phần</option>
                    </select>
                </div>
            </div>
            

            <div class="content-main__grade-table">
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">STT</th>
                            <th rowspan="2">Mã lớp học phần</th>
                            <th rowspan="2">Tên học phần</th>
                            <th colspan="4">Số tiết</th>
                            <th rowspan="2">Thông tin</th>
                            <th rowspan="2">Giảng viên</th>
                            <th rowspan="2">Ngày bắt đầu</th>
                            <th rowspan="2">Ngày kết thúc</th>
                        </tr>

                        
                        <tr>
                            <th>LT</th>
                            <th>TH</th>
                            <th>TL</th>
                            <th>STC</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php include("../BackEnd/connectSQL.php"); ?>

                        <?php
                            $sql = "
                                SELECT * FROM ThongTinCaNhan ttcn 
                                INNER JOIN nganh n On ttcn.MaNganh = n.MaNganh  
                                INNER JOIN ChuongTrinhDaoTao ctdt On n.MaNganh = ctdt.maNganh 
                                INNER JOIN DangKyHocPhan dkhp ON dkhp.MaHocPhan = ctdt.MaHocPhan
                                INNER JOIN KetQuaDangKyHocPhan kqdkhp ON dkhp.MaLopHocPhan = kqdkhp.MaLopHocPhan
                                AND kqdkhp.MaSinhVien = ttcn.MaSinhVien 
                                WHERE ttcn.MaSinhVien = '" . $msv . "'";
                            $result = $conn->query($sql);
                        ?>

                        <?php $i = 0; while($row = $result->fetch_assoc()): ?>

                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $row['MaLopHocPhan'] ?></td>
                                <td><?= $row['TenHocPhan'] ?></td>
                                <td><?= $row['LyThuyet'] ?></td>
                                <td><?= $row['ThucHanh'] ?></td>
                                <td><?= $row['TuLuan'] ?></td>
                                <td><?= $row['SoTinChi'] ?></td>
                                <td><?= $row['LichHoc'] ?></td>
                                <td><?= $row['GiangVien'] ?></td>
                                <td><?= $row['NgayBatDau'] ?></td>
                                <td><?= $row['NgayKetThuc'] ?></td>
                            </tr>
                            
                            
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>