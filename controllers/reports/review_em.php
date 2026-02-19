<!-- controllers/reports/review_em.php -->
<?php
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['emergency']);

if ($_POST && isset($_POST['report_id'])) {
    $id = (int)$_POST['report_id'];
    $decision = $_POST['decision'];
    $notes = clean($_POST['em_notes']);

    $new_status = ($decision == 'approve') ? 'pending_em_manager' : 'rejected';

    $sql = "UPDATE reports SET 
            status = ?, 
            em_notes = ?, 
            em_decision = ?, 
            em_reject_reason = ? 
            WHERE id = ? AND status = 'pending_em'";
    
    $stmt = $conn->prepare($sql);
    $reason = ($decision == 'reject') ? $notes : null;
    $stmt->bind_param("ssssi", $new_status, $notes, $decision, $reason, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "تم تحديث البلاغ بنجاح";
    } else {
        $_SESSION['error'] = "حدث خطأ";
    }
    redirect('../../views/dashboard/emergency.php');
}
?>