<?php include ("../BackEnd/blockBugLogin.php") ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/mainIN.css">
    <title>Trường đại học Quy Nhơn</title>
</head>
<body>
    <?php include("../Template_Layout/main/header.php") ?>

    <div class = "content">
        <?php include("../Template_Layout/main/sidebar.php") ?>
        <div class="content__main">
            <div class="mailbox__nav">
                <div class="mailbox__nav-item">Hộp thư</div>
            </div>

            <div class="mailbox__table-container">
                <table class="mailbox__table">
                    <thead class="mailbox__head">
                        <tr class="mailbox__row-title">
                            <th class="mailbox__col-title">Tiêu đề</th>
                            <th class="mailbox__col-sender">Người gửi</th>
                            <th class="mailbox__col-time">Thời gian gửi</th>
                        </tr>
                    </thead>
                    <tbody class="mailbox__body">
                        <tr class="mailbox__row">
                            <td class="mailbox__cell">
                                <a href="#" class="mailbox__link">Hóa đơn điện tử ngày : 25/11/2024 8h:14:38 AM</a>
                            </td>
                            <td class="mailbox__cell">KHTC</td>
                            <td class="mailbox__cell">25/11/2024</td>
                        </tr>
                        <tr class="mailbox__row">
                            <td class="mailbox__cell">
                                <a href="#" class="mailbox__link">Hóa đơn điện tử ngày : 25/11/2024 8h:14:38 AM</a>
                            </td>
                            <td class="mailbox__cell">KHTC</td>
                            <td class="mailbox__cell">25/11/2024</td>
                        </tr>
                        <tr class="mailbox__row">
                            <td class="mailbox__cell">
                                <a href="#" class="mailbox__link">Hóa đơn điện tử ngày : 25/11/2024 8h:14:38 AM</a>
                            </td>
                            <td class="mailbox__cell">KHTC</td>
                            <td class="mailbox__cell">25/11/2024</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>



    </div>
    
    <?php include("../Template_Layout/main/footer.php") ?>
</body>
</html>