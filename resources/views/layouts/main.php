<?php
use App\Config\App;
use App\Helpers\Session;
use App\Helpers\Csrf;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'عقار اليمن - بيتك يبدأ من هنا') ?></title>
    
    <!-- Meta tags for AJAX and base routing -->
    <meta name="csrf-token" content="<?= Csrf::token() ?>">
    <meta name="base-url" content="<?= App::baseUrl('') ?>">

    <!-- Bootstrap 5 RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Theme Styles -->
    <link rel="stylesheet" href="<?= App::asset('css/custom.css') ?>">
</head>
<body>

    <!-- Top Announcement & Contact Bar -->
    <div class="bg-dark text-white py-1 px-3 d-none d-md-block" style="background-color: var(--navy-dark) !important; font-size: 0.85rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span><i class="bi bi-telephone-fill text-warning me-1"></i> خدمة العملاء: +967 770 000 001</span>
                <span><i class="bi bi-whatsapp text-success me-1"></i> واتساب الدعم الفني المباشر</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span><i class="bi bi-geo-alt text-danger me-1"></i> يغطي جميع المحافظات الـ 22 في الجمهورية اليمنية</span>
                <span class="badge bg-warning text-dark"><i class="bi bi-shield-check"></i> معاملات موثقة وآمنة</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= App::baseUrl('') ?>">
                <img src="<?= App::asset('images/logo.svg') ?>" alt="عقار اليمن" height="46">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="تبديل القائمة">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-semibold">
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= App::baseUrl('') ?>"><i class="bi bi-house-door me-1"></i> الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= App::baseUrl('properties?purpose=sale') ?>"><i class="bi bi-tag me-1"></i> عقارات للبيع</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= App::baseUrl('properties?purpose=rent') ?>"><i class="bi bi-key me-1"></i> عقارات للإيجار</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= App::baseUrl('offices') ?>"><i class="bi bi-building me-1"></i> المكاتب العقارية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3" href="<?= App::baseUrl('brokers') ?>"><i class="bi bi-people me-1"></i> الوسطاء المعتمدون</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <!-- Dark Mode Toggle Button -->
                    <button class="btn btn-sm btn-outline-secondary rounded-circle" id="darkModeToggle" title="تبديل الوضع الليلي" style="width: 38px; height: 38px;">
                        <i class="bi bi-moon-stars-fill"></i>
                    </button>

                    <?php if (Session::check()): ?>
                        <!-- User Dropdown Menu -->
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5"></i>
                                <span><?= htmlspecialchars(Session::user()['full_name']) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <?php if (Session::isAdmin()): ?>
                                    <li><a class="dropdown-item fw-bold text-danger" href="<?= App::baseUrl('admin') ?>"><i class="bi bi-shield-lock me-2"></i> لوحة الإدارة الرئيسية</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="<?= App::baseUrl('dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i> لوحة التحكم</a></li>
                                <li><a class="dropdown-item" href="<?= App::baseUrl('dashboard/properties') ?>"><i class="bi bi-houses me-2"></i> عقاراتي</a></li>
                                <li><a class="dropdown-item" href="<?= App::baseUrl('dashboard/favorites') ?>"><i class="bi bi-heart me-2"></i> المفضلة</a></li>
                                <li><a class="dropdown-item" href="<?= App::baseUrl('dashboard/inquiries') ?>"><i class="bi bi-chat-left-dots me-2"></i> الاستفسارات</a></li>
                                <li><a class="dropdown-item" href="<?= App::baseUrl('dashboard/profile') ?>"><i class="bi bi-person-gear me-2"></i> إعدادات الحساب</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= App::baseUrl('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> تسجيل الخروج</a></li>
                            </ul>
                        </div>

                        <!-- Add Property CTA -->
                        <a href="<?= App::baseUrl('dashboard/properties/create') ?>" class="btn btn-primary fw-bold px-3">
                            <i class="bi bi-plus-circle me-1"></i> أضف عقارك الآن
                        </a>
                    <?php else: ?>
                        <!-- Login / Register Buttons -->
                        <a href="<?= App::baseUrl('login') ?>" class="btn btn-outline-primary fw-semibold px-3">
                            <i class="bi bi-box-arrow-in-left me-1"></i> تسجيل الدخول
                        </a>
                        <a href="<?= App::baseUrl('register') ?>" class="btn btn-primary fw-bold px-3">
                            <i class="bi bi-plus-circle me-1"></i> أعلن عن عقارك مجاناً
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages Container -->
    <div class="container mt-3">
        <?php foreach (['success', 'danger', 'warning', 'info'] as $msgType): ?>
            <?php if (Session::hasFlash($msgType)): ?>
                <div class="alert alert-<?= $msgType ?> alert-dismissible fade show shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <?php if ($msgType === 'success'): ?><i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <?php elseif ($msgType === 'danger'): ?><i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <?php elseif ($msgType === 'warning'): ?><i class="bi bi-exclamation-circle-fill fs-5 me-2"></i>
                        <?php else: ?><i class="bi bi-info-circle-fill fs-5 me-2"></i><?php endif; ?>
                        <div><?= Session::getFlash($msgType) ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="إغلاق"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Main View Content -->
    <main>
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="footer-section mt-5" style="background-color: var(--navy-dark); color: #94A3B8; border-top: 3px solid var(--gold);">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="mb-3">
                        <img src="<?= App::asset('images/logo.svg') ?>" alt="عقار اليمن" height="52" style="filter: brightness(0) invert(1);">
                    </div>
                    <p class="text-light opacity-75">
                        «عقار اليمن... بيتك يبدأ من هنا». المنصة العقارية الإلكترونية المتكاملة الأولى لخدمة السوق اليمني في كافة المحافظات للبيع، الشراء، الإيجار والاستثمار العقاري الموثق.
                    </p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width:36px;height:36px;"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width:36px;height:36px;"><i class="bi bi-whatsapp"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width:36px;height:36px;"><i class="bi bi-telegram"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle" style="width:36px;height:36px;"><i class="bi bi-twitter-x"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="text-white mb-3 fw-bold">روابط سريعة</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?= App::baseUrl('properties?purpose=sale') ?>" class="text-decoration-none text-light opacity-75">عقارات للبيع</a></li>
                        <li class="mb-2"><a href="<?= App::baseUrl('properties?purpose=rent') ?>" class="text-decoration-none text-light opacity-75">عقارات للإيجار</a></li>
                        <li class="mb-2"><a href="<?= App::baseUrl('offices') ?>" class="text-decoration-none text-light opacity-75">دليل المكاتب العقارية</a></li>
                        <li class="mb-2"><a href="<?= App::baseUrl('brokers') ?>" class="text-decoration-none text-light opacity-75">دليل الوسطاء والسماسرة</a></li>
                        <li class="mb-2"><a href="<?= App::baseUrl('dashboard/properties/create') ?>" class="text-decoration-none text-warning fw-bold">أضف عقارك مجاناً</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-3 fw-bold">أبرز المحافظات</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?= App::baseUrl('properties?governorate_id=1') ?>" class="text-decoration-none text-light opacity-75">عقارات صنعاء (أمانة العاصمة)</a></li>
                        <li class="mb-2"><a href="<?= App::baseUrl('properties?governorate_id=3') ?>" class="text-decoration-none text-light opacity-75">عقارات عدن (خور مكسر، المعلا)</a></li>
                        <li class="mb-2"><a href="<?= App::baseUrl('properties?governorate_id=4') ?>" class="text-decoration-none text-light opacity-75">عقارات تعز</a></li>
                        <li class="mb-2"><a href="<?= App::baseUrl('properties?governorate_id=5') ?>" class="text-decoration-none text-light opacity-75">عقارات حضرموت (المكلا، سيئون)</a></li>
                        <li class="mb-2"><a href="<?= App::baseUrl('properties?governorate_id=6') ?>" class="text-decoration-none text-light opacity-75">عقارات إب ومأرب</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white mb-3 fw-bold">المساعد العقاري والأمان</h5>
                    <div class="p-3 rounded" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="fw-bold text-warning mb-1"><i class="bi bi-robot me-1"></i> هل تحتاج لمساعدة؟</div>
                        <p class="small text-light opacity-75 mb-2">استخدم «مساعد عقار اليمن الذكي» في أسفل الشاشة للبحث المباشر والإجابة عن المصطلحات العقارية كالبصيرة واللبنة.</p>
                        <button class="btn btn-sm btn-outline-warning w-100" onclick="document.getElementById('chatbotBubble').click();">
                            فتح المساعد العقاري
                        </button>
                    </div>
                </div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.1);" class="my-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center text-light opacity-75 small">
                <div>جميع الحقوق محفوظة &copy; <?= date('Y') ?> منصة <strong>عقار اليمن</strong> - Yemen Real Estate Platform.</div>
                <div class="d-flex gap-3 mt-2 mt-md-0">
                    <span>صُمم وطُوّر وفق أعلى معايير الأمان والجودة</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Smart Real Estate Assistant Widget -->
    <div id="chatbotBubble" class="chatbot-bubble" title="مساعد عقار اليمن الذكي">
        <i class="bi bi-chat-dots-fill fs-4"></i>
    </div>

    <div id="chatbotWindow" class="chatbot-window">
        <div class="chatbot-header">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                    <i class="bi bi-robot"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size:0.95rem;">مساعد عقار اليمن الذكي</div>
                    <div class="text-success small" style="font-size:0.75rem;"><i class="bi bi-circle-fill" style="font-size:8px;"></i> متصل الآن بقاعدة البيانات</div>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" id="chatbotClose" aria-label="إغلاق"></button>
        </div>

        <div class="chatbot-body" id="chatbotBody">
            <div class="chat-bubble-bot">
                مرحباً بك في <strong>عقار اليمن</strong>! أنا مساعدك العقاري الذكي.<br>
                أستطيع مساعدتك في العثور على عقار مناسب للبيع أو الإيجار، شرح المصطلحات العقارية كـ <em>اللبنة والبصيرة</em>، أو إرشادك لنشر عقارك.<br>
                <div class="chatbot-suggestions mt-2">
                    <span class="chatbot-chip" data-prompt="أريد شراء فيلا في صنعاء">شراء فيلا بصنعاء</span>
                    <span class="chatbot-chip" data-prompt="شقق للإيجار في عدن">شقق للإيجار بعدن</span>
                    <span class="chatbot-chip" data-prompt="ما هي اللبنة الصنعانية؟">شرح اللبنة الصنعانية</span>
                    <span class="chatbot-chip" data-prompt="كيف أنشر عقاري؟">طريقة نشر عقار</span>
                </div>
            </div>
        </div>

        <form id="chatbotForm" class="chatbot-footer">
            <input type="text" id="chatbotInput" class="form-control form-control-sm" placeholder="اكتب سؤالك أو طلبك العقاري هنا..." autocomplete="off">
            <button type="submit" class="btn btn-sm btn-primary px-3">
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Application Scripts -->
    <script src="<?= App::asset('js/app.js') ?>"></script>
    <script src="<?= App::asset('js/chatbot.js') ?>"></script>
</body>
</html>
