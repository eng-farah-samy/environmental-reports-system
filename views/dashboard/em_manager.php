<!-- views/dashboard/em_manager.php -->
<?php 
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['em_manager']);
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<!-- Hero Header -->
<div class="bg-success text-white py-5 rounded-4 shadow-lg mb-5" style="background: linear-gradient(135deg, #013220 0%, #015933 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-2">
                    <i class="bi bi-person-fill-gear"></i> مدير إدارة الطوارئ البيئية
                </h1>
                <p class="lead mb-0">المراجعة النهائية قبل رفع البلاغ للمدير العام</p>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="badge bg-warning text-dark fs-5 px-4 py-3">
                    <i class="bi bi-exclamation-triangle-fill"></i> 
                    <?= $conn->query("SELECT COUNT(*) FROM reports WHERE status = 'pending_em_manager'")->fetch_row()[0] ?> بلاغ في انتظارك
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- إحصائيات -->
    <div class="row g-4 mb-5">
        <?php
        $pending = $conn->query("SELECT COUNT(*) FROM reports WHERE status = 'pending_em_manager'")->fetch_row()[0];
        $urgent = $conn->query("SELECT COUNT(*) FROM reports WHERE status = 'pending_em_manager' AND priority = 'urgent'")->fetch_row()[0];
        $today  = $conn->query("SELECT COUNT(*) FROM reports WHERE status = 'pending_em_manager' AND DATE(created_at) = CURDATE()")->fetch_row()[0];
        ?>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-white" style="background: #013220;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-hourglass-split display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $pending ?></h3>
                    <p>في انتظار اعتمادك</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-white" style="background: #dc3545;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-fire display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $urgent ?></h3>
                    <p>عاجلة</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-white" style="background: #fd7e14;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-calendar-check display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $today ?></h3>
                    <p>واردة اليوم</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-white" style="background: #198754;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-check2-all display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $conn->query("SELECT COUNT(*) FROM reports WHERE em_manager_decision IS NOT NULL")->fetch_row()[0] ?></h3>
                    <p>تمت مراجعتها</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول البلاغات -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="bi bi-list-check"></i> البلاغات في انتظار اعتماد مدير الطوارئ</h4>
        </div>
        <div class="card-body p-0">
            <?php
            $sql = "SELECT r.*, u.full_name as creator_name, e.full_name as em_name
                    FROM reports r
                    LEFT JOIN users u ON r.created_by = u.id
                    LEFT JOIN users e ON r.em_decision IS NOT NULL AND e.id = (SELECT uploaded_by FROM attachments WHERE report_id = r.id LIMIT 1)
                    WHERE r.status = 'pending_em_manager'
                    ORDER BY FIELD(r.priority, 'urgent', 'high', 'medium', 'low'), r.created_at DESC";
            $result = $conn->query($sql);
            ?>

            <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark" style="background:#013220;">
                        <tr>
                            <th>رقم</th>
                            <th>العنوان</th>
                            <th>مقدم البلاغ</th>
                            <th>إدارة الطوارئ</th>
                            <th>الأولوية</th>
                            <th>التاريخ</th>
                            <th>إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = $result->fetch_assoc()): ?>
                        <tr class="<?= $r['priority'] == 'urgent' ? 'table-danger' : '' ?>">
                            <td class="fw-bold">#<?= str_pad($r['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <strong><?= htmlspecialchars($r['title']) ?></strong>
                                <br><small class="text-muted"><?= mb_substr($r['description'], 0, 80) ?>...</small>
                            </td>
                            <td><small><?= $r['reporter_name'] ?: 'غير محدد' ?></small></td>
                            <td><small><?= $r['em_name'] ?? 'غير محدد' ?></small></td>
                            <td>
                                <span class="badge badge-priority-<?= $r['priority'] ?>">
                                    <?= ['low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية','urgent'=>'عاجلة'][$r['priority']] ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                            <td>
                                <a href="../reports/review_em_manager.php?id=<?= $r['id'] ?>" class="btn btn-warning btn-sm text-white">
                                    <i class="bi bi-eye-fill"></i> مراجعة
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-check-circle-fill display-1 text-success"></i>
                <h4 class="text-success mt-3">لا توجد بلاغات في انتظار اعتمادك حاليًا</h4>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

