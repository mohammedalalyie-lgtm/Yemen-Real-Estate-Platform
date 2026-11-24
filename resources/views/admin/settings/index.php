<?php
use App\Config\App;
use App\Helpers\Csrf;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">إعدادات المنصة العامة</h4>
            <div class="text-muted small">تخصيص هوية المنصة، بيانات التواصل، وإعدادات المساعد العقاري</div>
        </div>
    </div>

    <form action="<?= App::baseUrl('admin/settings/update') ?>" method="POST">
        <?= Csrf::field() ?>

        <div class="row g-4">
            <!-- General Settings -->
            <div class="col-lg-6">
                <div class="p-3 rounded border bg-light">
                    <h5 class="fw-bold mb-3 text-navy"><i class="bi bi-info-circle me-1"></i> الإعدادات الأساسية</h5>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">اسم المنصة</label>
                        <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($settings['general']['site_name'] ?? 'عقار اليمن') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">الشعار التسويقي (Slogan)</label>
                        <input type="text" name="site_slogan" class="form-control" value="<?= htmlspecialchars($settings['general']['site_slogan'] ?? 'عقار اليمن... بيتك يبدأ من هنا') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">وصف المنصة</label>
                        <textarea name="site_description" class="form-control" rows="3"><?= htmlspecialchars($settings['general']['site_description'] ?? 'منصتك العقارية الشاملة للبيع والإيجار والاستثمار في اليمن') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Contact Settings -->
            <div class="col-lg-6">
                <div class="p-3 rounded border bg-light">
                    <h5 class="fw-bold mb-3 text-navy"><i class="bi bi-telephone me-1"></i> معلومات الاتصال والدعم الفني</h5>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">رقم الهاتف الرسمي</label>
                        <input type="text" name="site_phone" class="form-control" value="<?= htmlspecialchars($settings['contact']['site_phone'] ?? '+967 770 000 001') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">رقم الواتساب المباشر</label>
                        <input type="text" name="site_whatsapp" class="form-control" value="<?= htmlspecialchars($settings['contact']['site_whatsapp'] ?? '+967770000001') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">البريد الإلكتروني الرسمي</label>
                        <input type="email" name="site_email" class="form-control" value="<?= htmlspecialchars($settings['contact']['site_email'] ?? 'info@aqar-yemen.com') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">العنوان الرئيسي</label>
                        <input type="text" name="site_address" class="form-control" value="<?= htmlspecialchars($settings['contact']['site_address'] ?? 'اليمن - صنعاء - شارع حدة') ?>">
                    </div>
                </div>
            </div>

            <!-- Smart Assistant Settings -->
            <div class="col-12">
                <div class="p-3 rounded border bg-light">
                    <h5 class="fw-bold mb-3 text-navy"><i class="bi bi-robot text-primary me-1"></i> إعدادات «مساعد عقار اليمن الذكي»</h5>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">اسم المساعد</label>
                            <input type="text" name="chatbot_name" class="form-control" value="<?= htmlspecialchars($settings['ai']['chatbot_name'] ?? 'مساعد عقار اليمن الذكي') ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">رسالة الترحيب الأولى</label>
                            <input type="text" name="chatbot_welcome" class="form-control" value="<?= htmlspecialchars($settings['ai']['chatbot_welcome'] ?? 'مرحباً بك في عقار اليمن! أنا مساعدك العقاري الذكي...') ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary fw-bold px-4">
                <i class="bi bi-save me-1"></i> حفظ جميع الإعدادات
            </button>
        </div>
    </form>
</div>
