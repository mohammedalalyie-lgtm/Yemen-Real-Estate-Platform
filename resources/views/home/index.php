<?php
use App\Config\App;
use App\Helpers\View;
?>

<!-- Hero Section with Premium Search Engine -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="hero-title mb-3">
            اعثر على عقارك المثالي في <span>اليمن</span>
        </h1>
        <p class="hero-subtitle mx-auto" style="max-width: 650px;">
            منصتك العقارية الشاملة للبيع والإيجار والاستثمار في جميع محافظات الجمهورية اليمنية... بيتك يبدأ من هنا.
        </p>

        <!-- Search Box Card -->
        <div class="search-card mt-4 text-start mx-auto" style="max-width: 1050px;">
            <form action="<?= App::baseUrl('properties') ?>" method="GET">
                <!-- Search Purpose Tabs -->
                <ul class="nav nav-pills search-tabs mb-4 justify-content-center justify-content-md-start" id="searchTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" type="button" onclick="document.getElementById('searchPurpose').value='sale'; this.classList.add('active'); this.parentElement.nextElementSibling.children[0].classList.remove('active');">
                            <i class="bi bi-tag-fill me-1"></i> عقارات للبيع
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" type="button" onclick="document.getElementById('searchPurpose').value='rent'; this.classList.add('active'); this.parentElement.previousElementSibling.children[0].classList.remove('active');">
                            <i class="bi bi-key-fill me-1"></i> عقارات للإيجار
                        </button>
                    </li>
                </ul>
                <input type="hidden" name="purpose" id="searchPurpose" value="sale">

                <!-- Form Controls Grid -->
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">المحافظة</label>
                        <select name="governorate_id" id="governorate_id" class="form-select form-select-lg" style="font-size: 0.95rem;">
                            <option value="">كل المحافظات اليمنية</option>
                            <?php foreach ($governorates as $gov): ?>
                                <option value="<?= $gov['id'] ?>"><?= htmlspecialchars($gov['name_ar']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">المديرية</label>
                        <select name="district_id" id="district_id" class="form-select form-select-lg" style="font-size: 0.95rem;">
                            <option value="">اختر المديرية</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">نوع العقار</label>
                        <select name="category_id" class="form-select form-select-lg" style="font-size: 0.95rem;">
                            <option value="">جميع التصنيفات</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name_ar']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">العملة</label>
                        <select name="currency" class="form-select form-select-lg" style="font-size: 0.95rem;">
                            <option value="">أي عملة</option>
                            <option value="YER">ريال يمني (YER)</option>
                            <option value="USD">دولار أمريكي (USD)</option>
                            <option value="SAR">ريال سعودي (SAR)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="number" name="min_price" class="form-control" placeholder="الحد الأدنى للسعر">
                            <span class="input-group-text">إلى</span>
                            <input type="number" name="max_price" class="form-control" placeholder="الحد الأعلى">
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="keyword" class="form-control" placeholder="ابحث باسم الحي، الشارع، أو وصف العقار (مثال: حدة، بيت بوس، لبنة حر...)">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-search"></i> بحث في العقارات
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Stats Ribbon -->
<section class="py-4 border-bottom bg-white">
    <div class="container">
        <div class="row text-center g-3">
            <div class="col-6 col-md-3">
                <div class="fs-3 fw-bold text-primary"><?= number_format($stats['properties_count']) ?>+</div>
                <div class="text-muted small">عقار معتمد ومتاح</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-3 fw-bold text-warning"><?= $stats['governorates_count'] ?></div>
                <div class="text-muted small">محافظة يمنية مشمولة</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-3 fw-bold text-success"><?= $stats['offices_count'] ?>+</div>
                <div class="text-muted small">مكتب عقاري معتمد</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="fs-3 fw-bold text-navy"><?= $stats['brokers_count'] ?>+</div>
                <div class="text-muted small">وسيط عقاري موثق</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="badge bg-warning text-dark px-3 py-1 mb-2 fw-bold"><i class="bi bi-star-fill me-1"></i> مميز وموثق</span>
                <h2 class="fw-bold mb-1">عقارات مختارة ومميزة</h2>
                <p class="text-muted mb-0">أفضل الفرص العقارية والاستثمارية المعتمدة في صنعاء، عدن، وباقي المحافظات</p>
            </div>
            <a href="<?= App::baseUrl('properties?featured=1') ?>" class="btn btn-outline-primary fw-semibold">
                عرض المزيد <i class="bi bi-arrow-left"></i>
            </a>
        </div>

        <?php if (!empty($featuredProperties)): ?>
            <div class="row g-4">
                <?php foreach ($featuredProperties as $prop): ?>
                    <div class="col-lg-4 col-md-6">
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
                                    <span class="property-badge badge-featured">
                                        <i class="bi bi-star-fill"></i> مميز
                                    </span>
                                </div>
                                <button class="favorite-btn favorite-btn-action" data-property-id="<?= $prop['id'] ?>" title="حفظ في المفضلة">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>

                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($prop['category_name']) ?></span>
                                    <span class="text-muted small"><i class="bi bi-eye"></i> <?= number_format($prop['views_count']) ?> مشاهدة</span>
                                </div>

                                <h5 class="fw-bold mb-2 text-truncate" title="<?= htmlspecialchars($prop['title']) ?>">
                                    <a href="<?= App::baseUrl('property/' . $prop['id']) ?>" class="text-decoration-none text-navy">
                                        <?= htmlspecialchars($prop['title']) ?>
                                    </a>
                                </h5>

                                <p class="text-muted small mb-3">
                                    <i class="bi bi-geo-alt text-danger me-1"></i>
                                    <?= htmlspecialchars($prop['governorate_name']) ?> - <?= htmlspecialchars($prop['district_name']) ?>
                                    <?= !empty($prop['area_name']) ? ' (' . htmlspecialchars($prop['area_name']) . ')' : '' ?>
                                </p>

                                <div class="d-flex justify-content-between align-items-center my-2">
                                    <div class="property-price">
                                        <?= View::formatPrice($prop['price'], $prop['currency'], $prop['price_type']) ?>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <?= View::formatArea($prop['area_size'], $prop['area_unit']) ?>
                                    </span>
                                </div>

                                <div class="property-specs">
                                    <?php if ($prop['bedrooms'] > 0): ?>
                                        <div class="property-spec-item" title="عدد الغرف"><i class="bi bi-door-closed text-primary"></i> <?= $prop['bedrooms'] ?> غرف</div>
                                    <?php endif; ?>
                                    <?php if ($prop['bathrooms'] > 0): ?>
                                        <div class="property-spec-item" title="عدد الحمامات"><i class="bi bi-droplet text-info"></i> <?= $prop['bathrooms'] ?> حمام</div>
                                    <?php endif; ?>
                                    <div class="property-spec-item ms-auto text-muted small">
                                        <?= View::timeAgo($prop['created_at']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-4">
                لا توجد عقارات مميزة حالياً. كن أول من يضيف عقاراً مميزاً على المنصة!
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Latest Properties Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold mb-1">أحدث العقارات المضافة</h2>
                <p class="text-muted mb-0">تحديث مستمر للعقارات والفرص السكنية والتجارية الجديدة</p>
            </div>
            <a href="<?= App::baseUrl('properties') ?>" class="btn btn-outline-primary fw-semibold">
                جميع العقارات <i class="bi bi-arrow-left"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($latestProperties as $prop): ?>
                <div class="col-lg-4 col-md-6">
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

                            <h5 class="fw-bold mb-2 text-truncate" title="<?= htmlspecialchars($prop['title']) ?>">
                                <a href="<?= App::baseUrl('property/' . $prop['id']) ?>" class="text-decoration-none text-navy">
                                    <?= htmlspecialchars($prop['title']) ?>
                                </a>
                            </h5>

                            <p class="text-muted small mb-3">
                                <i class="bi bi-geo-alt text-danger me-1"></i>
                                <?= htmlspecialchars($prop['governorate_name']) ?> - <?= htmlspecialchars($prop['district_name']) ?>
                            </p>

                            <div class="d-flex justify-content-between align-items-center my-2">
                                <div class="property-price">
                                    <?= View::formatPrice($prop['price'], $prop['currency'], $prop['price_type']) ?>
                                </div>
                                <span class="badge bg-secondary-subtle text-dark border">
                                    <?= View::formatArea($prop['area_size'], $prop['area_unit']) ?>
                                </span>
                            </div>

                            <div class="property-specs">
                                <?php if ($prop['bedrooms'] > 0): ?>
                                    <div class="property-spec-item"><i class="bi bi-door-closed text-primary"></i> <?= $prop['bedrooms'] ?> غرف</div>
                                <?php endif; ?>
                                <?php if ($prop['bathrooms'] > 0): ?>
                                    <div class="property-spec-item"><i class="bi bi-droplet text-info"></i> <?= $prop['bathrooms'] ?> حمام</div>
                                <?php endif; ?>
                                <div class="property-spec-item ms-auto text-success small fw-bold">
                                    <i class="bi bi-check-circle-fill"></i> متاح
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Explore by Governorates -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">تصفح العقارات حسب المحافظة</h2>
            <p class="text-muted">اختر المحافظة لاستكشاف الفلل، الشقق، والأراضي المعروضة بها</p>
        </div>

        <div class="row g-3">
            <?php foreach (array_slice($governorates, 0, 8) as $gov): ?>
                <div class="col-6 col-md-3">
                    <a href="<?= App::baseUrl('properties?governorate_id=' . $gov['id']) ?>" class="gov-card">
                        <i class="bi bi-geo-alt-fill text-primary fs-3 mb-2 d-block"></i>
                        <h6 class="fw-bold mb-1"><?= htmlspecialchars($gov['name_ar']) ?></h6>
                        <span class="badge bg-light text-muted border"><?= number_format($gov['properties_count'] ?? 0) ?> عقار</span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-5" style="background: linear-gradient(135deg, var(--navy-dark) 0%, var(--blue-primary) 100%); color: #fff;">
    <div class="container text-center py-4">
        <h2 class="fw-bold mb-3">هل تملك عقاراً وتريد بيعه أو تأجيره بأسرع وقت وبأعلى عائد؟</h2>
        <p class="lead opacity-90 mx-auto mb-4" style="max-width: 700px;">
            انضم إلى آلاف الملاك والمكاتب العقارية وانشر إعلانك مجاناً لتصل إلى آلاف المشترين والباحثين الجادين في عموم اليمن وخارجه.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= App::baseUrl('dashboard/properties/create') ?>" class="btn btn-warning btn-lg fw-bold px-4 shadow">
                <i class="bi bi-plus-circle me-1"></i> أضف عقارك الآن مجاناً
            </a>
            <a href="<?= App::baseUrl('register') ?>" class="btn btn-outline-light btn-lg px-4">
                تسجيل حساب جديد
            </a>
        </div>
    </div>
</section>
