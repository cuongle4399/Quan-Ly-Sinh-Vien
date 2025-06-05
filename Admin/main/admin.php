<?php
include ('../../BackEnd/blockBugLogin.php');
include ('../../BackEnd/phantrang.php');
include ('../../BackEnd/connectSQL.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Trang Quản lý Sinh viên</title>
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="icon" href="../image/logo.png" type="image/jpeg">
  <style>
    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 20px;
    }
    .pagination a {
      padding: 8px 12px;
      text-decoration: none;
      border: 1px solid #ddd;
      border-radius: 4px;
      color: #333;
      background-color: #fff;
      transition: background-color 0.3s;
    }
    .pagination a:hover {
      background-color: #f0f0f0;
    }
    .pagination a.active {
      background-color: #007bff;
      color: white;
      border-color: #007bff;
    }
    .pagination a.disabled {
      background-color: #f9f9f9;
      color: #ccc;
      cursor: not-allowed;
    }
    .pagination span {
      padding: 8px 12px;
      color: #333;
    }
  </style>
</head>
<body>
  <?php include('header.php'); ?>
  <div class="Content-main">
    <?php include('sidebar.php'); ?>
    <div class="main">
      <?php
      // Xử lý tìm kiếm
      $searchCondition = "";
      $queryParams = [];
      if (!empty($_GET['MaSV'])) {
          $msv = mysqli_real_escape_string($conn, $_GET['MaSV']);
          $searchCondition = " WHERE ttcn.MaSinhVien = '$msv'";
          $queryParams['MaSV'] = $msv;
      }

      // Đếm số lượng sinh viên phù hợp
      $countQuery = "SELECT COUNT(*) as total FROM thongtincanhan ttcn 
                     JOIN Nganh n ON ttcn.MaNganh = n.MaNganh $searchCondition";
      $countResult = $conn->query($countQuery);
      $totalStudents = $countResult ? $countResult->fetch_assoc()['total'] : 0;

      echo "<div>Số lượng sinh viên trường đang quản lý: $totalStudents</div>";
      ?>

      <form class="main-search" method="GET">
        <button class="btnSearch" type="submit">Tìm🔍</button>
        <input name="MaSV" type="text" class="inputSearch" placeholder="Nhập mã sinh viên" value="<?= isset($_GET['MaSV']) ? htmlspecialchars($_GET['MaSV']) : '' ?>">
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
        $limit = 5;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $currentPage = max(1, $currentPage); // Đảm bảo trang ít nhất là 1
        $offset = ($currentPage - 1) * $limit;

        // Truy vấn dữ liệu chính
        $query = "SELECT * FROM thongtincanhan ttcn 
                  JOIN Nganh n ON ttcn.MaNganh = n.MaNganh 
                  $searchCondition 
                  LIMIT $limit OFFSET $offset";

        $result = $conn->query($query);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['MaSinhVien']}</td>
                    <td>{$row['HoTen']}</td>
                    <td>{$row['LopSinhVien']}</td>
                    <td>{$row['GioiTinh']}</td>
                    <td>{$row['SoDienThoai']}</td>
                    <td>{$row['NgaySinh']}</td>
                    <td>{$row['DanToc']}</td>
                    <td>{$row['TenNganh']}</td>
                    <td>{$row['TinhTrangHoc']}</td>
                    <td>{$row['TinhThanhPho']}</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='10'>Không có dữ liệu</td></tr>";
        }
        ?>
      </table>

      <?php
      if ($totalStudents > 0):
          $totalPages = ceil($totalStudents / $limit);
          if ($totalPages > 1): 
      ?>
        <div class="pagination">
        <?php
        $prevPage = $currentPage - 1;
        if ($currentPage <= 1) {
            echo "<span class='disabled'>« Trước</span>";
        } else {
            $prevParams = array_merge($queryParams, ['page' => $prevPage]);
            echo "<a href='admin.php?" . http_build_query($prevParams) . "'>« Trước</a>";
        }
        $firstParams = array_merge($queryParams, ['page' => 1]);
        $firstActive = ($currentPage == 1) ? "class='active'" : "";
        echo "<a href='admin.php?" . http_build_query($firstParams) . "' $firstActive>1</a>";
        if ($currentPage > 2) {
            echo "<span>...</span>";
        }
        if ($currentPage > 1 && $currentPage < $totalPages) {
            $currentParams = array_merge($queryParams, ['page' => $currentPage]);
            echo "<a href='admin.php?" . http_build_query($currentParams) . "' class='active'>$currentPage</a>";
        }

        if ($currentPage < $totalPages - 1) {
            echo "<span>...</span>";
        }
        if ($currentPage < $totalPages) {
            $lastParams = array_merge($queryParams, ['page' => $totalPages]);
            $lastActive = ($currentPage == $totalPages) ? "class='active'" : "";
            echo "<a href='admin.php?" . http_build_query($lastParams) . "' $lastActive>$totalPages</a>";
        }

        $nextPage = $currentPage + 1;
        if ($currentPage >= $totalPages) {
            echo "<span class='disabled'>Sau »</span>";
        } else {
            $nextParams = array_merge($queryParams, ['page' => $nextPage]);
            echo "<a href='admin.php?" . http_build_query($nextParams) . "'>Sau »</a>";
        }
        ?>
      </div>

      <?php
          endif; 
      endif; 
      ?>
    </div>
  </div>
  <script src="../../Js/Nofinish.js"></script>
</body>
</html>