<?php
// controllers/login.php
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['success'] = "مرحباً بعودتك، " . $user['full_name'];
            redirect('../views/dashboard/' . $user['role'] . '.php');
        }
    }
    
    $_SESSION['error'] = "اسم المستخدم أو كلمة المرور غير صحيحة";
    redirect('../login.php');
}
?>