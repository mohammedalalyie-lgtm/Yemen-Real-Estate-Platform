<?php
use App\Config\App;
use App\Helpers\View;
use App\Helpers\Csrf;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1">إدارة جميع العقارات</h4>
            <div class="text-muted small">تصفح، فلترة، والتحكم في إعلانات المنصة بالكامل</div>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= App::baseUrl('admin/properties/pending') ?>" class="btn btn-warning fw-bold btn-sm">
                <i class="bi bi-clock-history me-1"></i> مراجعة الإعلانات المعلقة
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="mb-4 d-flex gap-2 flex-wrap">
        <a href="<?= App::baseUrl('admin/properties') ?>" class="btn btn-sm <?= empty($selectedStatus) ? 'btn-primary' : 'btn-outline-secondary' ?>">الكل</a>
        <a href="<?= App::baseUrl('admin/properties?status=approved') ?>" class="btn btn-sm <?= $selectedStatus === 'approved' ? 'btn-primary' : 'btn-outline-secondary' ?>">منشور ومتاح</a>
        <a href="<?= App::baseUrl('admin/properties?status=pending') ?>" class="btn btn-sm <?= $selectedStatus === 'pending' ? 'btn-primary' : 'btn-outline-secondary' ?>">معلق</a>
        <a href="<?= App::baseUrl('admin/properties?status=rejected') ?>" class="btn btn-sm <?= $selectedStatus === 'rejected' ? 'btn-primary' : 'btn-outline-secondary' ?>">مرفوض</a>
        <a href="<?= App::baseUrl('admin/properties?status=reserved') ?>" class="btn btn-sm <?= $selectedStatus === 'reserved' ? 'btn-primary' : 'btn-outline-secondary' ?>">محجوز</a>
        <a href="<?= App::baseUrl('admin/properties?status=sold') ?>" class="btn btn-sm <?= $selectedStatus === 'sold' ? 'btn-primary' : 'btn-outline-secondary' ?>">تم البيع</a>
        <a href="<?= App::baseUrl('admin/properties?status=rented') ?>" class="btn btn-sm <?= $selectedStatus === 'rented' ? 'btn-primary' : 'btn-outline-secondary' ?>">تم التأجير</a>
    </div>

    <!-- Table -->
    <?php if (!empty($properties)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>#</th>
                        <th>العقار</th>
                        <th>المعلن</th>
                        <th>السعر</th>
                        <th>الموقع</th>
                        <th>الحالة</th>
                        <th>المشاهدات</th>
                        <th>مميز؟</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $p): ?>
                        <?php $st = View::formatStatus($p['status']); ?>
                        <tr>
                            <td class="small text-muted"><?= $p['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php $imgSrc = !empty($p['primary_image']) ? App::upload('properties/' . $p['primary_image']) : App::asset('images/placeholder.svg'); ?>
                                    <img src="<?= $imgSrc ?>" class="rounded" style="width: 48px; height: 36px; object-fit: cover;" alt="">
                                    <div class="text-truncate" style="max-width: 220px;">
                                        <div class="fw-bold small text-navy text-truncate"><?= htmlspecialchars($p['title']) ?></div>
                                        <div class="text-muted small" style="font-size: 0.72rem;"><?= htmlspecialchars($p['category_name']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-semibold"><?= htmlspecialchars($p['user_name']) ?></div>
                                <div class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars($p['user_phone']) ?></div>
                            </td>
                            <td class="small fw-bold text-primary">
                                <?= View::formatPrice($p['price'], $p['currency']) ?>
                            </td>
                            <td class="small text-muted">
                                <?= htmlspecialchars($p['governorate_name']) ?>
                            </td>
                            <td>
                                <span class="badge <?= $st['class'] ?> small"><?= $st['label'] ?></span>
                            </td>
                            <td class="small text-muted"><?= number_format($p['views_count']) ?></td>
                            <td>
                                <form action="<?= App::baseUrl('admin/properties/featured/' . $p['id']) ?>" method="POST">
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="btn btn-sm <?= $p['is_featured'] ? 'btn-warning text-dark' : 'btn-outline-secondary' ?> py-0 px-2" title="تبديل التمييز">
                                        <i class="bi bi-star-fill"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?= App::baseUrl('property/' . $p['id']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" target="_blank" title="عرض">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= App::baseUrl('dashboard/properties/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-secondary py-0 px-2" title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">لا توجد عقارات مطابقة.</div>
    <?php endif; ?>
</div>
