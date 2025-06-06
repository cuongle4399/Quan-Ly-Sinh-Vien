<?php
session_start();
include("../BackEnd/connectSQL.php");

// Kiểm tra đăng nhập
if (!isset($_SESSION['MSV'])) {
    die("Bạn chưa đăng nhập!");
}

$user_id = $_SESSION['MSV'];

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
    $file = $_FILES['avatar'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        die("Chỉ chấp nhận file ảnh JPG, JPEG, PNG, GIF");
    }
    $uploadDir = "../taoanhsv/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Tìm số lớn nhất trong tên ảnh hiện có, ví dụ avatar1.jpg, avatar2.png ...
    $files = glob($uploadDir . 'avatar*.' . $ext);
    $maxNumber = 0;
    foreach ($files as $filePath) {
        $filename = basename($filePath);
        if (preg_match('/avatar(\d+)\.' . preg_quote($ext, '/') . '$/', $filename, $matches)) {
            $num = (int)$matches[1];
            if ($num > $maxNumber) {
                $maxNumber = $num;
            }
        }
    }

    $newNumber = $maxNumber + 1;
    $newFileName = "avatar" . $newNumber . "." . $ext;
    $uploadPath = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Cập nhật LinkAvatar vào database, lưu đường dẫn tương đối
        $linkAvatar = "taoanhsv/" . $newFileName;
        $sqlUpdate = "UPDATE ThongTinCaNhan SET LinkAvatar = '$linkAvatar' WHERE MaSinhVien = '$user_id'";
        if (mysqli_query($conn, $sqlUpdate)) {
            // Sau khi upload thành công, quay về trang thông tin cá nhân
            header("Location: ../My_Page/thongTinCaNhan.php?uploadsuccess=1");
            exit();
        } else {
            echo "Lỗi cập nhật database: " . mysqli_error($conn);
        }
    } else {
        echo "Lỗi upload file.";
    }
} else {
    echo "Chưa chọn file hoặc lỗi file upload.";
}
