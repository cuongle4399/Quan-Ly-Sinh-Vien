<?php
include("../BackEnd/connectSQL.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}



$msv = isset($_SESSION['MSV']) ? $_SESSION['MSV'] : '';
file_put_contents($log_file, "MaSinhVien: $msv\n", FILE_APPEND);

if (empty($msv)) {
    file_put_contents($log_file, "Lỗi: MaSinhVien rỗng, chuyển hướng về dangKyHocPhan.php\n", FILE_APPEND);
    header("Location: dangKyHocPhan.php");
    exit();
}


$sql = "SELECT MaLopHocPhan FROM ketquadangkyhocphan WHERE MaSinhVien = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    file_put_contents($log_file, "Lỗi chuẩn bị truy vấn ketquadangkyhocphan: " . $conn->error . "\n", FILE_APPEND);
    header("Location: dangKyHocPhan.php");
    exit();
}
$stmt->bind_param("s", $msv);
$stmt->execute();
$result = $stmt->get_result();
file_put_contents($log_file, "Số bản ghi ketquadangkyhocphan: " . $result->num_rows . "\n", FILE_APPEND);


$sql_max = "SELECT MAX(MaPhi) AS maxMaPhi FROM chitiethocphi";
$max_result = $conn->query($sql_max);
if (!$max_result) {
    file_put_contents($log_file, "Lỗi truy vấn MAX(MaPhi): " . $conn->error . "\n", FILE_APPEND);
    $stmt->close();
    header("Location: dangKyHocPhan.php");
    exit();
}
$max_row = $max_result->fetch_assoc();
$maxMaPhi = $max_row['maxMaPhi'];
$next_number = $maxMaPhi ? (int)substr($maxMaPhi, 2) + 1 : 1;
file_put_contents($log_file, "MaPhi tiếp theo: MP" . str_pad($next_number, 4, '0', STR_PAD_LEFT) . "\n", FILE_APPEND);

$insert_sql = "INSERT INTO chitiethocphi (MaPhi, MaLopHocPhan, MaSinhVien, TrangThai) VALUES (?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_sql);
if (!$insert_stmt) {
    file_put_contents($log_file, "Lỗi chuẩn bị truy vấn INSERT: " . $conn->error . "\n", FILE_APPEND);
    $stmt->close();
    header("Location: dangKyHocPhan.php");
    exit();
}

while ($row = $result->fetch_assoc()) {
    $maLopHocPhan = $row['MaLopHocPhan'];
    file_put_contents($log_file, "Xử lý MaLopHocPhan: $maLopHocPhan\n", FILE_APPEND);

    
    $check_sql = "SELECT COUNT(*) AS count FROM chitiethocphi WHERE MaLopHocPhan = ? AND MaSinhVien = ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        file_put_contents($log_file, "Lỗi chuẩn bị truy vấn CHECK: " . $conn->error . "\n", FILE_APPEND);
        continue;
    }
    $check_stmt->bind_param("ss", $maLopHocPhan, $msv);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();
    file_put_contents($log_file, "Kiểm tra trùng lặp ($maLopHocPhan, $msv): " . $check_row['count'] . "\n", FILE_APPEND);

    if ($check_row['count'] == 0) {
        
        $newMaPhi = 'MP' . str_pad($next_number, 4, '0', STR_PAD_LEFT);
        $trangThai = 0;

        
        $insert_stmt->bind_param("sssi", $newMaPhi, $maLopHocPhan, $msv, $trangThai);
        if ($insert_stmt->execute()) {
            file_put_contents($log_file, "Thêm thành công: $newMaPhi, $maLopHocPhan, $msv\n", FILE_APPEND);
            $next_number++;
        } else {
            file_put_contents($log_file, "Lỗi thêm bản ghi: " . $insert_stmt->error . "\n", FILE_APPEND);
        }
    }
    $check_stmt->close();
}
$stmt->close();
$insert_stmt->close();


header("Location: dangKyHocPhan.php");
exit();
?>