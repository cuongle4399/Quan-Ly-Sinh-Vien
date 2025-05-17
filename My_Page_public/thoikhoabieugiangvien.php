<?php include("../BackEnd/saveLogin.php") ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>
    <?php include("../headerHome.php") ?>

    <!--Main-->
    <main class="main">
        <!--Sidebar-->
        <div class="main__sidebar">
            <div class="main__sidebar-items"><a href="tintuc.php">Tin tức</a></div>
            <div class="main__sidebar-items"><a href="thongbaochung.php">Thông báo chung</a></div>
            <div class="main__sidebar-items"><a href="cacquydinh.php">Các quy định</a></div>
            <div class="main__sidebar-items"><a href="thongbaohocphi.php">Thông báo học phí</a></div>
        </div>

        <!--Content-->
        <div class="main__content">
            <div class="main__content-title">
                <p>Thời Khóa Biểu Giảng Viên</p>
            </div>

            <div class="main__content-select">
                <div class="main__content-select-items">
                    Năm học:
                    <select name="year" id="year">
                        <option value="nam1">2020-2021</option>
                        <option value="nam2">2021-2022</option>
                        <option value="nam3">2022-2023</option>
                        <option value="nam4">2023-2024</option>
                    </select>
                </div>
    
                <div class="main__content-select-items">
                    Học kỳ:
                    <select name="semester" id="semester">
                        <option value="hk1">Học kỳ 1</option>
                        <option value="hk2">Học kỳ 2</option>
                        <option value="hk3">Học kỳ 3</option>
                        <option value="hk4">Học kỳ 4</option>
                    </select>
                </div>
    
                <div class="main__content-select-items">
                    Khoa:
                    <select name="department" id="department">
                        <option value="k1">Khoa 1</option>
                        <option value="k2">Khoa 2</option>
                        <option value="k3">Khoa 3</option>
                        <option value="k4">Khoa 4</option>
                    </select>
                </div>  
                
                <div class="main__content-select-items">
                    <input type="text" placeholder="Mã CBGV hoặc họ tên">
                    <button class="find" type="button">Lọc dữ liệu</button>
                </div>
            </div>
        </div>
    </main>

    <?php include("../footer.php") ?>
</body>
</html>
