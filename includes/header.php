<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>نظام إدارة البلاغات البيئية - مكة المكرمة</title>

    <!-- Bootstrap 5 RTL + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?= time() ?>">
</head>
<body class="bg-light">

<!-- Navbar ثابتة وأنيقة -->
<?php if (is_logged_in()): ?>
<nav class="navbar navbar-expand-lg navbar-dark shadow" style="background: linear-gradient(90deg, #013220 0%, #015933 100%);">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-tree-fill"></i> البلاغات البيئية
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="views/dashboard/<?= $_SESSION['role'] ?>.php">
                        <i class="bi bi-speedometer2"></i> لوحة التحكم
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center text-white">
                <span class="me-3"><i class="bi bi-person-circle"></i> <?= get_user_name() ?></span>
                <a href="/environmental-reports-system/controllers/logout.php" class="btn btn-outline-light btn-sm">تسجيل خروج</a>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="container mt-4">
    <?php flash_message(); ?>