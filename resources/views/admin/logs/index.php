<?php
use App\Config\App;
use App\Helpers\View;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">سجل العمليات والتدقيق الأمني (Audit Logs)</h4>
            <div class="text-muted small">تسجيل وتتبع كافة الإجراءات الإدارية وتغييرات الحالة وتسجيلات الدخول</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>#</th>
                    <th>المستخدم</th>
                    <th>العملية (Action)</th>
                    <th>الكيان (Entity)</th>
                    <th>التفاصيل</th>
                    <th>عنوان IP</th>
                    <th>الوقت</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td class="small text-muted"><?= $l['id'] ?></td>
                        <td>
                            <div class="fw-bold small text-navy"><?= htmlspecialchars($l['user_name'] ?? 'زائر / نظام') ?></div>
                            <div class="text-muted" style="font-size:0.72rem;"><?= htmlspecialchars($l['user_email'] ?? '') ?></div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-dark border small"><?= htmlspecialchars($l['action']) ?></span>
                        </td>
                        <td class="small text-muted"><?= htmlspecialchars($l['entity_type']) ?> #<?= $l['entity_id'] ?? '-' ?></td>
                        <td class="small text-muted" style="max-width: 250px;">
                            <?= htmlspecialchars($l['details'] ?? '') ?>
                        </td>
                        <td class="small text-muted" dir="ltr"><?= htmlspecialchars($l['ip_address']) ?></td>
                        <td class="small text-muted"><?= View::timeAgo($l['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
