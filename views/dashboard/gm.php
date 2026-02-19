<!-- views/dashboard/gm.php -->
<?php 
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['gm']);

// اسم المدير العام
$gm_name = !empty($_SESSION['user_name']) ? $_SESSION['user_name'] : (!empty($_SESSION['full_name']) ? $_SESSION['full_name'] : 'المدير العام');

// إحصائيات
$pending_gm = $conn->query("SELECT COUNT(*) FROM reports WHERE status = 'pending_gm'")->fetch_row()[0];
$completed  = $conn->query("SELECT COUNT(*) FROM reports WHERE status IN ('approved','rejected','closed')")->fetch_row()[0];
$total      = $conn->query("SELECT COUNT(*) FROM reports")->fetch_row()[0];
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<!-- Hero بالأخضر الغامق + زرين صغيرين أنيقين -->
<div class="text-white py-5 mb-5 rounded-4 shadow-lg" style="background: linear-gradient(135deg, #1b5e20, #2e7d32, #388e3c);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 text-center text-lg-start">
                <h1 class="display-5 fw-bold mb-2">
                    مرحبًا، <?= htmlspecialchars($gm_name) ?>
                </h1>
                <p class="lead mb-4 opacity-90">المدير العام للطوارئ البيئية</p>

                <!-- زرين صغيرين وأنيقين -->
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                    <a href="completed_reports.php" class="btn btn-light btn-sm px-4 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2">
                        <i class="bi bi-archive"></i>
                        البلاغات المنتهية <span class="badge bg-success ms-2"><?= $completed ?></span>
                    </a>

                    <a href="system_settings.php" class="btn btn-warning btn-sm px-4 py-2 rounded-pill shadow-sm d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-gear"></i>
                        إدارة إعدادات النظام
                    </a>
                </div>
            </div>

            <div class="col-lg-4 text-center mt-4 mt-lg-0">
                <?php if ($pending_gm > 0): ?>
                    <div class="alert alert-danger d-inline-block p-4 rounded-4 shadow">
                        <h3 class="mb-0 fw-bold"><?= $pending_gm ?></h3>
                        <p class="mb-0">بلاغ ينتظر قرارك</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success d-inline-block p-4 rounded-4 shadow">
                        <h5 class="mb-0">لا توجد بلاغات معلقة</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- البلاغات في انتظار القرار -->
    <div class="card border-0 shadow-lg">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #1b5e20;">
            <h4 class="mb-0">البلاغات في انتظار قرارك النهائي</h4>
            <span class="badge bg-light text-dark fs-5"><?= $pending_gm ?></span>
        </div>
        <div class="card-body p-0">
            <?php
            $sql = "SELECT r.id, r.title, r.priority, r.hijri_date, u.full_name as creator
                    FROM reports r 
                    LEFT JOIN users u ON r.created_by = u.id 
                    WHERE r.status = 'pending_gm'
                    ORDER BY FIELD(r.priority,'urgent','high','medium','low'), r.created_at DESC";
            $result = $conn->query($sql);
            ?>
            <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead style="background-color: #2e7d32; color: white;">
                        <tr>
                            <th class="text-white">رقم البلاغ</th>
                            <th class="text-white">العنوان</th>
                            <th class="text-white">الأولوية</th>
                            <th class="text-white">التاريخ الهجري</th>
                            <th class="text-white">مسجل بواسطة</th>
                            <th class="text-white"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = $result->fetch_assoc()): ?>
                        <tr class="<?= $r['priority']=='urgent' ? 'table-danger' : '' ?>">
                            <td class="fw-bold">#<?= str_pad($r['id'],5,'0',STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars(mb_substr($r['title'],0,60)) ?>...</td>
                            <td>
                                <span class="badge badge-priority-<?= $r['priority'] ?>">
                                    <?= ['low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية','urgent'=>'عاجلة'][$r['priority']] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($r['hijri_date']) ?></td>
                            <td class="small"><?= htmlspecialchars($r['creator'] ?? 'غير معروف') ?></td>
                            <td>
                                <a href="../reports/review_gm.php?id=<?= $r['id'] ?>" 
                                   class="btn btn-success btn-sm">مراجعة</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-6">
                <h4 class="text-success">لا توجد بلاغات في انتظار قرارك حاليًا</h4>
                <p class="text-muted">جميع البلاغات تمت معالجتها بنجاح</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

