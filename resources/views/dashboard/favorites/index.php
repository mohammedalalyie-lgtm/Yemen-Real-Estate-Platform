<?php
use App\Config\App;
use App\Helpers\View;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">العقارات المحفوظة في المفضلة</h4>
            <div class="text-muted small">قائمتك المختارة من العقارات للرجوع إليها ومقارنتها في أي وقت</div>
        </div>
    </div>

    <?php if (!empty($favorites)): ?>
        <div class="row g-4">
            <?php foreach ($favorites as $prop): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="property-card">
                        <div class="property-card-img-wrapper" style="height: 180px;">
                            <a href="<?= App::baseUrl('property/' . $prop['id']) ?>">
                                <?php $imgSrc = !empty($prop['primary_image']) ? App::upload('properties/' . $prop['primary_image']) : App::asset('images/placeholder.svg'); ?>
                                <img src="<?= $imgSrc ?>" class="property-card-img" alt="<?= htmlspecialchars($prop['title']) ?>">
                            </a>
                            <button class="favorite-btn favorite-btn-action active" data-property-id="<?= $prop['id'] ?>" title="إزالة من المفضلة">
                                <i class="bi bi-heart-fill text-danger"></i>
                            </button>
                        </div>
                        <div class="p-3">
                            <h6 class="fw-bold text-truncate mb-2">
                                <a href="<?= App::baseUrl('property/' . $prop['id']) ?>" class="text-decoration-none text-navy">
                                    <?= htmlspecialchars($prop['title']) ?>
                                </a>
                            </h6>
                            <div class="property-price small mb-2">
                                <?= View::formatPrice($prop['price'], $prop['currency'], $prop['price_type']) ?>
                            </div>
                            <div class="text-muted small mb-3">
                                <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($prop['governorate_name']) ?> - <?= htmlspecialchars($prop['district_name']) ?>
                            </div>
                            <a href="<?= App::baseUrl('property/' . $prop['id']) ?>" class="btn btn-sm btn-outline-primary w-100">
                                عرض كامل التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-heart fs-1 mb-2"></i>
            <h5>قائمة المفضلة فارغة</h5>
            <p class="small">يمكنك تصفح العقارات والضغط على أيقونة القلب لحفظ العقارات التي تعجبك هنا.</p>
            <a href="<?= App::baseUrl('properties') ?>" class="btn btn-outline-primary mt-2">
                تصفح العقارات الآن
            </a>
        </div>
    <?php endif; ?>
</div>
