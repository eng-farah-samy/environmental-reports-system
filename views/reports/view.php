<!-- views/reports/view.php -->
<?php 
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';


if (!isset($_GET['id'])) redirect('../dashboard/' . $_SESSION['role'] . '.php');
$report_id = (int)$_GET['id'];

$report = $conn->query("SELECT r.*, u.full_name as creator FROM reports r LEFT JOIN users u ON r.created_by = u.id WHERE r.id = $report_id")->fetch_assoc();
if (!$report) {
    $_SESSION['error'] = "البلاغ غير موجود";
    redirect('../dashboard/' . $_SESSION['role'] . '.php');
}
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<div class="text-white py-5 mb-5 rounded-4 shadow-lg" style="background: linear-gradient(135deg, #1b5e20, #2e7d32, #388e3c);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="display-5 fw-bold mb-2">#<?= str_pad($report['id'],5,'0',STR_PAD_LEFT) ?> - <?= htmlspecialchars($report['title']) ?></h1>
                <p class="mb-0 opacity-90">
                    الحالة: 
                    <span class="badge bg-<?= $report['status']=='approved'?'success':($report['status']=='rejected'?'danger':($report['status']=='closed'?'secondary':'warning')) ?> fs-5">
                        <?= $report['status']=='pending_gm'?'في انتظار قرار المدير العام':($report['status']=='approved'?'معتمد':($report['status']=='rejected'?'مرفوض':'مغلق')) ?>
                    </span>
                </p>
            </div>
            <div class="col-auto">
                <a href="javascript:history.back()" class="btn btn-light btn-lg">
                    رجوع
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-header text-white" style="background:#1b5e20;">
                    <h5 class="mb-0">تفاصيل البلاغ</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6"><strong>التصنيف الأساسي:</strong> <?= htmlspecialchars($report['category'] ?? 'غير محدد') ?></div>
                        <div class="col-md-6"><strong>التصنيف الفرعي:</strong> <?= htmlspecialchars($report['subcategory'] ?? 'غير محدد') ?></div>
                        <div class="col-md-6"><strong>مصدر التلوث:</strong> <?= htmlspecialchars($report['source_type'] ?? 'غير محدد') ?></div>
                        <div class="col-md-6"><strong>اسم الجهة:</strong> <?= htmlspecialchars($report['source_name'] ?? 'غير محدد') ?></div>
                        <div class="col-12"><strong>الوصف:</strong><br><p class="mt-2"><?= nl2br(htmlspecialchars($report['description'])) ?></p></div>
                        <div class="col-md-6"><strong>التاريخ الهجري:</strong> <span class="text-success fs-5"><?= htmlspecialchars($report['hijri_date']) ?></span></div>
                        <div class="col-md-6"><strong>التاريخ الميلادي:</strong> <?= date('d/m/Y', strtotime($report['gregorian_date'])) ?></div>
                        <div class="col-md-6"><strong>وسيلة الاستلام:</strong> <?= htmlspecialchars($report['receipt_method']) ?></div>
                        <div class="col-md-6"><strong>الأولوية:</strong> 
                            <span class="badge badge-priority-<?= $report['priority'] ?>">
                                <?= ['low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية','urgent'=>'عاجلة'][$report['priority']] ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بيانات الموقع -->
            <div class="card border-0 shadow-lg mt-4">
                <div class="card-header text-white" style="background:#1b5e20;">
                    <h5 class="mb-0">موقع البلاغ</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><strong>اسم الموقع:</strong> <?= htmlspecialchars($report['location_name']) ?></div>
                        <div class="col-md-6"><strong>التفاصيل:</strong> <?= htmlspecialchars($report['location_details'] ?? 'غير محدد') ?></div>
                        <?php if ($report['latitude'] && $report['longitude']): ?>
                        <div class="col-12 mt-3">
                            <iframe width="100%" height="300" style="border:0" loading="lazy" 
                                    src="https://www.google.com/maps/embed/v1/view?key=AIzaSyD0F7lY8z7Z7Z7Z7Z7Z7Z7Z7Z7Z7Z7Z7Z7&center=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>&zoom=15">
                            </iframe>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- بيانات مقدم البلاغ -->
            <div class="card border-0 shadow-lg">
                <div class="card-header text-white" style="background:#1b5e20;">
                    <h5 class="mb-0">مقدم البلاغ</h5>
                </div>
                <div class="card-body">
                    <p><strong>الاسم:</strong> <?= htmlspecialchars($report['reporter_name'] ?? 'غير محدد') ?></p>
                    <p><strong>الجوال:</strong> <?= htmlspecialchars($report['reporter_phone'] ?? 'غير محدد') ?></p>
                    <p><strong>الهوية:</strong> <?= htmlspecialchars($report['reporter_id'] ?? 'غير محدد') ?></p>
                </div>
            </div>

            <!-- المرفقات -->
            <?php if (!empty($report['attachments'])): 
                $files = json_decode($report['attachments'], true);
            ?>
            <div class="card border-0 shadow-lg mt-4">
                <div class="card-header text-white" style="background:#1b5e20;">
                    <h5 class="mb-0">المرفقات (<?= count($files) ?>)</h5>
                </div>
                <div class="card-body p-2">
                    <?php foreach($files as $f): ?>
                    <a href="../../uploads/<?= basename($f) ?>" target="_blank" class="d-block mb-2">
                        <img src="../../uploads/<?= basename($f) ?>" class="img-thumbnail" style="max-height:150px;">
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

