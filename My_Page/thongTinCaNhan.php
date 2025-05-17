<?php include ("../BackEnd/blockBugLogin.php")?>

<?php
include("../BackEnd/connectSQL.php");

$sqlQuery = "SELECT t.*, n.TenNganh FROM ThongTinCaNhan as t join Nganh  as n  on t.MaNganh = n.MaNganh where t.MaSinhVien='" . $msv . "'";
$result = mysqli_query($conn, $sqlQuery);
if(!$result){
  die ("lỗi truy vấn dữ liệu");
}
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Thông tin sinh viên</title>
  <link rel="stylesheet" href="../css/mainIN.css">
  <link rel="stylesheet" href="../css/thongtinsinhvien.css" />
</head>
<body>
  <?php include("../Template_Layout/main/header.php"); ?>

  <div class="content">
    <?php include("../Template_Layout/main/sidebar.php"); ?>

    <div class="content__main">
      <header>
        <h1>Thông tin sinh viên</h1>
      </header>

      <div class="cc">
        <section class="ttsv">
        <?php
         echo "
        <p>Họ và tên: ".$row['HoTen']."</p>
        <p>Ngày sinh: ".$row['NgaySinh']."</p>
        <p>Giới tính: ".$row['GioiTinh']."</p>
        <p>Số điện thoại: ".$row['SoDienThoai']."</p>
        <p>Dân tộc: ".$row['DanToc']."</p>
        <p>Số CCCD: ".$row['SoCCCD']."</p>
        <p>Mã sinh viên: ".$row['MaSinhVien']."</p>
        <p>Email: ".$row['Email']."</p>
        <p>Tình trạng học: ".$row['TinhTrangHoc']."</p>
        <p>Tỉnh/Thành phố: ".$row['TinhThanhPho']."</p>
        <p>Quận/Huyện: ".$row['QuanHuyen']."</p>
        <p>Quốc gia: ".$row['QuocGia']."</p>
        <p>Địa chỉ thường trú: ".$row['DiaChiThuongTru']."</p>  
        <p>Trình độ ngôn ngữ: ".$row['TrinhDoNgoaiNgu']."</p>
         ";
        ?>
        </section>

        <div class="right">
          <section class="ttkh">
          <?php
          echo "
          <p>Ngành học: ".$row['TenNganh']."</p>
          <p>Niên khóa: ".$row['NienKhoa']."</p>
          <p>LH đào tạo: ".$row['LoaiHinhDaoTao']."</p>
          <p>Lớp sinh viên: ".$row['LopSinhVien']."</p>
          <p>Chức vụ: ".$row['ChucVu']."</p>
          <p>Cố vấn học tập: ".$row['CoVanHocTap']."</p>
          <p>LH cvht: ".$row['LH_CVHT']."</p>
          <p>Thông tin tài khoản liên kết ngân hàng:</p>
          <p>Tên ngân hàng: ".$row['TenNganHang']."</p>
          <p>STK ngân hàng: ".$row['SoTaiKhoan']."</p>
          <p>Gmail của tài khoản ngân hàng: ".$row['EmailNganHang']."</p>
          ";
          ?>
          </section>
          <div class="hi">
          <aside>
            <h2>Liên hệ người thân</h2>
            <?php
              echo "
                <p>Họ và tên: ".$row['TenNguoiThan']."</p>
                <p>Quan hệ: ".$row['QuanHe']."</p>
                <p>Số điện thoại: ".$row['SDTNguoiThan']."</p>
                <p>Địa chỉ thường trú: ".$row['DiaChiNguoiThan']."</p>
              ";
            ?>
          </aside>
        </div>
      </div>
    </div>
  </div>
</div>

  <?php include("../Template_Layout/main/footer.php"); ?>
</body>
</html>
