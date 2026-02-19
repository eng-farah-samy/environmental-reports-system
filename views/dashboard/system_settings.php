<!-- views/dashboard/system_settings.php -->
<?php 
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['gm']);

if ($_POST) {
    $table = $_POST['table'] ?? '';
    $name  = clean($_POST['name'] ?? '');
    $cat_id = (int)($_POST['cat_id'] ?? 0);

    if ($name != '') {
        if ($table == 'categories') {
            $conn->query("INSERT INTO categories (name) VALUES ('$name')");
        } elseif ($table == 'subcategories' && $cat_id > 0) {
            $conn->query("INSERT INTO subcategories (category_id, name) VALUES ($cat_id, '$name')");
        } elseif ($table == 'sources') {
            $conn->query("INSERT INTO sources (name) VALUES ('$name')");
        } elseif ($table == 'receipt_methods') {
            $conn->query("INSERT INTO receipt_methods (name) VALUES ('$name')");
        }
    }
    redirect('system_settings.php');
}

if (isset($_GET['del'])) {
    $id = (int)$_GET['del'];
    $type = $_GET['type'];
    if ($type == 'cat') $conn->query("DELETE FROM categories WHERE id = $id");
    if ($type == 'sub') $conn->query("DELETE FROM subcategories WHERE id = $id");
    if ($type == 'src') $conn->query("DELETE FROM sources WHERE id = $id");
    if ($type == 'met') $conn->query("DELETE FROM receipt_methods WHERE id = $id");
    redirect('system_settings.php');
}
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<div class="text-white py-5 mb-5 rounded-4 shadow-lg" style="background: linear-gradient(135deg, #1b5e20, #2e7d32, #388e3c);">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">إدارة إعدادات النظام</h1>
        <p class="lead opacity-90">التحكم في التصنيفات والقوائم المستخدمة في تسجيل البلاغات</p>
    </div>
</div>

<div class="container">
    <div class="row g-4">

        <!-- التصنيف الأساسي -->
        <div class="col-lg-6">
            <div class="card border-0 shadow">
                <div class="card-header text-white" style="background:#1b5e20;">
                    <h5 class="mb-0">التصنيف الأساسي</h5>
                </div>
                <div class="card-body">
                    <form method="post" class="input-group mb-3">
                        <input type="hidden" name="table" value="categories">
                        <input type="text" name="name" class="form-control" placeholder="اسم التصنيف الجديد" required>
                        <button class="btn btn-success">إضافة</button>
                    </form>
                    <ul class="list-group">
                        <?php foreach($conn->query("SELECT * FROM categories ORDER BY name") as $c): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($c['name']) ?>
                            <a href="?del=<?= $c['id'] ?>&type=cat" class="text-danger" onclick="return confirm('تأكيد الحذف؟')">
                                حذف
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- التصنيف الفرعي -->
        <div class="col-lg-6">
            <div class="card border-0 shadow">
                <div class="card-header text-white" style="background:#1b5e20;">
                    <h5 class="mb-0">التصنيف الفرعي</h5>
                </div>
                <div class="card-body">
                    <form method="post" class="row g-2 mb-3">
                        <input type="hidden" name="table" value="subcategories">
                        <div class="col-md-7">
                            <input type="text" name="name" class="form-control" placeholder="اسم التصنيف الفرعي" required>
                        </div>
                        <div class="col-md-3">
                            <select name="cat_id" class="form-select" required>
                                <option value="">اختر التصنيف الأساسي</option>
                                <?php foreach($conn->query("SELECT * FROM categories") as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success w-100">إضافة</button>
                        </div>
                    </form>
                    <ul class="list-group">
                        <?php foreach($conn->query("SELECT s.*, c.name as cat FROM subcategories s LEFT JOIN categories c ON s.category_id=c.id ORDER BY c.name, s.name") as $s): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($s['cat']) ?></strong> → <?= htmlspecialchars($s['name']) ?>
                            </div>
                            <a href="?del=<?= $s['id'] ?>&type=sub" class="text-danger" onclick="return confirm('تأكيد الحذف؟')">
                                حذف
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- مصدر التلوث -->
        <div class="col-lg-6">
            <div class="card border-0 shadow">
                <div class="card-header text-white" style="background:#1b5e20;">
                    <h5 class="mb-0">مصدر التلوث / الحادث</h5>
                </div>
                <div class="card-body">
                    <form method="post" class="input-group mb-3">
                        <input type="hidden" name="table" value="sources">
                        <input type="text" name="name" class="form-control" placeholder="مثال: مصنع" required>
                        <button class="btn btn-success">إضافة</button>
                    </form>
                    <ul class="list-group">
                        <?php foreach($conn->query("SELECT * FROM sources ORDER BY name") as $s): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($s['name']) ?>
                            <a href="?del=<?= $s['id'] ?>&type=src" class="text-danger" onclick="return confirm('تأكيد الحذف؟')">
                                حذف
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- وسيلة الاستلام -->
        <div class="col-lg-6">
            <div class="card border-0 shadow">
                <div class="card-header text-white" style="background:#1b5e20;">
                    <h5 class="mb-0">وسيلة الاستلام</h5>
                </div>
                <div class="card-body">
                    <form method="post" class="input-group mb-3">
                        <input type="hidden" name="table" value="receipt_methods">
                        <input type="text" name="name" class="form-control" placeholder="مثال: رقم 988" required>
                        <button class="btn btn-success">إضافة</button>
                    </form>
                    <ul class="list-group">
                        <?php foreach($conn->query("SELECT * FROM receipt_methods ORDER BY name") as $m): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($m['name']) ?>
                            <a href="?del=<?= $m['id'] ?>&type=met" class="text-danger" onclick="return confirm('تأكيد الحذف؟')">
                                حذف
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

