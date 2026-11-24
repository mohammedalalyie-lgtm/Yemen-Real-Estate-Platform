<?php
use App\Config\App;
use App\Helpers\View;
use App\Helpers\Csrf;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1">إدارة عقاراتي</h4>
            <div class="text-muted small">متابعة حالة الإعلانات، المشاهدات، وإجراءات البيع والتأجير</div>
        </div>
        <a href="<?= App::baseUrl('dashboard/properties/create') ?>" class="btn btn-primary fw-bold">
            <i class="bi bi-plus-circle me-1"></i> إضافة عقار جديد
        </a>
    </div>

    <!-- Status Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $currentStatus === 'all' ? 'active fw-bold' : '' ?>" href="<?= App::baseUrl('dashboard/properties') ?>">الكل</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentStatus === 'approved' ? 'active fw-bold' : '' ?>" href="<?= App::baseUrl('dashboard/properties?status=approved') ?>">منشور ومتاح</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentStatus === 'pending' ? 'active fw-bold' : '' ?>" href="<?= App::baseUrl('dashboard/properties?status=pending') ?>">بانتظار المراجعة</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentStatus === 'rejected' ? 'active fw-bold' : '' ?>" href="<?= App::baseUrl('dashboard/properties?status=rejected') ?>">مرفوض</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentStatus === 'reserved' ? 'active fw-bold' : '' ?>" href="<?= App::baseUrl('dashboard/properties?status=reserved') ?>">محجوز</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentStatus === 'sold' ? 'active fw-bold' : '' ?>" href="<?= App::baseUrl('dashboard/properties?status=sold') ?>">تم البيع</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $currentStatus === 'rented' ? 'active fw-bold' : '' ?>" href="<?= App::baseUrl('dashboard/properties?status=rented') ?>">تم التأجير</a>
        </li>
    </ul>

    <!-- Properties Table -->
    <?php if (!empty($properties)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>الصورة والعقار</th>
                        <th>السعر</th>
                        <th>الموقع</th>
                        <th>العملية</th>
                        <th>الحالة</th>
                        <th>المشاهدات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $prop): ?>
                        <?php $statusInfo = View::formatStatus($prop['status']); ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php $imgSrc = !empty($prop['primary_image']) ? App::upload('properties/' . $prop['primary_image']) : App::asset('images/placeholder.svg'); ?>
                                    <img src="<?= $imgSrc ?>" class="rounded" style="width: 56px; height: 44px; object-fit: cover;" alt="">
                                    <div>
                                        <div class="fw-bold text-navy"><?= htmlspecialchars($prop['title']) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($prop['category_name']) ?></div>
                                        <?php if (!empty($prop['rejection_reason']) && in_array($prop['status'], ['rejected', 'revision_required'])): ?>
                                            <div class="text-danger small mt-1">
                                                <i class="bi bi-info-circle"></i> سبب الرفض/التعديل: <?= htmlspecialchars($prop['rejection_reason']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-bold text-primary">
                                <?= View::formatPrice($prop['price'], $prop['currency'], $prop['price_type']) ?>
                            </td>
                            <td class="small text-muted">
                                <?= htmlspecialchars($prop['governorate_name']) ?> - <?= htmlspecialchars($prop['district_name']) ?>
                            </td>
                            <td>
                                <span class="badge <?= $prop['purpose'] === 'sale' ? 'bg-primary' : 'bg-warning text-dark' ?>">
                                    <?= View::formatPurpose($prop['purpose']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $statusInfo['class'] ?>"><?= $statusInfo['label'] ?></span>
                            </td>
                            <td class="small fw-semibold">
                                <i class="bi bi-eye"></i> <?= number_format($prop['views_count']) ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= App::baseUrl('property/' . $prop['id']) ?>" class="btn btn-outline-primary" title="معاينة" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= App::baseUrl('dashboard/properties/edit/' . $prop['id']) ?>" class="btn btn-outline-secondary" title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-dark dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="تحديث الحالة">
                                        <span class="visually-hidden">تحديث الحالة</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><h6 class="dropdown-header">تحديث حالة العقار</h6></li>
                                        <li>
                                            <form action="<?= App::baseUrl('dashboard/properties/status/' . $prop['id']) ?>" method="POST">
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="status" value="reserved">
                                                <button type="submit" class="dropdown-item"><i class="bi bi-bookmark text-warning me-2"></i> تحديد كمحجوز</button>
                                            </form>
                                        </li>
                                        <?php if ($prop['purpose'] === 'sale'): ?>
                                            <li>
                                                <form action="<?= App::baseUrl('dashboard/properties/status/' . $prop['id']) ?>" method="POST">
                                                    <?= Csrf::field() ?>
                                                    <input type="hidden" name="status" value="sold">
                                                    <button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i> تم البيع</button>
                                                </form>
                                            </li>
                                        <?php else: ?>
                                            <li>
                                                <form action="<?= App::baseUrl('dashboard/properties/status/' . $prop['id']) ?>" method="POST">
                                                    <?= Csrf::field() ?>
                                                    <input type="hidden" name="status" value="rented">
                                                    <button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i> تم التأجير</button>
                                                </form>
                                            </li>
                                        <?php endif; ?>
                                        <li>
                                            <form action="<?= App::baseUrl('dashboard/properties/status/' . $prop['id']) ?>" method="POST">
                                                <?= Csrf::field() ?>
                                                <input type="hidden" name="status" value="suspended">
                                                <button type="submit" class="dropdown-item text-secondary"><i class="bi bi-pause-circle me-2"></i> إيقاف الإعلان مؤقتاً</button>
                                            </form>
                                        </li>
                                        <?php if ($prop['status'] !== 'approved'): ?>
                                            <li>
                                                <form action="<?= App::baseUrl('dashboard/properties/status/' . $prop['id']) ?>" method="POST">
                                                    <?= Csrf::field() ?>
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="dropdown-item text-primary"><i class="bi bi-play-circle me-2"></i> إعادة النشر كمتاح</button>
                                                </form>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-houses fs-1 mb-2"></i>
            <h5>لا توجد عقارات في هذا القسم</h5>
            <p class="small">يمكنك إضافة عقارك الجديد والبدء في تلقي طلبات المشترين والمستأجرين.</p>
            <a href="<?= App::baseUrl('dashboard/properties/create') ?>" class="btn btn-primary mt-2">
                <i class="bi bi-plus-circle me-1"></i> إضافة عقار الآن
            </a>
        </div>
    <?php endif; ?>
</div>
