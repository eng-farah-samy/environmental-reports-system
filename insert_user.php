<?php
// -----------------------------
// الاتصال بقاعدة البيانات
// -----------------------------
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '8801');
define('DB_NAME', 'environmental_reports');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// -----------------------------
// كلمة المرور الجديدة
// -----------------------------

$password_plain = "123456_gm@gm";
$password_hash = password_hash($password_plain, PASSWORD_BCRYPT);

// -----------------------------
// إدخال المستخدم 988
// -----------------------------

$sql = "INSERT INTO users (username, password, full_name, role)
        VALUES ('gm', '$password_hash', 'المدير العام', 'gm')";

if ($conn->query($sql) === TRUE) {
    echo "✔️ تم إنشاء المستخدم 988 بنجاح<br>";
    echo "🔐 الهاش المستخدم:<br>$password_hash";
} else {
    echo "❌ خطأ أثناء إنشاء المستخدم: " . $conn->error;
}

$conn->close();
?>
