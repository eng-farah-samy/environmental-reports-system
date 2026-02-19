<?php
// includes/functions.php
// منع إعادة تحميل الملف أكتر من مرة (الحل السحري للغلطة دي)
if (!defined('FUNCTIONS_LOADED')) {
    define('FUNCTIONS_LOADED', true);

    session_start();
    require 'C:\xampp\htdocs\environmental-reports-system\config\db.php';

    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }

    function redirect($url) {
        header("Location: $url");
        exit();
    }

    function check_role($allowed_roles = []) {
        if (!is_logged_in()) {
            redirect('../login.php');
        }
        if (!empty($allowed_roles) && !in_array($_SESSION['role'], $allowed_roles)) {
            $_SESSION['error'] = "غير مصرح لك بالوصول إلى هذه الصفحة";
            redirect('../login.php');
        }
    }

    function get_user_name() {
        return $_SESSION['full_name'] ?? 'مستخدم';
    }

    function flash_message() {
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show">' . $_SESSION['success'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show">' . $_SESSION['error'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            unset($_SESSION['error']);
        }
    }

    function clean($data) {
        global $conn;
        return mysqli_real_escape_string($conn, trim($data));
    }

    function gregorian_to_hijri($date = null) {
        if (!$date) $date = date('Y-m-d');
        return "1447/" . date("m/d", strtotime($date));
    }
}
?>