<?php
use App\Config\App;
use App\Helpers\View;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">الاستفسارات والرسائل الواردة</h4>
            <div class="text-muted small">تواصل مع المشترين والمهتمين بعقاراتك المعروضة</div>
        </div>
    </div>

    <?php if (!empty($inquiries)): ?>
        <div class="d-flex flex-column gap-3">
            <?php foreach ($inquiries as $inq): ?>
                <div class="p-4 rounded border <?= $inq['is_read'] ? 'bg-white' : 'bg-light border-primary' ?> shadow-sm">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <div>
                            <span class="fw-bold fs-6 text-navy"><?= htmlspecialchars($inq['sender_name']) ?></span>
                            <?php if (!$inq['is_read']): ?>
                                <span class="badge bg-primary ms-2 small">جديد غير مقروء</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-clock"></i> <?= View::timeAgo($inq['created_at']) ?>
                        </div>
                    </div>

                    <div class="mb-2">
                        <span class="text-muted small">بخصوص الإعلان:</span>
                        <a href="<?= App::baseUrl('property/' . $inq['property_id']) ?>" class="fw-bold text-decoration-none text-primary" target="_blank">
                            <?= htmlspecialchars($inq['property_title']) ?> <i class="bi bi-box-arrow-up-right small"></i>
                        </a>
                    </div>

                    <div class="p-3 rounded bg-white border text-secondary mb-3" style="line-height: 1.6;">
                        <?= nl2br(htmlspecialchars($inq['message'])) ?>
                    </div>

                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <a href="tel:<?= htmlspecialchars($inq['sender_phone']) ?>" class="btn btn-sm btn-primary fw-bold">
                            <i class="bi bi-telephone me-1"></i> اتصال: <?= htmlspecialchars($inq['sender_phone']) ?>
                        </a>

                        <?php 
                            $cleanPhone = preg_replace('/[^0-9]/', '', $inq['sender_phone']);
                            $waText = urlencode("مرحباً أخي العزيز، بخصوص استفسارك في منصة عقار اليمن حول: " . $inq['property_title']);
                        ?>
                        <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $waText ?>" target="_blank" class="btn btn-sm btn-success fw-bold">
                            <i class="bi bi-whatsapp me-1"></i> محادثة واتساب
                        </a>

                        <?php if (!empty($inq['sender_email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($inq['sender_email']) ?>" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-envelope me-1"></i> إرسال بريد
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-left-dots fs-1 mb-2"></i>
            <h5>لا توجد استفسارات واردة بعد</h5>
            <p class="small">عندما يقوم الزوار بإرسال استفسار عبر صفحة عقارك ستظهر جميع رسائلهم وأرقام هواتفهم هنا مباشرة.</p>
        </div>
    <?php endif; ?>
</div>
