<?php 
include("../BackEnd/saveLogin.php");
include("../BackEnd/connectSQL.php");

$sql = "SELECT * FROM QuyDinh";
$resultSql = mysqli_query($conn, $sql);
if (!$resultSql) {
    die("Lỗi truy vấn SQL: " . mysqli_error($conn));
}

$quydinh = [];
while ($row = mysqli_fetch_assoc($resultSql)) {
    $maQuyDinh = $row['MaQuyDinh'];
    if (!isset($quydinh[$maQuyDinh])) {
        $quydinh[$maQuyDinh] = [
            'TieuDe' => $row['TieuDe'],
            'MoTa' => $row['MoTa'],
            'LoaiQuyDinh' => $row['LoaiQuyDinh'],
            'MucDoViPham' => $row['MucDoViPham'],
            'HinhThucXuLy' => $row['HinhThucXuLy'],
            'GhiChu' => $row['GhiChu']
        ];
    }
}

$cacquydinh = [];
$cacmucdovipham = [];
foreach ($quydinh as $qd) {
    if (!in_array($qd['LoaiQuyDinh'], $cacquydinh)) {
        $cacquydinh[] = $qd['LoaiQuyDinh'];
    }

    if (!in_array($qd['MucDoViPham'], $cacmucdovipham)) {
        $cacmucdovipham[] = $qd['MucDoViPham'];
    }
}


$keyword = '';
if (isset($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Các quy định</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="icon" href="../image/logo.png" type="image/jpeg">
    <link rel="stylesheet" href="../css/table_cacQuyDinh.css">
</head>
<body>
    
   <?php include("../Template_Layout/main/header.php") ?>
    <main class="main">
      
        <?php include("../My_Page_public/sidebar.php") ?>

        <div class="main__content">
            <div class="main__content-title">
                <p>Các quy định</p>
            </div>

            <div class="main__content-content">
                <div class="main__select">
                    <form method="GET" class="search-form">
                        <input type="text" name="keyword" placeholder="Tìm theo mã SV hoặc tên" value="<?= $keyword ?>" >
                        <button class="search" type="submit">Tìm kiếm</button>
                    </form>

                    <form method="get" action="" class="search-form">
                        <label for="cacloaiquydinh">Loại quy định:</label>
                        <select name="loaiquydinh" id="cacloaiquydinh" onchange="this.form.submit()">
                            <option value="" <?= (!isset($_GET['loaiquydinh']) || $_GET['loaiquydinh'] === '') ? 'selected' : '' ?>>-- Tất cả --</option>
                            <?php for ($i = 0; $i < count($cacquydinh); $i++) { ?>  
                                <option value="<?= $cacquydinh[$i] ?>" 
                                    <?= (isset($_GET['loaiquydinh']) && $_GET['loaiquydinh'] == $cacquydinh[$i]) ? 'selected' : '' ?>>
                                    <?= $cacquydinh[$i] ?>
                                </option>

                            <?php } ?>
                        </select>
                    </form>

                    <form method="get" action="" class="search-form">
                        <label for="cacmucdovipham">Mức quy định:</label>
                        <select name="mucdovipham" id="cacmucdovipham" onchange="this.form.submit()">
                            <option value="" <?= (!isset($_GET['mucdovipham']) || $_GET['mucdovipham'] === '') ? 'selected' : '' ?>>-- Tất cả --</option>
                            <?php for ($i = 0; $i < count($cacmucdovipham); $i++) { ?>  
                                <option value="<?= $cacmucdovipham[$i] ?>" 
                                    <?= (isset($_GET['mucdovipham']) && $_GET['mucdovipham'] == $cacmucdovipham[$i]) ? 'selected' : '' ?>>
                                    <?= $cacmucdovipham[$i] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </form>

                </div>

                <div class="main__table">
                    <table class="table">
                        <thead class="table__head">
                            <tr class="table__head-row">
                                <th class="table__head-cell">TT</th>
                                <th class="table__head-cell">TIÊU ĐỀ</th>
                                <th class="table__head-cell">MÔ TẢ</th>
                                <th class="table__head-cell">LOẠI QUY ĐỊNH</th>
                                <th class="table__head-cell">MỨC QUY PHẠM</th>
                                <th class="table__head-cell">HÌNH THỨC XỬ LÝ</th>
                                <th class="table__head-cell">GHI CHÚ</th>
                            </tr>
                        </thead>

                        <tbody class="table__body">
                            <?php if (isset($quydinh)) {
                                $stt = 1;
                                foreach ($quydinh as $qd) {
                                    $isMatched = true;

                                    if ($keyword !== '' && strpos(strtolower($qd['TieuDe']), strtolower($keyword)) === false) {
                                        $isMatched = false;
                                    }
                                    if (isset($_GET['loaiquydinh']) && $_GET['loaiquydinh'] !== '' && $qd['LoaiQuyDinh'] !== $_GET['loaiquydinh']) {
                                        $isMatched = false;
                                    }
                                    if (isset($_GET['mucdovipham']) && $_GET['mucdovipham'] !== '' && $qd['MucDoViPham'] !== $_GET['mucdovipham']) {
                                        $isMatched = false;
                                    }

                                    if ($isMatched) {
                                        // in dòng bảng
                            ?>
                                        <tr class="table__body-row">
                                            <td class="table__body-cell"><?= htmlspecialchars($stt++) ?></td>
                                            <td class="table__body-cell"><?= htmlspecialchars($qd['TieuDe']) ?></td>
                                            <td class="table__body-cell"><?= htmlspecialchars($qd['MoTa']) ?></td>
                                            <td class="table__body-cell"><?= htmlspecialchars($qd['LoaiQuyDinh']) ?></td>
                                            <td class="table__body-cell"><?= htmlspecialchars($qd['MucDoViPham']) ?></td>
                                            <td class="table__body-cell"><?= htmlspecialchars($qd['HinhThucXuLy']) ?></td>
                                            <td class="table__body-cell"><?= htmlspecialchars($qd['GhiChu']) ?></td>
                                        </tr>
                                <?php } } ?>
                            <?php } else { ?>
                                    <tr class="table__body-row">
                                        <td class="table__body-cell" colspan="7"><?= "Không có quy định!" ?></td>
                                    </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>


    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>
