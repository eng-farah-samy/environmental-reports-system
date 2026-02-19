<?php
require '../../includes/functions.php';
check_role(['data_entry']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../../views/reports/create.php');
}

// البيانات...
$title            = clean($_POST['title'] ?? '');
$description      = clean($_POST['description'] ?? '');
$category         = clean($_POST['category'] ?? '');
$subcategory      = clean($_POST['subcategory'] ?? '');
$source_type      = clean($_POST['source_type'] ?? '');
$source_name      = clean($_POST['source_name'] ?? '');
$reporter_name    = clean($_POST['reporter_name'] ?? '');
$reporter_phone   = clean($_POST['reporter_phone'] ?? '');
$reporter_id      = clean($_POST['reporter_id'] ?? '');
$receipt_method   = clean($_POST['receipt_method'] ?? '');
$location_name    = clean($_POST['location_name'] ?? '');
$location_details = clean($_POST['location_details'] ?? '');
$latitude         = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
$longitude        = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
$priority         = clean($_POST['priority'] ?? 'medium');
$observation_time = clean($_POST['observation_time'] ?? '');
$hijri_date       = clean($_POST['hijri_date'] ?? '');
$gregorian_date   = $_POST['gregorian_date'] ?? date('Y-m-d');
$receiver_name    = clean(get_user_name());
$user_id          = $_SESSION['user_id'];

// التحقق من الحقول الإجبارية
if (empty($title) || empty($description) || empty($category) || empty($receipt_method) || empty($hijri_date)) {
    $_SESSION['error'] = "يرجى ملء جميع الحقول المطلوبة";
    redirect('../../views/reports/create.php');
}

// إنشاء البلاغ → يروح لإدارة الطوارئ أولاً
$sql = "INSERT INTO reports (
    title, description, category, subcategory, source_type, source_name,
    reporter_name, reporter_phone, reporter_id, receipt_method,
    location_name, location_details, latitude, longitude,
    priority, observation_time, hijri_date, gregorian_date,
    receiver_name, created_by, status
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending_em')";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssssssssddssssis",
    $title, $description, $category, $subcategory, $source_type, $source_name,
    $reporter_name, $reporter_phone, $reporter_id, $receipt_method,
    $location_name, $location_details, $latitude, $longitude,
    $priority, $observation_time, $hijri_date, $gregorian_date,
    $receiver_name, $user_id
);

if (!$stmt->execute()) {
    $_SESSION['error'] = "فشل في حفظ البلاغ";
    redirect('../../views/reports/create.php');
}

$report_id = $conn->insert_id;
$stmt->close();

// رفع المرفقات (اختياري)
if (!empty($_FILES['attachments']['name'][0])) {
    $upload_dir = '../../uploads/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['attachments']['name'][$key], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','pdf','mp4','mov','doc','docx'];
            if (in_array($ext, $allowed) && $_FILES['attachments']['size'][$key] <= 25*1024*1024) {
                $new_name = uniqid('att_') . '_' . time() . '.' . $ext;
                if (move_uploaded_file($tmp_name, $upload_dir . $new_name)) {
                    $type = match(true) {
                        in_array($ext, ['jpg','jpeg','png','gif']) => 'image',
                        $ext === 'pdf' => 'pdf',
                        in_array($ext, ['doc','docx']) => 'word',
                        in_array($ext, ['mp4','mov']) => 'video',
                        default => 'image'
                    };
                    $asql = "INSERT INTO attachments (report_id, file_path, file_type, uploaded_by) VALUES (?, ?, ?, ?)";
                    $astmt = $conn->prepare($asql);
                    $astmt->bind_param("issi", $report_id, $new_name, $type, $user_id);
                    $astmt->execute();
                    $astmt->close();
                }
            }
        }
    }
}

$_SESSION['success'] = "تم تسجيل البلاغ بنجاح ورقمه: <strong>#" . str_pad($report_id, 5, '0', STR_PAD_LEFT) . "</strong>";
redirect('../../views/dashboard/data_entry.php');
?>