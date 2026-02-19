<!-- views/dashboard/emergency.php -->
<?php 
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['emergency']);
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<!-- Hero Header -->
<div class="bg-success text-white py-5 rounded-4 shadow-lg mb-5" style="background: linear-gradient(135deg, #013220 0%, #015933 100%);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-2">
                    <i class="bi bi-truck"></i> إدارة الطوارئ البيئية
                </h1>
                <p class="lead mb-0">مراجعة واعتماد البلاغات الواردة من موظفي 988</p>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="badge bg-light text-dark fs-5 px-4 py-3">
                    <i class="bi bi-bell-fill"></i> 
                    <?= $conn->query("SELECT COUNT(*) FROM reports WHERE status = 'pending_em'")->fetch_row()[0] ?> بلاغ معلق
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- إحصائيات -->
    <div class="row g-4 mb-5">
        <?php
        $total_pending = $conn->query("SELECT COUNT(*) FROM reports WHERE status = 'pending_em'")->fetch_row()[0];
        $urgent_pending = $conn->query("SELECT COUNT(*) FROM reports WHERE status = 'pending_em' AND priority = 'urgent'")->fetch_row()[0];
        $today = $conn->query("SELECT COUNT(*) FROM reports WHERE status = 'pending_em' AND DATE(created_at) = CURDATE()")->fetch_row()[0];
        ?>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-white" style="background: #013220;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-inbox display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $total_pending ?></h3>
                    <p>في انتظار المراجعة</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-white" style="background: #dc3545;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-exclamation-triangle-fill display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $urgent_pending ?></h3>
                    <p>عاجلة</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-white" style="background: #fd7e14;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-calendar-day display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $today ?></h3>
                    <p>واردة اليوم</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm text-white" style="background: #198754;">
                <div class="card-body text-center py-4">
                    <i class="bi bi-check2-all display-4 mb-3"></i>
                    <h3 class="fw-bold mb-0"><?= $conn->query("SELECT COUNT(*) FROM reports WHERE em_decision IS NOT NULL")->fetch_row()[0] ?></h3>
                    <p>تم المراجعة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول البلاغات المعلقة مع كل التفاصيل -->
    <div class="card border-0 shadow-lg">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="bi bi-list-ul"></i> البلاغات في انتظار المراجعة (pending_em)</h4>
        </div>
        <div class="card-body p-0">
            <?php
            $sql = "SELECT r.*, u.full_name as creator_name 
                    FROM reports r 
                    LEFT JOIN users u ON r.created_by = u.id 
                    WHERE r.status = 'pending_em' 
                    ORDER BY FIELD(r.priority, 'urgent', 'high', 'medium', 'low'), r.created_at DESC";
            $result = $conn->query($sql);
            ?>

            <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark" style="background:#013220;">
                        <tr>
                            <th width="8%">رقم البلاغ</th>
                            <th width="25%">العنوان والوصف</th>
                            <th width="15%">مقدم البلاغ</th>
                            <th width="15%">الموقع والإحداثيات</th>
                            <th width="12%">التصنيف</th>
                            <th width="10%">الأولوية</th>
                            <th width="10%">التاريخ</th>
                            <th width="5%">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = $result->fetch_assoc()): ?>
                        <tr class="<?= $r['priority'] == 'urgent' ? 'table-danger' : '' ?>">
                            <td class="text-center fw-bold">
                                #<?= str_pad($r['id'], 5, '0', STR_PAD_LEFT) ?>
                                <br>
                                <small class="text-muted">من: <?= htmlspecialchars($r['creator_name']) ?></small>
                            </td>

                            <td>
                                <strong><?= htmlspecialchars($r['title']) ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?= mb_substr(strip_tags($r['description']), 0, 120) ?>...
                                </small>
                            </td>

                            <td>
                                <?php if($r['reporter_name']): ?>
                                    <strong><?= htmlspecialchars($r['reporter_name']) ?></strong>
                                    <br>
                                    <small><?= $r['reporter_phone'] ?? 'غير مدخل' ?></small>
                                <?php else: ?>
                                    <span class="text-muted">غير محدد</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <small>
                                    <?= $r['location_name'] ? htmlspecialchars($r['location_name']) : 'غير محدد' ?>
                                    <?php if($r['latitude'] && $r['longitude']): ?>
                                        <br>
                                        <a href="https://maps.google.com/?q=<?= $r['latitude'] ?>,<?= $r['longitude'] ?>" target="_blank" class="text-success">
                                            <i class="bi bi-geo-alt-fill"></i> عرض على الخريطة
                                        </a>
                                    <?php endif; ?>
                                </small>
                            </td>

                            <td>
                                <small>
                                    <strong><?= ucfirst($r['category']) ?></strong>
                                    <?= $r['subcategory'] ? '<br>' . htmlspecialchars($r['subcategory']) : '' ?>
                                </small>
                            </td>

                            <td class="text-center">
                                <span class="badge badge-priority-<?= $r['priority'] ?> fs-6">
                                    <?= ['low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية','urgent'=>'عاجلة'][$r['priority']] ?>
                                </span>
                            </td>

                            <td>
                                <small>
                                    <?= date('d/m/Y', strtotime($r['gregorian_date'])) ?>
                                    <br>
                                    <?= date('H:i', strtotime($r['created_at'])) ?>
                                </small>
                            </td>

                            <td>
                                <a href="../reports/review_em.php?id=<?= $r['id'] ?>" class="btn btn-success btn-sm">
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
                <h4 class="text-success mt-3">لا توجد بلاغات في انتظار المراجعة حاليًا</h4>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

