<?php 
include ('../../BackEnd/blockBugLogin.php');
include('../../BackEnd/connectSQL.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/quanlyhocphi.css">
    <title>Quản lý học phí</title>
</head>
<body>
    <?php include('header.php'); ?>
    <div class="Content-main">
    <?php include('sidebar.php'); ?>
        <div class="main">
            <div class="main_1">
            <form action="" method="POST">
                <p><strong>Tra cứu chi tiết học phí của sinh viên</strong></p>
                <label for="">Chọn Ngành: </label>
                <select name="nganh" id="nganh">
                    <option value="CNTT" <?php if(isset($_POST['nganh']) && $_POST['nganh'] == 'CNTT') echo 'selected'; ?>>CNTT</option>
                    <option value="KT" <?php if(isset($_POST['nganh']) && $_POST['nganh'] == 'KT') echo 'selected'; ?>>KT</option>
                    <option value="QTKD" <?php if(isset($_POST['nganh']) && $_POST['nganh'] == 'QTKD') echo 'selected'; ?>>QTKD</option>
                </select>
                <label for="">Mã sinh viên: </label>
                <input type="text" name="msv" placeholder="Nhập mã sinh viên" value="<?php echo isset($_POST['msv']) ? htmlspecialchars($_POST['msv']) : '' ?>">
                <button name="tra_cuu_theo_msv">Tra cứu</button>
            </form>
            <?php
            $ketqua = $_SESSION['ketqua'] ?? [];
            $error = "";

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tra_cuu_theo_msv'])) {
                $nganh = $_POST['nganh'] ?? '';
                $masv = $_POST['msv'] ?? '';

                if (empty($nganh) || empty($masv)) {
                    $error = "Vui lòng nhập đúng hoặc đầy đủ thông tin.";
                    $_SESSION['ketqua'] = [];
                } else {
                    $sql = "SELECT t.MaSinhVien, t.HoTen, n.MaNganh, n.TenNganh, c.MaPhi, c.TrangThai 
                            FROM thongtincanhan AS t 
                            JOIN nganh AS n ON t.MaNganh = n.MaNganh 
                            JOIN chitiethocphi AS c ON c.MaSinhVien = t.MaSinhVien 
                            WHERE t.MaSinhVien = ? AND t.MaNganh = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $masv, $nganh);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $ketqua = [];
                    while ($row = $result->fetch_assoc()) {
                        $ketqua[] = $row;
                    }
                    $_SESSION['ketqua'] = $ketqua;

                    if (empty($ketqua)) {
                        $error = "Không tìm thấy sinh viên với thông tin đã nhập.";
                    }
                }
            }
            ?>  
            <?php if (!empty($ketqua)): ?>
                <table>
                <tr>
                    <th>Mã Sinh Viên</th>
                    <th>Tên Sinh Viên</th>
                    <th>Ngành</th>
                    <th>Tên Ngành</th>
                    <th>Mã Phí</th>
                    <th>Trạng Thái</th>
                </tr>
                <?php foreach ($ketqua as $row): ?>
                    <tr>
                        <td><?php echo ($row['MaSinhVien']); ?></td>
                        <td><?php echo ($row['HoTen']); ?></td>
                        <td><?php echo ($row['MaNganh']); ?></td>
                        <td><?php echo ($row['TenNganh']); ?></td>
                        <td><?php echo ($row['MaPhi']); ?></td>
                        <td><?php echo ($row['TrangThai']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
            </div>


            <div class="main_2">
                <form action="" method="POST">
                    <p><strong>Tra cứu số lượng chưa đóng học phí theo kỳ của Ngành</strong></p>
                    <label for="">Ngành học: </label>
                    <select name="nganh_hoc" id="nganh_hoc">
                        <option value="CNTT" <?php if(isset($_POST['nganh_hoc']) && $_POST['nganh_hoc'] == 'CNTT') echo 'selected'; ?>>CNTT</option>
                        <option value="KT" <?php if(isset($_POST['nganh_hoc']) && $_POST['nganh_hoc'] == 'KT') echo 'selected'; ?>>KT</option>
                        <option value="QTKD" <?php if(isset($_POST['nganh_hoc']) && $_POST['nganh_hoc'] == 'QTKD') echo 'selected'; ?>>QTKD</option>
                    </select>
                    <label for="">Kỳ học: </label>
                    <input type="text" name="kyhoc" placeholder="Nhập kỳ học" value="<?php echo isset($_POST['kyhoc']) ? htmlspecialchars($_POST['kyhoc']) : '' ?>">
                    <button name="tra_cuu_theo_ky">Tra cứu</button>
                </form>
                <?php
                $ketqua_1 = $_SESSION['ketqua_1'] ?? [];
                $error_1 = "";

                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tra_cuu_theo_ky'])) {
                    $nganh = $_POST['nganh_hoc'] ?? '';
                    $kyhoc = $_POST['kyhoc'] ?? '';

                    if (empty($nganh) || empty($kyhoc)) {
                        $error_1 = "Vui lòng nhập đúng hoặc đầy đủ thông tin.";
                        $_SESSION['ketqua_1'] = [];
                    } else {
                        $sql = "SELECT COUNT(c.TrangThai) AS TongSoLuong, ctdt.MaNganh
                                FROM chitiethocphi AS c
                                JOIN thongtincanhan AS t ON c.MaSinhVien = t.MaSinhVien
                                JOIN nganh AS n ON n.MaNganh = t.MaNganh
                                JOIN chuongtrinhdaotao AS ctdt ON ctdt.MaNganh = n.MaNganh
                                WHERE ctdt.HocKy = ? AND ctdt.MaNganh = ? AND c.TrangThai = 0
                                ";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ss", $kyhoc, $nganh);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $ketqua_1 = [];
                        while ($row = $result->fetch_assoc()) {
                            $ketqua_1[] = $row;
                        }
                        $_SESSION['ketqua_1'] = $ketqua_1;

                        if (empty($ketqua_1)) {
                            $error_1 = "Không tìm thấy sinh viên với thông tin đã nhập.";
                        }
                    }
                }
                ?>  
                <?php if (!empty($ketqua_1)): ?>
                    <table>
                    <tr>
                        <th>Tổng Số Lượng</th>
                        <th>Mã Ngành</th>
                    </tr>
                    <?php foreach ($ketqua_1 as $row): ?>
                        <tr>
                            <td><?php echo ($row['TongSoLuong']); ?></td>
                            <td><?php echo ($row['MaNganh']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if (!empty($error)): ?>
    <script>
        alert("<?php echo $error; ?>");
    </script>
    <?php endif; ?>

    <?php if (!empty($error_1)): ?>
    <script>
        alert("<?php echo $error_1; ?>");
    </script>
    <?php endif; ?>
</body>
</html>
