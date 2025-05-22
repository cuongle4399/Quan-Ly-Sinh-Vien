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
        <input name = "mail" id = "form_Main__input"type="email" placeholder="Nhập Gmail của bạn"></input>
         <label class = "lbl" for="">Ma OTP</label>
        <input name = "OTP" id = "form_Main__input" type="number" placeholder="Nhập mã OTP">
         <label for="" class = "thongBao">Vui lòng kiểm tra Gmail để lấy mã OTP</label>
        <a class = "Back" href="../My_Page_public/login.php">Quay lại trang chủ</a>
        <button id = "form_Main__btn" type="submit">Gửi mã OTP</button>
    </form>
</body>
</html>