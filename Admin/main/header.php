<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
  unset($_SESSION['MSV']);
  header("Location: ../../My_Page_public/index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <div class="header">
    <div>Quản trị viên</div>
    <div><strong>Trường đại học Quy Nhơn</strong></div>
    <form action="" method="POST">
        <button type="submit" name ='logout' class="user">Đăng Xuất</button>
    </form>
    
  </div>
</body>
</html>