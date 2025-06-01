<?php
include ('../../BackEnd/blockBugLogin.php');
?>
<?php include ('../../BackEnd/connectSQL.php') ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Trang Quản lý Sinh viên</title>
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="icon" href="../image/logo.png" type="image/jpeg">
</head>
<body>
  <?php include('header.php'); ?>
  <div class="Content-main">
    <?php include('sidebar.php'); ?>
    <div class="main">
       <?php
      // Query to count total students
      $countQuery = "SELECT COUNT(*) as total FROM thongtincanhan";
      $countResult = $conn->query($countQuery);
      $totalStudents = $countResult ? $countResult->fetch_assoc()['total'] : 0;
      ?>
      <div>Số lượng sinh viên trường đang quản lý: <?php echo $totalStudents; ?></div>
      <form class="main-search" method="POST">
        <button class="btnSearch" type="submit" name="search">Tìm🔍</button>
        <input name="MaSV" type="text" class="inputSearch" placeholder="Nhập mã sinh viên">
      </form>
      
      <table>
        <tr>
          <th>Mã sinh viên</th>
          <th>Họ và Tên</th>
          <th>Lớp sinh viên</th>
          <th>Giới tính</th>
          <th>Số điện thoại</th>
          <th>Ngày sinh</th>
          <th>Dân tộc</th>
          <th>Ngành học</th>
          <th>Tình trạng học</th>
          <th>Tỉnh/Thành phố</th>
        </tr>
        <?php
        // Base query
        $query = "SELECT * FROM thongtincanhan ttcn JOIN Nganh n ON ttcn.MaNganh = n.MaNganh";
        
        // Handle search
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search']) && !empty($_POST['MaSV'])) {
            $msv = mysqli_real_escape_string($conn, $_POST['MaSV']);
            $query .= " WHERE ttcn.MaSinhVien = '$msv'";
        }

        $result = $conn->query($query);
        
        if ($result) {
            while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['MaSinhVien'] ?></td>
                    <td><?= $row['HoTen'] ?></td>
                    <td><?= $row['LopSinhVien'] ?></td>
                    <td><?= $row['GioiTinh'] ?></td>
                    <td><?= $row['SoDienThoai'] ?></td>
                    <td><?= $row['NgaySinh'] ?></td>
                    <td><?= $row['DanToc'] ?></td>
                    <td><?= $row['TenNganh'] ?></td>
                    <td><?= $row['TinhTrangHoc'] ?></td>
                    <td><?= $row['TinhThanhPho'] ?></td>
                </tr>
            <?php endwhile;
        } else {
            echo "<tr><td colspan='10'>Vui lòng nhập đúng Mã Sinh viên</td></tr>";
        }
        ?>
      </table>
    </div>
  </div>
  <script src="../../Js/Nofinish.js"></script>
</body>
</html>