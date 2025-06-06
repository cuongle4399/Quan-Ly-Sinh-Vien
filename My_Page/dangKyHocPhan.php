<?php
include("../BackEnd/blockBugLogin.php");
include("../BackEnd/connectSQL.php");
include("../BackEnd/phantrang.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$msv = $_SESSION['MSV'] ?? '';
$hocKy = isset($_GET['hocKy']) ? $_GET['hocKy'] : '';
$maHPFilter = isset($_GET['maHP']) ? $_GET['maHP'] : '';
$giangVienFilter = isset($_GET['giangVien']) ? $_GET['giangVien'] : '';
$lichHocFilter = isset($_GET['lichHoc']) ? $_GET['lichHoc'] : '';
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

if (empty($msv)) {
    echo "<div class='message error'>Vui lòng đăng nhập để xem danh sách học phần.</div>";
    exit;
}

if (!isset($_SESSION['pending_courses'])) {
    $_SESSION['pending_courses'] = [];
}

// Lấy danh sách học phần đã đăng ký
$registeredCourses = [];
$totalRegisteredCourses = 0;
$totalRegisteredCredits = 0;
$sqlRegistered = "
    SELECT dkhp.MaLopHocPhan, dkhp.TenLopHocPhan, ctdt.SoTinChi, dkhp.GiangVien, dkhp.LichHoc
    FROM KetQuaDangKyHocPhan kqdkhp
    INNER JOIN DangKyHocPhan dkhp ON kqdkhp.MaLopHocPhan = dkhp.MaLopHocPhan
    INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
    WHERE kqdkhp.MaSinhVien = '$msv'";
$resultRegistered = $conn->query($sqlRegistered);
if ($resultRegistered) {
    while ($row = $resultRegistered->fetch_assoc()) {
        $registeredCourses[] = $row;
        $totalRegisteredCourses++;
        $totalRegisteredCredits += $row['SoTinChi'];
    }
}

// Xử lý hủy học phần trong hàng chờ
if (isset($_GET['cancel_pending']) && isset($_GET['maHP'])) {
    $maHP = $_GET['maHP'];
    $_SESSION['pending_courses'] = array_filter($_SESSION['pending_courses'], function($course) use ($maHP) {
        return $course['MaLopHocPhan'] != $maHP;
    });
    $success = "Học phần $maHP đã được xóa khỏi hàng chờ.";
    header("Location: dangKyHocPhan.php?success=" . urlencode($success));
    exit;
}

// Xử lý xác nhận một học phần
if (isset($_GET['confirm_pending']) && isset($_GET['maHP'])) {
    $maHP = $_GET['maHP'];
    // Kiểm tra trùng lịch học
    $conflictCourses = [];
    foreach ($_SESSION['pending_courses'] as $course1) {
        foreach ($registeredCourses as $course2) {
            if ($course1['LichHoc'] == $course2['LichHoc']) {
                $conflictCourses[] = $course1['MaLopHocPhan'];
                break;
            }
        }
        foreach ($_SESSION['pending_courses'] as $course2) {
            if ($course1['MaLopHocPhan'] != $course2['MaLopHocPhan'] && $course1['LichHoc'] == $course2['LichHoc']) {
                $conflictCourses[] = $course1['MaLopHocPhan'];
                break;
            }
        }
    }
    if (in_array($maHP, $conflictCourses)) {
        $error = "Học phần $maHP bị trùng lịch học, không thể xác nhận.";
        header("Location: dangKyHocPhan.php?error=" . urlencode($error));
        exit;
    }
    $courseToConfirm = null;
    foreach ($_SESSION['pending_courses'] as $course) {
        if ($course['MaLopHocPhan'] == $maHP) {
            $courseToConfirm = $course;
            break;
        }
    }
    if ($courseToConfirm) {
        $tenHocPhan = $courseToConfirm['TenLopHocPhan'];
        $ngayDangKy = date("Y-m-d H:i:s");
        $sqlInsert = "INSERT INTO KetQuaDangKyHocPhan (MaLopHocPhan, NgayDangKy, TenHocPhan, MaSinhVien) 
                      VALUES ('$maHP', '$ngayDangKy', '$tenHocPhan', '$msv')";
        if ($conn->query($sqlInsert)) {
            // Thêm vào ChiTietHocPhi
            $maPhi = "HP" . time() . "_" . $maHP . "_" . $msv;
            $sqlHocPhi = "INSERT INTO ChiTietHocPhi (MaPhi, MaLopHocPhan, MaSinhVien, TrangThai) 
                          VALUES ('$maPhi', '$maHP', '$msv', '0')";
            $conn->query($sqlHocPhi);
            $_SESSION['pending_courses'] = array_filter($_SESSION['pending_courses'], function($course) use ($maHP) {
                return $course['MaLopHocPhan'] != $maHP;
            });
            $success = "Học phần $maHP đã được xác nhận và lưu thành công.";
        } else {
            $error = "Lỗi khi lưu học phần $maHP: " . $conn->error;
        }
        header("Location: dangKyHocPhan.php?" . ($success ? "success=" . urlencode($success) : "error=" . urlencode($error)));
        exit;
    }
}

// Xử lý xác nhận tất cả học phần
if (isset($_GET['confirm_all'])) {
    // Kiểm tra trùng lịch học
    $conflictCourses = [];
    foreach ($_SESSION['pending_courses'] as $course1) {
        foreach ($registeredCourses as $course2) {
            if ($course1['LichHoc'] == $course2['LichHoc']) {
                $conflictCourses[] = $course1['MaLopHocPhan'];
                break;
            }
        }
        foreach ($_SESSION['pending_courses'] as $course2) {
            if ($course1['MaLopHocPhan'] != $course2['MaLopHocPhan'] && $course1['LichHoc'] == $course2['LichHoc']) {
                $conflictCourses[] = $course1['MaLopHocPhan'];
                break;
            }
        }
    }
    if (!empty($conflictCourses)) {
        $conflictList = implode(", ", $conflictCourses);
        $error = "Không thể xác nhận tất cả vì các học phần $conflictList bị trùng lịch học.";
        header("Location: dangKyHocPhan.php?error=" . urlencode($error));
        exit;
    }
    if (empty($_SESSION['pending_courses'])) {
        $error = "Không có học phần nào trong hàng chờ để xác nhận.";
    } else {
        $ngayDangKy = date("Y-m-d H:i:s");
        $successCount = 0;
        foreach ($_SESSION['pending_courses'] as $course) {
            $maHP = $course['MaLopHocPhan'];
            $tenHocPhan = $course['TenLopHocPhan'];
            $sqlInsert = "INSERT INTO KetQuaDangKyHocPhan (MaLopHocPhan, NgayDangKy, TenHocPhan, MaSinhVien) 
                          VALUES ('$maHP', '$ngayDangKy', '$tenHocPhan', '$msv')";
            if ($conn->query($sqlInsert)) {
                // Thêm vào ChiTietHocPhi
                $maPhi = "HP" . time() . "_" . $maHP . "_" . $msv;
                $sqlHocPhi = "INSERT INTO ChiTietHocPhi (MaPhi, MaLopHocPhan, MaSinhVien, TrangThai) 
                              VALUES ('$maPhi', '$maHP', '$msv', '0')";
                $conn->query($sqlHocPhi);
                $successCount++;
            }
        }
        $_SESSION['pending_courses'] = [];
        $success = "Đã xác nhận thành công $successCount học phần.";
    }
    header("Location: dangKyHocPhan.php?" . ($success ? "success=" . urlencode($success) : "error=" . urlencode($error)));
    exit;
}

// Xử lý hủy tất cả học phần
if (isset($_GET['cancel_all'])) {
    $_SESSION['pending_courses'] = [];
    $success = "Đã hủy tất cả học phần trong hàng chờ.";
    header("Location: dangKyHocPhan.php?success=" . urlencode($success));
    exit;
}

// Tính toán tổng số học phần và tín chỉ trong hàng chờ
$pendingCourses = $_SESSION['pending_courses'];
$conflictCourses = [];
foreach ($pendingCourses as $course1) {
    foreach ($registeredCourses as $course2) {
        if ($course1['LichHoc'] == $course2['LichHoc']) {
            $conflictCourses[] = $course1['MaLopHocPhan'];
            break;
        }
    }
    foreach ($pendingCourses as $course2) {
        if ($course1['MaLopHocPhan'] != $course2['MaLopHocPhan'] && $course1['LichHoc'] == $course2['LichHoc']) {
            $conflictCourses[] = $course1['MaLopHocPhan'];
            break;
        }
    }
}

$totalPendingCourses = count($pendingCourses);
$totalPendingCredits = array_sum(array_column($pendingCourses, 'SoTinChi'));

$totalCourses = $totalRegisteredCourses + $totalPendingCourses;
$totalCredits = $totalRegisteredCredits + $totalPendingCredits;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/dangkyhocphan.css">
    <link rel="stylesheet" href="../css/phantrang.css">
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
                    <?php if ($success): ?>
                        <div class="message success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($conflictCourses)): ?>
                        <div class="message error">Cảnh báo: Có học phần trong hàng chờ bị trùng lịch học!</div>
                    <?php endif; ?>

                    <form method="GET" action="" class="filter-form">
                        <input type="text" name="maHP" placeholder="Mã LHP..." value="<?php echo htmlspecialchars($maHPFilter); ?>">
                        <input type="text" name="giangVien" placeholder="Giảng viên..." value="<?php echo htmlspecialchars($giangVienFilter); ?>">
                        <input type="text" name="lichHoc" placeholder="Lịch học..." value="<?php echo htmlspecialchars($lichHocFilter); ?>">
                        <select name="hocKy" onchange="this.form.submit()">
                            <option value="">-- Chọn học kỳ --</option>
                            <?php
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
                        <button type="submit" style="background-color: #007bff; color: white; padding: 5px 10px;">Tìm kiếm</button>
                    </form>

                    <fieldset>
                        <legend>Đăng ký học phần</legend>
                        <form method="GET" action="xacnhandangky.php">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Mã LHP</th>
                                        <th>Ngày bắt đầu</th>
                                        <th>Ngày kết thúc</th>
                                        <th>Sĩ số ĐK</th>
                                        <th>GV</th>
                                        <th>Lịch học</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $pagination = new Pagination($conn, 10);
                                    $sql = "
                                        SELECT dkhp.MaLopHocPhan, dkhp.TenLopHocPhan, ctdt.SoTinChi, dkhp.GiangVien, dkhp.LichHoc, dkhp.NgayBatDau, dkhp.NgayKetThuc,
                                        (SELECT COUNT(*) FROM KetQuaDangKyHocPhan kqdk WHERE kqdk.MaLopHocPhan = dkhp.MaLopHocPhan) as SiSoDK
                                        FROM DangKyHocPhan dkhp
                                        INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
                                        INNER JOIN Nganh n ON n.MaNganh = ctdt.MaNganh
                                        INNER JOIN ThongTinCaNhan ttcn ON n.MaNganh = ttcn.MaNganh
                                        WHERE ttcn.MaSinhVien = '$msv'";
                                    if (!empty($hocKy)) {
                                        $sql .= " AND ctdt.HocKy = '$hocKy'";
                                    }
                                    if (!empty($maHPFilter)) {
                                        $sql .= " AND dkhp.MaLopHocPhan LIKE '%$maHPFilter%'";
                                    }
                                    if (!empty($giangVienFilter)) {
                                        $sql .= " AND dkhp.GiangVien LIKE '%$giangVienFilter%'";
                                    }
                                    if (!empty($lichHocFilter)) {
                                        $sql .= " AND dkhp.LichHoc LIKE '%$lichHocFilter%'";
                                    }

                                    $pagination->setQuery($sql);
                                    $result = $pagination->getData();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $isRegistered = false;
                                            foreach ($registeredCourses as $regCourse) {
                                                if ($regCourse['MaLopHocPhan'] == $row['MaLopHocPhan']) {
                                                    $isRegistered = true;
                                                    break;
                                                }
                                            }
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['MaLopHocPhan']) . "</td>";
                                            echo "<td>" . ($row['NgayBatDau'] ? date("d/m/Y", strtotime($row['NgayBatDau'])) : '') . "</td>";
                                            echo "<td>" . ($row['NgayKetThuc'] ? date("d/m/Y", strtotime($row['NgayKetThuc'])) : '') . "</td>";
                                            echo "<td>" . $row['SiSoDK'] . "</td>";
                                            echo "<td>" . htmlspecialchars($row['GiangVien']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['LichHoc']) . "</td>";
                                            echo "<td>";
                                            if ($isRegistered) {
                                                echo "<span>Đã đăng ký</span>";
                                            } else {
                                                echo "<a href='xacnhandangky.php?selected_course=" . urlencode($row['MaLopHocPhan']) . "' class='btn-register'>Đăng ký</a>";
                                            }
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>Không có dữ liệu</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php echo $pagination->generatePagination($_SERVER['PHP_SELF']); ?>
                        </form>
                    </fieldset>

                    <fieldset>
                        <legend>Kết quả đăng ký: <?php echo $totalPendingCourses; ?> học phần, <?php echo $totalPendingCredits; ?> tín chỉ</legend>
                        <div class="reload_kqdk">
                            <div class="reload_1">Ghi chú:</div>
                            <div class="reload_mau01" style="background-color: rgb(247, 247, 13);"></div>
                            <div class="reload_2">Trùng lịch</div>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Mã LHP</th>
                                    <th>Tên LHP</th>
                                    <th>STC</th>
                                    <th>GV</th>
                                    <th>Lịch học</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pendingCourses)): ?>
                                    <?php foreach ($pendingCourses as $course): ?>
                                        <tr class="<?php echo in_array($course['MaLopHocPhan'], $conflictCourses) ? 'conflict-yellow' : ''; ?>">
                                            <td><?php echo htmlspecialchars($course['MaLopHocPhan']); ?></td>
                                            <td><?php echo htmlspecialchars($course['TenLopHocPhan']); ?></td>
                                            <td><?php echo $course['SoTinChi']; ?></td>
                                            <td><?php echo htmlspecialchars($course['GiangVien']); ?></td>
                                            <td><?php echo htmlspecialchars($course['LichHoc']); ?></td>
                                            <td>
                                                <a href="?confirm_pending=1&maHP=<?php echo urlencode($course['MaLopHocPhan']); ?>" class="btn-confirm">Xác nhận</a>
                                                <a href="?cancel_pending=1&maHP=<?php echo urlencode($course['MaLopHocPhan']); ?>" class="btn-cancel">Hủy</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6">Chưa có học phần nào trong hàng chờ.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <?php if (!empty($pendingCourses)): ?>
                            <div style="margin-top: 10px;">
                                <a href="?confirm_all=1" class="btn-confirm">Xác nhận tất cả</a>
                                <a href="?cancel_all=1" class="btn-cancel">Hủy tất cả</a>
                            </div>
                        <?php endif; ?>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <?php include("../Template_Layout/main/footer.php") ?>
    <?php $conn->close(); ?>
</body>
</html>