<?php
require_once 'env_loader.php';

$host = getenv('DB_HOST');      
$dbname = getenv('DB_NAME');     
$username = getenv('DB_USER');       
$password = getenv('DB_PASS');

// Tạo kết nối
$conn = new mysqli($host, $username, $password, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4'); // Thiết lập UTF-8 để tránh lỗi tiếng Việt
?>
