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
        <form action="taotaikhoan.php" method="POST">
        <p><strong>THÔNG TIN SINH VIÊN</strong></p>
        <p>Họ và tên: <input type="text" name="ho_ten_sv" required></p>
        <p>Ngày sinh: <input type="text" name="nam_sinh"></p>
        <p>Giới tính: <input type="text" name="gioi_tinh"></p>
        <p>Số điện thoại: <input type="text" name="so_dt"></p>
        <p>Dân tộc: <input type="text" name="dan_toc"></p>
        <p>Số CCCD: <input type="text" name="so_cccd"></p>
        <p>Mã sinh viên: <input type="text" name="msv"></p>
        <p>Email: <input type="text" name="email"></p>
        <p>Mật khẩu <input type="text" name="mk"></p>
        <p>Vai trò <input type="text" name="vt"></p>
        <p>Tình trạng học: <input type="text" name="tinh_trang_hoc"></p>
        <p>Tỉnh/Thành Phố: <input type="text" name="tinh-thanh_pho"></p>
        <p>Quận/Huyện: <input type="text" name="quan_huyen"></p>
        <p>Quốc Gia: <input type="text" name="quoc_gia"></p>
        <p>Địa chỉ thường trú: <input type="text" name="dia_chi_thuong_tru"></p>
        <p>Trình độ ngôn ngữ: <input type="text" name="trinh_do_ngon_ngu"></p>
        <p>Ngành học: <input type="text" name="nganh_hoc"></p>
        <p>Niên khóa: <input type="text" name="nien_khoa"></p>
        <p>LH đào tạo: <input type="text" name="lh_dt"></p>
        <p>Lớp sinh viên: <input type="text" name="lop_sv"></p>
        <p>Chức vụ: <input type="text" name="cv"></p>
        <p>Cố vấn học tập: <input type="text" name="cvht"></p>
        <p>LH cvht: <input type="text" name="lhcvht"></p>
        <p>Thông tin tài khoản liên kết ngân hàng: <input type="text" name="tttklknh"></p>
        <p>Tên ngân hàng: <input type="text" name="tnh"></p>
        <p>STK ngân hàng: <input type="text" name="stknh"></p>
        <p>Gmail của tài khoản ngân hàng: <input type="text" name="gmailnh"></p>
        <p>LinkAvatar <input type="text" name="linkavt"></p>
        <p><strong>LIÊN HỆ NGƯỜI THÂN</strong></p>
        <p>Họ và tên <input type="text" name="ho_ten_nt"></p>
        <p>Quan hệ <input type="text" name="quan_he_voi_nt"></p>
        <p>Số điện thoại <input type="text" name="sdt"></p>
        <p>Địa chỉ thường trú <input type="text" name="dctr"></p>
        <button type="submit" name="taotk">Tạo tài khoản</button>
    </form>
<?php
if (isset($_POST['taotk'])) {
    $msv = $_POST['msv'];
    $check = "SELECT * FROM nguoidung WHERE MaSinhVien = '$msv'";
    $result = $conn->query($check);
    
    if ($result->num_rows == 0) {
        $ho_ten_sv = $_POST['ho_ten_sv'];
        $nam_sinh = $_POST['nam_sinh'];
        $gioi_tinh = $_POST['gioi_tinh'];
        $so_dt = $_POST['so_dt'];
        $dan_toc = $_POST['dan_toc'];
        $so_cccd = $_POST['so_cccd'];
        $email = $_POST['email'];
        $linkavt = $_POST['linkavt'];
        $mk = $_POST['mk'];
        $vt = $_POST['vt'];
        $tinh_trang_hoc = $_POST['tinh_trang_hoc'];
        $tinh_thanh_pho = $_POST['tinh-thanh_pho'];
        $quan_huyen = $_POST['quan_huyen'];
        $quoc_gia = $_POST['quoc_gia'];
        $dia_chi_thuong_tru = $_POST['dia_chi_thuong_tru'];
        $trinh_do_ngon_ngu = $_POST['trinh_do_ngon_ngu'];
        $nganh_hoc = $_POST['nganh_hoc'];
        $nien_khoa = $_POST['nien_khoa'];
        $lh_dt = $_POST['lh_dt'];
        $lop_sv = $_POST['lop_sv'];
        $cv = $_POST['cv'];
        $cvht = $_POST['cvht'];
        $lhcvht = $_POST['lhcvht'];
        $tnh = $_POST['tnh'];
        $stknh = $_POST['stknh'];
        $gmailnh = $_POST['gmailnh'];

        $ho_ten_nt = $_POST['ho_ten_nt'];
        $quan_he_voi_nt = $_POST['quan_he_voi_nt'];
        $sdt_nt = $_POST['sdt'];
        $dctr = $_POST['dctr'];

        $query1 = "INSERT INTO nguoidung (MaSinhVien, MatKhau, VaiTro, Email)
                   VALUES ('$msv', '$mk', '$vt', '$email')";

        $query2 = "INSERT INTO thongtincanhan (
            MaSinhVien, LinkAvatar, HoTen, GioiTinh, SoDienThoai, NgaySinh, DanToc, SoCCCD,
            TinhThanhPho, QuanHuyen, QuocGia, DiaChiThuongTru, TrinhDoNgoaiNgu, TinhTrangHoc,
            NienKhoa, LoaiHinhDaoTao, LopSinhVien, ChucVu, CoVanHocTap, LH_CVHT,
            TenNganHang, SoTaiKhoan, EmailNganHang, TenNguoiThan, QuanHe, SDTNguoiThan, DiaChiNguoiThan
        ) VALUES (
            '$msv', '$linkavt', '$ho_ten_sv', '$gioi_tinh', '$so_dt', '$nam_sinh', '$dan_toc', '$so_cccd',
            '$tinh_thanh_pho', '$quan_huyen', '$quoc_gia', '$dia_chi_thuong_tru', '$trinh_do_ngon_ngu', '$tinh_trang_hoc',
             '$nien_khoa', '$lh_dt', '$lop_sv', '$cv', '$cvht', '$lhcvht',
            '$tnh', '$stknh', '$gmailnh', '$ho_ten_nt', '$quan_he_voi_nt', '$sdt_nt', '$dctr'
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