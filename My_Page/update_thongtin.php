<?php
session_start();

if (!isset($_SESSION['MSV'])) {
    die("Truy cập bị từ chối");
}

include("../BackEnd/connectSQL.php");
$msv = $_SESSION['MSV'];

// Lấy dữ liệu từ form
$SoDienThoai      = $_POST['SoDienThoai'] ?? '';
$DiaChiThuongTru  = $_POST['DiaChiThuongTru'] ?? '';
$TenNguoiThan     = $_POST['TenNguoiThan'] ?? '';
$SDTNguoiThan     = $_POST['SDTNguoiThan'] ?? '';
$DiaChiNguoiThan  = $_POST['DiaChiNguoiThan'] ?? '';
$SoTaiKhoan       = $_POST['SoTaiKhoan'] ?? '';
$TenNganHang      = $_POST['TenNganHang'] ?? '';

// Câu lệnh cập nhật
$sql = "UPDATE ThongTinCaNhan 
        SET SoDienThoai = ?, 
            DiaChiThuongTru = ?, 
            TenNguoiThan = ?, 
            SDTNguoiThan = ?, 
            DiaChiNguoiThan = ?, 
            SoTaiKhoan = ?, 
            TenNganHang = ?
        WHERE MaSinhVien = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Lỗi chuẩn bị truy vấn: " . mysqli_error($conn));
}

// Gán tham số
mysqli_stmt_bind_param($stmt, "ssssssss", 
    $SoDienThoai, 
    $DiaChiThuongTru, 
    $TenNguoiThan, 
    $SDTNguoiThan, 
    $DiaChiNguoiThan, 
    $SoTaiKhoan, 
    $TenNganHang, 
    $msv
);

// Thực thi và kiểm tra
if (mysqli_stmt_execute($stmt)) {
    header("Location: thongTinCaNhan.php");
    exit();
} else {
    echo "Cập nhật thất bại!";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
