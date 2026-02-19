<!-- views/reports/create.php -->
<?php 
require 'C:\xampp\htdocs\environmental-reports-system\includes\functions.php';
check_role(['data_entry']);

// جلب القوائم من الداتابيز
$categories     = $conn->query("SELECT * FROM categories ORDER BY name");
$subcategories  = $conn->query("SELECT * FROM subcategories ORDER BY name");
$sources        = $conn->query("SELECT * FROM sources ORDER BY name");
$methods        = $conn->query("SELECT * FROM receipt_methods ORDER BY name");
?>
<?php require 'C:\xampp\htdocs\environmental-reports-system\includes\header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white text-center py-4">
                    <h2 class="mb-0">
                        إنشاء بلاغ بيئي جديد
                    </h2>
                    <p class="mb-0 fs-5">موظف 988 - تسجيل بلاغ جديد</p>
                </div>

                <div class="card-body p-5 bg-light">
                    <form action="../../controllers/reports/create.php" method="POST" enctype="multipart/form-data" class="row g-4 needs-validation" novalidate>

                        <!-- 1. بيانات البلاغ الأساسية -->
                        <div class="col-12">
                            <h5 class="text-success border-bottom border-3 border-success pb-3 mb-4">
                                بيانات البلاغ الأساسية
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">اسم مختصر للحالة <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg" required 
                                   placeholder="مثال: تلوث هواء شديد بمنطقة المعادي">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-success">التاريخ الهجري <span class="text-danger">*</span></label>
                            <input type="text" name="hijri_date" class="form-control form-control-lg text-center" 
                                   placeholder="مثال: ١٥/٠٥/١٤٤٦ هـ" required>
                            <div class="form-text">اكتب التاريخ الهجري يدويًا (مثال: ١٥/٠٥/١٤٤٦ هـ)</div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-success">التاريخ الميلادي <span class="text-danger">*</span></label>
                            <input type="date" name="gregorian_date" class="form-control form-control-lg" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">وصف تفصيلي للمشكلة <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="6" required 
                                      placeholder="اكتب كل التفاصيل: نوع التلوث، الوقت، الأضرار، الشهود، أي معلومات مهمة..."></textarea>
                        </div>

                        <!-- 2. بيانات مقدم البلاغ -->
                        <div class="col-12 mt-5">
                            <h5 class="text-success border-bottom border-3 border-success pb-3 mb-4">
                                بيانات مقدم البلاغ (اختياري)
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" name="reporter_name" class="form-control" placeholder="اسم المبلغ">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">رقم الجوال</label>
                            <input type="text" name="reporter_phone" class="form-control" placeholder="05xxxxxxxx">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">رقم الهوية / الإقامة</label>
                            <input type="text" name="reporter_id" class="form-control" placeholder="رقم الهوية">
                        </div>

                        <!-- 3. التصنيف -->
                        <div class="col-12 mt-5">
                            <h5 class="text-success border-bottom border-3 border-success pb-3 mb-4">
                                التصنيف
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">التصنيف الأساسي <span class="text-danger">*</span></label>
                            <select name="category" class="form-select form-select-lg" required>
                                <option value="">اختر التصنيف الأساسي</option>
                                <?php while($c = $categories->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($c['name']) ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">التصنيف الفرعي</label>
                            <select name="subcategory" class="form-select form-select-lg">
                                <option value="">اختياري</option>
                                <?php 
                                $subcategories->data_seek(0);
                                while($s = $subcategories->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($s['name']) ?>"><?= htmlspecialchars($s['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">مصدر التلوث / الحادث</label>
                            <select name="source_type" class="form-select form-select-lg">
                                <option value="">اختر المصدر</option>
                                <?php while($src = $sources->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($src['name']) ?>"><?= htmlspecialchars($src['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">اسم الجهة المسببة</label>
                            <input type="text" name="source_name" class="form-control" placeholder="اسم المصنع أو الشركة">
                        </div>

                        <!-- 4. الموقع -->
                        <div class="col-12 mt-5">
                            <h5 class="text-success border-bottom border-3 border-success pb-3 mb-4">
                                موقع الحادث / التجاوز
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">اسم الموقع</label>
                            <input type="text" name="location_name" class="form-control" placeholder="مثال: شارع الهرم - الجيزة">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تفاصيل إضافية</label>
                            <input type="text" name="location_details" class="form-control" placeholder="علامات مميزة، معالم قريبة">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">خط العرض (Latitude)</label>
                            <input type="text" name="latitude" class="form-control" placeholder="مثال: 30.0444">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">خط الطول (Longitude)</label>
                            <input type="text" name="longitude" class="form-control" placeholder="مثال: 31.2357">
                        </div>

                        <!-- 5. تفاصيل إضافية -->
                        <div class="col-12 mt-5">
                            <h5 class="text-success border-bottom border-3 border-success pb-3 mb-4">
                                تفاصيل إضافية
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">وسيلة الاستلام <span class="text-danger">*</span></label>
                            <select name="receipt_method" class="form-select form-select-lg" required>
                                <option value="">اختر وسيلة الاستلام</option>
                                <?php while($m = $methods->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($m['name']) ?>"><?= htmlspecialchars($m['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">مستلم البلاغ</label>
                            <input type="text" name="receiver_name" class="form-control form-control-lg" 
                                   value="<?= htmlspecialchars(get_user_name()) ?>" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">الأولوية <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select form-select-lg" required>
                                <option value="low">منخفضة</option>
                                <option value="medium">متوسطة</option>
                                <option value="high">عالية</option>
                                <option value="urgent">عاجلة</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">وقت المشاهدة</label>
                            <select name="observation_time" class="form-select form-select-lg">
                                <option value="">غير محدد</option>
                                <option value="morning">صباحًا</option>
                                <option value="evening">مساءً</option>
                            </select>
                        </div>

                        <!-- 6. المرفقات -->
                        <div class="col-12 mt-5">
                            <h5 class="text-success border-bottom border-3 border-success pb-3 mb-4">
                                المرفقات (صور، فيديو، ملفات...)
                            </h5>
                            <input type="file" name="attachments[]" class="form-control form-control-lg" multiple 
                                   accept="image/*,video/*,.pdf,.doc,.docx">
                            <div class="form-text">يمكنك رفع أكثر من ملف في نفس الوقت</div>
                        </div>

                        <!-- الأزرار -->
                        <div class="col-12 mt-5 text-center">
                            <button type="submit" class="btn btn-success btn-lg px-5 py-3">
                                إرسال البلاغ لإدارة الطوارئ البيئية
                            </button>
                            <a href="../dashboard/data_entry.php" class="btn btn-outline-secondary btn-lg px-5 py-3">
                                إلغاء
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

