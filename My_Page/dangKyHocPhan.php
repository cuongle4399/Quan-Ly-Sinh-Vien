<?php
include("../BackEnd/blockBugLogin.php");
include("../BackEnd/connectSQL.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$msv = $_SESSION['MSV'] ?? '';

// Lấy giá trị lọc từ GET
$hocKy = isset($_GET['hocKy']) ? $_GET['hocKy'] : '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/dangkyhocphan.css">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Đăng ký học phần</title>
</head>
<body>
    <?php include("../Template_Layout/main/header.php") ?>
    <div class="content">
        <?php include("../Template_Layout/main/sidebar.php") ?>
        <div class="main-content">
            <div class="panel">
                <div class="panel-heading"><strong>Đăng ký học phần</strong></div>
                <div class="panel-body">
                    <!-- Form lọc -->
                    <form method="GET" action="" id="filterForm">
                        <label>Chương trình đào tạo:</label>
                        <select style="width: 300px;">
                            <option>Công nghệ thông tin</option>
                        </select>

                        <label>Học kỳ:</label>
                        <select name="hocKy" style="width: 150px;" onchange="this.form.submit()">
                            <option value="">-- Chọn học kỳ --</option>
                            <?php
                            // Lấy danh sách học kỳ từ bảng ChuongTrinhDaoTao
                            $sqlHocKy = "SELECT DISTINCT HocKy FROM ChuongTrinhDaoTao ORDER BY HocKy";
                            $resultHocKy = $conn->query($sqlHocKy);
                            if ($resultHocKy->num_rows > 0) {
                                while ($rowHocKy = $resultHocKy->fetch_assoc()) {
                                    $hk = $rowHocKy['HocKy'];
                                    $selected = ($hocKy == $hk) ? "selected" : "";
                                    echo "<option value='$hk' $selected>Học kỳ $hk</option>";
                                }
                            }
                            ?>
                        </select>
                    </form>

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
                                // Truy vấn SQL với bộ lọc học kỳ
                                $sql = "
                                    SELECT dkhp.MaLopHocPhan, dkhp.TenLopHocPhan, ctdt.SoTinChi, dkhp.GiangVien, dkhp.LichHoc, dkhp.NgayBatDau, dkhp.NgayKetThuc
                                    FROM DangKyHocPhan dkhp
                                    INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
                                    INNER JOIN Nganh n ON n.MaNganh = ctdt.MaNganh
                                    INNER JOIN ThongTinCaNhan ttcn ON n.MaNganh = ttcn.MaNganh
                                    WHERE ttcn.MaSinhVien = ?";

                                // Thêm điều kiện lọc HocKy nếu có
                                $params = [$msv];
                                $types = "s";
                                if (!empty($hocKy)) {
                                    $sql .= " AND ctdt.HocKy = ?";
                                    $params[] = $hocKy;
                                    $types .= "s";
                                }

                                // Chuẩn bị và thực thi truy vấn
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param($types, ...$params);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    $i = 0;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . ++$i . "</td>";
                                        echo "<td>" . $row['MaLopHocPhan'] . "</td>";
                                        echo "<td>" . $row['TenLopHocPhan'] . "</td>";
                                        echo "<td>" . $row['SoTinChi'] . "</td>";
                                        echo "<td>" . $row['GiangVien'] . "</td>";
                                        echo "<td>" . $row['LichHoc'] . "</td>";
                                        echo "<td>" . $row['NgayBatDau'] . "</td>";
                                        echo "<td>" . $row['NgayKetThuc'] . "</td>";
                                        echo "
                                            <form method='POST'>
                                                <input type='hidden' name='maHP' value='" . $row['MaLopHocPhan'] . "'>
                                                <input type='hidden' name='msv' value='$msv'>
                                                <td><button type='submit'>Đăng ký</button></td>
                                            </form>
                                        ";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>Không có dữ liệu</td></tr>";
                                }

                                // Xử lý form đăng ký
                                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['maHP']) && isset($_POST['msv'])) {
                                    $maHP = trim($_POST['maHP']);
                                    $msv = trim($_POST['msv']);

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
                                        $sqlCheckMaHP = "SELECT MaLopHocPhan, MaHocPhan FROM DangKyHocPhan WHERE MaLopHocPhan = ?";
                                        $stmtCheckMaHP = $conn->prepare($sqlCheckMaHP);
                                        $stmtCheckMaHP->bind_param("s", $maHP);
                                        $stmtCheckMaHP->execute();
                                        $resultCheckMaHP = $stmtCheckMaHP->get_result();
                                        if ($resultCheckMaHP->num_rows == 0) {
                                            throw new Exception("Mã lớp học phần $maHP không tồn tại trong hệ thống.");
                                        }
                                        $rowMaHP = $resultCheckMaHP->fetch_assoc();
                                        $maHocPhan = $rowMaHP['MaHocPhan'];

                                        // Kiểm tra đăng ký trùng
                                        $sqlCheckDuplicate = "SELECT MaLopHocPhan FROM KetQuaDangKyHocPhan WHERE MaLopHocPhan = ? AND MaSinhVien = ?";
                                        $stmtCheckDuplicate = $conn->prepare($sqlCheckDuplicate);
                                        $stmtCheckDuplicate->bind_param("ss", $maHP, $msv);
                                        $stmtCheckDuplicate->execute();
                                        $resultCheckDuplicate = $stmtCheckDuplicate->get_result();
                                        if ($resultCheckDuplicate->num_rows > 0) {
                                            throw new Exception("Học phần $maHP đã được đăng ký trước đó.");
                                        }

                                        // Kiểm tra học phần học trước
                                        $sqlCheckPreReq = "SELECT HocPhanHocTruoc FROM ChuongTrinhDaoTao WHERE MaHocPhan = ?";
                                        $stmtCheckPreReq = $conn->prepare($sqlCheckPreReq);
                                        $stmtCheckPreReq->bind_param("s", $maHocPhan);
                                        $stmtCheckPreReq->execute();
                                        $resultCheckPreReq = $stmtCheckPreReq->get_result();
                                        $rowPreReq = $resultCheckPreReq->fetch_assoc();
                                        $hocPhanHocTruoc = $rowPreReq['HocPhanHocTruoc'];

                                        if (!empty($hocPhanHocTruoc)) {
                                            // Kiểm tra xem đã đăng ký học phần học trước chưa
                                            $sqlCheckPreReg = "
                                                SELECT kqdkhp.MaLopHocPhan 
                                                FROM KetQuaDangKyHocPhan kqdkhp
                                                INNER JOIN DangKyHocPhan dkhp ON kqdkhp.MaLopHocPhan = dkhp.MaLopHocPhan
                                                WHERE dkhp.MaHocPhan = ? AND kqdkhp.MaSinhVien = ?";
                                            $stmtCheckPreReg = $conn->prepare($sqlCheckPreReg);
                                            $stmtCheckPreReg->bind_param("ss", $hocPhanHocTruoc, $msv);
                                            $stmtCheckPreReg->execute();
                                            $resultCheckPreReg = $stmtCheckPreReg->get_result();

                                            if ($resultCheckPreReg->num_rows == 0) {
                                                throw new Exception("Bạn chưa đăng ký học phần học trước ($hocPhanHocTruoc).");
                                            }

                                            // Kiểm tra điểm của học phần học trước
                                            $sqlCheckPreScore = "
                                                SELECT Diem4 
                                                FROM Diem d
                                                INNER JOIN DangKyHocPhan dkhp ON d.MaLopHocPhan = dkhp.MaLopHocPhan
                                                WHERE dkhp.MaHocPhan = ? AND d.MaSinhVien = ?";
                                            $stmtCheckPreScore = $conn->prepare($sqlCheckPreScore);
                                            $stmtCheckPreScore->bind_param("ss", $hocPhanHocTruoc, $msv);
                                            $stmtCheckPreScore->execute();
                                            $resultCheckPreScore = $stmtCheckPreScore->get_result();
                                            $hasPassingScore = false;

                                            while ($rowScore = $resultCheckPreScore->fetch_assoc()) {
                                                if ($rowScore['Diem4'] >= 4) {
                                                    $hasPassingScore = true;
                                                    break;
                                                }
                                            }

                                            if (!$hasPassingScore) {
                                                throw new Exception("Bạn chưa hoàn thành học phần học trước ($hocPhanHocTruoc) với điểm đạt (>= 4).");
                                            }
                                        }

                                        // Lấy TenHocPhan
                                        $sqlGetTenHP = "SELECT TenHocPhan FROM ChuongTrinhDaoTao WHERE MaHocPhan = ?";
                                        $stmtTenHP = $conn->prepare($sqlGetTenHP);
                                        $stmtTenHP->bind_param("s", $maHocPhan);
                                        $stmtTenHP->execute();
                                        $resultTenHP = $stmtTenHP->get_result();
                                        $tenHocPhan = $resultTenHP->fetch_assoc()['TenHocPhan'] ?? 'Tên mặc định';

                                        // Thêm dữ liệu
                                        $now = date("Y-m-d H:i:s");
                                        $sqlInsert = "INSERT INTO KetQuaDangKyHocPhan (MaLopHocPhan, NgayDangKy, TenHocPhan, MaSinhVien) VALUES (?, ?, ?, ?)";
                                        $stmtInsert = $conn->prepare($sqlInsert);
                                        $stmtInsert->bind_param("ssss", $maHP, $now, $tenHocPhan, $msv);
                                        $stmtInsert->execute();

                                        $conn->commit();
                                        echo "<script>alert('Đăng ký thành công $maHP');</script>";
                                    } catch (Exception $e) {
                                        $conn->rollback();
                                        echo "<script>alert('Đăng ký thất bại: " . addslashes($e->getMessage()) . "');</script>";
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