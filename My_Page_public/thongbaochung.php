<?php include("../BackEnd/saveLogin.php"); ?>
<?php include("../BackEnd/connectSQL.php"); ?>

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
            
            <?php 
            
            $sqlTinTuc = "SELECT * FROM TinTuc";
            $resultTinTuc = mysqli_query($conn, $sqlTinTuc);

            ?>

            <div class="main__content-content">
                <?php 
                
                while ($row = mysqli_fetch_assoc($resultTinTuc)):
                    $TieuDe = $row['TieuDe'];
                    $NoiDung = $row['NoiDung'];
                    $NgayDang = $row['NgayDang'];
                ?>

                <div class="main__content-content-items">
                    <div class="main__content-content-items-link">
                        <a href="../My_Page_public/noidungtintuc.php?id=<?= $row['Id'] ?>"> <?= $TieuDe ?> </a>
                    </div>
                   
                    <div class="main__content-content-items-date">
                        <div> Ngày đăng <?= $NgayDang ?> </div>
                    </div>
                </div>
            
                <?php endwhile ?>

            <div class="main__content--number-of-page">
                <p>Trang [1]</p>
            </div>
        </div>
    </main>

    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>