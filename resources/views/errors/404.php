<?php
use App\Config\App;
?>

<div class="container py-5 text-center">
    <div class="py-5">
        <div class="display-1 fw-bold text-primary mb-3">404</div>
        <h2 class="fw-bold mb-3 text-navy">الصفحة أو العقار غير موجود</h2>
        <p class="text-muted mx-auto mb-4" style="max-width: 500px;">
            قد يكون الرابط خاطئاً، أو تم حذف العقار أو انتهاء فترة عرضه من قبل المعلن.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= App::baseUrl('') ?>" class="btn btn-primary fw-bold px-4">
                <i class="bi bi-house-door me-1"></i> العودة للصفحة الرئيسية
            </a>
            <a href="<?= App::baseUrl('properties') ?>" class="btn btn-outline-secondary px-4">
                تصفح العقارات المتاحة
            </a>
        </div>
    </div>
</div>
