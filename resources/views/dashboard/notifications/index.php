<?php
use App\Config\App;
use App\Helpers\View;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">مركز الإشعارات</h4>
            <div class="text-muted small">تنبيهات مراجعة العقارات، الاستفسارات، وقرارات الإدارة</div>
        </div>
    </div>

    <?php if (!empty($notifications)): ?>
        <div class="list-group list-group-flush">
            <?php foreach ($notifications as $n): ?>
                <div class="list-group-item list-group-item-action p-3 d-flex align-items-start gap-3 <?= $n['is_read'] ? '' : 'bg-light' ?>">
                    <div class="p-2 rounded-circle bg-primary-subtle text-primary">
                        <i class="bi bi-bell-fill fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold mb-0 text-navy"><?= htmlspecialchars($n['title']) ?></h6>
                            <span class="text-muted small" style="font-size:0.75rem;"><?= View::timeAgo($n['created_at']) ?></span>
                        </div>
                        <p class="text-muted small mb-2"><?= htmlspecialchars($n['message']) ?></p>
                        <?php if (!empty($n['link'])): ?>
                            <a href="<?= App::baseUrl(ltrim($n['link'], '/')) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:0.8rem;">
                                عرض التفاصيل <i class="bi bi-arrow-left"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash fs-1 mb-2"></i>
            <h5>لا توجد إشعارات حالياً</h5>
        </div>
    <?php endif; ?>
</div>
