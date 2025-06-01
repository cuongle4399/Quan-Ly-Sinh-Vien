 <?php 
    if (!isset($_SESSION['MSV'])) {
       echo '<a id="user" href="../My_Page_public/login.php">Đăng nhập</a>';
         } else {
       echo '
      
    <form method="post" action="../My_Page_public/index.php">
       <button class="btnDangXuat" type="submit" name="submit_btn">Đăng xuất</button>
     </form>
 ';
  if (isset($_POST['submit_btn'])) {
             unset($_SESSION['MSV']);
           }
         }
     ?>