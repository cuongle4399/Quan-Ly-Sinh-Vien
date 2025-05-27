   <?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include("../BackEnd/connectSQL.php");
require '../vendor/autoload.php';

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
        $token = random_int(100000,999999);
        $expires_at = date("Y-m-d H:i:s", time() + 300);
        $querysql = "UPDATE NguoiDung
        SET Token_Expires_at = '$expires_at',
        Token = '$token'
        WHERE Email = '$email'";
        if (mysqli_query($conn, $querysql)) {
        } else {
            echo "Lỗi: " . mysqli_error($conn);
            return;
        }
        mysqli_close($conn);
        $mail = new PHPMailer(true);

        try {
        //Server settings

        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                     
        $mail->isSMTP();                                           
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                               
        $mail->Username   = 'cuongmikasa@gmail.com';           
        $mail->Password   = 'yadaeejroykepazh';                             
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;          
        $mail->Port       = 465;
        $mail->CharSet ='UTF-8';                               

        //Recipients
        $mail->setFrom('cuongmikasa@gmail.com', 'Đại học trâm mâm');
        $mail->addAddress($email);      
        $mail->addReplyTo('cuongmikasa@gmail.com', 'Information');

        //Content
        $mail->isHTML(true);                             
        $mail->Subject = 'Mã OTP Khôi phục mật khẩu';
        $mail->Body = '
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
  <div style="max-width: 500px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 10px; text-align: center; border: 1px solid #ddd;">
    <h2 style="color: #333333; font-size: 20px; margin-bottom: 20px;">Mã OTP khôi phục mật khẩu của bạn là:</h2>
    <div style="display: inline-block; background-color: #e0f0ff; color: #007bff; padding: 15px 30px; font-size: 28px; font-weight: bold; border-radius: 6px; letter-spacing: 5px; margin: 20px 0;">
      ' .$token. '
    </div>
    <p style="font-size: 14px; color: #666666; margin-top: 20px;">Vui lòng không chia sẻ mã này với bất kỳ ai. Mã có hiệu lực trong 5 phút.</p>
  </div>
</body>
</html>
';
        $mail->AltBody = 'Cường Lê';
        $mail->send();
       header("Location: EnterCodeOTP.php?email=".base64_encode($email)."");
       exit();
    } catch (Exception $e) {
        echo "Lỗi send mail: {$mail->ErrorInfo}";
    }
    } else {
        echo '<script>document.getElementById("thongBao").style.display = "block"</script>';
    }
}
?>

  
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
         <label for="" id = "thongBao">Gmail không tồn tại</label>
        <a class = "Back" href="../My_Page_public/login.php">Quay lại trang chủ</a>
        <button id = "form_Main__btn" type="submit">Gửi mail</button>
    </form>
</body>
</html>