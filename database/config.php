<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "baopr";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Bắt đầu phiên làm việc (session)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
