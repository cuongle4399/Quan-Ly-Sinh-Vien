<?php
include("../BackEnd/connectSQL.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}



$msv = isset($_SESSION['MSV']) ? $_SESSION['MSV'] : '';

if (empty($msv)) {
    header("Location: dangKyHocPhan.php");
    exit();
}


$sql = "SELECT MaLopHocPhan FROM ketquadangkyhocphan WHERE MaSinhVien = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header("Location: dangKyHocPhan.php");
    exit();
    //
}
$stmt->bind_param("s", $msv);
$stmt->execute();
$result = $stmt->get_result();


$sql_max = "SELECT MAX(MaPhi) AS maxMaPhi FROM chitiethocphi";
$max_result = $conn->query($sql_max);
if (!$max_result) {
    $stmt->close();
    header("Location: dangKyHocPhan.php");
    exit();
}
$max_row = $max_result->fetch_assoc();
$maxMaPhi = $max_row['maxMaPhi'];
$next_number = $maxMaPhi ? (int)substr($maxMaPhi, 2) + 1 : 1;

$insert_sql = "INSERT INTO chitiethocphi (MaPhi, MaLopHocPhan, MaSinhVien, TrangThai) VALUES (?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_sql);
if (!$insert_stmt) {
    $stmt->close();
    header("Location: dangKyHocPhan.php");
    exit();
}

while ($row = $result->fetch_assoc()) {
    $maLopHocPhan = $row['MaLopHocPhan'];

    $check_sql = "SELECT COUNT(*) AS count FROM chitiethocphi WHERE MaLopHocPhan = ? AND MaSinhVien = ?";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        continue;
    }
    $check_stmt->bind_param("ss", $maLopHocPhan, $msv);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $check_row = $check_result->fetch_assoc();

    if ($check_row['count'] == 0) {
        
        $newMaPhi = 'MP' . str_pad($next_number, 4, '0', STR_PAD_LEFT);
        $trangThai = 0;

        
        $insert_stmt->bind_param("sssi", $newMaPhi, $maLopHocPhan, $msv, $trangThai);
        if ($insert_stmt->execute()) {
            $next_number++;
        } else {
        }
    }
    $check_stmt->close();
}
$stmt->close();
$insert_stmt->close();


header("Location: dangKyHocPhan.php");
exit();
?>