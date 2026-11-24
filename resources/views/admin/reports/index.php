<?php
use App\Config\App;
use App\Helpers\View;
use App\Helpers\Csrf;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">إدارة البلاغات ومكافحة الاحتيال</h4>
            <div class="text-muted small">التحقق من الإعلانات المشبوهة أو المخالفة واتخاذ الإجراءات الرادعة</div>
        </div>
        <span class="badge bg-danger fs-6 px-3 py-2">
            <?= count($reports) ?> بلاغات بانتظار المراجعة
        </span>
    </div>

    <?php if (!empty($reports)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>العقار المبلّغ عنه</th>
                        <th>صاحب الإعلان</th>
                        <th>المبلّغ</th>
                        <th>سبب البلاغ</th>
                        <th>التفاصيل</th>
                        <th>التاريخ</th>
                        <th>الإجراء الإداري</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $r): ?>
                        <tr>
                            <td>
                                <a href="<?= App::baseUrl('property/' . $r['property_id']) ?>" class="fw-bold text-navy text-decoration-none" target="_blank">
                                    <?= htmlspecialchars($r['property_title']) ?> <i class="bi bi-box-arrow-up-right small"></i>
                                </a>
                            </td>
                            <td>
                                <div class="small fw-semibold"><?= htmlspecialchars($r['owner_name']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($r['owner_phone']) ?></div>
                            </td>
                            <td>
                                <div class="small"><?= htmlspecialchars($r['reporter_name'] ?? 'زائر') ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($r['reporter_phone'] ?? '-') ?></div>
                            </td>
                            <td>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">
                                    <?= match($r['reason']) {
                                        'incorrect_info' => 'معلومات غير صحيحة',
                                        'misleading_price' => 'سعر مضلل',
                                        'fake_images' => 'صور وهمية',
                                        'duplicate_ad' => 'إعلان مكرر',
                                        'invalid_phone' => 'رقم هاتف خاطئ',
                                        'suspicious_ad' => 'إعلان مشبوه',
                                        default => 'أخرى'
                                    } ?>
                                </span>
                            </td>
                            <td class="small text-muted" style="max-width: 250px;">
                                <?= htmlspecialchars($r['details'] ?? '') ?>
                            </td>
                            <td class="small text-muted"><?= View::timeAgo($r['created_at']) ?></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <!-- Suspend Property -->
                                    <form action="<?= App::baseUrl('admin/reports/resolve/' . $r['id']) ?>" method="POST" onsubmit="return confirm('هل تريد إيقاف هذا العقار فوراً وسحب الإعلان؟');">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="action" value="suspend_property">
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold" title="إيقاف العقار المخالف">
                                            <i class="bi bi-slash-circle"></i> إيقاف العقار
                                        </button>
                                    </form>

                                    <!-- Dismiss Report -->
                                    <form action="<?= App::baseUrl('admin/reports/resolve/' . $r['id']) ?>" method="POST" onsubmit="return confirm('تأكيد حفظ وتجاهل البلاغ لعدم وجود مخالفة؟');">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="action" value="dismiss">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="رفض وتجاهل البلاغ">
                                            <i class="bi bi-check2"></i> سليم
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-shield-check text-success fs-1 mb-2"></i>
            <h5>لا توجد بلاغات معلقة</h5>
            <p class="small">جميع الإعلانات تعمل بانتظام دون مخالفات مسجلة.</p>
        </div>
    <?php endif; ?>
</div>
