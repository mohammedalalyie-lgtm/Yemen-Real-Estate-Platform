<?php
use App\Config\App;
use App\Helpers\View;
?>

<!-- KPI Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="kpi-card">
            <div>
                <div class="text-muted small fw-semibold">إجمالي العقارات</div>
                <div class="fs-3 fw-bold text-navy mt-1"><?= number_format($stats['total']) ?></div>
            </div>
            <div class="kpi-icon-box bg-primary-subtle text-primary">
                <i class="bi bi-buildings"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="kpi-card border-warning">
            <div>
                <div class="text-warning small fw-bold">بانتظار المراجعة</div>
                <div class="fs-3 fw-bold text-warning mt-1"><?= number_format($stats['pending']) ?></div>
            </div>
            <div class="kpi-icon-box bg-warning-subtle text-warning">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="kpi-card">
            <div>
                <div class="text-muted small fw-semibold">العقارات المنشورة</div>
                <div class="fs-3 fw-bold text-success mt-1"><?= number_format($stats['approved']) ?></div>
            </div>
            <div class="kpi-icon-box bg-success-subtle text-success">
                <i class="bi bi-check2-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="kpi-card <?= $pendingReportsCount > 0 ? 'border-danger' : '' ?>">
            <div>
                <div class="text-danger small fw-bold">البلاغات المعلقة</div>
                <div class="fs-3 fw-bold text-danger mt-1"><?= number_format($pendingReportsCount) ?></div>
            </div>
            <div class="kpi-icon-box bg-danger-subtle text-danger">
                <i class="bi bi-shield-exclamation"></i>
            </div>
        </div>
    </div>
</div>

<!-- User Roles Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm p-3 bg-white text-center">
            <div class="text-muted small">الملاك (Owners)</div>
            <div class="fs-4 fw-bold text-primary mt-1"><?= number_format($userCounts['owner'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm p-3 bg-white text-center">
            <div class="text-muted small">الوسطاء (Brokers)</div>
            <div class="fs-4 fw-bold text-navy mt-1"><?= number_format($userCounts['broker'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm p-3 bg-white text-center">
            <div class="text-muted small">المكاتب (Offices)</div>
            <div class="fs-4 fw-bold text-warning mt-1"><?= number_format($userCounts['office'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm p-3 bg-white text-center">
            <div class="text-muted small">العملاء والباحثون</div>
            <div class="fs-4 fw-bold text-success mt-1"><?= number_format($userCounts['customer'] ?? 0) ?></div>
        </div>
    </div>
</div>

<!-- Interactive Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-line text-primary me-2"></i> توزيع العقارات حسب المحافظات</h6>
            <div style="height: 270px;">
                <canvas id="govChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart text-warning me-2"></i> توزيع العقارات حسب التصنيف</h6>
            <div style="height: 270px;">
                <canvas id="catChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities Table -->
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold m-0"><i class="bi bi-journal-text text-primary me-2"></i> آخر العمليات الإدارية والنشاطات</h6>
        <a href="<?= App::baseUrl('admin/logs') ?>" class="small text-primary text-decoration-none">عرض سجل التدقيق الكامل</a>
    </div>

    <?php if (!empty($recentActivities)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>المستخدم</th>
                        <th>العملية</th>
                        <th>التفاصيل</th>
                        <th>عنوان IP</th>
                        <th>الوقت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentActivities as $log): ?>
                        <tr>
                            <td class="fw-bold small text-navy">
                                <?= htmlspecialchars($log['user_name'] ?? 'زائر / مجهول') ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark border small"><?= htmlspecialchars($log['action']) ?></span>
                            </td>
                            <td class="small text-muted text-truncate" style="max-width: 250px;">
                                <?= htmlspecialchars($log['details'] ?? '') ?>
                            </td>
                            <td class="small text-muted" dir="ltr"><?= htmlspecialchars($log['ip_address']) ?></td>
                            <td class="small text-muted"><?= View::timeAgo($log['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-4 text-muted small">لا توجد عمليات مسجلة حديثاً.</div>
    <?php endif; ?>
</div>

<!-- Chart initialization script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Governorates Chart
    const govData = <?= json_encode($govDistribution) ?>;
    const govLabels = govData.map(item => item.name_ar);
    const govCounts = govData.map(item => Number(item.count));

    new Chart(document.getElementById('govChart'), {
        type: 'bar',
        data: {
            labels: govLabels,
            datasets: [{
                label: 'عدد العقارات',
                data: govCounts,
                backgroundColor: '#2563EB',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // 2. Categories Chart
    const catData = <?= json_encode($catDistribution) ?>;
    const catLabels = catData.map(item => item.name_ar);
    const catCounts = catData.map(item => Number(item.count));

    new Chart(document.getElementById('catChart'), {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                data: catCounts,
                backgroundColor: ['#2563EB', '#C8A96B', '#10B981', '#06B6D4', '#F59E0B', '#64748B']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
