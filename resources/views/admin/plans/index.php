<?php
use App\Config\App;
use App\Helpers\View;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">باقات الاشتراكات والإعلانات</h4>
            <div class="text-muted small">إدارة الباقات والحدود المسموحة للإعلانات للملاك والمكاتب</div>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($plans as $plan): ?>
            <?php $features = json_decode($plan['features_json'] ?? '[]', true) ?: []; ?>
            <div class="col-md-4">
                <div class="card border <?= $plan['price'] > 0 ? 'border-primary' : '' ?> h-100 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-navy mb-0"><?= htmlspecialchars($plan['name']) ?></h5>
                        <?php if ($plan['is_active']): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">مفعلة</span>
                        <?php endif; ?>
                    </div>

                    <div class="fs-2 fw-bold text-primary mb-3">
                        <?= View::formatPrice($plan['price'], $plan['currency']) ?>
                        <span class="text-muted fs-6 fw-normal">/ <?= $plan['duration_days'] ?> يوم</span>
                    </div>

                    <ul class="list-unstyled mb-4 small text-secondary">
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> حتى <?= $plan['max_listings'] ?> عقار</li>
                        <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <?= $plan['max_featured'] ?> عقارات مميزة بالصفحة الأولى</li>
                        <?php foreach ($features as $f): ?>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> <?= htmlspecialchars($f) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
