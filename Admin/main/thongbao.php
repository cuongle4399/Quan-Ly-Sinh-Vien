<?php
include ('../../BackEnd/blockBugLogin.php');
include('../../BackEnd/connectSQL.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['TieuDe']) && isset($_POST['NoiDung'])) {
    $tieuDe = $_POST['TieuDe'];
    $noiDung = $_POST['NoiDung'];

    $sqlquery = "INSERT INTO TinTuc (TieuDe, NoiDung) VALUES (?, ?)";
    $stmt = $conn->prepare($sqlquery);

    if ($stmt) {
        $stmt->bind_param("ss", $tieuDe, $noiDung); 
        if ($stmt->execute()) {
            echo "<script>alert('Thông báo đã được gửi thành công.');</script>";
        } else {
            echo "<script>alert('Gửi thông báo thất bại: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Lỗi prepare statement: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <link rel="stylesheet" href="../css/admin.css">
     <link rel="stylesheet" href="../css/thongbao.css">
    <title>Thông báo chung</title>
</head>
<body>
    <?php include('header.php'); ?>
  <div class="Content-main">
    <?php include('sidebar.php'); ?>
    <div class="main">
      <form action="" method="POST">
          <label class = "lbl" for="">Tiêu đề thông báo: </label>
          <input id = "title" name = "TieuDe" type = "text" required> </input>
          <div>
            <label class = "lbl" for="">Nội dung thông báo: </label>
            <textarea id="conten" name="NoiDung" rows="5" cols="40"></textarea>
          </div>
          <button class = "btnsend" type="submit">Gửi thông báo cho toàn sinh viên</button>
      </form>
    </div>
    </div>
  </div>
  <script src="../../Js/Nofinish.js"></script>
</body>
</html>