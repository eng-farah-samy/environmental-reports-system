<!-- views/dashboard/completed_reports.php -->
<?php 
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['gm']);
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<div class="text-white py-5 mb-5 rounded-4 shadow-lg" style="background: linear-gradient(135deg, #1b5e20, #2e7d32, #388e3c);">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">البلاغات المنتهية</h1>
        <p class="lead opacity-90">جميع البلاغات التي تم اتخاذ قرار نهائي بشأنها</p>
    </div>
</div>

<div class="container">
    <div class="card border-0 shadow-lg">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background:#1b5e20;">
            <h4 class="mb-0">قائمة البلاغات المنتهية</h4>
            <span class="badge bg-light text-dark fs-5">
                <?= $conn->query("SELECT COUNT(*) FROM reports WHERE status IN ('approved','rejected','closed')")->fetch_row()[0] ?>
            </span>
        </div>
        <div class="card-body p-0">
            <?php
            $sql = "SELECT r.*, u.full_name as creator 
                    FROM reports r 
                    LEFT JOIN users u ON r.created_by = u.id 
                    WHERE r.status IN ('approved','rejected','closed')
                    ORDER BY r.updated_at DESC";
            $result = $conn->query($sql);
            ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#2e7d32; color:white;">
                        <tr>
                            <th class="text-white">رقم</th>
                            <th class="text-white">العنوان</th>
                            <th class="text-white">الحالة</th>
                            <th class="text-white">الأولوية</th>
                            <th class="text-white">التاريخ الهجري</th>
                            <th class="text-white">مسجل بواسطة</th>
                            <th class="text-white"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($r = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold">#<?= str_pad($r['id'],5,'0',STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($r['title']) ?></td>
                            <td>
                                <span class="badge bg-<?= $r['status']=='approved'?'success':($r['status']=='rejected'?'danger':'secondary') ?>">
                                    <?= $r['status']=='approved'?'معتمد':($r['status']=='rejected'?'مرفوض':'مغلق') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-priority-<?= $r['priority'] ?>">
                                    <?= ['low'=>'منخفضة','medium'=>'متوسطة','high'=>'عالية','urgent'=>'عاجلة'][$r['priority']] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($r['hijri_date']) ?></td>
                            <td><?= htmlspecialchars($r['creator'] ?? 'غير معروف') ?></td>
                            <td>
                                <a href="../reports/view.php?id=<?= $r['id'] ?>" class="btn btn-success btn-sm">عرض التفاصيل</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

