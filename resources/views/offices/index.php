<?php
use App\Config\App;
use App\Helpers\View;
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= App::baseUrl('') ?>" class="text-decoration-none">الرئيسية</a></li>
            <li class="breadcrumb-item active" aria-current="page">دليل المكاتب العقارية المعتمدة</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">دليل المكاتب والشركات العقارية في اليمن</h2>
            <p class="text-muted mb-0">تواصل مع أفضل المكاتب العقارية المرخصة والمعتمدة لتسويق وإدارة العقارات</p>
        </div>
    </div>

    <?php if (!empty($offices)): ?>
        <div class="row g-4">
            <?php foreach ($offices as $office): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 p-4 h-100 d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.6rem;">
                                <i class="bi bi-building"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-navy">
                                    <a href="<?= App::baseUrl('office/' . $office['id']) ?>" class="text-decoration-none text-navy">
                                        <?= htmlspecialchars($office['name']) ?>
                                    </a>
                                </h5>
                                <?php if ($office['is_verified']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle small">
                                        <i class="bi bi-patch-check-fill"></i> مكتب معتمد
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <p class="text-muted small mb-3 flex-grow-1 text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($office['description'] ?? 'خدمات الوساطة والتسويق العقاري والاستثمار في اليمن.') ?>
                        </p>

                        <div class="small text-muted mb-3">
                            <div><i class="bi bi-geo-alt text-danger me-1"></i> <?= htmlspecialchars($office['address'] ?? 'اليمن') ?></div>
                            <div class="mt-1"><i class="bi bi-telephone text-primary me-1"></i> <?= htmlspecialchars($office['phone']) ?></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <span class="small text-muted"><strong><?= $office['properties_count'] ?></strong> عقارات معروضة</span>
                            <a href="<?= App::baseUrl('office/' . $office['id']) ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                                زيارة المكتب <i class="bi bi-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">لا توجد مكاتب مسجلة حالياً.</div>
    <?php endif; ?>
</div>
