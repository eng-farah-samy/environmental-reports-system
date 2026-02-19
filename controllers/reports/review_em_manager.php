<?php
// controllers/reports/review_em_manager.php
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['em_manager']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['report_id'])) {
    $_SESSION['error'] = "طلب غير صحيح";
    redirect('../../views/dashboard/em_manager.php');
}

$report_id = (int)$_POST['report_id'];
$decision  = $_POST['decision']; // approve أو reject
$notes     = clean($_POST['notes']);

// تحقق من أن البلاغ فعلاً في حالة pending_em_manager
$check = $conn->query("SELECT status FROM reports WHERE id = $report_id")->fetch_assoc();
if (!$check || $check['status'] !== 'pending_em_manager') {
    $_SESSION['error'] = "البلاغ غير متاح للمراجعة حاليًا";
    redirect('../../views/dashboard/em_manager.php');
}

// تحديد الحالة الجديدة
$new_status = ($decision === 'approve') ? 'pending_gm' : 'rejected';

$em_manager_notes = $notes;
$em_manager_decision = $decision;
$em_manager_reject_reason = ($decision === 'reject') ? $notes : null;

// تحديث البلاغ بدون الحقل updated_at
$sql = "UPDATE reports SET 
        status = ?,
        em_manager_notes = ?,
        em_manager_decision = ?,
        em_manager_reject_reason = ?,
        updated_at = NOW()
        WHERE id = ? AND status = 'pending_em_manager'";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssi",
    $new_status,
    $em_manager_notes,
    $em_manager_decision,
    $em_manager_reject_reason,
    $report_id
);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    if ($decision === 'approve') {
        $_SESSION['success'] = "تمت الموافقة على البلاغ بنجاح ورقمه #" . str_pad($report_id, 5, '0', STR_PAD_LEFT) . " وتم إرساله للمدير العام";
    } else {
        $_SESSION['success'] = "تم رفض البلاغ نهائيًا بنجاح ورقمه #" . str_pad($report_id, 5, '0', STR_PAD_LEFT);
    }
} else {
    $_SESSION['error'] = "حدث خطأ أثناء حفظ القرار، حاول مرة أخرى";
}

redirect('../../views/dashboard/em_manager.php');
?>