<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Template_Layout/css/header.css">
</head>
<body>
<header class="header">
    <img class="header__logo" src="../image/logoHeader.jpg" alt="logoQuyNhon">
    
    <nav class="header__nav nav">
        <div class="nav__section nav__section--left">
            <div class="nav__menu-icon">
                <img width="25" height="25" src="https://img.icons8.com/ios-filled/50/menu--v1.png" alt="menu icon" />
            </div>
            <ul class="nav__menu">
                <li class="nav__item">
                    <a href="../My_Page_public/index.php">Trang chủ</a>
                </li>
                <li class="nav__item">
                    <a href="../My_Page/dangKyHocPhan.php">Đăng ký học phần</a>
                </li>
            </ul>
        </div>

        <div class="nav__section nav__section--right">
            <ul class="nav__user-list">
                <li class="nav__user">
                    <?php 
                        if (!isset($_SESSION['MSV'])) {
                            echo '<a id="user" href="../My_Page_public/login.php">Đăng nhập</a>';
                        } else {
                            echo '
                                <!-- index.php -->
                                <form method="post" action="../My_Page_public/index.php">
                                    <button class="btnDangXuat" type="submit" name="submit_btn">Đăng xuất</button>
                                </form>
                            ';

                            if (isset($_POST['submit_btn'])) {
                                unset($_SESSION['MSV']);
                                 exit();
                            }
                        }
                    ?>
                </li>
            </ul>
        </div>
    </nav>
</header>
</body>
</html>
