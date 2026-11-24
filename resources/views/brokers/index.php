<?php
use App\Config\App;
use App\Helpers\View;
?>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= App::baseUrl('') ?>" class="text-decoration-none">الرئيسية</a></li>
            <li class="breadcrumb-item active" aria-current="page">دليل الوسطاء العقاريين المعتمدين</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">دليل الوسطاء والسماسرة العقاريين المعتمدين</h2>
            <p class="text-muted mb-0">نخبة من السماسرة والوسطاء ذوي الخبرة والمصداقية في السوق العقاري اليمني</p>
        </div>
    </div>

    <?php if (!empty($brokers)): ?>
        <div class="row g-4">
            <?php foreach ($brokers as $broker): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-3 p-4 text-center h-100 d-flex flex-column">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 70px; height: 70px; font-size: 1.8rem;">
                            <i class="bi bi-person-fill"></i>
                        </div>

                        <h5 class="fw-bold mb-1 text-navy">
                            <a href="<?= App::baseUrl('broker/' . $broker['id']) ?>" class="text-decoration-none text-navy">
                                <?= htmlspecialchars($broker['full_name']) ?>
                            </a>
                        </h5>

                        <div class="text-muted small mb-2">وسيط عقاري معتمد</div>

                        <?php if (!empty($broker['office_name'])): ?>
                            <div class="badge bg-light text-dark border small mb-3">
                                <?= htmlspecialchars($broker['office_name']) ?>
                            </div>
                        <?php endif; ?>

                        <p class="text-muted small mb-3 flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= htmlspecialchars($broker['bio'] ?? 'وسيط عقاري خبير في بيع وشراء وتأجير العقارات.') ?>
                        </p>

                        <div class="d-grid gap-2">
                            <a href="<?= App::baseUrl('broker/' . $broker['id']) ?>" class="btn btn-outline-primary btn-sm fw-semibold">
                                العقارات المسوقة (<?= $broker['properties_count'] ?>)
                            </a>
                            <a href="tel:<?= htmlspecialchars($broker['phone']) ?>" class="btn btn-primary btn-sm fw-bold">
                                <i class="bi bi-telephone"></i> اتصال مباشر
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">لا يوجد وسطاء مسجلون حالياً.</div>
    <?php endif; ?>
</div>
