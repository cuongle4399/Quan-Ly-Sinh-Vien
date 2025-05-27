<?php
if(!isset($_GET['email'])){
    header('Location:  ForgotPass.php');
    exit();
}
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['CodeOTP'])){
    include('../BackEnd/connectSQL.php');
    $MailSendOTP = base64_decode($_GET['email']);
    $sqlquery = "SELECT Token,Token_Expires_at from NguoiDung WHERE Email ='$MailSendOTP'";
    $result = mysqli_query($conn, $sqlquery);
    if (!$result) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}
    if($result->num_rows > 0){
        $row = mysqli_fetch_assoc($result);
        if($row['Token'] == $_POST['CodeOTP'] && strtotime($row['Token_Expires_at']) >= time()){
            $querysql = "UPDATE NguoiDung
            SET Token_Expires_at = NULL,
            Token = NULL
            WHERE Email = '$MailSendOTP'";
            if (mysqli_query($conn, $querysql)) {
                header("Location: ResetPass.php?email1=".base64_encode($MailSendOTP)."");
                exit();
            } 
            else {
                echo "Lỗi: " . mysqli_error($conn);
                return;
            }
            mysqli_close($conn);
        }
        else {
            echo "<script>document.getElementById('thongBao').style.display = 'block';
document.getElementById('thongBao').innerHTML = 'Mã OTP không chính xác hoặc hết hạn';</script>";
        }
}
}
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập mã otp</title>
    <link rel="stylesheet" href="../css/EnterCodeOTP.css">
</head>
<body>
    <form class = "form_Main"method="POST">
        <h1 for="" >Nhập mã OTP</h1>
        <label class = "lbl" for="">Mã OTP</label>
        <input name = "CodeOTP" id = "form_Main__input"type="number" placeholder="Nhập mã OTP đã được gửi về gmail của bạn" required></input>
        <a class = "Back" href="ForgotPass.php">Quay lại</a>
        <label for="" class = "thongBao">thông báo</label>
        <button id = "form_Main__btn" type="submit">Xác nhận</button>
    </form>   
</body>
</html>