<?php
include("../BackEnd/blockBugLogin.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$msv = $_SESSION['MSV'] ?? '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Đăng ký học phần</title>
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/ketquadangkyhocphan.css">
</head>
<body>
<?php include("../Template_Layout/main/header.php") ?>
<div class="content">
    <?php include("../Template_Layout/main/sidebar.php") ?>
    <div class="main-content">
        <div class="panel">
            <div class="panel-heading"><strong>Kết quả đăng ký học phần</strong></div>
            <div class="panel-body">
                <?php
                include("../BackEnd/connectSQL.php");

                $sql_sv = "SELECT ttcn.MaSinhVien, ttcn.HoTen, ttcn.LopSinhVien, ng.TenNganh 
                           FROM ThongTinCaNhan ttcn
                           INNER JOIN Nganh ng ON ttcn.MaNganh = ng.MaNganh
                           WHERE ttcn.MaSinhVien = ?";
                $stmt_sv = $conn->prepare($sql_sv);
                $stmt_sv->bind_param("s", $msv);
                $stmt_sv->execute();
                $result_sv = $stmt_sv->get_result();

                if ($row_sv = $result_sv->fetch_assoc()) {
                    echo "<p><strong>Sinh viên:</strong> {$row_sv['MaSinhVien']} - {$row_sv['HoTen']} | Lớp: {$row_sv['LopSinhVien']} | Khoa: {$row_sv['TenNganh']}</p>";
                } else {
                    echo "<p>Không tìm thấy thông tin sinh viên.</p>";
                }

                $sql = "SELECT kqdkhp.MaLopHocPhan, kqdkhp.TenHocPhan, ctdt.SoTinChi, ctdt.HocKy, kqdkhp.NgayDangKy 
                        FROM KetQuaDangKyHocPhan kqdkhp
                        INNER JOIN DangKyHocPhan dkhp ON dkhp.MaLopHocPhan = kqdkhp.MaLopHocPhan
                        INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
                        WHERE kqdkhp.MaSinhVien = ?
                        ORDER BY ctdt.HocKy";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $msv);
                $stmt->execute();
                $result = $stmt->get_result();

                $semester_data = [];
                $semester_credits = [];
                while ($row = $result->fetch_assoc()) {
                    $semester = $row['HocKy'];
                    $semester_data[$semester][] = $row;
                    $semester_credits[$semester] = ($semester_credits[$semester] ?? 0) + $row['SoTinChi'];
                }

                if (empty($semester_data)) {
                    echo "<p>Không có dữ liệu đăng ký học phần.</p>";
                } else {
                    foreach ($semester_data as $semester => $rows) {
                        echo "<fieldset><legend>Học kỳ $semester</legend><table>
                              <tr><th>STT</th><th>Mã lớp học phần</th><th>Tên học phần</th><th>STC</th><th>Ngày đăng ký</th></tr>";
                        $i = 1;
                        foreach ($rows as $row) {
                            echo "<tr><td>$i</td><td>{$row['MaLopHocPhan']}</td><td>{$row['TenHocPhan']}</td><td>{$row['SoTinChi']}</td><td>{$row['NgayDangKy']}</td></tr>";
                            $i++;
                        }
                        echo "<tr><td colspan='3' style='text-align: right; font-weight: bold;'>Tổng số tín chỉ:</td><td colspan='2'>{$semester_credits[$semester]}</td></tr>";
                        echo "</table></fieldset>";
                    }
                }
                $conn->close();
                ?>
            </div>
        </div>
    </div>
</div>
<?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>