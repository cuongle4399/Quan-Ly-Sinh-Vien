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
        $this->msv = $conn->real_escape_string($msv);
    }

    public function calculateGrade($diemCC, $diemCk) {
        $diem10 = $diemCC * 0.3 + $diemCk * 0.7;
        $diem4 = ($diem10 / 10) * 4;
        $diemChu = $diem10 >= 8.5 ? 'A' :
                   ($diem10 >= 7.8 ? 'B+' :
                   ($diem10 >= 7.0 ? 'B' :
                   ($diem10 >= 6.3 ? 'C+' :
                   ($diem10 >= 5.5 ? 'C' :
                   ($diem10 >= 4.8 ? 'D+' :
                   ($diem10 >= 4.0 ? 'D' : 'F'))))));
        $ketQua = $diem10 >= 4.0 ? 'Đạt' : 'Không đạt';
        return [
            'diem10' => number_format($diem10, 2),
            'diem4' => number_format($diem4, 2),
            'diemChu' => $diemChu,
            'ketQua' => $ketQua
        ];
    }

    public function getGrades() {
        $sql = "
            SELECT d.*, ctdt.MaHocPhan, ctdt.TenHocPhan, ctdt.SoTinChi, ctdt.HocKy,
                   CONCAT(YEAR(dkhp.NgayBatDau), '-', YEAR(dkhp.NgayKetThuc)) AS NamHoc
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
$msv = $_SESSION['MaSinhVien'] ?? 'SV001';
$gradeViewer = new GradeViewer($conn, $msv);
$grades = $gradeViewer->getGrades();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <?php
                    if ($grades->num_rows == 0) {
                        echo "<tr><td colspan='9'>Không có dữ liệu.</td></tr>";
                    } else {
                        $i = 1;
                        while ($row = $grades->fetch_assoc()) {
                            $grade = $gradeViewer->calculateGrade($row['DiemCC'], $row['DiemCk']);
                    ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($row['MaHocPhan']) ?></td>
                                <td><?= htmlspecialchars($row['TenHocPhan']) ?></td>
                                <td><?= $row['SoTinChi'] ?></td>
                                <td><?= $grade['diem10'] ?></td>
                                <td><?= $grade['diem4'] ?></td>
                                <td><?= $grade['diemChu'] ?></td>
                                <td>
                                    <img src="../image/Dau.png" alt="tich" <?= $grade['ketQua'] == 'Đạt' ? '' : 'style="display:none;"' ?>>
                                    <img src="../image/X.png" alt="x" <?= $grade['ketQua'] == 'Không đạt' ? '' : 'style="display:none;"' ?>>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" onclick="showDetail('<?= addslashes($row['MaHocPhan']) ?>', '<?= addslashes($row['TenHocPhan']) ?>', '<?= $row['DiemCC'] ?>', '<?= $row['DiemCk'] ?>')">
                                        <img src="../image/detail.png" alt="Chi tiết">
                                    </a>
                                </td>
                            </tr>
                    <?php } } ?>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include("../Template_Layout/main/footer.php") ?>

<div id="detailModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeDetail()">×</span>
        <h3 id="modalTitle"></h3>
        <table>
            <tr>
                <th>STT</th>
                <th>Tên thành phần</th>
                <th>Điểm</th>
            </tr>
            <tr>
                <td>1</td>
                <td>Điểm Quá trình</td>
                <td id="diemQuaTrinh"></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Điểm thi kết thúc</td>
                <td id="diemThi"></td>
            </tr>
        </table>
    </div>
</div>

<script>
function showDetail(maHocPhan, tenHocPhan, diemQuaTrinh, diemThi) {
    document.getElementById('modalTitle').textContent = tenHocPhan || 'Không có dữ liệu';
    document.getElementById('diemQuaTrinh').textContent = diemQuaTrinh ? parseFloat(diemQuaTrinh).toFixed(2) : 'N/A';
    document.getElementById('diemThi').textContent = diemThi ? parseFloat(diemThi).toFixed(2) : 'N/A';
    document.getElementById('detailModal').style.display = 'block';
}

function closeDetail() {
    document.getElementById('detailModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('detailModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
};
</script>
</body>
</html>
<?php $conn->close(); ?>