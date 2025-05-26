<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fotgotpass.css">
    <title>Quên mật khẩu</title>
</head>
<body>
    <form class = "form_Main"method="POST">
        <h1 for="" >Quên Mật Khẩu</h1>
        <label class = "lbl" for="">Email</label>
        <input name = "mail" id = "form_Main__input"type="email" placeholder="Nhập Gmail của bạn" required></input>
         <label for="" class = "thongBao">Vui lòng kiểm tra Gmail để lấy mã OTP</label>
        <a class = "Back" href="../My_Page_public/login.php">Quay lại trang chủ</a>
        <button id = "form_Main__btn" type="submit">Gửi mail</button>
    </form>
   <?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include("../BackEnd/connectSQL.php");
require 'vendor/autoload.php'; // Nên load autoloader ngay đầu file

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mail'])) {
    $email = $_POST['mail'];

    $querysql = "SELECT Email FROM nguoidung";
    $query = mysqli_query($conn, $querysql);
    if (!$query) {
        die("Lỗi truy vấn: " . mysqli_error($conn));
    }

    $ktra = false;
    while ($row = mysqli_fetch_assoc($query)) {
        if ($row['Email'] == $email) {
            $ktra = true;
            break;
        }
    }

    if ($ktra) {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_OFF; 
            $mail->isSMTP();
            $mail->Host       = 'smtp.example.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'user@example.com';
            $mail->Password   = 'secret';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            //Recipients
            $mail->setFrom('from@example.com', 'Mailer');
            $mail->addAddress($email); // Gửi đến email người dùng

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Khôi phục mật khẩu';
            $mail->Body    = 'Đây là email để khôi phục mật khẩu của bạn.';
            $mail->AltBody = 'Đây là nội dung thuần văn bản.';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
        }
    } else {
        echo "<script>alert('Email không tồn tại')</script>";
    }
}
?>

    
</body>
</html>