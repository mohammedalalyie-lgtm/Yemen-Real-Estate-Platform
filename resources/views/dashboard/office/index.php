<?php
use App\Config\App;
use App\Helpers\View;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">إدارة المكتب العقاري وفريق الوسطاء</h4>
            <div class="text-muted small">متابعة السماسرة والوسطاء التابعين للمكتب وإعلاناتهم</div>
        </div>
    </div>

    <?php if ($office): ?>
        <div class="p-3 rounded bg-light border mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="fw-bold text-navy mb-1"><?= htmlspecialchars($office['name']) ?></h5>
                    <div class="text-muted small mb-2"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($office['address'] ?? 'اليمن') ?></div>
                    <div class="small">رقم الترخيص: <strong><?= htmlspecialchars($office['license_number'] ?? 'معتمد') ?></strong></div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="<?= App::baseUrl('office/' . $office['id']) ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i> عرض صفحة المكتب العامة
                    </a>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3"><i class="bi bi-people text-primary me-2"></i> الوسطاء التابعون للمكتب (<?= count($office['brokers'] ?? []) ?>)</h5>
        <?php if (!empty($office['brokers'])): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light small">
                        <tr>
                            <th>الاسم</th>
                            <th>المسمى داخل المكتب</th>
                            <th>رقم الهاتف</th>
                            <th>عدد العقارات</th>
                            <th>تاريخ الانضمام</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($office['brokers'] as $b): ?>
                            <tr>
                                <td class="fw-bold text-navy"><?= htmlspecialchars($b['full_name']) ?></td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($b['role_title']) ?></span></td>
                                <td class="small"><?= htmlspecialchars($b['phone']) ?></td>
                                <td class="fw-bold text-primary"><?= number_format($b['properties_count']) ?></td>
                                <td class="small text-muted"><?= date('Y-m-d', strtotime($b['joined_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info py-3">
                لا يوجد وسطاء تابعون للمكتب حالياً. يمكنك دعوة السماسرة للانضمام إلى فريق مكتبك.
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-4 text-muted">
            يرجى التواصل مع الإدارة لإتمام ربط وتفعيل بيانات مكتبك العقاري.
        </div>
    <?php endif; ?>
</div>
