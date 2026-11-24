<?php
use App\Config\App;
use App\Helpers\View;
use App\Helpers\Csrf;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">مراجعة وتدقيق الإعلانات بانتظار الاعتماد</h4>
            <div class="text-muted small">التحقق من صحة بيانات الإعلانات ومطابقتها للمعايير قبل نشرها للعامة</div>
        </div>
        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
            <?= count($properties) ?> إعلانات بانتظار القرار
        </span>
    </div>

    <?php if (!empty($properties)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>العقار والموقع</th>
                        <th>المعلن</th>
                        <th>السعر والمساحة</th>
                        <th>تاريخ الإضافة</th>
                        <th>القرار الإداري</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($properties as $prop): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php $imgSrc = !empty($prop['primary_image']) ? App::upload('properties/' . $prop['primary_image']) : App::asset('images/placeholder.svg'); ?>
                                    <img src="<?= $imgSrc ?>" class="rounded border" style="width: 70px; height: 50px; object-fit: cover;" alt="">
                                    <div>
                                        <div class="fw-bold text-navy"><?= htmlspecialchars($prop['title']) ?></div>
                                        <div class="text-muted small">
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($prop['category_name']) ?></span>
                                            <?= htmlspecialchars($prop['governorate_name']) ?> - <?= htmlspecialchars($prop['district_name']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold small"><?= htmlspecialchars($prop['user_name']) ?></div>
                                <div class="text-muted small"><i class="bi bi-telephone"></i> <?= htmlspecialchars($prop['user_phone']) ?></div>
                                <span class="badge bg-secondary-subtle text-dark border small"><?= $prop['user_role'] ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-primary small">
                                    <?= View::formatPrice($prop['price'], $prop['currency'], $prop['price_type']) ?>
                                </div>
                                <div class="text-muted small">
                                    <?= View::formatArea($prop['area_size'], $prop['area_unit']) ?>
                                </div>
                            </td>
                            <td class="small text-muted">
                                <?= View::timeAgo($prop['created_at']) ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <!-- View details -->
                                    <a href="<?= App::baseUrl('property/' . $prop['id']) ?>" class="btn btn-sm btn-outline-primary" target="_blank" title="معاينة الإعلان">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- Approve Form -->
                                    <form action="<?= App::baseUrl('admin/properties/approve/' . $prop['id']) ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من اعتماد ونشر هذا الإعلان للعامة؟');">
                                        <?= Csrf::field() ?>
                                        <button type="submit" class="btn btn-sm btn-success fw-bold" title="موافقة واعتماد">
                                            <i class="bi bi-check2"></i> اعتماد
                                        </button>
                                    </form>

                                    <!-- Request Revision Button -->
                                    <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold" data-bs-toggle="modal" data-bs-target="#revisionModal_<?= $prop['id'] ?>" title="طلب تعديل">
                                        <i class="bi bi-pencil-square"></i> طلب تعديل
                                    </button>

                                    <!-- Reject Button -->
                                    <button type="button" class="btn btn-sm btn-outline-danger fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal_<?= $prop['id'] ?>" title="رفض الإعلان">
                                        <i class="bi bi-x-circle"></i> رفض
                                    </button>
                                </div>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal_<?= $prop['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-danger">رفض إعلان العقار</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                            </div>
                                            <form action="<?= App::baseUrl('admin/properties/reject/' . $prop['id']) ?>" method="POST">
                                                <?= Csrf::field() ?>
                                                <div class="modal-body">
                                                    <p class="small text-muted mb-2">يرجى كتابة سبب رفض الإعلان ليصل في إشعار واضح إلى المعلن:</p>
                                                    <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="مثال: الصور غير واضحة، السعر غير واقعي، أو البيانات غير مطابقة..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-danger btn-sm fw-bold">تأكيد الرفض</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Revision Modal -->
                                <div class="modal fade" id="revisionModal_<?= $prop['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold text-warning">طلب تعديل على الإعلان</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                                            </div>
                                            <form action="<?= App::baseUrl('admin/properties/revision/' . $prop['id']) ?>" method="POST">
                                                <?= Csrf::field() ?>
                                                <div class="modal-body">
                                                    <p class="small text-muted mb-2">حدد للمعلن التعديلات المطلوبة بدقة:</p>
                                                    <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="مثال: يرجى إضافة صورة لواجهة العمارة وتوضيح اتجاه الشارع..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-warning btn-sm fw-bold">إرسال طلب التعديل</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-check2-all text-success fs-1 mb-2"></i>
            <h5>قائمة المراجعة فارغة حالياً!</h5>
            <p class="small">تمت مراجعة واعتماد جميع الإعلانات الواردة بنجاح.</p>
        </div>
    <?php endif; ?>
</div>
