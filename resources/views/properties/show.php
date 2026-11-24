<?php
use App\Config\App;
use App\Helpers\View;
use App\Helpers\Session;
use App\Helpers\Csrf;
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= App::baseUrl('') ?>" class="text-decoration-none">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="<?= App::baseUrl('properties') ?>" class="text-decoration-none">العقارات</a></li>
            <li class="breadcrumb-item"><a href="<?= App::baseUrl('properties?governorate_id=' . $property['governorate_id']) ?>" class="text-decoration-none"><?= htmlspecialchars($property['governorate_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($property['title']) ?></li>
        </ol>
    </nav>

    <!-- Header info & Action buttons -->
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge <?= $property['purpose'] === 'sale' ? 'bg-primary' : 'bg-warning text-dark' ?> fs-6 px-3 py-1">
                    <?= View::formatPurpose($property['purpose']) ?>
                </span>
                <span class="badge bg-light text-dark border fs-6 px-3 py-1">
                    <?= htmlspecialchars($property['category_name']) ?>
                </span>
                <?php if ($property['is_featured']): ?>
                    <span class="badge bg-warning text-dark fs-6 px-2"><i class="bi bi-star-fill"></i> مميز</span>
                <?php endif; ?>
                <span class="text-muted small ms-2"><i class="bi bi-eye"></i> <?= number_format($property['views_count']) ?> مشاهدة</span>
            </div>

            <h1 class="fw-bold h2 mb-2"><?= htmlspecialchars($property['title']) ?></h1>
            <p class="text-muted mb-0">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                <?= htmlspecialchars($property['governorate_name']) ?> - <?= htmlspecialchars($property['district_name']) ?>
                <?= !empty($property['area_name']) ? ' - ' . htmlspecialchars($property['area_name']) : '' ?>
                <?= !empty($property['address_details']) ? ' (' . htmlspecialchars($property['address_details']) . ')' : '' ?>
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-danger favorite-btn-action <?= $isFavorited ? 'active' : '' ?>" data-property-id="<?= $property['id'] ?>" title="حفظ في المفضلة">
                <i class="bi <?= $isFavorited ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                <span><?= $isFavorited ? 'في المفضلة' : 'حفظ' ?></span>
            </button>

            <button class="btn btn-outline-secondary" id="sharePropertyBtn" title="مشاركة رابط العقار">
                <i class="bi bi-share"></i> مشاركة
            </button>

            <button class="btn btn-outline-warning text-dark" data-bs-toggle="modal" data-bs-target="#reportModal" title="إبلاغ عن إعلان مخالف">
                <i class="bi bi-flag"></i> إبلاغ
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Property Details (Images, Specs, Description, Features) -->
        <div class="col-lg-8">
            <!-- Photo Gallery Card -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="bg-dark text-center position-relative" style="max-height: 480px; overflow: hidden;">
                    <?php 
                        $mainImg = !empty($property['images'][0]['image_path']) 
                            ? App::upload('properties/' . $property['images'][0]['image_path']) 
                            : App::asset('images/placeholder.svg');
                    ?>
                    <img id="mainGalleryImage" src="<?= $mainImg ?>" class="img-fluid w-100" style="max-height: 480px; object-fit: cover;" alt="<?= htmlspecialchars($property['title']) ?>">
                </div>

                <!-- Thumbnails Bar -->
                <?php if (count($property['images']) > 1): ?>
                    <div class="p-3 bg-white border-top d-flex gap-2 overflow-x-auto">
                        <?php foreach ($property['images'] as $img): ?>
                            <img src="<?= App::upload('properties/' . $img['image_path']) ?>" 
                                 class="rounded border thumbnail-img" 
                                 style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;" 
                                 onclick="document.getElementById('mainGalleryImage').src=this.src;" 
                                 alt="صورة العقار">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Price & Key Specs Card -->
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-3 border-bottom">
                    <div>
                        <div class="text-muted small">السعر المطلوب</div>
                        <div class="property-price fs-2">
                            <?= View::formatPrice($property['price'], $property['currency'], $property['price_type']) ?>
                        </div>
                        <?php if ($property['is_negotiable']): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check2"></i> السعر قابل للتفاوض</span>
                        <?php endif; ?>
                    </div>

                    <div class="text-end">
                        <div class="text-muted small">المساحة الإجمالية</div>
                        <div class="fs-4 fw-bold text-navy">
                            <?= View::formatArea($property['area_size'], $property['area_unit']) ?>
                        </div>
                    </div>
                </div>

                <!-- Specs Grid -->
                <h5 class="fw-bold mb-3"><i class="bi bi-card-checklist text-primary me-2"></i> المواصفات الرئيسية</h5>
                <div class="row g-3 text-center">
                    <?php if ($property['bedrooms'] > 0): ?>
                        <div class="col-4 col-md-3">
                            <div class="p-3 rounded bg-light border">
                                <i class="bi bi-door-closed text-primary fs-3 d-block mb-1"></i>
                                <div class="fw-bold"><?= $property['bedrooms'] ?> غرف</div>
                                <div class="text-muted small">غرف نوم</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($property['bathrooms'] > 0): ?>
                        <div class="col-4 col-md-3">
                            <div class="p-3 rounded bg-light border">
                                <i class="bi bi-droplet text-info fs-3 d-block mb-1"></i>
                                <div class="fw-bold"><?= $property['bathrooms'] ?> حمامات</div>
                                <div class="text-muted small">دورات مياه</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($property['living_rooms'] > 0): ?>
                        <div class="col-4 col-md-3">
                            <div class="p-3 rounded bg-light border">
                                <i class="bi bi-tv text-warning fs-3 d-block mb-1"></i>
                                <div class="fw-bold"><?= $property['living_rooms'] ?> صالات</div>
                                <div class="text-muted small">مجلس وصالة</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($property['floors'] > 0): ?>
                        <div class="col-4 col-md-3">
                            <div class="p-3 rounded bg-light border">
                                <i class="bi bi-layers text-success fs-3 d-block mb-1"></i>
                                <div class="fw-bold"><?= $property['floors'] ?> طوابق</div>
                                <div class="text-muted small">عدد الطوابق</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($property['direction'])): ?>
                        <div class="col-4 col-md-3">
                            <div class="p-3 rounded bg-light border">
                                <i class="bi bi-compass text-secondary fs-3 d-block mb-1"></i>
                                <div class="fw-bold"><?= htmlspecialchars($property['direction']) ?></div>
                                <div class="text-muted small">الاتجاه والواجهة</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($property['building_year'])): ?>
                        <div class="col-4 col-md-3">
                            <div class="p-3 rounded bg-light border">
                                <i class="bi bi-calendar-check text-navy fs-3 d-block mb-1"></i>
                                <div class="fw-bold"><?= $property['building_year'] ?></div>
                                <div class="text-muted small">سنة البناء</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Full Description Card -->
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-card-text text-primary me-2"></i> تفاصيل ووصف العقار</h5>
                <div class="property-description text-secondary" style="white-space: pre-line; line-height: 1.8;">
                    <?= nl2br(htmlspecialchars($property['description'])) ?>
                </div>
            </div>

            <!-- Features & Amenities Card -->
            <?php if (!empty($property['features'])): ?>
                <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-stars text-warning me-2"></i> المميزات والتجهيزات</h5>
                    <div class="row g-3">
                        <?php foreach ($property['features'] as $feat): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex align-items-center gap-2 p-2 rounded border bg-light">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span class="small fw-semibold"><?= htmlspecialchars($feat['name_ar']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Location on Map (OpenStreetMap) -->
            <?php if (!empty($property['latitude']) && !empty($property['longitude'])): ?>
                <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-map text-primary me-2"></i> موقع العقار على الخريطة</h5>
                    <div class="rounded overflow-hidden border" style="height: 300px;">
                        <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" 
                                src="https://www.openstreetmap.org/export/embed.html?bbox=<?= ($property['longitude'] - 0.01) ?>%2C<?= ($property['latitude'] - 0.01) ?>%2C<?= ($property['longitude'] + 0.01) ?>%2C<?= ($property['latitude'] + 0.01) ?>&amp;layer=mapnik&amp;marker=<?= $property['latitude'] ?>%2C<?= $property['longitude'] ?>">
                        </iframe>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Advertiser Box & Quick Inquiry Action -->
        <div class="col-lg-4">
            <!-- Advertiser Profile Box -->
            <div class="card border-0 shadow-sm rounded-3 p-4 mb-4 sticky-top" style="top: 85px;">
                <h5 class="fw-bold pb-2 mb-3 border-bottom"><i class="bi bi-person-badge text-primary me-2"></i> بيانات المعلن</h5>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; font-size: 1.5rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0"><?= htmlspecialchars($property['user_name']) ?></h6>
                        <div class="text-muted small">
                            <?= match($property['user_role']) {
                                'office' => 'مكتب عقاري معتمد',
                                'broker' => 'وسيط عقاري معتمد',
                                'owner' => 'مالك العقار المباشر',
                                default => 'معلن مسجل'
                            } ?>
                        </div>
                        <?php if ($property['user_verified']): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle small mt-1">
                                <i class="bi bi-patch-check-fill"></i> هوية موثقة
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($property['office_name'])): ?>
                    <div class="p-2 mb-3 rounded bg-light border small">
                        <strong>تابع لـ:</strong> <?= htmlspecialchars($property['office_name']) ?>
                    </div>
                <?php endif; ?>

                <!-- Direct Phone & WhatsApp Call Buttons -->
                <div class="d-grid gap-2 mb-3">
                    <a href="tel:<?= htmlspecialchars($property['user_phone']) ?>" class="btn btn-primary btn-lg fw-bold d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-telephone-fill"></i>
                        <span>اتصال: <?= htmlspecialchars($property['user_phone']) ?></span>
                    </a>

                    <?php 
                        $cleanWhatsApp = preg_replace('/[^0-9]/', '', $property['user_whatsapp'] ?? $property['user_phone']);
                        $waText = urlencode("السلام عليكم، أنا مهتم بعقارك المعروض في عقار اليمن: " . $property['title']);
                    ?>
                    <a href="https://wa.me/<?= $cleanWhatsApp ?>?text=<?= $waText ?>" target="_blank" class="btn btn-success btn-lg fw-bold d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-whatsapp"></i>
                        <span>محادثة واتساب مباشرة</span>
                    </a>

                    <button class="btn btn-outline-dark fw-bold" data-bs-toggle="modal" data-bs-target="#inquiryModal">
                        <i class="bi bi-chat-left-text me-1"></i> إرسال استفسار عبر المنصة
                    </button>
                </div>

                <!-- Safety tips -->
                <div class="p-3 rounded bg-light border text-muted small">
                    <div class="fw-bold text-dark mb-1"><i class="bi bi-shield-lock text-warning me-1"></i> نصائح الأمان:</div>
                    لا تقم بتحويل أي مبالغ مالية أو عربون دون معاينة العقار على الواقع والتأكد من أصل البصيرة في قلم التوثيق والسجل العقاري.
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Properties Section -->
    <?php if (!empty($similarProperties)): ?>
        <div class="mt-5 pt-4 border-top">
            <h3 class="fw-bold mb-4">عقارات مشابهة قد تهمك</h3>
            <div class="row g-4">
                <?php foreach ($similarProperties as $sim): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="property-card">
                            <div class="property-card-img-wrapper" style="height: 180px;">
                                <a href="<?= App::baseUrl('property/' . $sim['id']) ?>">
                                    <?php $simImg = !empty($sim['primary_image']) ? App::upload('properties/' . $sim['primary_image']) : App::asset('images/placeholder.svg'); ?>
                                    <img src="<?= $simImg ?>" class="property-card-img" alt="<?= htmlspecialchars($sim['title']) ?>">
                                </a>
                            </div>
                            <div class="p-3">
                                <h6 class="fw-bold text-truncate mb-2">
                                    <a href="<?= App::baseUrl('property/' . $sim['id']) ?>" class="text-decoration-none text-navy">
                                        <?= htmlspecialchars($sim['title']) ?>
                                    </a>
                                </h6>
                                <div class="property-price small mb-2">
                                    <?= View::formatPrice($sim['price'], $sim['currency'], $sim['price_type']) ?>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($sim['governorate_name']) ?> - <?= htmlspecialchars($sim['district_name']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal: Send Inquiry -->
<div class="modal fade" id="inquiryModal" tabindex="-1" aria-labelledby="inquiryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="inquiryModalLabel">إرسال استفسار حول العقار</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form id="inquiryForm">
                <div class="modal-body">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="property_id" value="<?= $property['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">الاسم الكامل <span class="text-danger">*</span></label>
                        <input type="text" name="sender_name" class="form-control" required value="<?= Session::check() ? htmlspecialchars(Session::user()['full_name']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">رقم الهاتف <span class="text-danger">*</span></label>
                        <input type="tel" name="sender_phone" class="form-control" required placeholder="مثال: 771234567" value="<?= Session::check() ? htmlspecialchars(Session::user()['phone']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">البريد الإلكتروني (اختياري)</label>
                        <input type="email" name="sender_email" class="form-control" value="<?= Session::check() ? htmlspecialchars(Session::user()['email']) : '' ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">رسالتك واستفسارك <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="4" required placeholder="اكتب استفسارك بالتفصيل (مثال: هل العقار متاح للمعاينة؟ وهل السعر قابل للتفاوض؟)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">إرسال الاستفسار الآن</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Report Property -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger" id="reportModalLabel"><i class="bi bi-shield-exclamation me-1"></i> إبلاغ عن إعلان مخالف أو مشبوه</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <form id="reportForm">
                <div class="modal-body">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="property_id" value="<?= $property['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">سبب الإبلاغ <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select" required>
                            <option value="">اختر سبب البلاغ</option>
                            <option value="incorrect_info">معلومات غير صحيحة أو مضللة</option>
                            <option value="misleading_price">سعر وهمي أو مضلل</option>
                            <option value="fake_images">صور غير حقيقية أو منسوخة</option>
                            <option value="duplicate_ad">إعلان مكرر</option>
                            <option value="invalid_phone">رقم هاتف خاطئ أو لا يجيب</option>
                            <option value="suspicious_ad">إعلان مشبوه أو احتيالي</option>
                            <option value="other">أسباب أخرى</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">تفاصيل البلاغ <span class="text-danger">*</span></label>
                        <textarea name="details" class="form-control" rows="3" required placeholder="يرجى توضيح سبب البلاغ بدقة ليتمكن فريق التدقيق من مراجعته فوراً"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger fw-bold">تقديم البلاغ للإدارة</button>
                </div>
            </form>
        </div>
    </div>
</div>
