<?php
$user = "root";
$passwork = "";
$nameDatabase = "quanlysinhvien";
$host = "localhost";
$conn = new mysqli($host, $user, $passwork, $nameDatabase, 3307);
if ($conn->connect_errno) {
    die("Error connect Database" . $conn->connect_errno);
}
