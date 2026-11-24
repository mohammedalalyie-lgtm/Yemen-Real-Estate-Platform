<?php
use App\Config\App;
use App\Helpers\Csrf;
?>

<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">إضافة عقار جديد</h4>
            <div class="text-muted small">اتبع الخطوات لإدخال بيانات عقارك بدقة لعرضه على آلاف الزوار</div>
        </div>
        <a href="<?= App::baseUrl('dashboard/properties') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-right"></i> العودة لقائمة عقاراتي
        </a>
    </div>

    <!-- Wizard Steps Header -->
    <div class="wizard-steps d-none d-md-flex">
        <div class="wizard-step active" id="stepIndicator1">
            <div class="wizard-step-circle">1</div>
            <div>المعلومات الأساسية</div>
        </div>
        <div class="wizard-step" id="stepIndicator2">
            <div class="wizard-step-circle">2</div>
            <div>التصنيف والعملية</div>
        </div>
        <div class="wizard-step" id="stepIndicator3">
            <div class="wizard-step-circle">3</div>
            <div>الموقع الجغرافي</div>
        </div>
        <div class="wizard-step" id="stepIndicator4">
            <div class="wizard-step-circle">4</div>
            <div>السعر والمساحة</div>
        </div>
        <div class="wizard-step" id="stepIndicator5">
            <div class="wizard-step-circle">5</div>
            <div>المواصفات والمميزات</div>
        </div>
        <div class="wizard-step" id="stepIndicator6">
            <div class="wizard-step-circle">6</div>
            <div>الصور والإرسال</div>
        </div>
    </div>

    <!-- Form Container -->
    <form id="addPropertyForm" action="<?= App::baseUrl('dashboard/properties/store') ?>" method="POST" enctype="multipart/form-data">
        <?= Csrf::field() ?>

        <!-- STEP 1: Basic Information -->
        <div class="wizard-step-pane" id="stepPane1">
            <h5 class="fw-bold mb-3 border-bottom pb-2">1. المعلومات الأساسية</h5>
            <div class="mb-3">
                <label class="form-label fw-bold small">عنوان الإعلان <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control form-control-lg" required placeholder="مثال: فيلا فاخرة دورين حجر دوران في أرقى أحياء حدة">
                <div class="form-text small">اكتب عنواناً جذاباً يوضح نوع العقار والموقع والميزة الأبرز.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small">وصف العقار بالتفصيل <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="6" required placeholder="اشرح تفاصيل العقار: تقسيم الغرف، جودة البناء، التجهيزات، توفر الخدمات، والوثائق..."></textarea>
            </div>
        </div>

        <!-- STEP 2: Category & Purpose -->
        <div class="wizard-step-pane d-none" id="stepPane2">
            <h5 class="fw-bold mb-3 border-bottom pb-2">2. التصنيف ونوع العملية</h5>
            <div class="mb-4">
                <label class="form-label fw-bold small">نوع العملية <span class="text-danger">*</span></label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="radio" class="btn-check" name="purpose" id="purposeSale" value="sale" checked>
                        <label class="btn btn-outline-primary w-100 py-3 fw-bold" for="purposeSale">
                            <i class="bi bi-tag-fill fs-5 d-block mb-1"></i> للبيع
                        </label>
                    </div>
                    <div class="col-md-6">
                        <input type="radio" class="btn-check" name="purpose" id="purposeRent" value="rent">
                        <label class="btn btn-outline-primary w-100 py-3 fw-bold" for="purposeRent">
                            <i class="bi bi-key-fill fs-5 d-block mb-1"></i> للإيجار
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small">تصنيف العقار <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select form-select-lg" required>
                    <option value="">اختر تصنيف العقار</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name_ar']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small">طريقة دفع السعر</label>
                <select name="price_type" class="form-select">
                    <option value="total">سعر إجمالي كامل</option>
                    <option value="per_month">شهرياً (للإيجار)</option>
                    <option value="per_year">سنوياً (للإيجار)</option>
                    <option value="per_labna">سعر اللبنة الواحدة (للأراضي)</option>
                    <option value="per_meter">سعر المتر المربع</option>
                </select>
            </div>
        </div>

        <!-- STEP 3: Geographic Location -->
        <div class="wizard-step-pane d-none" id="stepPane3">
            <h5 class="fw-bold mb-3 border-bottom pb-2">3. الموقع الجغرافي في اليمن</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold small">المحافظة <span class="text-danger">*</span></label>
                    <select name="governorate_id" id="governorate_id" class="form-select form-select-lg" required>
                        <option value="">اختر المحافظة</option>
                        <?php foreach ($governorates as $gov): ?>
                            <option value="<?= $gov['id'] ?>"><?= htmlspecialchars($gov['name_ar']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">المديرية <span class="text-danger">*</span></label>
                    <select name="district_id" id="district_id" class="form-select form-select-lg" required>
                        <option value="">اختر المحافظة أولاً</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">المنطقة / الحي (اختياري)</label>
                    <select name="area_id" id="area_id" class="form-select form-select-lg">
                        <option value="">اختر المنطقة</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small">تفاصيل العنوان والشارع</label>
                <input type="text" name="address_details" class="form-control" placeholder="مثال: شارع الخمسين، جوار جامع الصالح، أمام حديقة النصر">
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold small">خط العرض Latitude (اختياري)</label>
                    <input type="number" step="any" name="latitude" class="form-control" placeholder="مثال: 15.3125">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">خط الطول Longitude (اختياري)</label>
                    <input type="number" step="any" name="longitude" class="form-control" placeholder="مثال: 44.1852">
                </div>
            </div>
        </div>

        <!-- STEP 4: Pricing & Area -->
        <div class="wizard-step-pane d-none" id="stepPane4">
            <h5 class="fw-bold mb-3 border-bottom pb-2">4. السعر والمساحة</h5>
            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold small">السعر <span class="text-danger">*</span></label>
                    <input type="number" step="any" name="price" class="form-control form-control-lg" required placeholder="مثال: 850000">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small">العملة <span class="text-danger">*</span></label>
                    <select name="currency" class="form-select form-select-lg" required>
                        <option value="YER">ريال يمني (YER)</option>
                        <option value="USD" selected>دولار أمريكي (USD)</option>
                        <option value="SAR">ريال سعودي (SAR)</option>
                    </select>
                </div>
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="is_negotiable" id="isNegotiable" value="1" checked>
                <label class="form-check-label fw-semibold" for="isNegotiable">السعر قابل للتفاوض</label>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold small">المساحة <span class="text-danger">*</span></label>
                    <input type="number" step="any" name="area_size" class="form-control form-control-lg" required placeholder="مثال: 12 أو 250">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small">وحدة قياس المساحة <span class="text-danger">*</span></label>
                    <select name="area_unit" class="form-select form-select-lg" required>
                        <option value="sqm">متر مربع (م²)</option>
                        <option value="labna">لبنة صنعانية حر</option>
                        <option value="qasaba">قصبة</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- STEP 5: Specs & Features -->
        <div class="wizard-step-pane d-none" id="stepPane5">
            <h5 class="fw-bold mb-3 border-bottom pb-2">5. المواصفات والمميزات</h5>
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <label class="form-label fw-bold small">غرف النوم</label>
                    <input type="number" name="bedrooms" class="form-control" min="0" value="0">
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label fw-bold small">دورات المياه</label>
                    <input type="number" name="bathrooms" class="form-control" min="0" value="0">
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label fw-bold small">الصالات / المجالس</label>
                    <input type="number" name="living_rooms" class="form-control" min="0" value="0">
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label fw-bold small">عدد الطوابق</label>
                    <input type="number" name="floors" class="form-control" min="1" value="1">
                </div>
            </div>

            <h6 class="fw-bold mb-3">التجهيزات والمميزات الإضافية (اختر ما ينطبق):</h6>
            <div class="row g-3">
                <?php foreach ($features as $f): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="form-check p-2 border rounded bg-light">
                            <input class="form-check-input ms-2" type="checkbox" name="features[]" value="<?= $f['id'] ?>" id="feat_<?= $f['id'] ?>">
                            <label class="form-check-label fw-semibold small" for="feat_<?= $f['id'] ?>">
                                <?= htmlspecialchars($f['name_ar']) ?>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- STEP 6: Images & Submit -->
        <div class="wizard-step-pane d-none" id="stepPane6">
            <h5 class="fw-bold mb-3 border-bottom pb-2">6. صور العقار والمراجعة النهائية</h5>
            
            <div class="mb-4">
                <label class="form-label fw-bold small">رفع صور العقار (يمكن اختيار عدة صور معاً) <span class="text-danger">*</span></label>
                <input type="file" name="images[]" class="form-control form-control-lg" multiple accept="image/jpeg,image/png,image/webp">
                <div class="form-text small">الصورة الأولى ستكون الصورة الرئيسية للغلاف. يفضل رفع صور أفقية واضحة بدقة جيدة (الحد الأقصى لكل صورة 5 ميجابايت).</div>
            </div>

            <div class="alert alert-info border-0 p-3 mb-4">
                <div class="fw-bold mb-1"><i class="bi bi-info-circle-fill me-1"></i> آلية النشر والمراجعة:</div>
                <ul class="mb-0 small ps-3">
                    <li>يتم إرسال الإعلان فوراً إلى فريق المراجعة والتدقيق الإداري.</li>
                    <li>يتم فحص الصور ومطابقة البيانات لضمان مصداقية المنصة وحماية المشترين من الإعلانات الوهمية.</li>
                    <li>يصلك إشعار فوري عند اعتماد الإعلان وظهوره على محرك البحث.</li>
                </ul>
            </div>
        </div>

        <!-- Wizard Navigation Buttons -->
        <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
            <button type="button" class="btn btn-secondary px-4 fw-bold" id="wizardPrevBtn" style="display: none;">
                <i class="bi bi-arrow-right me-1"></i> الخطوة السابقة
            </button>
            <div class="ms-auto">
                <button type="button" class="btn btn-primary px-4 fw-bold" id="wizardNextBtn">
                    الخطوة التالية <i class="bi bi-arrow-left ms-1"></i>
                </button>
                <button type="submit" class="btn btn-success px-4 fw-bold" id="wizardSubmitBtn" style="display: none;">
                    <i class="bi bi-check2-circle me-1"></i> حفظ وإرسال الإعلان للمراجعة
                </button>
            </div>
        </div>
    </form>
</div>
