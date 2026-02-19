<?php
// config/db.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '8801');
define('DB_NAME', 'environmental_reports');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>