<?php
include("../BackEnd/blockBugLogin.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class GradeViewer {
    private $conn;
    private $msv;

    public function __construct($conn, $msv) {
        $this->conn = $conn;
        $this->msv = $msv;
    }

    public function calculateGrade($diemCC, $diemCk) {
        $diem10 = $diemCC * 0.4 + $diemCk * 0.6;
        $diem4 = $diem10 * 0.4;

        $grades = [
           9 => 'A+', 8 => 'A', 7 => 'B+', 6 => 'B', 5 => 'C', 4.0 => 'D',
        ];
        $diemChu = 'F';
        foreach ($grades as $threshold => $letter) {
            if ($diem10 >= $threshold) {
                $diemChu = $letter;
                break;
            }
        }

        return [
            'diem10' => round($diem10, 2),
            'diem4' => round($diem4, 2),
            'diemChu' => $diemChu,
            'ketQua' => $diem10 >= 4.0 ? 'Đạt' : 'Không đạt'
        ];
    }

    public function getGrades() {
        $sql = "
            SELECT d.*, ctdt.MaHocPhan, ctdt.TenHocPhan, ctdt.SoTinChi, ctdt.HocKy,
                CASE 
                    WHEN MONTH(dkhp.NgayBatDau) >= 9 THEN CONCAT(YEAR(dkhp.NgayBatDau), '-', YEAR(dkhp.NgayBatDau) + 1)
                    ELSE CONCAT(YEAR(dkhp.NgayBatDau) - 1, '-', YEAR(dkhp.NgayBatDau))
                END AS NamHoc
            FROM Diem d
            INNER JOIN DangKyHocPhan dkhp ON d.MaLopHocPhan = dkhp.MaLopHocPhan
            INNER JOIN ChuongTrinhDaoTao ctdt ON dkhp.MaHocPhan = ctdt.MaHocPhan
            WHERE d.MaSinhVien = ?
            ORDER BY dkhp.NgayBatDau DESC, ctdt.HocKy ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $this->msv);
        $stmt->execute();
        return $stmt->get_result();
    }

}

include("../BackEnd/connectSQL.php");
$msv = $_SESSION['MSV'] ?? '';
$gradeViewer = new GradeViewer($conn, $msv);
$grades = $gradeViewer->getGrades();

$groupedGrades = [];
$overall = ['credits' => 0, 'creditsPassed' => 0, 'diem10' => 0, 'diem4' => 0, 'subjects' => 0, 'passedSubjects' => 0];

while ($row = $grades->fetch_assoc()) {
    $key = $row['HocKy'].'-'.$row['NamHoc'];
    $grade = $gradeViewer->calculateGrade($row['DiemCC'], $row['DiemCk']);
    $row['grade'] = $grade;
    $groupedGrades[$key][] = $row;

    $overall['credits'] += $row['SoTinChi'];
    $overall['subjects']++;
    if ($grade['ketQua'] == 'Đạt') {
        $overall['creditsPassed'] += $row['SoTinChi'];
        $overall['passedSubjects']++;
    }
    $overall['diem10'] += $grade['diem10'] * $row['SoTinChi'];
    $overall['diem4'] += $grade['diem4'] * $row['SoTinChi'];
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <title>Trường Đại học Quy Nhơn - Xem Điểm</title>
    <link rel="stylesheet" href="../css/mainIN.css">
    <link rel="stylesheet" href="../css/xemDiem.css">
</head>
<body>
<?php include("../Template_Layout/main/header.php") ?>
<div class="content">
    <?php include("../Template_Layout/main/sidebar.php") ?>
    <div class="content__main">
        <div class="panel">
            <div class="panel-heading">Kết Quả Học Tập</div>
            <div class="panel-body">
                <?php if (empty($groupedGrades)): ?>
                    <table><tr><td colspan="9">Không có dữ liệu.</td></tr></table>
                <?php else: ?>
                    <?php foreach ($groupedGrades as $key => $group): ?>
                        <?php
                        $parts = explode('-', $key);
                        $hocKy = $parts[0];
                        $namHoc = $parts[1] . '-' . $parts[2];
                        $semester = ['credits' => 0, 'creditsPassed' => 0, 'diem10' => 0, 'diem4' => 0, 'subjects' => count($group), 'passedSubjects' => 0];
                        foreach ($group as $row) {
                            $semester['credits'] += $row['SoTinChi'];
                            $semester['diem10'] += $row['grade']['diem10'] * $row['SoTinChi'];
                            $semester['diem4'] += $row['grade']['diem4'] * $row['SoTinChi'];
                            if ($row['grade']['ketQua'] == 'Đạt') {
                                $semester['creditsPassed'] += $row['SoTinChi'];
                                $semester['passedSubjects']++;
                            }
                        }
                        $avgDiem10 = $semester['credits'] ? round($semester['diem10'] / $semester['credits'], 2) : 0;
                        $avgDiem4 = $semester['credits'] ? round($semester['diem4'] / $semester['credits'], 2) : 0;
                        ?>
                        <h4>Học kỳ: <?= $hocKy ?> - Năm học: <?= $namHoc ?></h4>
                        <table>
                            <tr>
                                <th>STT</th>
                                <th>Mã học phần</th>
                                <th>Tên học phần</th>
                                <th>Tín chỉ</th>
                                <th>Điểm 10</th>
                                <th>Điểm 4</th>
                                <th>Điểm chữ</th>
                                <th>Kết quả</th>
                                <th>Chi tiết</th>
                            </tr>
                            <?php $i = 1; foreach ($group as $row): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($row['MaHocPhan']) ?></td>
                                    <td><?= htmlspecialchars($row['TenHocPhan']) ?></td>
                                    <td><?= $row['SoTinChi'] ?></td>
                                    <td><?= $row['grade']['diem10'] ?></td>
                                    <td><?= $row['grade']['diem4'] ?></td>
                                    <td><?= $row['grade']['diemChu'] ?></td>
                                    <td>
                                        <img src="../image/<?= $row['grade']['ketQua'] == 'Đạt' ? 'Dau.png' : 'X.png' ?>" alt="status">
                                    </td>
                                    <td>
                                        <a href="#" class="detail-link" data-mahp="<?= addslashes($row['MaHocPhan']) ?>" 
                                           data-tenhp="<?= addslashes($row['TenHocPhan']) ?>" 
                                           data-diemqt="<?= $row['DiemCC'] ?>" 
                                           data-diemthi="<?= $row['DiemCk'] ?>">
                                            <img src="../image/detail.png" alt="Chi tiết">
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        <p><strong>Tổng kết học kỳ:</strong><br>
                        - Tổng số tín chỉ: <?= $semester['credits'] ?><br>
                        - Số tín chỉ đạt: <?= $semester['creditsPassed'] ?><br>
                        - Số tín chỉ không đạt: <?= $semester['credits'] - $semester['creditsPassed'] ?><br>
                        - Điểm trung bình học kỳ (Hệ 10): <?= $avgDiem10 ?><br>
                        - Điểm trung bình học kỳ (Hệ 4): <?= $avgDiem4 ?></p>
                    <?php endforeach; ?>
                    <h4>Tổng kết tất cả các kỳ</h4>
                    <p><strong>Tổng kết tích lũy:</strong><br>
                    - Tổng số tín chỉ: <?= $overall['credits'] ?><br>
                    - Số tín chỉ đạt: <?= $overall['creditsPassed'] ?><br>
                    - Số tín chỉ không đạt: <?= $overall['credits'] - $overall['creditsPassed'] ?><br>
                    - Số môn học: <?= $overall['subjects'] ?><br>
                    - Số môn đạt: <?= $overall['passedSubjects'] ?><br>
                    - Điểm trung bình tích lũy (Hệ 10): <?= $overall['credits'] ? round($overall['diem10'] / $overall['credits'], 2) : 0 ?><br>
                    - Điểm trung bình tích lũy (Hệ 4): <?= $overall['credits'] ? round($overall['diem4'] / $overall['credits'], 2) : 0 ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include("../Template_Layout/main/footer.php") ?>

<div id="detailModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3 id="modalTitle"></h3>
        <table>
            <tr><th>STT</th><th>Tên thành phần</th><th>Điểm</th></tr>
            <tr><td>1</td><td>Điểm Quá trình</td><td id="diemQuaTrinh"></td></tr>
            <tr><td>2</td><td>Điểm thi kết thúc</td><td id="diemThi"></td></tr>
        </table>
    </div>
</div>

<script>
document.querySelectorAll('.detail-link').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        const modal = document.getElementById('detailModal');
        document.getElementById('modalTitle').textContent = link.dataset.tenhp || 'Không có dữ liệu';
        document.getElementById('diemQuaTrinh').textContent = link.dataset.diemqt ? parseFloat(link.dataset.diemqt).toFixed(2) : 'N/A';
        document.getElementById('diemThi').textContent = link.dataset.diemthi ? parseFloat(link.dataset.diemthi).toFixed(2) : 'N/A';
        modal.style.display = 'block';
    });
});

document.querySelector('.close').addEventListener('click', () => {
    document.getElementById('detailModal').style.display = 'none';
});

window.addEventListener('click', e => {
    if (e.target === document.getElementById('detailModal')) {
        e.target.style.display = 'none';
    }
});
</script>
</body>
</html>
<?php $conn->close(); ?>