<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../BackEnd/saveLogin.php");
include("../BackEnd/connectSQL.php");

$sql = "
SELECT 
    sv.MaSinhVien,
    sv.HoTen,
    lhp.TenLopHocPhan,
    ctdt.SoTinChi * ng.GiaCua1TinChi AS HocPhi,
    CASE WHEN cthp.TrangThai = 1 THEN ctdt.SoTinChi * ng.GiaCua1TinChi ELSE 0 END AS SoTienDaDong
FROM 
    ThongTinCaNhan sv
JOIN 
    KetQuaDangKyHocPhan dk ON sv.MaSinhVien = dk.MaSinhVien
JOIN 
    DangKyHocPhan lhp ON dk.MaLopHocPhan = lhp.MaLopHocPhan
JOIN 
    ChuongTrinhDaoTao ctdt ON lhp.MaHocPhan = ctdt.MaHocPhan
JOIN 
    Nganh ng ON sv.MaNganh = ng.MaNganh
LEFT JOIN 
    ChiTietHocPhi cthp ON cthp.MaLopHocPhan = dk.MaLopHocPhan AND cthp.MaSinhVien = dk.MaSinhVien
WHERE 
    (cthp.TrangThai IS NULL OR cthp.TrangThai = 0)
ORDER BY 
    sv.MaSinhVien, lhp.TenLopHocPhan
";

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Lỗi truy vấn SQL: " . mysqli_error($conn));
}

$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $maSinhVien = $row['MaSinhVien'];
    if (!isset($students[$maSinhVien])) {
        $students[$maSinhVien] = [
            'HoTen' => $row['HoTen'],
            'HocPhan' => []
        ];
    }
    $students[$maSinhVien]['HocPhan'][] = [
        'TenHocPhan' => $row['TenLopHocPhan'],
        'HocPhi' => $row['HocPhi'],
        'SoTienDaDong' => $row['SoTienDaDong']
    ];
}

// Tìm kiếm
$keyword = isset($_GET['keyword']) ? strtolower(trim($_GET['keyword'])) : '';
if (!empty($keyword)) {
    $students = array_filter($students, function ($value, $key) use ($keyword) {
        return strpos(strtolower($key), $keyword) !== false || strpos(strtolower($value['HoTen']), $keyword) !== false;
    }, ARRAY_FILTER_USE_BOTH);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Trang Chủ</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/tbhocphi.css">
</head>
<body>
    <?php include("../Template_Layout/main/header.php") ?>

    <main class="main">
       
        <?php include("../My_Page_public/sidebar.php") ?>
 
        <div class="main__content">
            <div class="main__content-title">
                <p>Thông báo học phí</p>
            </div>

            <form method="GET" class="search-form">
                <input type="text" name="keyword" placeholder="Tìm theo mã SV hoặc tên" value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit">Tìm kiếm</button>
            </form>

            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Mã Sinh Viên</th>
                        <th>Họ Tên</th>
                        <th>Tên Học Phần</th>
                        <th>Học Phí</th>
                        <th>Đã Đóng</th>
                        <th>Còn Nợ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($students)) : ?>
                        <tr><td colspan="6">Không có dữ liệu phù hợp.</td></tr>
                    <?php else : ?>
                        <?php foreach ($students as $maSV => $data) : ?>
                            <?php foreach ($data['HocPhan'] as $index => $hp) : ?>
                                <tr>
                                    <?php if ($index === 0): ?>
                                        <td rowspan="<?= count($data['HocPhan']) ?>"><?= $maSV ?></td>
                                        <td rowspan="<?= count($data['HocPhan']) ?>"><?= $data['HoTen'] ?></td>
                                    <?php endif; ?>
                                    <td><?= $hp['TenHocPhan'] ?></td>
                                    <td><?= number_format($hp['HocPhi']) ?>đ</td>
                                    <td><?= number_format($hp['SoTienDaDong']) ?>đ</td>
                                    <td><?= number_format($hp['HocPhi'] - $hp['SoTienDaDong']) ?>đ</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    </main>
        <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>
