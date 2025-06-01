<?php
include("../BackEnd/saveLogin.php");
include("../BackEnd/connectSQL.php");

$NoiDung = '';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM TinTuc WHERE Id = $id";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $NoiDung = $row['NoiDung'];
    } else {
        $NoiDung = "Không tìm thấy bài viết!";
    }
} else {
    $NoiDung = "Không xác định bài viết!";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Trang Chủ</title>
    <link rel="stylesheet" href="../css/main.css">
    
</head>
<body>
    <?php include("../Template_Layout/main/header.php") ?>

    <main class="main">
       
        <?php include("../My_Page_public/sidebar.php") ?>
 
        <div class="main__content">
            <div class="main__content-title">
                <p>Thông báo chung</p>
            </div>
    
            <div class="main__content-content">

                <div class="main__content-content-items">
                    <?= $NoiDung ?>
                </div>
        </div>
    </main>

    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>