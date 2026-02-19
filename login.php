<!-- login.php -->
<?php require 'C:\xampp\htdocs\environmental-reports-system/includes/functions.php'; 
if (is_logged_in()) redirect('views/dashboard/' . $_SESSION['role'] . '.php');
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<div class="row justify-content-center min-vh-100 align-items-center">
    <div class="col-xxl-4 col-xl-5 col-lg-6 col-md-7 col-sm-9">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <!-- الخلفية الخضراء العلوية -->
            <div class="bg-success text-white text-center py-5" style="background: linear-gradient(135deg, #013220 0%, #015933 100%);">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="bi bi-tree-fill"></i> نظام البلاغات البيئية
                </h1>
                <p class="lead mb-0">فرع مكة المكرمة</p>
            </div>

            <div class="card-body p-5">
                <h3 class="text-center mb-4" style="color:#013220;">تسجيل الدخول</h3>

                <?php flash_message(); ?>

                <form action="controllers/login.php" method="POST" class="needs-validation" novalidate>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">اسم المستخدم</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" class="form-control form-control-lg" required autofocus>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">كلمة المرور</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control form-control-lg" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">
                        <i class="bi bi-box-arrow-in-right"></i> دخول
                    </button>
                </form>

                
            </div>
        </div>
    </div>
</div>

