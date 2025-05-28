<?php
if(!isset($_GET['email'])){
    header('Location:  ForgotPass.php');
    exit();
}?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập mã otp</title>
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <link rel="stylesheet" href="../css/EnterCodeOTP.css">
</head>
<body>
    <form class = "form_Main"method="POST">
        <h1 for="" >Nhập mã OTP</h1>
        <label class = "lbl" for="">Mã OTP</label>
        <input name = "CodeOTP" id = "form_Main__input"type="number" placeholder="Nhập mã OTP đã được gửi về gmail của bạn" required></input>
        <a class = "Back" href="ForgotPass.php">Quay lại</a>
        <label for="" id = "thongBao">thông báo</label>
        <button id = "form_Main__btn" type="submit">Xác nhận</button>
    </form>   
</body>
</html>
<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['CodeOTP'])){
    include('../BackEnd/connectSQL.php');
    $MailSendOTP = base64_decode($_GET['email']);
    $sqlquery = "SELECT Token,Token_Expires_at from NguoiDung WHERE Email = ?";
    $stmt = mysqli_prepare($conn,$sqlquery);
    mysqli_stmt_bind_param($stmt,'s',$MailSendOTP);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}
    if($result->num_rows > 0){
        $row = mysqli_fetch_assoc($result);
        if($row['Token'] == $_POST['CodeOTP'] && strtotime($row['Token_Expires_at']) >= time()){
            header("Location: ResetPass.php?email1=".base64_encode($MailSendOTP)."&token=".base64_encode($row['Token']));
            exit();
        }
        else {
            echo "<script>document.getElementById('thongBao').style.display = 'block';
document.getElementById('thongBao').innerHTML = 'Mã OTP không chính xác hoặc hết hạn';</script>";
        }
}
}
 ?>
