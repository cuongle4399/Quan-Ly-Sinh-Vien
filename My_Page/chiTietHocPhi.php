<?php
include("../BackEnd/blockBugLogin.php");
include("../BackEnd/connectSQL.php");

// Handle payment update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['msv'])) {
    $msv = $_POST['msv'];
    $sql = "UPDATE ChiTietHocPhi SET TrangThai = '1' WHERE MaSinhVien = '$msv'";
    if ($conn->query($sql)) {
        header("Location: ".$_SERVER['PHP_SELF']."?msv=$msv");
        exit();
    } else {
        echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
    }
}

// Check if all subjects are paid
$msv = isset($_GET['msv']) ? $_GET['msv'] : $msv; // Ensure $msv is defined
$allPaid = true;
$checkSql = "
    SELECT cthp.TrangThai
    FROM ChiTietHocPhi AS cthp
    WHERE cthp.MaSinhVien = '$msv'";
$checkResult = $conn->query($checkSql);
if ($checkResult) {
    while ($row = $checkResult->fetch_assoc()) {
        if ($row['TrangThai'] != '1') {
            $allPaid = false;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/cthp.css">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Trường đại học Quy Nhơn</title>
</head>
<body>
  <?php include("../Template_Layout/main/header.php"); ?>

  <div class="content">
    <?php include("../Template_Layout/main/sidebar.php"); ?>

    <div class="content__main">
      <header>
        <h1>Tài chính sinh viên</h1>
      </header>
      <div class="main__table">
        <?php
          // Fetch distinct semesters
          $semesterSql = "
            SELECT DISTINCT ctdt.HocKy
            FROM ChiTietHocPhi AS cthp
            INNER JOIN DangKyHocPhan AS dkhp ON cthp.MaLopHocPhan = dkhp.MaLopHocPhan 
            INNER JOIN KetQuaDangKyHocPhan AS kqdkhp ON kqdkhp.MaLopHocPhan = dkhp.MaLopHocPhan 
                AND kqdkhp.MaSinhVien = cthp.MaSinhVien 
            INNER JOIN ChuongTrinhDaoTao AS ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan 
            INNER JOIN Nganh AS n ON n.MaNganh = ctdt.MaNganh
            WHERE cthp.MaSinhVien = '$msv'
            ORDER BY ctdt.HocKy";
          $semesterResult = $conn->query($semesterSql);

          if (!$semesterResult) {
              die("Lỗi truy vấn học kỳ: " . $conn->error);
          }

          while ($semesterRow = $semesterResult->fetch_assoc()) :
              $hocKy = $semesterRow['HocKy'];
              $totalDebt = 0;

              // Fetch data for this semester
              $sql = "
                SELECT cthp.MaPhi, cthp.MaLopHocPhan, (ctdt.SoTinChi * n.GiaCua1TinChi) AS 'Nợ',
                       cthp.TrangThai, kqdkhp.MaSinhVien, ctdt.HocKy
                FROM ChiTietHocPhi AS cthp
                INNER JOIN DangKyHocPhan AS dkhp ON cthp.MaLopHocPhan = dkhp.MaLopHocPhan 
                INNER JOIN KetQuaDangKyHocPhan AS kqdkhp ON kqdkhp.MaLopHocPhan = dkhp.MaLopHocPhan 
                    AND kqdkhp.MaSinhVien = cthp.MaSinhVien 
                INNER JOIN ChuongTrinhDaoTao AS ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan 
                INNER JOIN Nganh AS n ON n.MaNganh = ctdt.MaNganh
                WHERE cthp.MaSinhVien = '$msv' AND ctdt.HocKy = '$hocKy'";
              $result = $conn->query($sql);

              if (!$result) {
                  die("Lỗi truy vấn: " . $conn->error);
              }

              $debtResult = $conn->query($sql);
              while ($debtRow = $debtResult->fetch_assoc()) {
                  if ($debtRow['TrangThai'] != '1') {
                      $totalDebt += $debtRow['Nợ'];
                  }
              }
        ?>
          <table class="table">
            <thead>
              <tr>
                <th>Mã phí</th>
                <th>Tên phí</th>
                <th>Phải đóng</th>
                <th>Trạng thái</th>
                <th>Ngày đóng</th>
                <th>Còn nợ</th>
              </tr>
            </thead>
            <tbody>
              <tr class="semester-row">
                <td colspan="6">Học kỳ: <?= $hocKy ?></td>
              </tr>
              <?php while ($row = $result->fetch_assoc()) : ?>
                <tr class="hocphi-row">
                  <td><?= $row['MaPhi'] ?></td>
                  <td><?= $row['MaLopHocPhan'] ?></td>
                  <td><?= $row['Nợ'] ?></td>
                  <td><?= ($row['TrangThai'] == "1") ? "Đã đóng" : "Chưa đóng" ?></td>
                  <td><?= ($row['TrangThai'] == "1") ? date("d/m/Y") : "" ?></td>
                  <td><?= ($row['TrangThai'] == "1") ? "0" : $row['Nợ'] ?></td>
                </tr>
              <?php endwhile; ?>
              <tr class="total-debt-row">
                <td colspan="5" style="text-align: right; font-weight: bold;">Tổng nợ:</td>
                <td><?= number_format($totalDebt, 0, ',', '.') ?></td>
              </tr>
            </tbody>
          </table>
        <?php endwhile; ?>
        <form method="POST" onsubmit="return confirm('Bạn muốn thanh toán?')">
            <input type="hidden" name="msv" value="<?= $msv ?>">
            <button type="submit" class="payment-btn <?= $allPaid ? 'paid' : 'unpaid' ?>" <?= $allPaid ? 'disabled' : '' ?>>
                <?= $allPaid ? 'Đã thanh toán' : 'Thanh toán' ?>
            </button>
        </form>
      </div>
    </div>
  </div>

  <?php include("../Template_Layout/main/footer.php"); ?>
</body>
</html>