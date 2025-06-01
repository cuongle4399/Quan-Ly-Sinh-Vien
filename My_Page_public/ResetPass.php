<?php
if(!isset($_GET['email1']) || !isset($_COOKIE['OTP'])){
    header('Location:  ForgotPass.php');
    exit();
    
}
include('../BackEnd/connectSQL.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Khôi phục mật khẩu</title>
    <link rel="stylesheet" href="../css/resetpassword.css">
</head>
<body>
    <form class = "form_Main"method="POST">
        <h1 for="" >Nhập mật khẩu mới</h1>
        <label class = "lbl" for="">Nhập mật khẩu</label>
        <input name = "PassNew1" id = "form_Main__input"type= "password" placeholder="Nhập mật khẩu mới" required></input>
        <input name = "PassNew2" id = "form_Main__input"type= "password" placeholder="Nhập lại mật khẩu" required></input>
         <label for="" id = "thongBao">Vui lòng nhập mật khẩu 1 với mật khẩu 2 phải trùng nhau</label>
         <a id = "Back" href="../My_Page_public/login.php">Đăng nhập ngay</a>
        <button id = "form_Main__btn" type="submit">Đổi mật khẩu</button>
    </form>
    
</body>
</html>
<?php
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['PassNew1']) && isset($_POST['PassNew2'])){
    if($_POST['PassNew1'] != $_POST['PassNew2']){
        echo "<script>document.getElementById('thongBao').style.display = 'block';
    document.getElementById('thongBao').style.color = 'red';
    document.getElementById('thongBao').innerHTML='Mật khẩu không khớp vui lòng nhập lại';
     document.getElementById('Back').style.display = 'none'</script>";
        return;
    }
    $pass = $_POST['PassNew1'];
    $MailSendOTP = base64_decode($_GET['email1']);
    $sqlquery = "SELECT MaSinhVien,MatKhau from NguoiDung WHERE Email =?";
    $stmt = mysqli_prepare($conn,$sqlquery);
    mysqli_stmt_bind_param($stmt,"s",$MailSendOTP);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}
    if($result->num_rows > 0){
        while($row = mysqli_fetch_assoc($result)){
            // update pass
            $querysql = "UPDATE NguoiDung
            SET MatKhau = ?
            WHERE Email = ?";
            $stmt = mysqli_prepare($conn,$querysql);
            mysqli_stmt_bind_param($stmt,"ss",$pass,$MailSendOTP);
            if (mysqli_stmt_execute($stmt)) {
                setcookie('OTP', '', time() - 300, '/');
                header("Location: login.php");
                exit();
            } else {
                echo "Lỗi: " . mysqli_error($conn);
                return;
            }
        }
       
}
}
 ?>`