<?php
// controllers/logout.php

// نستخدم required عشان لو الملف مش موجود يوقف النظام (أكثر أمانًا)
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';

// نلغي كل البيانات اللي في السيشن
$_SESSION = [];

// لو في كوكيز للسيشن بنمسحها كمان
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ننهي السيشن تمامًا
session_destroy();

// نرجع المستخدم لصفحة اللوجن مع رسالة نجاح
$_SESSION['success'] = "تم تسجيل الخروج بنجاح، إلى اللقاء!";
redirect('../login.php');
?>