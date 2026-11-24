<?php
use App\Config\App;
use App\Helpers\Csrf;
?>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4">
            <h4 class="fw-bold mb-1">إعدادات الملف الشخصي</h4>
            <div class="text-muted small mb-4">تحديث معلومات حسابك ورقم هاتفك ووسائل التواصل</div>

            <form action="<?= App::baseUrl('dashboard/profile/update') ?>" method="POST" enctype="multipart/form-data">
                <?= Csrf::field() ?>

                <div class="mb-3">
                    <label class="form-label fw-bold small">الاسم الكامل <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($user['full_name']) ?>">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">البريد الإلكتروني</label>
                        <input type="email" class="form-control bg-light" disabled value="<?= htmlspecialchars($user['email']) ?>">
                        <div class="form-text small">البريد الإلكتروني لا يمكن تغييره لأسباب أمنية.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">نوع الحساب</label>
                        <input type="text" class="form-control bg-light" disabled value="<?= match($user['role']) {
                            'office' => 'مكتب عقاري',
                            'broker' => 'وسيط عقاري',
                            'owner' => 'مالك عقار',
                            'super_admin' => 'المالك الرئيسي Super Admin',
                            'admin' => 'مدير النظام Admin',
                            default => 'عميل / باحث'
                        } ?>">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">رقم الهاتف اليمني <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" required value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">رقم الواتساب المباشر</label>
                        <input type="tel" name="whatsapp" class="form-control" placeholder="+967771234567" value="<?= htmlspecialchars($user['whatsapp'] ?? $user['phone']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">رقم الترخيص العقاري أو السجل (إن وجد)</label>
                    <input type="text" name="license_number" class="form-control" placeholder="مثال: YE-SAN-2024-102" value="<?= htmlspecialchars($user['license_number'] ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">النبذة التعريفية (تظهر في صفحتك العامة)</label>
                    <textarea name="bio" class="form-control" rows="4" placeholder="اكتب نبذة عن خبرتك العقارية والمناطق التي تنشط فيها..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small">الصورة الشخصية أو الشعار</label>
                    <input type="file" name="avatar" class="form-control" accept="image/jpeg,image/png,image/webp">
                </div>

                <button type="submit" class="btn btn-primary fw-bold px-4">
                    <i class="bi bi-save me-1"></i> حفظ التحديثات
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 text-center">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.2rem;">
                <i class="bi bi-person-circle"></i>
            </div>
            <h5 class="fw-bold mb-1"><?= htmlspecialchars($user['full_name']) ?></h5>
            <div class="text-muted small mb-2"><?= htmlspecialchars($user['email']) ?></div>
            
            <?php if ($user['is_verified']): ?>
                <div class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3 mb-3">
                    <i class="bi bi-patch-check-fill me-1"></i> حساب موثق ومعتمد رسمياً
                </div>
            <?php else: ?>
                <div class="badge bg-warning-subtle text-dark border border-warning-subtle py-2 px-3 mb-3">
                    <i class="bi bi-shield-slash me-1"></i> غير موثق بعد
                </div>
                <p class="small text-muted">لطلب شارة التوثيق (وسيط موثق)، تواصل مع الإدارة عبر الواتساب مع إرفاق صورة الهوية أو السجل التجاري.</p>
            <?php endif; ?>

            <hr>

            <div class="text-start small text-muted">
                <div><i class="bi bi-calendar3 me-1"></i> تاريخ الانضمام: <?= date('Y-m-d', strtotime($user['created_at'])) ?></div>
                <div class="mt-1"><i class="bi bi-telephone me-1"></i> الهاتف: <?= htmlspecialchars($user['phone']) ?></div>
            </div>
        </div>
    </div>
</div>
