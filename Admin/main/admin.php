<?php
include('../../BackEnd/blockBugLogin.php');
include('../../BackEnd/phantrang.php');
include('../../BackEnd/connectSQL.php');

// Handle search conditions
$searchCondition = "";
$queryParams = [];
$conditions = [];

if (!empty($_GET['MaSV'])) {
    $msv = mysqli_real_escape_string($conn, $_GET['MaSV']);
    $conditions[] = "ttcn.MaSinhVien = '$msv'";
    $queryParams['MaSV'] = $msv;
}

if (!empty($_GET['TinhTrangHoc']) && $_GET['TinhTrangHoc'] !== 'all') {
    $tinhTrangHoc = mysqli_real_escape_string($conn, $_GET['TinhTrangHoc']);
    $conditions[] = "ttcn.TinhTrangHoc = '$tinhTrangHoc'";
    $queryParams['TinhTrangHoc'] = $tinhTrangHoc;
}

if (!empty($_GET['MaNganh']) && $_GET['MaNganh'] !== 'all') {
    $maNganh = mysqli_real_escape_string($conn, $_GET['MaNganh']);
    $conditions[] = "ttcn.MaNganh = '$maNganh'";
    $queryParams['MaNganh'] = $maNganh;
}

if (!empty($conditions)) {
    $searchCondition = " WHERE " . implode(" AND ", $conditions);
}

// Count total students
$countQuery = "SELECT COUNT(*) as total FROM thongtincanhan ttcn 
               JOIN Nganh n ON ttcn.MaNganh = n.MaNganh $searchCondition";
$countResult = $conn->query($countQuery);
$totalStudents = $countResult ? $countResult->fetch_assoc()['total'] : 0;

// Fetch distinct study statuses
$statusQuery = "SELECT DISTINCT TinhTrangHoc FROM thongtincanhan ORDER BY TinhTrangHoc";
$statusResult = $conn->query($statusQuery);

// Fetch distinct majors
$majorQuery = "SELECT MaNganh, TenNganh FROM Nganh ORDER BY TenNganh";
$majorResult = $conn->query($majorQuery);
?>

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
      <div>Số lượng sinh viên đang quản lý: <?= $totalStudents ?></div>

      <form class="main-search" method="GET">
        <input name="MaSV" type="text" class="inputSearch" placeholder="Nhập mã sinh viên" value="<?= isset($_GET['MaSV']) ? htmlspecialchars($_GET['MaSV']) : '' ?>">
        <select name="TinhTrangHoc" class="inputSearch">
          <option value="all">Tất cả trạng thái</option>
          <?php
          if ($statusResult && $statusResult->num_rows > 0) {
              while ($status = $statusResult->fetch_assoc()) {
                  $selected = (isset($_GET['TinhTrangHoc']) && $_GET['TinhTrangHoc'] == $status['TinhTrangHoc']) ? 'selected' : '';
                  echo "<option value='{$status['TinhTrangHoc']}' $selected>{$status['TinhTrangHoc']}</option>";
              }
          }
          ?>
        </select>
        <select name="MaNganh" class="inputSearch">
          <option value="all">Tất cả ngành học</option>
          <?php
          if ($majorResult && $majorResult->num_rows > 0) {
              while ($major = $majorResult->fetch_assoc()) {
                  $selected = (isset($_GET['MaNganh']) && $_GET['MaNganh'] == $major['MaNganh']) ? 'selected' : '';
                  echo "<option value='{$major['MaNganh']}' $selected>{$major['TenNganh']}</option>";
              }
          }
          ?>
        </select>
        <button class="btnSearch" type="submit">Tìm🔍</button>
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
        $currentPage = max(1, $currentPage);
        $offset = ($currentPage - 1) * $limit;

        // Main data query
        $query = "SELECT ttcn.MaSinhVien, ttcn.HoTen, ttcn.LopSinhVien, ttcn.GioiTinh, ttcn.SoDienThoai, 
                         ttcn.NgaySinh, ttcn.DanToc, n.TenNganh, ttcn.TinhTrangHoc, ttcn.TinhThanhPho 
                  FROM thongtincanhan ttcn 
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

      <?php if ($totalStudents > 0):
          $totalPages = ceil($totalStudents / $limit);
          if ($totalPages > 1): ?>
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
      <?php endif; endif; ?>
    </div>
  </div>
  <script src="../../Js/Nofinish.js"></script>
</body>
</html>