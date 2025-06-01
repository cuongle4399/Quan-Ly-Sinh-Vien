<?php include ("../BackEnd/blockBugLogin.php") ?>;
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/cthp.css">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Trường đại học Quy Nhơn</title>
</head>
<body>
  <?php include("../Template_Layout/main/header.php"); ?>


  <div class="content">
    <?php include("../Template_Layout/main/sidebar.php"); ?>


    <div class="content__main">
      <header>
        <h1>Tài chính sinh viên</h1>
      </header>
      <div class="main__table">
        <table class="table">
          <!-- Header -->
          <thead class="chitiet">
            <tr class="table__head-row">
              <th class="chitiet-cell" rowspan="7">Mã phí</th>
              <th class="chitiet-cell" rowspan="4">Tên phí</th>
              <th class="chitiet-cell" rowspan="3">Phải đóng</th>
              <th class="chitiet-cell" rowspan="2">Trạng thái học phí</th>
              <th class="chitiet-cell" rowspan="5">Ngày đóng</th>
              <th class="chitiet-cell" rowspan="3">Còn nợ</th>
            </tr>
          </thead>


          <tbody class="hocphi">
            <tr class="hocphi-row">
              <td class="hocphi-cell" colspan="13">Năm học: 2024-2025, Học kỳ: HK02</td>
            </tr>


            <?php
              include("../BackEnd/connectSQL.php");


              $sql = "
                  				  SELECT cthp.MaPhi, cthp.MaLopHocPhan, (ctdt.SoTinChi * n.GiaCua1TinChi) AS 'Nợ',
                                  cthp.TrangThai, kqdkhp.MaSinhVien 
                            FROM ChiTietHocPhi AS cthp
                            INNER JOIN DangKyHocPhan AS dkhp ON cthp.MaLopHocPhan = dkhp.MaLopHocPhan 
                            INNER JOIN KetQuaDangKyHocPhan AS kqdkhp ON kqdkhp.MaLopHocPhan = dkhp.MaLopHocPhan 
                                AND kqdkhp.MaSinhVien = cthp.MaSinhVien 
                            INNER JOIN ChuongTrinhDaoTao AS ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan 
                            INNER JOIN Nganh AS n ON n.MaNganh = ctdt.MaNganh
                            WHERE cthp.MaSinhVien = '" . $msv . "'";
              $result = $conn->query($sql);


              if (!$result) {
                  die("Lỗi truy vấn: " . $conn->error);
              }


              while ($row = $result->fetch_assoc()) :
            ?>
              <tr class="hocphi-row">
                <td class="hocphi-cell"><?= $row['MaPhi'] ?></td>
                <td class="hocphi-cell"><?= $row['MaLopHocPhan'] ?></td>
                <td class="hocphi-cell"><?= $row['Nợ'] ?></td>
                <td class="hocphi-cell"><?= ($row['TrangThai']== "1") ? "Đã đóng":"Chưa đóng" ?></td>
                <td class="hocphi-cell"><?= $now = date("H:i:s d/m/Y")?></td>
                <td class="hocphi-cell"><?= ($row['TrangThai']== "1") ?"0":$row['Nợ'] ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>


  <?php include("../Template_Layout/main/footer.php"); ?>
</body>
</html>



