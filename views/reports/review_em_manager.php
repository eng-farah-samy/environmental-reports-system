<!-- views/reports/review_em_manager.php -->
<?php 
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['em_manager']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "بلاغ غير صحيح";
    redirect('../dashboard/em_manager.php');
}

$report_id = (int)$_GET['id'];

// جلب البلاغ مع اسم موظف 988
$sql = "SELECT r.*, u.full_name as creator_name 
        FROM reports r 
        LEFT JOIN users u ON r.created_by = u.id 
        WHERE r.id = ? AND r.status = 'pending_em_manager'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();

if (!$report) {
    $_SESSION['error'] = "البلاغ غير موجود أو لم يُمرر من إدارة الطوارئ بعد";
    redirect('../dashboard/em_manager.php');
}
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<div class="container my-5">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="text-warning mb-2">
                        بلاغ رقم #<?= str_pad($report['id'], 5, '0', STR_PAD_LEFT) ?>
                    </h2>
                    <p class="text-muted mb-0">
                        <strong>العنوان:</strong> <?= htmlspecialchars($report['title']) ?>  
                        <span class="mx-3">|</span>
                        <strong>مسجل بواسطة:</strong> <?= htmlspecialchars($report['creator_name']) ?>
                        <span class="mx-3">|</span>
                        <strong>التاريخ:</strong> <?= date('d/m/Y H:i', strtotime($report['created_at'])) ?>
                    </p>
                </div>
                <span class="badge badge-priority-<?= $report['priority'] ?> fs-4 px-4 py-2">
                    <?= ['low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية','urgent'=>'عاجلة'][$report['priority']] ?>
                </span>
            </div>
            <hr class="border-warning border-3">
        </div>
    </div>

    <div class="row g-5">
        <!-- التفاصيل + رد إدارة الطوارئ -->
        <div class="col-lg-8">

            <!-- وصف البلاغ الأصلي -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">وصف البلاغ (موظف 988)</h5>
                </div>
                <div class="card-body">
                    <p class="lead"><?= nl2br(htmlspecialchars($report['description'])) ?></p>
                </div>
            </div>

            <!-- رد إدارة الطوارئ بالكامل -->
            <div class="card shadow border-0 mb-4 border-start border-success border-5">
                <div class="card-header bg-success text-white d-flex justify-content-between">
                    <h5 class="mb-0">
                        رد إدارة الطوارئ البيئية
                    </h5>
                    <span class="badge bg-<?= $report['em_decision'] == 'approve' ? 'success' : 'danger' ?> fs-6">
                        <?= $report['em_decision'] == 'approve' ? 'تمت الموافقة' : 'تم الرفض' ?>
                    </span>
                </div>
                <div class="card-body bg-light">

                    <?php if ($report['em_notes']): ?>
                    <div class="mb-4">
                        <strong class="text-success">الملاحظات:</strong>
                        <p class="mt-2 bg-white p-3 rounded border-start border-success border-4">
                            <?= nl2br(htmlspecialchars($report['em_notes'])) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if ($report['em_actions']): ?>
                    <div class="mb-4">
                        <strong class="text-success">الإجراءات المتخذة:</strong>
                        <p class="mt-2 bg-white p-3 rounded border-start border-primary border-4">
                            <?= nl2br(htmlspecialchars($report['em_actions'])) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if ($report['em_reject_reason']): ?>
                    <div class="mb-4">
                        <strong class="text-danger">سبب الرفض:</strong>
                        <p class="mt-2 bg-white p-3 rounded border-start border-danger border-4">
                            <?= nl2br(htmlspecialchars($report['em_reject_reason'])) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- باقي التفاصيل (موقع، مرفقات، إلخ) -->
            <?php if ($report['location_name'] || $report['latitude']): ?>
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">الموقع</h5>
                </div>
                <div class="card-body">
                    <p><strong>اسم الموقع:</strong> <?= htmlspecialchars($report['location_name'] ?? 'غير محدد') ?></p>
                    <?php if ($report['latitude'] && $report['longitude']): ?>
                    <a href="https://maps.google.com/?q=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>" 
                       target="_blank" class="btn btn-success btn-sm">
                        فتح في الخريطة
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- المرفقات -->
            <?php
            $atts = $conn->query("SELECT * FROM attachments WHERE report_id = $report_id");
            if ($atts->num_rows > 0):
            ?>
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">المرفقات (<?= $atts->num_rows ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php while($a = $atts->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <a href="../../assets/uploads/<?= $a['file_path'] ?>" target="_blank">
                                <?php if ($a['file_type'] == 'image'): ?>
                                    <img src="../../assets/uploads/<?= $a['file_path'] ?>" class="img-fluid rounded shadow" style="height:160px; object-fit:cover;">
                                <?php else: ?>
                                    <div class="bg-light border rounded text-center p-4">
                                        <i class="bi bi-file-earmark-<?= $a['file_type'] == 'pdf' ? 'pdf-fill text-danger' : 'text-primary' ?> display-4"></i>
                                        <small class="d-block mt-2"><?= basename($a['file_path']) ?></small>
                                    </div>
                                <?php endif; ?>
                            </a>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- نموذج قرار مدير الطوارئ -->
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 sticky-top" style="top: 100px;">
                <div class="card-header bg-warning text-dark text-center">
                    <h5 class="mb-0">القرار النهائي</h5>
                </div>
                <div class="card-body">
                    <form action="../../controllers/reports/review_em_manager.php" method="POST">
                        <input type="hidden" name="report_id" value="<?= $report_id ?>">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">القرار</label>
                            <select name="decision" class="form-select form-select-lg" required>
                                <option value="approve">موافقة وإرسال للمدير العام</option>
                                <option value="reject">رفض نهائي</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">ملاحظاتك</label>
                            <textarea name="notes" class="form-control" rows="6" placeholder="اكتب تقييمك النهائي..." required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-lg text-white">
                                تأكيد القرار
                            </button>
                            <a href="../dashboard/em_manager.php" class="btn btn-outline-secondary btn-lg mt-2">
                                رجوع
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

