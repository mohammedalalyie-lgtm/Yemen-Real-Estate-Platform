<?php
use App\Config\App;
use App\Helpers\Csrf;
?>

<h3 class="fw-bold mb-2 text-center text-navy">استعادة كلمة المرور</h3>
<p class="text-muted text-center small mb-4">أدخل بريدك الإلكتروني أو رقم هاتفك المسجل وسنرسل لك تعليمات إعادة تعيين كلمة المرور</p>

<form onsubmit="event.preventDefault(); showToast('تم إرسال رابط استعادة كلمة المرور بنجاح إلى بريدك الإلكتروني المسجل!', 'success');">
    <?= Csrf::field() ?>

    <div class="mb-3">
        <label class="form-label fw-semibold small">البريد الإلكتروني أو رقم الهاتف المسجل</label>
        <div class="input-group">
            <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
            <input type="text" class="form-control" required placeholder="name@example.com أو 771234567" autofocus>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
        <i class="bi bi-send-fill me-1"></i> إرسال رابط الاستعادة
    </button>
</form>

<div class="text-center mt-4 pt-3 border-top small text-muted">
    تذكرت كلمة المرور؟ 
    <a href="<?= App::baseUrl('login') ?>" class="fw-bold text-primary text-decoration-none">العودة لتسجيل الدخول</a>
</div>
