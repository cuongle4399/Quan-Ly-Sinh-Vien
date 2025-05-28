<?php include ("../BackEnd/blockBugLogin.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/ykien.css">
    <title>Trường đại học Quy Nhơn</title>
</head>
<body>
<?php include("../Template_Layout/main/header.php") ?>
    <div class = "content">
       <?php include("../Template_Layout/main/sidebar.php") ?>
        <div class="content__main">
            <div class ="content_1"><p>Ý kiến thảo luận</p></div>
            <div>
                <label>Năm học:</label>
                <select>
                    <option></option>
                </select>
                <label>Học kỳ:</label>
                <select>
                    <option></option>
                </select>
            </div>
            <div>
                <table>
                    <thead>
                        <th>STT</th>
                        <th>Mã học phần</th>
                        <th>Tên học phần</th>
                        <th>STC</th>
                        <th>Thông tin</th>
                        <th>Giảng viên</th>
                        <th>Đánh giá học phần</th>
                        <th>Thảo luận</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan ="8" class="cotent_2">Không tìm thấy môn học</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>