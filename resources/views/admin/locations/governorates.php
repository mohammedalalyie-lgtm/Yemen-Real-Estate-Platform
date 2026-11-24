<?php
use App\Config\App;
use App\Helpers\View;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">المحافظات اليمنية والتقسيم الجغرافي</h4>
            <div class="text-muted small">إدارة جميع محافظات الجمهورية اليمنية الـ 22 وعدد العقارات في كل محافظة</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>#</th>
                    <th>اسم المحافظة (بالعربي)</th>
                    <th>الاسم بالإنجليزية</th>
                    <th>الرمز التعريفي (Slug)</th>
                    <th>عدد العقارات المتاحة</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($governorates as $g): ?>
                    <tr>
                        <td class="small text-muted"><?= $g['id'] ?></td>
                        <td class="fw-bold text-navy"><?= htmlspecialchars($g['name_ar']) ?></td>
                        <td class="small text-muted" dir="ltr"><?= htmlspecialchars($g['name_en'] ?? '') ?></td>
                        <td class="small text-muted" dir="ltr"><?= htmlspecialchars($g['slug']) ?></td>
                        <td class="fw-bold text-primary"><?= number_format($g['properties_count'] ?? 0) ?></td>
                        <td>
                            <?php if ($g['is_active']): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle small">نشط</span>
                            <?php else: ?>
                                <span class="badge bg-secondary small">معطل</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
