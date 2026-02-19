<?php
// controllers/reports/review_gm.php
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['gm']);

if (!$_POST || !isset($_POST['report_id'])) {
    $_SESSION['error'] = "طلب غير صحيح";
    redirect('../../views/dashboard/gm.php');
}

$report_id = (int)$_POST['report_id'];
$final_decision = $_POST['final_decision']; // من الفورم: approved / rejected / closed
$gm_notes = clean($_POST['gm_notes']);

// تحويل القيم لتطابق الـ ENUM الموجود في الداتابيز
$decision_map = [
    'approved' => 'approve',
    'rejected' => 'reject',
    'closed'   => 'close'
];

$gm_decision = $decision_map[$final_decision] ?? 'reject'; // لو حصل خطأ نعمل reject بالأمان

// الحالة الجديدة للـ status (نفس القيمة اللي في الفورم)
$new_status = $final_decision; // approved | rejected | closed

// تحديث البلاغ
$sql = "UPDATE reports SET 
        status = ?, 
        gm_notes = ?, 
        gm_decision = ?
        WHERE id = ? AND status = 'pending_gm'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $new_status, $gm_notes, $gm_decision, $report_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $messages = [
        'approved' => "تم اعتماد البلاغ رسميًا بنجاح",
        'rejected' => "تم رفض البلاغ نهائيًا",
        'closed'   => "تم إغلاق البلاغ بنجاح (تم الحل ميدانيًا)"
    ];
    $_SESSION['success'] = $messages[$final_decision] . " - رقم البلاغ: #" . str_pad($report_id, 5, '0', STR_PAD_LEFT);
} else {
    $_SESSION['error'] = "حدث خطأ أثناء تنفيذ القرار: " . $stmt->error;
}

redirect('../../views/dashboard/gm.php');
?>