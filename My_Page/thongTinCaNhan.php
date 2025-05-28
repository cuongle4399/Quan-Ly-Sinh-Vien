<?php include ("../BackEnd/blockBugLogin.php"); ?>
<?php 
include("../BackEnd/connectSQL.php");

// Lấy thông tin sinh viên
$sqlQuery = "SELECT nguoi.Email, t.*, n.TenNganh
FROM NguoiDung as nguoi
JOIN ThongTinCaNhan as t ON nguoi.MaSinhVien = t.MaSinhVien
JOIN Nganh as n ON t.MaNganh = n.MaNganh
WHERE t.MaSinhVien = '" . $msv . "'
";
$result = mysqli_query($conn, $sqlQuery);
if (!$result) {
  die("Lỗi truy vấn dữ liệu");
}
$row = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../css/mainIN.css">
  <link rel="stylesheet" href="../css/thongtinsinhvien.css" />
  <title>Thông tin sinh viên</title>
</head>
<body>
  <?php include("../Template_Layout/main/header.php"); ?>

  <div class="content">
    <?php include("../Template_Layout/main/sidebar.php"); ?>

    <div class="content__main">
      <header>
        <div class="hello">Thông tin sinh viên</div>
      </header>

      <div class="cc">
        <section class="ttsv">
          <h2>Thông tin cá nhân</h2>
  <div class="avatar-section">
  <div class="avatar-container">
    <img src="../<?php echo $row['LinkAvatar'] . '?v=' . time(); ?>" alt="Ảnh đại diện" class="avatar-image" id="avatarPreview" />

  </div>

  <form action="upload_avatar.php" method="post" enctype="multipart/form-data" id="avatarForm" class="avatar-form">
    <input type="file" id="avatarInput" name="avatar" accept="image/*" class="avatar-input" />
    <label for="avatarInput" class="btn-edit">Chỉnh sửa ảnh đại diện</label>
  </form>
</div>
          <?php
           echo "
            <p>Họ và tên: ".$row['HoTen']."</p>
            <p>Ngày sinh: ".$row['NgaySinh']."</p>
            <p>Giới tính: ".$row['GioiTinh']."</p>
            <p>Số điện thoại: ".$row['SoDienThoai']."</p>
            <p>Dân tộc: ".$row['DanToc']."</p>
            <p>Số CCCD: ".$row['SoCCCD']."</p>
            <p>Mã sinh viên: ".$row['MaSinhVien']."</p>
            <p>Email: ".$row['Email']."</p>
            <p>Tình trạng học: ".$row['TinhTrangHoc']."</p>
            <p>Tỉnh/Thành phố: ".$row['TinhThanhPho']."</p>
            <p>Quận/Huyện: ".$row['QuanHuyen']."</p>
            <p>Quốc gia: ".$row['QuocGia']."</p>
            <p>Địa chỉ thường trú: ".$row['DiaChiThuongTru']."</p>  
            <p>Trình độ ngoại ngữ: ".$row['TrinhDoNgoaiNgu']."</p>
           ";
           
          ?>
          <button id="openModalBtn">Cập nhật thông tin cá nhân</button>
        </section>

        <div class="right">
          <section class="ttkh">
            <h2>Thông tin khóa học</h2>
            <?php
              echo "
              <p>Ngành học: ".$row['TenNganh']."</p>
              <p>Niên khóa: ".$row['NienKhoa']."</p>
              <p>Loại hình đào tạo: ".$row['LoaiHinhDaoTao']."</p>
              <p>Lớp sinh viên: ".$row['LopSinhVien']."</p>
              <p>Chức vụ: ".$row['ChucVu']."</p>
              <p>Cố vấn học tập: ".$row['CoVanHocTap']."</p>
              <p>Liên hệ CVHT: ".$row['LH_CVHT']."</p>
              <p>Thông tin tài khoản liên kết ngân hàng:</p>
              <p>Tên ngân hàng: ".$row['TenNganHang']."</p>
              <p>Số tài khoản: ".$row['SoTaiKhoan']."</p>
              <p>Gmail ngân hàng: ".$row['EmailNganHang']."</p>
              ";
            ?>
             

          <!-- Modal -->
          <div id="updateModal" class="modal">
            <div class="modal-content">
              <span class="close">&times;</span>
              <h2 class="modal-header">Cập nhật thông tin cá nhân</h2>
<form action="update_thongtin.php" method="POST">
  <fieldset class="modal-section">
    <legend>Cập nhật thông tin cá nhân</legend>
    <label>Điện thoại:</label><input name="SoDienThoai" />
    <label>Địa chỉ thường trú:</label><input name="DiaChiThuongTru" />
  </fieldset>

  <fieldset class="modal-section">
    <legend>Cập nhật thông tin liên lạc</legend>
    <label>Người liên hệ:</label><input name="TenNguoiThan" />
    <label>Điện thoại:</label><input name="SDTNguoiThan" />
    <label>Địa chỉ:</label><input name="DiaChiNguoiThan" />
  </fieldset>

  <fieldset class="modal-section">
    <legend>Cập nhật thông tin ngân hàng</legend>
    <label>Số tài khoản:</label><input name="SoTaiKhoan" />
    <label>Tên ngân hàng:</label><input name="TenNganHang" />
  </fieldset>

  <div class="modal-actions">
    <button type="submit" class="btn-save">Lưu</button>
    <button type="button" class="btn-close">Đóng</button>
  </div>
</form>

            </div>
          </div>
          </section>
         

          <div class="hi">
            <aside>
              <h2>Liên hệ người thân</h2>
              <?php
                echo "
                  <p>Họ và tên: ".$row['TenNguoiThan']."</p>
                  <p>Quan hệ: ".$row['QuanHe']."</p>
                  <p>Số điện thoại: ".$row['SDTNguoiThan']."</p>
                  <p>Địa chỉ: ".$row['DiaChiNguoiThan']."</p>
                ";
              ?>
            </aside>
          </div>
        </div>
      </div>
    </div>
  </div>
<script>
  document.getElementById('avatarInput').addEventListener('change', function() {
    document.getElementById('avatarForm').submit();
  });
</script>
<script>
    document.getElementById('avatarInput').addEventListener('change', function() {
      document.getElementById('avatarForm').submit();
    });

    const modal = document.getElementById('updateModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.querySelector('.close');
    const closeFooterBtn = document.querySelector('.btn-close');

    openBtn.onclick = () => modal.style.display = 'block';
    closeBtn.onclick = () => modal.style.display = 'none';
    closeFooterBtn.onclick = () => modal.style.display = 'none';

    window.onclick = (event) => {
      if (event.target === modal) {
        modal.style.display = 'none';
      }
    };
  </script>
  <?php include("../Template_Layout/main/footer.php"); ?>
</body>
</html>
