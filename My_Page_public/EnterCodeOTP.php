<?php
if(!isset($_GET['email']) && !isset($_COOKIE['OTP'])){
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
    $tokenInput = $_POST['CodeOTP'];
    if(isset($_COOKIE['OTP']) &&  base64_decode($_COOKIE['OTP']) == $tokenInput){
         header("Location: ResetPass.php?email1=".$_GET['email']);
            exit();
    }
    else{
        echo "<script>document.getElementById('thongBao').innerText = 'Mã OTP không đúng';
        document.getElementById('thongBao').style.display = 'block'</script>";
    }
}
 ?>
