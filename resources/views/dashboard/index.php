<?php
use App\Config\App;
use App\Helpers\View;
?>

<!-- KPI Stats Grid -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="kpi-card">
            <div>
                <div class="text-muted small fw-semibold">إجمالي عقاراتي</div>
                <div class="fs-3 fw-bold text-navy mt-1"><?= number_format($stats['total'] ?? 0) ?></div>
            </div>
            <div class="kpi-icon-box bg-primary-subtle text-primary">
                <i class="bi bi-houses-fill"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="kpi-card">
            <div>
                <div class="text-muted small fw-semibold">منشور ومتاح</div>
                <div class="fs-3 fw-bold text-success mt-1"><?= number_format($stats['approved'] ?? 0) ?></div>
            </div>
            <div class="kpi-icon-box bg-success-subtle text-success">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="kpi-card">
            <div>
                <div class="text-muted small fw-semibold">بانتظار المراجعة</div>
                <div class="fs-3 fw-bold text-warning mt-1"><?= number_format($stats['pending'] ?? 0) ?></div>
            </div>
            <div class="kpi-icon-box bg-warning-subtle text-warning">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="kpi-card">
            <div>
                <div class="text-muted small fw-semibold">إجمالي المشاهدات</div>
                <div class="fs-3 fw-bold text-info mt-1"><?= number_format($stats['total_views'] ?? 0) ?></div>
            </div>
            <div class="kpi-icon-box bg-info-subtle text-info">
                <i class="bi bi-eye-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Views Chart & Summary Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-graph-up-arrow text-primary me-2"></i> حركة المشاهدات خلال الأسبوع الأخير</h6>
                <span class="badge bg-light text-muted border">إحصائيات حية</span>
            </div>
            <div style="height: 250px;">
                <canvas id="viewsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart text-warning me-2"></i> ملخص حالات الإعلانات</h6>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                    <span><i class="bi bi-check-circle text-success me-1"></i> إعلانات معتمدة:</span>
                    <strong class="text-success"><?= $stats['approved'] ?? 0 ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                    <span><i class="bi bi-clock text-warning me-1"></i> بانتظار موافقة الإدارة:</span>
                    <strong class="text-warning"><?= $stats['pending'] ?? 0 ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                    <span><i class="bi bi-x-circle text-danger me-1"></i> إعلانات مرفوضة:</span>
                    <strong class="text-danger"><?= $stats['rejected'] ?? 0 ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                    <span><i class="bi bi-bookmark-check text-primary me-1"></i> محجوزة أو مكتملة:</span>
                    <strong class="text-primary"><?= ($stats['reserved'] ?? 0) + ($stats['completed'] ?? 0) ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Properties & Recent Inquiries -->
<div class="row g-4">
    <!-- Recent Properties Table -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-houses text-primary me-2"></i> آخر العقارات المضافة</h6>
                <a href="<?= App::baseUrl('dashboard/properties') ?>" class="small text-primary text-decoration-none">عرض الكل</a>
            </div>

            <?php if (!empty($recentProperties)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>العقار</th>
                                <th>السعر</th>
                                <th>الحالة</th>
                                <th>المشاهدات</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentProperties as $p): ?>
                                <?php $statusInfo = View::formatStatus($p['status']); ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php $imgSrc = !empty($p['primary_image']) ? App::upload('properties/' . $p['primary_image']) : App::asset('images/placeholder.svg'); ?>
                                            <img src="<?= $imgSrc ?>" class="rounded" style="width: 44px; height: 36px; object-fit: cover;" alt="">
                                            <div class="text-truncate" style="max-width: 170px;">
                                                <div class="fw-bold small text-truncate"><?= htmlspecialchars($p['title']) ?></div>
                                                <div class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars($p['governorate_name']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="small fw-bold text-primary">
                                        <?= View::formatPrice($p['price'], $p['currency']) ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $statusInfo['class'] ?> small"><?= $statusInfo['label'] ?></span>
                                    </td>
                                    <td class="small text-muted"><?= number_format($p['views_count']) ?></td>
                                    <td>
                                        <a href="<?= App::baseUrl('dashboard/properties/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-muted">
                    لم تقم بإضافة أي عقارات حتى الآن.
                    <div class="mt-2">
                        <a href="<?= App::baseUrl('dashboard/properties/create') ?>" class="btn btn-sm btn-primary">إضافة أول عقار الآن</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Inquiries -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold m-0"><i class="bi bi-chat-left-dots text-warning me-2"></i> آخر الاستفسارات</h6>
                <a href="<?= App::baseUrl('dashboard/inquiries') ?>" class="small text-primary text-decoration-none">عرض الكل</a>
            </div>

            <?php if (!empty($recentInquiries)): ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($recentInquiries as $inq): ?>
                        <div class="p-3 rounded border bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="small"><?= htmlspecialchars($inq['sender_name']) ?></strong>
                                <span class="text-muted" style="font-size: 0.75rem;"><?= View::timeAgo($inq['created_at']) ?></span>
                            </div>
                            <div class="text-primary small fw-semibold text-truncate mb-1">
                                <i class="bi bi-building"></i> <?= htmlspecialchars($inq['property_title']) ?>
                            </div>
                            <p class="small text-muted mb-2 text-truncate"><?= htmlspecialchars($inq['message']) ?></p>
                            <a href="tel:<?= htmlspecialchars($inq['sender_phone']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2 small">
                                <i class="bi bi-telephone"></i> <?= htmlspecialchars($inq['sender_phone']) ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-muted">
                    لا توجد استفسارات واردة حالياً.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
