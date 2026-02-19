<?php 
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['data_entry']);
$user_id = $_SESSION['user_id'];
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<!-- Hero Header -->
<div class="bg-success text-white py-5 rounded-4 shadow-lg mb-5" style="background: linear-gradient(135deg, #013220 0%, #015933 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-2">
                    <i class="bi bi-person-badge-fill"></i> مرحبًا، <?= get_user_name() ?>
                </h1>
                <p class="lead mb-0">موظف تسجيل البلاغات - رقم 988</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="../reports/create.php" class="btn btn-light btn-lg px-5 shadow">
                    <i class="bi bi-plus-circle-fill"></i> بلاغ جديد
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- إحصائيات سريعة -->
    <div class="row g-4 mb-5">
        <?php
        // كل الاستعلامات دلوقتي شغالة 100% لأن $user_id معرّف
        $total      = $conn->query("SELECT COUNT(*) FROM reports WHERE created_by = $user_id")->fetch_row()[0];
        $pending    = $conn->query("SELECT COUNT(*) FROM reports WHERE created_by = $user_id AND status = 'pending_em'")->fetch_row()[0];
        $approved   = $conn->query("SELECT COUNT(*) FROM reports WHERE created_by = $user_id AND status = 'approved'")->fetch_row()[0];
        $urgent     = $conn->query("SELECT COUNT(*) FROM reports WHERE created_by = $user_id AND priority = 'urgent'")->fetch_row()[0];
        ?>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 text-white" style="background: #013220;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-file-earmark-text display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $total ?></h3>
                    <p class="mb-0">إجمالي البلاغات</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 text-white" style="background: #fd7e14;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-hourglass-split display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $pending ?></h3>
                    <p class="mb-0">في انتظار المراجعة</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 text-white" style="background: #198754;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-check2-circle display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $approved ?></h3>
                    <p class="mb-0">تم اعتمادها</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 text-white" style="background: #dc3545;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-exclamation-triangle-fill display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $urgent ?></h3>
                    <p class="mb-0">عاجلة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- آخر البلاغات -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-list-check"></i> آخر البلاغات التي سجلتها</h4>
        </div>
        <div class="card-body p-0">
            <?php
            $sql = "SELECT id, title, priority, status, created_at, gregorian_date FROM reports WHERE created_by = ? ORDER BY created_at DESC LIMIT 15";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark" style="background:#013220;">
                        <tr>
                            <th>رقم البلاغ</th>
                            <th>العنوان</th>
                            <th>التاريخ</th>
                            <th>الأولوية</th>
                            <th>الحالة</th>
                            <th>إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold">#<?= str_pad($r['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars(mb_substr($r['title'], 0, 50)) ?><?= mb_strlen($r['title']) > 50 ? '...' : '' ?></td>
                            <td><?= date('d/m/Y', strtotime($r['gregorian_date'])) ?></td>
                            <td>
                                <span class="badge badge-priority-<?= $r['priority'] ?> fs-6">
                                    <?= ['low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية','urgent'=>'عاجلة'][$r['priority']] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge status-<?= $r['status'] ?> fs-6">
                                    <?= ['pending_em'=>'في انتظار الطوارئ','pending_em_manager'=>'في انتظار مدير الطوارئ','pending_gm'=>'في انتظار المدير العام','approved'=>'معتمد','rejected'=>'مرفوض','closed'=>'مغلق'][$r['status']] ?? $r['status'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="../reports/view.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-eye-fill"></i> عرض
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <p class="text-muted mt-3">لا توجد بلاغات مسجلة حتى الآن</p>
                <a href="../reports/create.php" class="btn btn-success btn-lg mt-3">
                    <i class="bi bi-plus-circle"></i> إنشاء أول بلاغ
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

