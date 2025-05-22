<?php include("../BackEnd/saveLogin.php") ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    

    <form class="form" method="POST">
        <div class="form__dntk">ĐĂNG NHẬP TÀI KHOẢN</div>
        <div class="form__dash">__________</div>
        <div class="form__tkmk">
            <label for="tk">Tên Tài Khoản</label>
            <input name="taiKhoan" type="text" id="tk" placeholder="Nhập tài khoản">
            <label for="mk">Mật khẩu</label>
            <input name="matkhau" type="password" id="mk" placeholder="Nhập mật khẩu">
            <a class = "QuenMatKhau"href="../My_Page_public/ForgotPass.php" >Quên mật khẩu</a>
        </div>
        <div id="thongbao" class = "thongBao">Thông tin tài khoản mật khẩu không chính xác</div>
        <div class="form__button"> 
            <button onclick = InputLoginNULL(); type= "submit" class="form__button-login">Đăng nhập</button>
        </div>
    </form>

    <?php
    include("../BackEnd/connectSQL.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['taiKhoan']) && isset($_POST['matkhau'])) {
        $tk = $_POST['taiKhoan'];
        $mk = $_POST['matkhau'];

        $querysql = "SELECT MaSinhVien, MatKhau, VaiTro FROM nguoidung";
        $query = mysqli_query($conn, $querysql);
        if (!$query) {
            die("Lỗi truy vấn: " . mysqli_error($conn));
        }

        $found = false;
        $KiemTraQuyenAdmin = false;
        while ($row = mysqli_fetch_assoc($query)) {
            if ($row['MaSinhVien'] == $tk && $row['MatKhau'] == $mk) {
                if($row['VaiTro'] == 1 && $row['MaSinhVien'] == "Admin"){
                    session_start();
                    $_SESSION['MSV'] = $row['MaSinhVien'];
                    header("Location: ../Admin/admin.php");
                    return;
            exit();
                    break;
                }
                $found = true;
                break;
            }
        }

        if ($found) {
            header("Location: ../My_Page/home.php");
            session_start();
            $_SESSION['MSV'] = $row['MaSinhVien'];
            exit();
        } else {
            echo "<script>document.getElementById('thongbao').style.display = 'block'</script>";
        }
    }
    ?>

</body>
</html>
