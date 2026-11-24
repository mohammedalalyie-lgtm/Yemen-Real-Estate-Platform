<?php
use App\Config\App;
use App\Helpers\Csrf;
use App\Helpers\Session;

$old = Session::get('old_input', []);
Session::remove('old_input');
?>

<h3 class="fw-bold mb-2 text-center text-navy">إنشاء حساب جديد</h3>
<p class="text-muted text-center small mb-4">انضم إلى أكبر منصة عقارية في اليمن وابدأ في تسويق عقاراتك أو العثور على منزلك</p>

<form action="<?= App::baseUrl('register') ?>" method="POST">
    <?= Csrf::field() ?>

    <div class="mb-3">
        <label class="form-label fw-semibold small">الاسم الكامل <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
            <input type="text" name="full_name" class="form-control" required placeholder="مثال: محمد عبدالله الأصبحي" value="<?= htmlspecialchars($old['full_name'] ?? '') ?>" autofocus>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold small">نوع الحساب <span class="text-danger">*</span></label>
        <div class="row g-2">
            <div class="col-6">
                <input type="radio" class="btn-check" name="role" id="roleOwner" value="owner" autocomplete="off" <?= ($old['role'] ?? 'owner') === 'owner' ? 'checked' : '' ?>>
                <label class="btn btn-outline-primary w-100 py-2 small fw-bold" for="roleOwner">
                    <i class="bi bi-house-door me-1"></i> مالك عقار
                </label>
            </div>
            <div class="col-6">
                <input type="radio" class="btn-check" name="role" id="roleBroker" value="broker" autocomplete="off" <?= ($old['role'] ?? '') === 'broker' ? 'checked' : '' ?>>
                <label class="btn btn-outline-primary w-100 py-2 small fw-bold" for="roleBroker">
                    <i class="bi bi-briefcase me-1"></i> وسيط / سمسار
                </label>
            </div>
            <div class="col-6">
                <input type="radio" class="btn-check" name="role" id="roleOffice" value="office" autocomplete="off" <?= ($old['role'] ?? '') === 'office' ? 'checked' : '' ?>>
                <label class="btn btn-outline-primary w-100 py-2 small fw-bold" for="roleOffice">
                    <i class="bi bi-building me-1"></i> مكتب عقاري
                </label>
            </div>
            <div class="col-6">
                <input type="radio" class="btn-check" name="role" id="roleCustomer" value="customer" autocomplete="off" <?= ($old['role'] ?? '') === 'customer' ? 'checked' : '' ?>>
                <label class="btn btn-outline-primary w-100 py-2 small fw-bold" for="roleCustomer">
                    <i class="bi bi-search me-1"></i> باحث / مشتري
                </label>
            </div>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold small">البريد الإلكتروني <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control" required placeholder="name@example.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold small">رقم الهاتف اليمني <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light" dir="ltr">+967</span>
                <input type="tel" name="phone" class="form-control" required placeholder="771234567" value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="row g-2 mb-4">
        <div class="col-md-6">
            <label class="form-label fw-semibold small">كلمة المرور <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control" required placeholder="6 أحرف على الأقل">
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold small">تأكيد كلمة المرور <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                <input type="password" name="password_confirmation" class="form-control" required placeholder="تطابق كلمة المرور">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> إتمام التسجيل والدخول
    </button>
</form>

<div class="text-center mt-4 pt-3 border-top small text-muted">
    لديك حساب بالفعل؟ 
    <a href="<?= App::baseUrl('login') ?>" class="fw-bold text-primary text-decoration-none">تسجيل الدخول هنا</a>
</div>
