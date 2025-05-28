<?php include ("../BackEnd/blockBugLogin.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/ccsv.css">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">

    <title>Trường đại học Quy Nhơn</title>
</head>
<body>
<?php include("../Template_Layout/main/header.php") ?>
    <div class = "content">
<?php include("../Template_Layout/main/sidebar.php") ?>
        <div class="content__main">
          <!-- Tiêu đề trang -->
            <div class="title">Chứng chỉ</div>
      <div class="main__table">
        <table class="table">
          <!-- Header -->
          <thead class="chungchi">
            <tr class="table__head-row">
              <th class="chitiet-cell" rowspan="2">STT</th>
              <th class="chitiet-cell" rowspan="3">Chương Trình Đạo Tạo</th>
              <th class="chitiet-cell" rowspan="3">Tên Chứng Chỉ</th>
              <th class="chitiet-cell" rowspan="2">Số Hiệu Bằng </th>
              <th class="chitiet-cell" rowspan="2">Số Vào Sổ </th>
              <th class="chitiet-cell" rowspan="2">Số Quyết Định </th>
              <th class="chitiet-cell" rowspan="3">Nơi Cấp</th>
              <th class="chitiet-cell" rowspan="3">Ngày Cấp </th>
            </tr>
          </thead>
            <?php
                include("../BackEnd/connectSQL.php") ;

              $sql = "
                SELECT STT, ChuongTrinhDaoTao, TenChungChi, SoHieuBang, SoVaoSo,SoQuyetDinh,NoiCap, NgayCap 
                FROM ChungChi
                WHERE MaSinhVien = '" . $msv . "'";
              $result = $conn->query($sql);

              if (!$result) {
                  die("Lỗi truy vấn: " . $conn->error);
              }

              while ($row = $result->fetch_assoc()) :
            ?>
              <tr class="chungchi-row">
                <td class="chungchi-cell"><?= $row['STT'] ?></td>
                <td class="chungchi-cell"><?= $row['ChuongTrinhDaoTao'] ?></td>
                <td class="chungchi-cell"><?= $row['TenChungChi'] ?></td>
                <td class="chungchi-cell"><?= $row['SoHieuBang'] ?></td>
                <td class="chungchi-cell"><?= $row['SoVaoSo'] ?></td>
                <td class="chungchi-cell"><?= $row['SoQuyetDinh'] ?></td>
                <td class="chungchi-cell"><?= $row['NoiCap'] ?></td>
                <td class="chungchi-cell"><?= $row['NgayCap'] ?></td>
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