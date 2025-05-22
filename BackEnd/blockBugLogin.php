<?php
session_start();
if (!isset($_SESSION['MSV'])) {
    header("Location: ../My_Page_public/index.php");
     exit();
} else {
    $msv =$_SESSION['MSV'];
}
?>