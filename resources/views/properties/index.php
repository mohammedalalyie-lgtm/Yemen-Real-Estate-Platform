<?php
use App\Config\App;
use App\Helpers\View;
?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= App::baseUrl('') ?>" class="text-decoration-none">الرئيسية</a></li>
            <li class="breadcrumb-item active" aria-current="page">البحث المتقدم عن العقارات</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar Filter Form -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 sticky-top" style="top: 85px;">
                <h5 class="fw-bold mb-3 d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-funnel text-primary me-1"></i> تصفية النتائج</span>
                    <a href="<?= App::baseUrl('properties') ?>" class="small text-danger text-decoration-none fw-normal">إعادة ضبط</a>
                </h5>

                <form action="<?= App::baseUrl('properties') ?>" method="GET">
                    <!-- Purpose -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">نوع العملية</label>
                        <select name="purpose" class="form-select">
                            <option value="">الكل (بيع وإيجار)</option>
                            <option value="sale" <?= ($filters['purpose'] ?? '') === 'sale' ? 'selected' : '' ?>>للبيع فقط</option>
                            <option value="rent" <?= ($filters['purpose'] ?? '') === 'rent' ? 'selected' : '' ?>>للإيجار فقط</option>
                        </select>
                    </div>

                    <!-- Governorate -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">المحافظة</label>
                        <select name="governorate_id" id="governorate_id" class="form-select">
                            <option value="">جميع المحافظات</option>
                            <?php foreach ($governorates as $gov): ?>
                                <option value="<?= $gov['id'] ?>" <?= ($filters['governorate_id'] ?? '') == $gov['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($gov['name_ar']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- District -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">المديرية</label>
                        <select name="district_id" id="district_id" class="form-select">
                            <option value="">اختر المديرية</option>
                            <?php foreach ($districts as $dist): ?>
                                <option value="<?= $dist['id'] ?>" <?= ($filters['district_id'] ?? '') == $dist['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dist['name_ar']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">نوع العقار</label>
                        <select name="category_id" class="form-select">
                            <option value="">جميع الأنواع</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name_ar']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">نطاق السعر</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="من" value="<?= htmlspecialchars($filters['min_price'] ?? '') ?>">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="إلى" value="<?= htmlspecialchars($filters['max_price'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Currency -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">العملة</label>
                        <select name="currency" class="form-select form-select-sm">
                            <option value="">أي عملة</option>
                            <option value="YER" <?= ($filters['currency'] ?? '') === 'YER' ? 'selected' : '' ?>>ريال يمني (YER)</option>
                            <option value="USD" <?= ($filters['currency'] ?? '') === 'USD' ? 'selected' : '' ?>>دولار أمريكي (USD)</option>
                            <option value="SAR" <?= ($filters['currency'] ?? '') === 'SAR' ? 'selected' : '' ?>>ريال سعودي (SAR)</option>
                        </select>
                    </div>

                    <!-- Bedrooms -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الغرف على الأقل</label>
                        <select name="bedrooms" class="form-select form-select-sm">
                            <option value="">غير محدد</option>
                            <option value="1" <?= ($filters['bedrooms'] ?? '') == '1' ? 'selected' : '' ?>>1 غرفة فأكثر</option>
                            <option value="2" <?= ($filters['bedrooms'] ?? '') == '2' ? 'selected' : '' ?>>2 غرف فأكثر</option>
                            <option value="3" <?= ($filters['bedrooms'] ?? '') == '3' ? 'selected' : '' ?>>3 غرف فأكثر</option>
                            <option value="4" <?= ($filters['bedrooms'] ?? '') == '4' ? 'selected' : '' ?>>4 غرف فأكثر</option>
                            <option value="5" <?= ($filters['bedrooms'] ?? '') == '5' ? 'selected' : '' ?>>5 غرف فأكثر</option>
                        </select>
                    </div>

                    <!-- Keyword -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold">كلمات مفتاحية</label>
                        <input type="text" name="keyword" class="form-control form-control-sm" placeholder="ابحث في العنوان أو الوصف" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
                    </div>

                    <!-- Sort -->
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($filters['sort'] ?? 'latest') ?>">

                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="bi bi-funnel-fill me-1"></i> تطبيق الفلاتر
                    </button>
                </form>
            </div>
        </div>

        <!-- Search Results Grid -->
        <div class="col-lg-9">
            <!-- Header bar -->
            <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h4 class="fw-bold mb-0">نتائج البحث عن العقارات</h4>
                        <div class="text-muted small">تم العثور على <strong><?= number_format($pagination['total']) ?></strong> عقار معتمد ومتاح</div>
                    </div>

                    <!-- Sorting options -->
                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted text-nowrap">الترتيب حسب:</label>
                        <select class="form-select form-select-sm" onchange="const url = new URL(window.location.href); url.searchParams.set('sort', this.value); window.location.href = url.toString();">
                            <option value="latest" <?= ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>الأحدث إضافة</option>
                            <option value="price_asc" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>الأقل سعراً</option>
                            <option value="price_desc" <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>الأعلى سعراً</option>
                            <option value="area_desc" <?= ($filters['sort'] ?? '') === 'area_desc' ? 'selected' : '' ?>>الأكبر مساحة</option>
                            <option value="views_desc" <?= ($filters['sort'] ?? '') === 'views_desc' ? 'selected' : '' ?>>الأكثر مشاهدة</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Properties Cards Grid -->
            <?php if (!empty($properties)): ?>
                <div class="row g-4">
                    <?php foreach ($properties as $prop): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="property-card">
                                <div class="property-card-img-wrapper">
                                    <a href="<?= App::baseUrl('property/' . $prop['id']) ?>">
                                        <?php $imgSrc = !empty($prop['primary_image']) ? App::upload('properties/' . $prop['primary_image']) : App::asset('images/placeholder.svg'); ?>
                                        <img src="<?= $imgSrc ?>" class="property-card-img" alt="<?= htmlspecialchars($prop['title']) ?>">
                                    </a>
                                    <div class="property-badges">
                                        <span class="property-badge <?= $prop['purpose'] === 'sale' ? 'badge-purpose-sale' : 'badge-purpose-rent' ?>">
                                            <?= View::formatPurpose($prop['purpose']) ?>
                                        </span>
                                        <?php if (!empty($prop['is_featured'])): ?>
                                            <span class="property-badge badge-featured"><i class="bi bi-star-fill"></i> مميز</span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="favorite-btn favorite-btn-action" data-property-id="<?= $prop['id'] ?>" title="حفظ في المفضلة">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                </div>

                                <div class="p-3 d-flex flex-column flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($prop['category_name']) ?></span>
                                        <span class="text-muted small"><?= View::timeAgo($prop['created_at']) ?></span>
                                    </div>

                                    <h6 class="fw-bold mb-2 text-truncate" title="<?= htmlspecialchars($prop['title']) ?>">
                                        <a href="<?= App::baseUrl('property/' . $prop['id']) ?>" class="text-decoration-none text-navy">
                                            <?= htmlspecialchars($prop['title']) ?>
                                        </a>
                                    </h6>

                                    <p class="text-muted small mb-2 text-truncate">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>
                                        <?= htmlspecialchars($prop['governorate_name']) ?> - <?= htmlspecialchars($prop['district_name']) ?>
                                    </p>

                                    <div class="d-flex justify-content-between align-items-center my-2">
                                        <div class="property-price" style="font-size: 1.15rem;">
                                            <?= View::formatPrice($prop['price'], $prop['currency'], $prop['price_type']) ?>
                                        </div>
                                        <span class="badge bg-secondary-subtle text-dark border" style="font-size: 0.75rem;">
                                            <?= View::formatArea($prop['area_size'], $prop['area_unit']) ?>
                                        </span>
                                    </div>

                                    <div class="property-specs">
                                        <?php if ($prop['bedrooms'] > 0): ?>
                                            <div class="property-spec-item"><i class="bi bi-door-closed text-primary"></i> <?= $prop['bedrooms'] ?></div>
                                        <?php endif; ?>
                                        <?php if ($prop['bathrooms'] > 0): ?>
                                            <div class="property-spec-item"><i class="bi bi-droplet text-info"></i> <?= $prop['bathrooms'] ?></div>
                                        <?php endif; ?>
                                        <div class="property-spec-item ms-auto text-muted small">
                                            <i class="bi bi-eye"></i> <?= number_format($prop['views_count']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['last_page'] > 1): ?>
                    <nav class="mt-5" aria-label="تنقل الصفحات">
                        <ul class="pagination justify-content-center">
                            <?php for ($p = 1; $p <= $pagination['last_page']; $p++): ?>
                                <?php
                                    $pageUrl = new \ArrayObject($_GET);
                                    $pageUrl['page'] = $p;
                                    $queryStr = http_build_query($pageUrl->getArrayCopy());
                                ?>
                                <li class="page-item <?= $pagination['page'] == $p ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= $queryStr ?>"><?= $p ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="card border-0 shadow-sm p-5 text-center">
                    <i class="bi bi-search text-muted fs-1 mb-3"></i>
                    <h5 class="fw-bold">لم نجد عقارات مطابقة لمعايير البحث الحالية</h5>
                    <p class="text-muted">جرب تقليل الفلاتر أو البحث في محافظة أخرى، أو اسأل مساعد عقار اليمن الذكي في الأسفل.</p>
                    <div class="mt-2">
                        <a href="<?= App::baseUrl('properties') ?>" class="btn btn-outline-primary">عرض جميع العقارات المتاحة</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
