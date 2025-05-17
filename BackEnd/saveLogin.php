<?php
session_start();
if (isset($_SESSION['MSV'])) {
    if($_SESSION['MSV'] == "Admin"){
        header("Location: ../Admin/admin.php");
    }
    else {
        header("Location: ../My_Page/home.php");
    }
    
}
?>