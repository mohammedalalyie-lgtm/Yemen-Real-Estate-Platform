<?php
use App\Config\App;
use App\Helpers\Csrf;
?>

<h3 class="fw-bold mb-2 text-center text-navy">تسجيل الدخول</h3>
<p class="text-muted text-center small mb-4">أدخل بريدك الإلكتروني أو رقم هاتفك للدخول إلى حسابك</p>

<!-- Demo Login Quick Fill Box -->
<div class="alert alert-primary p-3 mb-4 small" style="background-color: var(--blue-light); border-color: #BFDBFE;">
    <div class="fw-bold text-navy mb-2"><i class="bi bi-info-circle-fill me-1"></i> حسابات تجريبية سريعة للاختبار:</div>
    <div class="d-flex flex-wrap gap-1">
        <button type="button" class="btn btn-sm btn-outline-primary py-0" onclick="fillDemo('admin@aqar-yemen.com', 'Admin@123456')">المالك الرئيسي (Super Admin)</button>
        <button type="button" class="btn btn-sm btn-outline-primary py-0" onclick="fillDemo('albaraka@aqar-yemen.com', 'Admin@123456')">مكتب عقاري (Office)</button>
        <button type="button" class="btn btn-sm btn-outline-primary py-0" onclick="fillDemo('sami.broker@aqar-yemen.com', 'Admin@123456')">وسيط عقاري (Broker)</button>
        <button type="button" class="btn btn-sm btn-outline-primary py-0" onclick="fillDemo('owner.saleh@aqar-yemen.com', 'Admin@123456')">مالك عقار (Owner)</button>
    </div>
</div>

<form action="<?= App::baseUrl('login') ?>" method="POST">
    <?= Csrf::field() ?>

    <div class="mb-3">
        <label class="form-label fw-semibold small">البريد الإلكتروني أو رقم الهاتف</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
            <input type="text" name="login" id="loginInput" class="form-control" required placeholder="بريدك الإلكتروني أو 771234567" autofocus>
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label fw-semibold small mb-0">كلمة المرور</label>
            <a href="<?= App::baseUrl('forgot-password') ?>" class="small text-decoration-none text-primary">نسيت كلمة المرور؟</a>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" id="passwordInput" class="form-control" required placeholder="••••••••">
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mt-2 shadow-sm">
        <i class="bi bi-box-arrow-in-left me-1"></i> تسجيل الدخول
    </button>
</form>

<div class="text-center mt-4 pt-3 border-top small text-muted">
    ليس لديك حساب بعد؟ 
    <a href="<?= App::baseUrl('register') ?>" class="fw-bold text-primary text-decoration-none">إنشاء حساب جديد</a>
</div>

<script>
function fillDemo(login, pass) {
    document.getElementById('loginInput').value = login;
    document.getElementById('passwordInput').value = pass;
}
</script>
