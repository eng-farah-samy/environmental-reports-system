<!-- views/reports/review_em.php -->
<?php 
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['emergency']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "بلاغ غير صحيح";
    redirect('../dashboard/emergency.php');
}

$report_id = (int)$_GET['id'];

// جلب كل تفاصيل البلاغ + اسم موظف 988
$sql = "SELECT r.*, u.full_name as creator_name 
        FROM reports r 
        LEFT JOIN users u ON r.created_by = u.id 
        WHERE r.id = ? AND r.status = 'pending_em'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();

$report = $stmt->get_result()->fetch_assoc();

if (!$report) {
    $_SESSION['error'] = "البلاغ غير موجود أو تمت مراجعته مسبقًا";
    redirect('../dashboard/emergency.php');
}
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<div class="container my-5">
    <div class="row">
        <!-- عنوان البلاغ + معلومات أساسية -->
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="text-success mb-2">
                        <i class="bi bi-file-earmark-text"></i> 
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
            <hr class="border-success">
        </div>
    </div>

    <div class="row g-5">
        <!-- التفاصيل الكاملة -->
        <div class="col-lg-8">
            <!-- الوصف -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-chat-square-text"></i> وصف البلاغ</h5>
                </div>
                <div class="card-body">
                    <p class="lead"><?= nl2br(htmlspecialchars($report['description'])) ?></p>
                </div>
            </div>

            <!-- بيانات مقدم البلاغ -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-person-vcard"></i> بيانات مقدم البلاغ</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>الاسم:</strong> <?= $report['reporter_name'] ? htmlspecialchars($report['reporter_name']) : '<span class="text-muted">غير محدد</span>' ?></p>
                            <p><strong>رقم الجوال:</strong> <?= $report['reporter_phone'] ?: '<span class="text-muted">غير محدد</span>' ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>رقم الهوية:</strong> <?= $report['reporter_id'] ?: '<span class="text-muted">غير محدد</span>' ?></p>
                            <p><strong>وسيلة الاستلام:</strong> <span class="badge bg-info"><?= strtoupper($report['receipt_method']) ?></span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الموقع -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt-fill"></i> الموقع</h5>
                </div>
                <div class="card-body">
                    <p><strong>اسم الموقع:</strong> <?= $report['location_name'] ? htmlspecialchars($report['location_name']) : '<span class="text-muted">غير محدد</span>' ?></p>
                    <?php if ($report['location_details']): ?>
                        <p><strong>التفاصيل:</strong> <?= htmlspecialchars($report['location_details']) ?></p>
                    <?php endif; ?>
                    <?php if ($report['latitude'] && $report['longitude']): ?>
                        <p>
                            <strong>الإحداثيات:</strong> 
                            <?= $report['latitude'] ?>, <?= $report['longitude'] ?>
                            <a href="https://www.google.com/maps?q=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>" 
                               target="_blank" class="btn btn-sm btn-outline-success ms-3">
                                <i class="bi bi-map"></i> فتح في خرائط جوجل
                            </a>
                        </p>
                        <div class="mt-3 ratio ratio-16x9">
                            <iframe src="https://www.google.com/maps/embed/v1/view?key=AIzaSyDb9gJ8tI9Z9Z9Z9Z9Z9Z9Z9Z9Z9Z9Z9Z9&center=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>&zoom=15" 
                                    allowfullscreen="" loading="lazy" class="rounded"></iframe>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">لا توجد إحداثيات</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- تفاصيل إضافية -->
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> تفاصيل إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>التصنيف:</strong> <?= ucfirst($report['category']) ?> <?= $report['subcategory'] ? ' - ' . htmlspecialchars($report['subcategory']) : '' ?></p>
                            <p><strong>وقت المشاهدة:</strong> <?= $report['observation_time'] == 'morning' ? 'صباحًا' : 'مساءً' ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>مصدر التلوث:</strong> <?= $report['source_type'] ? htmlspecialchars($report['source_type']) : 'غير محدد' ?></p>
                            <p><strong>اسم الجهة:</strong> <?= $report['source_name'] ? htmlspecialchars($report['source_name']) : 'غير محدد' ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- المرفقات -->
            <?php
            $attachments = $conn->query("SELECT * FROM attachments WHERE report_id = $report_id");
            if ($attachments->num_rows > 0):
            ?>
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-paperclip"></i> المرفقات (<?= $attachments->num_rows ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php while($a = $attachments->fetch_assoc()): ?>
                        <div class="col-md-4 col-sm-6">
                            <a href="../../assets/uploads/<?= $a['file_path'] ?>" target="_blank" class="text-decoration-none">
                                <?php if ($a['file_type'] == 'image'): ?>
                                    <img src="../../assets/uploads/<?= $a['file_path'] ?>" class="img-fluid rounded shadow" style="height:180px; object-fit:cover;">
                                <?php elseif ($a['file_type'] == 'video'): ?>
                                    <div class="bg-dark rounded text-center p-4 text-white">
                                        <i class="bi bi-play-circle-fill display-4"></i>
                                        <p class="mt-2">فيديو</p>
                                    </div>
                                <?php else: ?>
                                    <div class="bg-light border rounded text-center p-4">
                                        <i class="bi bi-file-earmark-<?= $a['file_type'] == 'pdf' ? 'pdf-fill text-danger' : 'word-fill text-primary' ?> display-4"></i>
                                        <p class="mt-2 small"><?= $a['file_path'] ?></p>
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

        <!-- نموذج اتخاذ القرار (ثابت على اليمين) -->
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 sticky-top" style="top: 100px;">
                <div class="card-header bg-success text-white text-center">
                    <h5 class="mb-0"><i class="bi bi-check2-circle"></i> اتخاذ الإجراء</h5>
                </div>
                <div class="card-body">
                    <form action="../../controllers/reports/review_em.php" method="POST">
                        <input type="hidden" name="report_id" value="<?= $report_id ?>">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-success">القرار</label>
                            <select name="decision" class="form-select form-select-lg" required>
                                <option value="approve">موافقة وتمرير لمدير الطوارئ</option>
                                <option value="reject">رفض البلاغ</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-success">ملاحظاتك</label>
                            <textarea name="em_notes" class="form-control" rows="6" placeholder="اكتب تقييمك أو الإجراءات المقترحة..." required></textarea>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-send-check"></i> تأكيد الإجراء
                            </button>
                            <a href="../dashboard/emergency.php" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> رجوع للقائمة
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

