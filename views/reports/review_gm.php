<!-- views/reports/review_gm.php -->
<?php 
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['gm']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "بلاغ غير صحيح";
    redirect('../dashboard/gm.php');
}

$report_id = (int)$_GET['id'];

// جلب كل البيانات مرة واحدة
$sql = "SELECT r.*, u.full_name as creator_name 
        FROM reports r 
        LEFT JOIN users u ON r.created_by = u.id 
        WHERE r.id = ? AND r.status = 'pending_gm'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();

if (!$report) {
    $_SESSION['error'] = "البلاغ غير موجود أو تمت معالجته مسبقًا";
    redirect('../dashboard/gm.php');
}
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<div class="container my-5">
    <!-- عنوان البلاغ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="text-primary mb-2">
                        <i class="bi bi-file-earmark-check-fill"></i>
                        بلاغ رقم #<?= str_pad($report['id'], 5, '0', STR_PAD_LEFT) ?>
                    </h2>
                    <p class="text-muted mb-0 fs-5">
                        <strong>العنوان:</strong> <?= htmlspecialchars($report['title']) ?>  
                        <span class="mx-3">|</span>
                        <strong>مسجل بواسطة:</strong> <?= htmlspecialchars($report['creator_name']) ?>
                        <span class="mx-3">|</span>
                        <strong>التاريخ:</strong> <?= date('d/m/Y H:i', strtotime($report['created_at'])) ?>
                    </p>
                </div>
                <span class="badge badge-priority-<?= $report['priority'] ?> fs-3 px-4 py-3">
                    <?= ['low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية','urgent'=>'عاجلة'][$report['priority']] ?>
                </span>
            </div>
            <hr class="border-primary border-3">
        </div>
    </div>

    <div class="row g-5">
        <!-- كل التفاصيل -->
        <div class="col-lg-8">

            <!-- 1. وصف البلاغ الأصلي -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">وصف البلاغ (موظف 988)</h5>
                </div>
                <div class="card-body bg-light">
                    <p class="lead"><?= nl2br(htmlspecialchars($report['description'])) ?></p>
                </div>
            </div>

            <!-- 2. رد إدارة الطوارئ -->
            <?php if ($report['em_notes'] || $report['em_actions'] || $report['em_reject_reason']): ?>
            <div class="card shadow border-0 mb-4 border-start border-success border-5">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">رد إدارة الطوارئ البيئية</h5>
                    <span class="badge bg-<?= $report['em_decision']=='approve'?'success':'danger' ?> fs-6">
                        <?= $report['em_decision']=='approve' ? 'تمت الموافقة' : 'تم الرفض' ?>
                    </span>
                </div>
                <div class="card-body">

                    <?php if ($report['em_notes']): ?>
                    <div class="mb-3">
                        <strong class="text-success">الملاحظات:</strong>
                        <div class="bg-white p-3 rounded border-start border-success border-4 mt-2">
                            <?= nl2br(htmlspecialchars($report['em_notes'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($report['em_actions']): ?>
                    <div class="mb-3">
                        <strong class="text-primary">الإجراءات المتخذة:</strong>
                        <div class="bg-white p-3 rounded border-start border-primary border-4 mt-2">
                            <?= nl2br(htmlspecialchars($report['em_actions'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($report['em_reject_reason']): ?>
                    <div class="mb-3">
                        <strong class="text-danger">سبب الرفض:</strong>
                        <div class="bg-white p-3 rounded border-start border-danger border-4 mt-2">
                            <?= nl2br(htmlspecialchars($report['em_reject_reason'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php endif; ?>

            <!-- 3. رد مدير الطوارئ -->
            <?php if ($report['em_manager_notes'] || $report['em_manager_reject_reason']): ?>
            <div class="card shadow border-0 mb-4 border-start border-warning border-5">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">تقييم مدير الطوارئ البيئية</h5>
                </div>
                <div class="card-body">

                    <?php if ($report['em_manager_notes']): ?>
                    <div class="mb-3">
                        <strong>الملاحظات:</strong>
                        <div class="bg-white p-3 rounded border-start border-warning border-4 mt-2">
                            <?= nl2br(htmlspecialchars($report['em_manager_notes'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($report['em_manager_reject_reason']): ?>
                    <div class="mb-3">
                        <strong class="text-danger">سبب الرفض النهائي:</strong>
                        <div class="bg-white p-3 rounded border-start border-danger border-4 mt-2">
                            <?= nl2br(htmlspecialchars($report['em_manager_reject_reason'])) ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php endif; ?>

            <!-- 4. بيانات مقدم البلاغ -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">بيانات مقدم البلاغ</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><strong>الاسم:</strong> <?= $report['reporter_name'] ?: '<em>غير محدد</em>' ?></div>
                        <div class="col-md-6"><strong>الجوال:</strong> <?= $report['reporter_phone'] ?: '<em>غير محدد</em>' ?></div>
                        <div class="col-md-6"><strong>رقم الهوية:</strong> <?= $report['reporter_id'] ?: '<em>غير محدد</em>' ?></div>
                        <div class="col-md-6"><strong>وسيلة الاستلام:</strong> <span class="badge bg-light text-dark"><?= strtoupper($report['receipt_method']) ?></span></div>
                    </div>
                </div>
            </div>

            <!-- 5. الموقع + خريطة -->
            <?php if ($report['location_name'] || ($report['latitude'] && $report['longitude'])): ?>
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">الموقع</h5>
                </div>
                <div class="card-body">
                    <p><strong>اسم الموقع:</strong> <?= htmlspecialchars($report['location_name'] ?? 'غير محدد') ?></p>
                    <?php if ($report['latitude'] && $report['longitude']): ?>
                        <a href="https://maps.google.com/?q=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>" 
                           target="_blank" class="btn btn-dark btn-sm mb-3">
                            فتح في خرائط جوجل
                        </a>
                        <div class="ratio ratio-16x9 rounded overflow-hidden shadow">
                            <iframe src="https://www.google.com/maps/embed/v1/view?key=AIzaSyDb9gJ8tI9Z9Z9Z9Z9Z9Z9Z9Z9Z9Z9Z9Z&center=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>&zoom=16" 
                                    allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- 6. المرفقات -->
            <?php
            $attachments = $conn->query("SELECT * FROM attachments WHERE report_id = $report_id");
            if ($attachments->num_rows > 0):
            ?>
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">المرفقات (<?= $attachments->num_rows ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <?php while($a = $attachments->fetch_assoc()): ?>
                        <div class="col-md-4 col-6">
                            <a href="../../assets/uploads/<?= $a['file_path'] ?>" target="_blank" class="text-decoration-none">
                                <?php if ($a['file_type'] == 'image'): ?>
                                    <img src="../../assets/uploads/<?= $a['file_path'] ?>" class="img-fluid rounded shadow" style="height:200px; object-fit:cover;">
                                <?php else: ?>
                                    <div class="bg-secondary bg-opacity-10 border rounded text-center p-5">
                                        <i class="bi bi-file-earmark-<?= $a['file_type']=='pdf'?'pdf-fill text-danger':'text-primary' ?> display-1"></i>
                                        <p class="mt-3 small text-muted"><?= basename($a['file_path']) ?></p>
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

        <!-- 7. نموذج الاعتماد النهائي (ثابت على اليمين) -->
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 sticky-top" style="top: 100px;">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">القرار النهائي للمدير العام</h4>
                </div>
                <div class="card-body p-4">
                    <form action="../../controllers/reports/review_gm.php" method="POST">
                        <input type="hidden" name="report_id" value="<?= $report_id ?>">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary">القرار</label>
                            <select name="final_decision" class="form-select form-select-lg" required>
                                <option value="approved">اعتماد البلاغ رسميًا</option>
                                <option value="rejected">رفض نهائي</option>
                                <option value="closed">إغلاق البلاغ (تم الحل ميدانيًا)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary">ملاحظات المدير العام</label>
                            <textarea name="gm_notes" class="form-control" rows="6" placeholder="اكتب تعليماتك أو ملاحظاتك النهائية..." required></textarea>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check2-circle"></i> تنفيذ القرار النهائي
                            </button>
                            <a href="../dashboard/gm.php" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> رجوع للوحة التحكم
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

