<?php
use App\Config\App;
use App\Helpers\Session;
use App\Helpers\Csrf;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1">إدارة المستخدمين والصلاحيات</h4>
            <div class="text-muted small">التحكم في أدوار المستخدمين وتفعيل أو تجميد الحسابات</div>
        </div>
    </div>

    <!-- Role Filter Tabs -->
    <div class="mb-4 d-flex gap-2 flex-wrap">
        <a href="<?= App::baseUrl('admin/users') ?>" class="btn btn-sm <?= empty($selectedRole) ? 'btn-primary' : 'btn-outline-secondary' ?>">الكل</a>
        <a href="<?= App::baseUrl('admin/users?role=owner') ?>" class="btn btn-sm <?= $selectedRole === 'owner' ? 'btn-primary' : 'btn-outline-secondary' ?>">الملاك</a>
        <a href="<?= App::baseUrl('admin/users?role=broker') ?>" class="btn btn-sm <?= $selectedRole === 'broker' ? 'btn-primary' : 'btn-outline-secondary' ?>">الوسطاء</a>
        <a href="<?= App::baseUrl('admin/users?role=office') ?>" class="btn btn-sm <?= $selectedRole === 'office' ? 'btn-primary' : 'btn-outline-secondary' ?>">المكاتب العقارية</a>
        <a href="<?= App::baseUrl('admin/users?role=customer') ?>" class="btn btn-sm <?= $selectedRole === 'customer' ? 'btn-primary' : 'btn-outline-secondary' ?>">العملاء والباحثون</a>
        <a href="<?= App::baseUrl('admin/users?role=admin') ?>" class="btn btn-sm <?= $selectedRole === 'admin' ? 'btn-primary' : 'btn-outline-secondary' ?>">المديرون</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th>المستخدم</th>
                    <th>رقم الهاتف</th>
                    <th>الدور والصلاحية</th>
                    <th>الحالة</th>
                    <th>العقارات</th>
                    <th>تاريخ التسجيل</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-navy"><?= htmlspecialchars($u['full_name']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($u['email']) ?></div>
                        </td>
                        <td class="small" dir="ltr"><?= htmlspecialchars($u['phone']) ?></td>
                        <td>
                            <span class="badge <?= match($u['role']) {
                                'super_admin' => 'bg-danger',
                                'admin' => 'bg-primary',
                                'office' => 'bg-warning text-dark',
                                'broker' => 'bg-info text-dark',
                                'owner' => 'bg-secondary',
                                default => 'bg-light text-dark border'
                            } ?> small">
                                <?= match($u['role']) {
                                    'super_admin' => 'المالك الرئيسي Super Admin',
                                    'admin' => 'مدير النظام Admin',
                                    'office' => 'مكتب عقاري',
                                    'broker' => 'وسيط عقاري',
                                    'owner' => 'مالك عقار',
                                    default => 'عميل / باحث'
                                } ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['is_active']): ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle small">نشط</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">معطل</span>
                            <?php endif; ?>
                        </td>
                        <td class="small fw-semibold"><?= number_format($u['properties_count'] ?? 0) ?></td>
                        <td class="small text-muted"><?= date('Y-m-d', strtotime($u['created_at'])) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <!-- Toggle Active -->
                                <?php if ($u['id'] != 1): ?>
                                    <form action="<?= App::baseUrl('admin/users/toggle/' . $u['id']) ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من تغيير حالة حساب هذا المستخدم؟');">
                                        <?= Csrf::field() ?>
                                        <button type="submit" class="btn btn-sm <?= $u['is_active'] ? 'btn-outline-danger' : 'btn-outline-success' ?> py-0 px-2" title="<?= $u['is_active'] ? 'تعطيل الحساب' : 'تفعيل الحساب' ?>">
                                            <i class="bi <?= $u['is_active'] ? 'bi-person-slash' : 'bi-person-check' ?>"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Change Role (Super Admin only) -->
                                <?php if (Session::isSuperAdmin() && $u['id'] != 1): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#roleModal_<?= $u['id'] ?>" title="تعديل الدور والصلاحيات">
                                        <i class="bi bi-shield-shaded"></i>
                                    </button>

                                    <!-- Role Change Modal -->
                                    <div class="modal fade" id="roleModal_<?= $u['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">تعديل دور وصلاحية: <?= htmlspecialchars($u['full_name']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                                </div>
                                                <form action="<?= App::baseUrl('admin/users/role/' . $u['id']) ?>" method="POST">
                                                    <?= Csrf::field() ?>
                                                    <div class="modal-body text-start">
                                                        <label class="form-label small fw-bold">اختر الدور الجديد:</label>
                                                        <select name="role" class="form-select mb-3">
                                                            <option value="customer" <?= $u['role'] === 'customer' ? 'selected' : '' ?>>عميل / باحث (Customer)</option>
                                                            <option value="owner" <?= $u['role'] === 'owner' ? 'selected' : '' ?>>مالك عقار (Owner)</option>
                                                            <option value="broker" <?= $u['role'] === 'broker' ? 'selected' : '' ?>>وسيط عقاري / سمسار (Broker)</option>
                                                            <option value="office" <?= $u['role'] === 'office' ? 'selected' : '' ?>>مكتب عقاري (Office)</option>
                                                            <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>مدير نظام (Admin)</option>
                                                            <option value="super_admin" <?= $u['role'] === 'super_admin' ? 'selected' : '' ?>>المالك الرئيسي (Super Admin)</option>
                                                        </select>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-primary btn-sm fw-bold">حفظ الصلاحية</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
