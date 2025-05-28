<?php
include ('../../BackEnd/blockBugLogin.php');
?>
<?php include ('../../BackEnd/connectSQL.php') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <link rel="stylesheet" href="../css/admin.css">
    <title>Tạo tài khoản sinh viên</title>
</head>
<body>
    <?php include('header.php'); ?>
  <div class="Content-main">
    <?php include('sidebar.php'); ?>
       <div class="main">
            <form class="main-form" action="taotaikhoan.php" method="POST">
                <div class="input-info">
                    <div class="info-student">
                        <p><strong>THÔNG TIN SINH VIÊN</strong></p>
                        <p>Họ và tên: <input type="text" name="ho_ten_sv" required></p>
                        <p>Ngày sinh: <input type="text" name="nam_sinh" required></p>
                        <p>Giới tính: <input type="text" name="gioi_tinh" required></p>
                        <p>Dân tộc: <input type="text" name="dan_toc" required></p>
                        <p>Số CCCD: <input type="text" name="so_cccd" required></p>
                        <p>Mã sinh viên: <input type="text" name="msv" required></p>
                        <p>Email: <input type="email" name="email" required></p>
                        <p>Mật khẩu <input type="text" name="mk" required></p>
                        <p>Vai trò <input type="text" name="vt" required></p>
                        <p>Tình trạng học: <input type="text" name="tinh_trang_hoc" required></p>
                        <p>Tỉnh/Thành Phố: <input type="text" name="tinh-thanh_pho" required></p>
                        <p>Quận/Huyện: <input type="text" name="quan_huyen" required></p>
                        <p>Quốc Gia: <input type="text" name="quoc_gia" required></p>
                        <p>Trình độ ngôn ngữ: <input type="text" name="trinh_do_ngon_ngu" required></p>
                        <p>Ngành học: <input type="text" name="nganh_hoc" required></p>
                        <p>Niên khóa: <input type="text" name="nien_khoa" required></p>
                        <p>LH đào tạo: <input type="text" name="lh_dt" required></p>
                        <p>Lớp sinh viên: <input type="text" name="lop_sv" required></p>
                        <p>Chức vụ: <input type="text" name="cv" required></p>
                        <p>Cố vấn học tập: <input type="text" name="cvht" required></p>
                        <p>LH cvht: <input type="text" name="lhcvht" required></p>
                    </div>
                
                    <div class="contact-relation">
                        <p><strong>LIÊN HỆ NGƯỜI THÂN</strong></p>
                        <p>Họ và tên <input type="text" name="ho_ten_nt" required></p>
                        <p>Quan hệ <input type="text" name="quan_he_voi_nt" required></p>
                        <p>Số điện thoại <input type="text" name="sdt" required></p>
                        <p>Địa chỉ thường trú <input type="text" name="dctr" required></p>
                    </div>
                </div>

                <button class="create-account" type="submit" name="taotk">Tạo tài khoản</button>
            </form>
        </div>
<?php
if (isset($_POST['taotk'])) {
    $msv = $_POST['msv'];
    $check = "SELECT * FROM nguoidung WHERE MaSinhVien = '$msv'";
    $result = $conn->query($check);
    
    if ($result->num_rows == 0) {
        $ho_ten_sv = $_POST['ho_ten_sv'];
        $nam_sinh = $_POST['nam_sinh'];
        $gioi_tinh = $_POST['gioi_tinh'];
        $dan_toc = $_POST['dan_toc'];
        $so_cccd = $_POST['so_cccd'];
        $email = $_POST['email'];
        $mk = $_POST['mk'];
        $vt = $_POST['vt'];
        $tinh_trang_hoc = $_POST['tinh_trang_hoc'];
        $tinh_thanh_pho = $_POST['tinh-thanh_pho'];
        $quan_huyen = $_POST['quan_huyen'];
        $quoc_gia = $_POST['quoc_gia'];
        $trinh_do_ngon_ngu = $_POST['trinh_do_ngon_ngu'];
        $nganh_hoc = $_POST['nganh_hoc'];
        $nien_khoa = $_POST['nien_khoa'];
        $lh_dt = $_POST['lh_dt'];
        $lop_sv = $_POST['lop_sv'];
        $cv = $_POST['cv'];
        $cvht = $_POST['cvht'];
        $lhcvht = $_POST['lhcvht'];

        $ho_ten_nt = $_POST['ho_ten_nt'];
        $quan_he_voi_nt = $_POST['quan_he_voi_nt'];
        $sdt_nt = $_POST['sdt'];
        $dctr = $_POST['dctr'];

        $query1 = "INSERT INTO nguoidung (MaSinhVien, MatKhau, VaiTro, Email)
                   VALUES ('$msv', '$mk', '$vt', '$email')";

        $query2 = "INSERT INTO thongtincanhan (
            MaSinhVien, LinkAvatar, HoTen, GioiTinh, NgaySinh, DanToc, SoCCCD,
            TinhThanhPho, QuanHuyen, QuocGia, TrinhDoNgoaiNgu, TinhTrangHoc,
            NienKhoa, LoaiHinhDaoTao, LopSinhVien, ChucVu, CoVanHocTap, LH_CVHT,
            EmailNganHang, TenNguoiThan, QuanHe, SDTNguoiThan, DiaChiNguoiThan
        ) VALUES (
            '$msv', '$linkavt', '$ho_ten_sv', '$gioi_tinh', '$nam_sinh', '$dan_toc', '$so_cccd',
            '$tinh_thanh_pho', '$quan_huyen', '$quoc_gia', '$trinh_do_ngon_ngu', '$tinh_trang_hoc',
             '$nien_khoa', '$lh_dt', '$lop_sv', '$cv', '$cvht', '$lhcvht',
            '$gmailnh', '$ho_ten_nt', '$quan_he_voi_nt', '$sdt_nt', '$dctr'
        )";

        if ($conn->query($query1) && $conn->query($query2)) {
            echo "<script>alert('Tạo tài khoản thành công!');</script>";
        } else {
            echo "<script>alert('Lỗi khi tạo tài khoản: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Mã sinh viên đã tồn tại!');</script>";
    }
}
?>
    </div>
  </div>
    <script src="../../Js/Nofinish.js"></script>
</body>
</html>