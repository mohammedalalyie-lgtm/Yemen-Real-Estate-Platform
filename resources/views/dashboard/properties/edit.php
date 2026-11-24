<?php
use App\Config\App;
use App\Helpers\Csrf;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">تعديل بيانات العقار</h4>
            <div class="text-muted small">تحديث بيانات الإعلان أو تصحيح الملاحظات وإعادة إرساله للاعتماد</div>
        </div>
        <a href="<?= App::baseUrl('dashboard/properties') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i> العودة
        </a>
    </div>

    <?php if (in_array($property['status'], ['rejected', 'revision_required']) && !empty($property['rejection_reason'])): ?>
        <div class="alert alert-warning p-3 mb-4">
            <div class="fw-bold text-dark"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> ملاحظة الإدارة:</div>
            <div class="mt-1"><?= htmlspecialchars($property['rejection_reason']) ?></div>
            <div class="small text-muted mt-2">عند حفظ التعديلات سيتم إرسال الإعلان تلقائياً للمراجعة مرة أخرى دون الحاجة لإنشاء إعلان جديد.</div>
        </div>
    <?php endif; ?>

    <form action="<?= App::baseUrl('dashboard/properties/update/' . $property['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= Csrf::field() ?>

        <div class="mb-3">
            <label class="form-label fw-bold small">عنوان الإعلان <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($property['title']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold small">الوصف الكامل <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($property['description']) ?></textarea>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold small">نوع العملية <span class="text-danger">*</span></label>
                <select name="purpose" class="form-select">
                    <option value="sale" <?= $property['purpose'] === 'sale' ? 'selected' : '' ?>>للبيع</option>
                    <option value="rent" <?= $property['purpose'] === 'rent' ? 'selected' : '' ?>>للإيجار</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">التصنيف <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $property['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name_ar']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">طريقة دفع السعر</label>
                <select name="price_type" class="form-select">
                    <option value="total" <?= ($property['price_type'] ?? '') === 'total' ? 'selected' : '' ?>>سعر إجمالي</option>
                    <option value="per_month" <?= ($property['price_type'] ?? '') === 'per_month' ? 'selected' : '' ?>>شهرياً</option>
                    <option value="per_year" <?= ($property['price_type'] ?? '') === 'per_year' ? 'selected' : '' ?>>سنوياً</option>
                    <option value="per_labna" <?= ($property['price_type'] ?? '') === 'per_labna' ? 'selected' : '' ?>>للبنة</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold small">المحافظة <span class="text-danger">*</span></label>
                <select name="governorate_id" id="governorate_id" class="form-select" required>
                    <?php foreach ($governorates as $gov): ?>
                        <option value="<?= $gov['id'] ?>" <?= $property['governorate_id'] == $gov['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($gov['name_ar']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">المديرية <span class="text-danger">*</span></label>
                <select name="district_id" id="district_id" class="form-select" required>
                    <?php foreach ($districts as $dist): ?>
                        <option value="<?= $dist['id'] ?>" <?= $property['district_id'] == $dist['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dist['name_ar']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small">المنطقة</label>
                <select name="area_id" id="area_id" class="form-select">
                    <option value="">اختر المنطقة</option>
                    <?php foreach ($areas as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= ($property['area_id'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['name_ar']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold small">العنوان والشارع</label>
            <input type="text" name="address_details" class="form-control" value="<?= htmlspecialchars($property['address_details'] ?? '') ?>">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold small">السعر <span class="text-danger">*</span></label>
                <input type="number" step="any" name="price" class="form-control" required value="<?= $property['price'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">العملة <span class="text-danger">*</span></label>
                <select name="currency" class="form-select">
                    <option value="YER" <?= $property['currency'] === 'YER' ? 'selected' : '' ?>>ريال يمني (YER)</option>
                    <option value="USD" <?= $property['currency'] === 'USD' ? 'selected' : '' ?>>دولار أمريكي (USD)</option>
                    <option value="SAR" <?= $property['currency'] === 'SAR' ? 'selected' : '' ?>>ريال سعودي (SAR)</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold small">المساحة <span class="text-danger">*</span></label>
                <input type="number" step="any" name="area_size" class="form-control" required value="<?= $property['area_size'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">وحدة المساحة <span class="text-danger">*</span></label>
                <select name="area_unit" class="form-select">
                    <option value="sqm" <?= $property['area_unit'] === 'sqm' ? 'selected' : '' ?>>متر مربع (م²)</option>
                    <option value="labna" <?= $property['area_unit'] === 'labna' ? 'selected' : '' ?>>لبنة صنعانية حر</option>
                    <option value="qasaba" <?= $property['area_unit'] === 'qasaba' ? 'selected' : '' ?>>قصبة</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <label class="form-label fw-bold small">غرف النوم</label>
                <input type="number" name="bedrooms" class="form-control" value="<?= $property['bedrooms'] ?>">
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label fw-bold small">دورات المياه</label>
                <input type="number" name="bathrooms" class="form-control" value="<?= $property['bathrooms'] ?>">
            </div>
        </div>

        <!-- Current Images & Upload Additional -->
        <div class="mb-4">
            <label class="form-label fw-bold small">الصور الحالية</label>
            <div class="d-flex gap-2 flex-wrap mb-2">
                <?php foreach ($property['images'] as $img): ?>
                    <img src="<?= App::upload('properties/' . $img['image_path']) ?>" class="rounded border" style="width: 80px; height: 60px; object-fit: cover;">
                <?php endforeach; ?>
            </div>
            <label class="form-label fw-bold small">إضافة صور جديدة</label>
            <input type="file" name="images[]" class="form-control" multiple accept="image/jpeg,image/png,image/webp">
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="<?= App::baseUrl('dashboard/properties') ?>" class="btn btn-secondary px-3">إلغاء</a>
            <button type="submit" class="btn btn-primary px-4 fw-bold">حفظ التعديلات وإعادة الإرسال</button>
        </div>
    </form>
</div>
