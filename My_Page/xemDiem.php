<?php include ("../BackEnd/blockBugLogin.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/xemDiem.css">
    <link rel="stylesheet" href="../css/mainIN.css">
    <title>Trường đại học Quy Nhơn</title>
</head>
<body>
<?php include("../Template_Layout/main/header.php") ?>
    <div class="content">
    <?php include("../Template_Layout/main/sidebar.php") ?>
        <div class="content__main">
            <div class="content__xemDiem">
                <div class="form">
                    <div class="form__choose">
                        <label class="form__choose-lable">Chương trình đào tạo: </label>
                        <select class="form__choose-btn">
                            <option>Sư Phạm Tin học</option>
                            <option>Toán</option>
                        </select>
                        <label>Năm học</label>
                        <select class="form__choose-btn">
                            <option>Tất cả</option>
                        </select>
                        <label>Năm học</label>
                        <select class="form__choose-btn">
                            <option>tất cả</option>
                        </select>
                    </div>
                    <div style="text-decoration: 2px underline black;">Ghi chú</div>
                    <div>1. Những môn có dấu <span style="color: red">(*)</span> sẽ không tính điểm trung bình chỉ là môn điều kiện</div>
                    <table>
                        <tr>
                            <th>STT</th>
                            <th>Mã học phần</th>
                            <th class="tenhp">Tên học phần</th>
                            <th>Tín chỉ</th>
                            <th>Điểm 10</th>
                            <th>Điểm 4</th>
                            <th>Điểm chữ</th>
                            <th>Kết quả</th>
                            <th>Chi tiết</th>
                        </tr>
                        <tr><td colspan="9" class="title1">Năm học 2015-2016 học kỳ: HK1</td></tr>


                        <?php
                        include("../BackEnd/connectSQL.php");
                        $msv = "SV001"; // Giả sử $msv được lấy từ session hoặc tham số
                        $sql = "
                            SELECT d.*, ctdt.MaHocPhan, ctdt.TenHocPhan
                            FROM Diem d
                            INNER JOIN DangKyHocPhan dkhp ON d.MaLopHocPhan = dkhp.MaLopHocPhan
                            INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
                            WHERE d.MaSinhVien = '$msv'";
                        $result = $conn->query($sql);
                        ?>


                        <?php $i = 0;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $row['MaHocPhan'] ?></td>
                                <td><?= $row['TenHocPhan'] ?></td>
                                <td><?= $row['SoTinChi'] ?></td>
                                <td><?= number_format($row['Diem10'], 2) ?></td>
                                <td><?= number_format($row['Diem4'], 2) ?></td>
                                <td><?= $row['DiemChu'] ?></td>
                                <td>
                                    <img src="../image/Dau.png" alt="tich" <?php echo $row['KetQua'] == 'Đạt' ? '' : 'style="display:none;"' ?>>
                                </td>
                                <td>
                                    <img src="../image/detail.png">
                                </td>
                            </tr>
                        <?php endwhile; ?>


                    </table>
                </div>
                <div class="form__Tongket">
                    <div class="form__Tongket-child1">
                        <?php
                        $sql_tongket = "
                            SELECT
                                MaSinhVien,
                                SUM(SoTinChi) AS TongTinChi,
                                SUM(CASE WHEN KetQua = 'Đạt' THEN SoTinChi ELSE 0 END) AS TinChiDat,
                                SUM(CASE WHEN KetQua <> 'Đạt' THEN SoTinChi ELSE 0 END) AS TinChiKhongDat,
                                ROUND(SUM(Diem10 * SoTinChi) / SUM(SoTinChi), 2) AS DiemTrungBinh10,
                                ROUND(SUM(Diem4 * SoTinChi) / SUM(SoTinChi), 2) AS DiemTrungBinh4,
                                CASE
                                    WHEN SUM(Diem10 * SoTinChi) / NULLIF(SUM(SoTinChi), 0) < 5 THEN 'Yếu'
                                    WHEN SUM(Diem10 * SoTinChi) / NULLIF(SUM(SoTinChi), 0) < 6.5 THEN 'Trung Bình'
                                    WHEN SUM(Diem10 * SoTinChi) / NULLIF(SUM(SoTinChi), 0) < 8 THEN 'Khá'
                                    ELSE 'Giỏi'
                                END AS XepLoai
                            FROM Diem
                            WHERE MaSinhVien = '$msv'
                            GROUP BY MaSinhVien
                        ";
                        $result_tongket = $conn->query($sql_tongket);
                        $row_tongket = $result_tongket->fetch_assoc();
                        ?>
                        <div>Tổng số tín chỉ: <?= $row_tongket['TongTinChi'] ?></div>
                        <div>Số tín chỉ: <?= $row_tongket['TinChiDat'] ?> . Số tín chỉ không đạt: <?= $row_tongket['TinChiKhongDat'] ?></div>
                        <div>Điểm trung bình (hệ 10): <?= $row_tongket['DiemTrungBinh10'] ?></div>
                        <div>Điểm trung bình học kỳ (hệ 4): <?= $row_tongket['DiemTrungBinh4'] ?> . Điểm rèn luyện: . Xếp loại: <?= $row_tongket['XepLoai'] ?></div>
                    </div>
                    <div class="form__Tongket-child2">
                        <div>Số tín chỉ tích lũy: <?= $row_tongket['TinChiDat'] ?></div>
                        <div>Điểm trung bình (hệ 10) tích lũy: <?= $row_tongket['DiemTrungBinh10'] ?></div>
                        <div>Điểm trung bình học kỳ (hệ 4) tích lũy: <?= $row_tongket['DiemTrungBinh4'] ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>

