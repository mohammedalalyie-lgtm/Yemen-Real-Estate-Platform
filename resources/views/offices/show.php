<?php
use App\Config\App;
use App\Helpers\View;
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= App::baseUrl('') ?>" class="text-decoration-none">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="<?= App::baseUrl('offices') ?>" class="text-decoration-none">المكاتب العقارية</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($office['name']) ?></li>
        </ol>
    </nav>

    <!-- Office Banner Card -->
    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 75px; height: 75px; font-size: 2rem;">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1 text-navy"><?= htmlspecialchars($office['name']) ?></h2>
                        <div class="text-muted small mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($office['address'] ?? 'اليمن') ?></div>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <?php if ($office['is_verified']): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-patch-check-fill"></i> مكتب معتمد وموثق
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($office['license_number'])): ?>
                                <span class="badge bg-light text-dark border">
                                    رقم الترخيص: <?= htmlspecialchars($office['license_number']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <p class="text-secondary mt-3 mb-0" style="line-height: 1.7;">
                    <?= nl2br(htmlspecialchars($office['description'] ?? '')) ?>
                </p>
            </div>

            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <div class="d-grid gap-2">
                    <a href="tel:<?= htmlspecialchars($office['phone']) ?>" class="btn btn-primary fw-bold">
                        <i class="bi bi-telephone-fill me-1"></i> اتصال: <?= htmlspecialchars($office['phone']) ?>
                    </a>
                    <?php if (!empty($office['whatsapp'])): ?>
                        <?php $cleanWA = preg_replace('/[^0-9]/', '', $office['whatsapp']); ?>
                        <a href="https://wa.me/<?= $cleanWA ?>" target="_blank" class="btn btn-success fw-bold">
                            <i class="bi bi-whatsapp me-1"></i> تواصل عبر الواتساب
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Office Properties Listings -->
    <h3 class="fw-bold mb-4">العقارات المعروضة من قبل المكتب (<?= count($properties) ?>)</h3>
    <?php if (!empty($properties)): ?>
        <div class="row g-4">
            <?php foreach ($properties as $prop): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="property-card">
                        <div class="property-card-img-wrapper" style="height: 190px;">
                            <a href="<?= App::baseUrl('property/' . $prop['id']) ?>">
                                <?php $imgSrc = !empty($prop['primary_image']) ? App::upload('properties/' . $prop['primary_image']) : App::asset('images/placeholder.svg'); ?>
                                <img src="<?= $imgSrc ?>" class="property-card-img" alt="<?= htmlspecialchars($prop['title']) ?>">
                            </a>
                            <div class="property-badges">
                                <span class="property-badge <?= $prop['purpose'] === 'sale' ? 'badge-purpose-sale' : 'badge-purpose-rent' ?>">
                                    <?= View::formatPurpose($prop['purpose']) ?>
                                </span>
                            </div>
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
                                التفاصيل الكاملة
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">لا توجد عقارات معروضة حالياً لهذا المكتب.</div>
    <?php endif; ?>
</div>
