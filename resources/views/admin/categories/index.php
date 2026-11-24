<?php
use App\Config\App;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">تصنيفات العقارات</h4>
            <div class="text-muted small">إدارة أنواع العقارات السكنية والتجارية والأراضي</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>#</th>
                    <th>التصنيف بالعربي</th>
                    <th>الاسم بالإنجليزية</th>
                    <th>المجموعة</th>
                    <th>الرمز التعريفي</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td class="small text-muted"><?= $cat['id'] ?></td>
                        <td class="fw-bold text-navy"><?= htmlspecialchars($cat['name_ar']) ?></td>
                        <td class="small text-muted" dir="ltr"><?= htmlspecialchars($cat['name_en'] ?? '') ?></td>
                        <td>
                            <span class="badge <?= match($cat['type_group']) {
                                'residential' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                'commercial' => 'bg-warning-subtle text-dark border border-warning-subtle',
                                'land' => 'bg-success-subtle text-success border border-success-subtle',
                                default => 'bg-light text-dark'
                            } ?> small">
                                <?= match($cat['type_group']) {
                                    'residential' => 'سكني',
                                    'commercial' => 'تجاري',
                                    'land' => 'أراضي ومزارع',
                                    default => $cat['type_group']
                                } ?>
                            </span>
                        </td>
                        <td class="small text-muted" dir="ltr"><?= htmlspecialchars($cat['slug']) ?></td>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle small">نشط</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
