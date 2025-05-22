<?php
include("../BackEnd/blockBugLogin.php");
include("../BackEnd/connectSQL.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$msv = $_SESSION['MSV'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/dangkyhocphan.css">
    <title>Đăng kí học phần</title>
</head>
<body>
    <?php include("../Template_Layout/main/header.php") ?>
    <div class="content">
        <?php include("../Template_Layout/main/sidebar.php") ?>
        <div class="main-content">
            <div class="panel">
                <div class="panel-heading"><strong>Đăng ký học phần</strong></div>
                <div class="panel-body">
                    <label>Chương trình đào tạo:</label>
                    <select style="width: 300px;">
                        <option>Công nghệ thông tin</option>
                    </select>
                    <p><strong>Chưa đến thời hạn đăng ký môn học</strong></p>
                    <input type="checkbox"> Theo lớp sinh viên

                    <fieldset>
                        <legend>Kết quả đăng ký: 0 học phần, 0 tín chỉ</legend>
                        <div class="reload_kqdk">
                            <div class="reload_1">Ghi chú:</div>
                            <div class="reload_mau02"></div>
                            <div class="reload_2">Trùng lịch</div>
                            <div class="reload_mau03"></div>
                            <div class="reload_3">LHP hủy</div>
                        </div>
                        <table>
                            <thead>
                                <th>Loại</th>
                                <th>Mã LHP</th>
                                <th>Tên LHP</th>
                                <th>STC</th>
                                <th>GV</th>
                                <th>Lịch học</th>
                                <th>Từ ngày</th>
                                <th>Đến ngày</th>
                                <th>Trạng thái</th>
                            </thead>
                            <tbody>
                                <?php
                                    $sql = "
                                        SELECT dkhp.MaLopHocPhan, dkhp.TenLopHocPhan, ctdt.SoTinChi, dkhp.GiangVien, dkhp.LichHoc, dkhp.NgayBatDau, dkhp.NgayKetThuc
                                        FROM DangKyHocPhan dkhp
                                        INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
                                        INNER JOIN Nganh n ON n.MaNganh = ctdt.MaNganh
                                        INNER JOIN ThongTinCaNhan ttcn ON n.MaNganh = ttcn.MaNganh
                                        WHERE ttcn.MaSinhVien = '$msv'";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        $i = 0;
                                        while($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $i++ . "</td>";
                                            echo "<td>" . $row['MaLopHocPhan'] . "</td>";
                                            echo "<td>" . $row['TenLopHocPhan'] . "</td>";
                                            echo "<td>" . $row['SoTinChi'] . "</td>";
                                            echo "<td>" . $row['GiangVien'] . "</td>";
                                            echo "<td>" . $row['LichHoc'] . "</td>";
                                            echo "<td>" . $row['NgayBatDau'] . "</td>";
                                            echo "<td>" . $row['NgayKetThuc'] . "</td>";
                                            echo "<td><a href='xacnhandangky.php?maHP=" . $row['MaLopHocPhan'] . "'>Đăng ký</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='8'>Không có dữ liệu</td></tr>";
                                    }

                                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['maHP'])) {
                                        $maHP = trim($_POST['maHP']);
                                        $msv = trim($msv);

                                        $conn->begin_transaction();
                                        try {
                                            // Kiểm tra MaSinhVien
                                            $sqlCheckMSV = "SELECT MaSinhVien FROM ThongTinCaNhan WHERE MaSinhVien = ?";
                                            $stmtCheckMSV = $conn->prepare($sqlCheckMSV);
                                            $stmtCheckMSV->bind_param("s", $msv);
                                            $stmtCheckMSV->execute();
                                            $resultCheckMSV = $stmtCheckMSV->get_result();
                                            if ($resultCheckMSV->num_rows == 0) {
                                                throw new Exception("Mã sinh viên $msv không tồn tại trong hệ thống.");
                                            }

                                            // Kiểm tra MaLopHocPhan
                                            $sqlCheckMaHP = "SELECT MaLopHocPhan FROM DangKyHocPhan WHERE MaLopHocPhan = ?";
                                            $stmtCheckMaHP = $conn->prepare($sqlCheckMaHP);
                                            $stmtCheckMaHP->bind_param("s", $maHP);
                                            $stmtCheckMaHP->execute();
                                            $resultCheckMaHP = $stmtCheckMaHP->get_result();
                                            if ($resultCheckMaHP->num_rows == 0) {
                                                throw new Exception("Mã lớp học phần $maHP không tồn tại trong hệ thống.");
                                            }

                                            // Lấy TenHocPhan
                                            $sqlGetTenHP = "SELECT TenHocPhan FROM ChuongTrinhDaoTao WHERE MaHocPhan = (SELECT MaHocPhan FROM DangKyHocPhan WHERE MaLopHocPhan = ?)";
                                            $stmtTenHP = $conn->prepare($sqlGetTenHP);
                                            $stmtTenHP->bind_param("s", $maHP);
                                            $stmtTenHP->execute();
                                            $resultTenHP = $stmtTenHP->get_result();
                                            $tenHocPhan = $resultTenHP->fetch_assoc()['TenHocPhan'] ?? 'Tên mặc định';

                                            // Chèn dữ liệu
                                            $now = date("Y-m-d H:i:s");
                                            $sqlInsert = "INSERT INTO KetQuaDangKyHocPhan (MaLopHocPhan, NgayDangKy, TenHocPhan, MaSinhVien) VALUES (?, ?, ?, ?)";
                                            $stmtInsert = $conn->prepare($sqlInsert);
                                            $stmtInsert->bind_param("ssss", $maHP, $now, $tenHocPhan, $msv);
                                            $stmtInsert->execute();

                                            $conn->commit();
                                            echo "<script>alert('Đăng ký thành công $maHP');</script>";
                                        } catch (Exception $e) {
                                            $conn->rollback();
                                            echo "<script>alert('Đăng ký thất bại: " . $conn->error . "');</script>";
                                        }
                                    }

                                    $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>