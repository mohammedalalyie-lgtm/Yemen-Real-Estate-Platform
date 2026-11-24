-- عقار اليمن (Yemen Real Estate Platform)
-- Seed Data: Yemen Authentic Geo, Categories, Features, Users, and Sample Listings
USE `yemen_real_estate`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Roles
INSERT INTO `roles` (`id`, `slug`, `name`, `description`) VALUES
(1, 'super_admin', 'المالك الرئيسي', 'صاحب أعلى الصلاحيات وإدارة النظام بالكامل'),
(2, 'admin', 'مدير النظام', 'مراجعة الإعلانات وإدارة المحتوى والمستخدمين'),
(3, 'office', 'مكتب عقاري', 'مكتب معتمد لإدارة السماسرة والعقارات'),
(4, 'broker', 'وسيط عقاري / سمسار', 'وسيط عقاري مستقل أو تابع لمكتب'),
(5, 'owner', 'مالك عقار', 'أفراد وملاك يعرضون عقاراتهم الخاصة'),
(6, 'customer', 'عميل / باحث', 'باحث عن عقارات، حفظ المفضلة، إرسال استفسارات');

-- 2. Permissions
INSERT INTO `permissions` (`slug`, `name`, `module`) VALUES
('manage_all', 'إدارة كاملة للنظام', 'system'),
('manage_users', 'إدارة المستخدمين', 'users'),
('review_properties', 'مراجعة واعتماد الإعلانات', 'properties'),
('manage_locations', 'إدارة المحافظات والمديريات', 'locations'),
('manage_categories', 'إدارة التصنيفات', 'categories'),
('manage_reports', 'إدارة البلاغات ومكافحة الاحتيال', 'reports'),
('manage_verifications', 'مراجعة طلبات التوثيق', 'verifications'),
('manage_settings', 'إدارة إعدادات المنصة', 'settings'),
('manage_plans', 'إدارة الباقات والاشتراكات', 'plans');

-- 3. Super Admin & Demo Users
-- Password for all demo accounts: Admin@123456
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password_hash`, `role`, `is_verified`, `is_active`, `bio`, `whatsapp`) VALUES
(1, 'المهندس / مدير منصة عقار اليمن', 'admin@aqar-yemen.com', '+967770000001', '$2y$10$4pMZSSqI2Awf1Pu6/r94FOgB3aprOEv9h4Qgi0sEa2GeIkoMKDCfm', 'super_admin', 1, 1, 'الحساب الإداري الرئيسي لمنصة عقار اليمن', '+967770000001'),
(2, 'عبدالله المقطري (مدير العمليات)', 'moderator@aqar-yemen.com', '+967770000002', '$2y$10$4pMZSSqI2Awf1Pu6/r94FOgB3aprOEv9h4Qgi0sEa2GeIkoMKDCfm', 'admin', 1, 1, 'مدير مراجعة وتدقيق الإعلانات', '+967770000002'),
(3, 'مكتب البركة للخدمات العقارية', 'albaraka@aqar-yemen.com', '+967771234567', '$2y$10$4pMZSSqI2Awf1Pu6/r94FOgB3aprOEv9h4Qgi0sEa2GeIkoMKDCfm', 'office', 1, 1, 'مكتب عقاري رائد في صنعاء متخصص في بيع وشراء وتأجير الفلل والأراضي والشقق الاستثمارية.', '+967771234567'),
(4, 'سامي ناصر الحميري', 'sami.broker@aqar-yemen.com', '+967772345678', '$2y$10$4pMZSSqI2Awf1Pu6/r94FOgB3aprOEv9h4Qgi0sEa2GeIkoMKDCfm', 'broker', 1, 1, 'وسيط عقاري معتمد وخبير في العقارات السكنية والتجارية بمحافظتي صنعاء وعدن منذ 10 سنوات.', '+967772345678'),
(5, 'الحاج صالح اليافعي', 'owner.saleh@aqar-yemen.com', '+967733456789', '$2y$10$4pMZSSqI2Awf1Pu6/r94FOgB3aprOEv9h4Qgi0sEa2GeIkoMKDCfm', 'owner', 1, 1, 'مالك ومستثمر عقاري في صنعاء وعدن.', '+967733456789'),
(6, 'أحمد فؤاد الأهدل', 'client.yemen@aqar-yemen.com', '+967711223344', '$2y$10$4pMZSSqI2Awf1Pu6/r94FOgB3aprOEv9h4Qgi0sEa2GeIkoMKDCfm', 'customer', 0, 1, 'مستخدم يبحث عن شقة عائلية للإيجار في صنعاء أو عدن.', '+967711223344');

-- 4. Real Estate Office
INSERT INTO `offices` (`id`, `owner_id`, `name`, `slug`, `license_number`, `phone`, `email`, `whatsapp`, `address`, `description`, `is_verified`, `is_active`) VALUES
(1, 3, 'مكتب البركة للخدمات العقارية والاستثمار', 'albaraka-real-estate', 'YE-SAN-2024-889', '+967771234567', 'albaraka@aqar-yemen.com', '+967771234567', 'صنعاء - شارع حدة - أمام مجمع حدة التجاري', 'نقدم خدمات التقييم العقاري والوساطة المعتمدة وبيع وشراء الأراضي والمباني الاستثمارية بأعلى معايير الشفافية والأمانة.', 1, 1);

-- Link broker to office
UPDATE `users` SET `office_id` = 1 WHERE `id` = 4;
INSERT INTO `office_members` (`office_id`, `broker_id`, `role_title`, `status`) VALUES
(1, 4, 'كبير الوسطاء العقاريين', 'active');

-- 5. Yemeni Governorates (All 22 governorates)
INSERT INTO `governorates` (`id`, `name_ar`, `name_en`, `slug`, `sort_order`, `is_active`) VALUES
(1, 'أمانة العاصمة (صنعاء)', 'Sana\'a City', 'sanaa-capital', 1, 1),
(2, 'محافظة صنعاء', 'Sana\'a Governorate', 'sanaa-gov', 2, 1),
(3, 'عدن', 'Aden', 'aden', 3, 1),
(4, 'تعز', 'Taiz', 'taiz', 4, 1),
(5, 'حضرموت', 'Hadhramaut', 'hadhramaut', 5, 1),
(6, 'إب', 'Ibb', 'ibb', 6, 1),
(7, 'الحديدة', 'Al Hudaydah', 'hudaydah', 7, 1),
(8, 'مأرب', 'Marib', 'marib', 8, 1),
(9, 'ذمار', 'Dhamar', 'dhamar', 9, 1),
(10, 'شبوة', 'Shabwah', 'shabwah', 10, 1),
(11, 'المهرة', 'Al Mahrah', 'almahrah', 11, 1),
(12, 'لحج', 'Lahij', 'lahij', 12, 1),
(13, 'أبين', 'Abyan', 'abyan', 13, 1),
(14, 'صعدة', 'Saada', 'saada', 14, 1),
(15, 'عمران', 'Amran', 'amran', 15, 1),
(16, 'حجة', 'Hajjah', 'hajjah', 16, 1),
(17, 'البيضاء', 'Al Bayda', 'albayda', 17, 1),
(18, 'الضالع', 'Al Dhale', 'aldhale', 18, 1),
(19, 'ريمة', 'Raymah', 'raymah', 19, 1),
(20, 'المحويت', 'Al Mahwit', 'almahwit', 20, 1),
(21, 'الجوف', 'Al Jawf', 'aljawf', 21, 1),
(22, 'سقطرى', 'Socotra', 'socotra', 22, 1);

-- 6. Districts for Key Cities
INSERT INTO `districts` (`id`, `governorate_id`, `name_ar`, `name_en`, `slug`, `sort_order`) VALUES
-- أمانة العاصمة
(1, 1, 'مديرية السبعين', 'As Sabain', 'as-sabain', 1),
(2, 1, 'مديرية الوحدة (حدة)', 'Al Wahdah', 'al-wahdah', 2),
(3, 1, 'مديرية معين', 'Mueen', 'mueen', 3),
(4, 1, 'مديرية التحرير', 'At Tahrir', 'at-tahrir', 4),
(5, 1, 'مديرية الصافية', 'As Safiyah', 'as-safiyah', 5),
(6, 1, 'مديرية الثورة', 'Ath Thawrah', 'ath-thawrah', 6),
(7, 1, 'مديرية بني الحارث', 'Bani Al Harith', 'bani-al-harith', 7),
(8, 1, 'مديرية صنعاء القديمة', 'Old City', 'old-city', 8),
-- محافظة صنعاء
(9, 2, 'سنحان وبني بهلول', 'Sanhan', 'sanhan', 1),
(10, 2, 'بني مطر', 'Bani Matar', 'bani-matar', 2),
(11, 2, 'همدان', 'Hamdan', 'hamdan', 3),
-- عدن
(12, 3, 'مديرية صيرة (كريتر)', 'Crater', 'crater', 1),
(13, 3, 'مديرية خور مكسر', 'Khor Maksar', 'khor-maksar', 2),
(14, 3, 'مديرية المعلا', 'Al Mualla', 'al-mualla', 3),
(15, 3, 'مديرية التواهي', 'At Tawahi', 'at-tawahi', 4),
(16, 3, 'مديرية المنصورة', 'Al Mansoora', 'al-mansoora', 5),
(17, 3, 'مديرية الشيخ عثمان', 'Ash Shaikh Outhman', 'ash-shaikh-outhman', 6),
(18, 3, 'مديرية دار سعد', 'Dar Sad', 'dar-sad', 7),
(19, 3, 'مديرية البريقة', 'Al Buraiqeh', 'al-buraiqeh', 8),
-- تعز
(20, 4, 'مديرية المظفر', 'Al Mudhaffar', 'al-mudhaffar', 1),
(21, 4, 'مديرية القاهرة', 'Al Qahirah', 'al-qahirah', 2),
(22, 4, 'مديرية صالة', 'Salah', 'salah', 3),
-- حضرموت
(23, 5, 'مديرية المكلا', 'Al Mukalla', 'al-mukalla', 1),
(24, 5, 'مديرية سيئون', 'Sayun', 'sayun', 2),
(25, 5, 'مديرية تريم', 'Tarim', 'tarim', 3),
(26, 5, 'مديرية الشحر', 'Ash Shihr', 'ash-shihr', 4),
-- إب
(27, 6, 'مديرية الظهار', 'Ad Dhihar', 'ad-dhihar', 1),
(28, 6, 'مديرية المشنة', 'Al Mashannah', 'al-mashannah', 2),
-- مأرب
(29, 8, 'مديرية مدينة مأرب', 'Marib City', 'marib-city', 1),
(30, 8, 'مديرية الوادي', 'Al Wadi', 'al-wadi', 2);

-- 7. Areas (Popular Neighborhoods)
INSERT INTO `areas` (`id`, `district_id`, `name_ar`, `name_en`, `slug`) VALUES
-- أمانة العاصمة / الوحدة والسبعين ومعين
(1, 2, 'حي حدة السكني', 'Hadda Residential', 'hadda-residential'),
(2, 2, 'شارع الأربعين - حدة', 'Arbaeen Street', 'arbaeen-hadda'),
(3, 1, 'حي بيت بوس', 'Bait Baws', 'bait-baws'),
(4, 1, 'حي الأصبحي', 'Al Asbahi', 'al-asbahi'),
(5, 1, 'شارع الخمسين', 'Khamseen Street', 'khamseen-street'),
(6, 1, 'حي حي فج عطان', 'Fajj Attan', 'fajj-attan'),
(7, 3, 'شارع الستين الغربي', 'West Sittin Street', 'west-sittin'),
(8, 3, 'حي عصر', 'Asr District', 'asr-district'),
(9, 6, 'شارع المطار', 'Airport Road', 'airport-road'),
(10, 6, 'حي الحصبة', 'Al Hasabah', 'al-hasabah'),
-- عدن
(11, 13, 'حي السفارات - خور مكسر', 'Embassies Area', 'embassies-khor-maksar'),
(12, 13, 'ساحل أبين', 'Abyan Coast', 'abyan-coast'),
(13, 14, 'الشارع الرئيسي - المعلا', 'Main Street Mualla', 'main-street-mualla'),
(14, 12, 'حي الخساف - كريتر', 'Al Khasaf', 'al-khasaf'),
(15, 16, 'حي كابوتا - المنصورة', 'Kabota', 'kabota'),
(16, 16, 'ريمي - المنصورة', 'Remi', 'remi'),
-- المكلا
(17, 23, 'حي فوه السكني', 'Fowah', 'fowah'),
(18, 23, 'حي الديس', 'Ad Dees', 'ad-dees');

-- 8. Categories
INSERT INTO `property_categories` (`id`, `name_ar`, `name_en`, `slug`, `type_group`, `icon`, `sort_order`) VALUES
(1, 'فيلا راقية', 'Luxury Villa', 'luxury-villa', 'residential', 'home', 1),
(2, 'شقة سكنية', 'Apartment', 'apartment', 'residential', 'building', 2),
(3, 'منزل / بيت مستقل', 'Independent House', 'house', 'residential', 'home', 3),
(4, 'عمارة استثمارية', 'Commercial/Residential Building', 'building', 'residential', 'layers', 4),
(5, 'دور مستقل', 'Single Floor', 'single-floor', 'residential', 'layout', 5),
(6, 'استراحة / منتجع', 'Resort / Rest House', 'resort', 'residential', 'coffee', 6),
(7, 'مكتب تجاري', 'Commercial Office', 'commercial-office', 'commercial', 'briefcase', 7),
(8, 'محل / معرض تجاري', 'Shop / Showroom', 'shop-showroom', 'commercial', 'shopping-bag', 8),
(9, 'مخزن / هنجر', 'Warehouse / Storage', 'warehouse', 'commercial', 'package', 9),
(10, 'مبنى تجاري كامل', 'Commercial Building', 'commercial-building', 'commercial', 'hotel', 10),
(11, 'أرض سكنية', 'Residential Land', 'residential-land', 'land', 'map-pin', 11),
(12, 'أرض تجارية / استثمارية', 'Commercial Land', 'commercial-land', 'land', 'trending-up', 12),
(13, 'أرض زراعية / مزرعة', 'Agricultural Land / Farm', 'agricultural-land', 'land', 'sun', 13);

-- 9. Features
INSERT INTO `features` (`id`, `name_ar`, `name_en`, `icon`, `category`) VALUES
(1, 'منظومة طاقة شمسية متكاملة', 'Solar Energy System', 'sun', 'services'),
(2, 'خزان مياه أرضي خرساني مستقل', 'Underground Water Tank', 'droplet', 'services'),
(3, 'بئر ارتوازي للمياه', 'Artesian Water Well', 'droplets', 'services'),
(4, 'واجهة حجر دوران طبيعي', 'Natural Stone Facade', 'shield', 'construction'),
(5, 'مصعد كهربائي إيطالي', 'Modern Elevator', 'arrow-up-circle', 'building'),
(6, 'موقف سيارات / كراج خاص', 'Private Parking', 'truck', 'amenities'),
(7, 'حراسة وكاميرات مراقبة 24/7', 'Security & CCTV', 'video', 'security'),
(8, 'مدخل مستقل وحوش واسع', 'Independent Entrance & Yard', 'maximize', 'amenities'),
(9, 'شارع مسفلت نافذ 16-24 متر', 'Asphalt Commercial Street', 'navigation', 'location'),
(10, 'تشطيب سوبر ديلوكس VIP', 'Super Deluxe VIP Finishing', 'award', 'finishing'),
(11, 'تكييف مركزي / سبليت راكب', 'AC Installed', 'wind', 'amenities'),
(12, 'وثائق بصيرة معمدة شرعاً وسجل عقاري', 'Legal Title Deed & Real Estate Registry', 'file-check', 'legal'),
(13, 'موقع ركني على شارعين', 'Corner Plot (2 Streets)', 'compass', 'location'),
(14, 'جلسة ودكة مع مسبح', 'Swimming Pool & Terrace', 'umbrella', 'luxury');

-- 10. Sample Properties with Authentic Yemeni Real Estate Details
INSERT INTO `properties` (
    `id`, `user_id`, `office_id`, `title`, `slug`, `description`, `purpose`,
    `category_id`, `governorate_id`, `district_id`, `area_id`, `address_details`,
    `price`, `currency`, `price_type`, `is_negotiable`, `area_size`, `area_unit`,
    `bedrooms`, `bathrooms`, `living_rooms`, `floors`, `floor_number`, `building_year`,
    `direction`, `latitude`, `longitude`, `status`, `is_featured`, `views_count`, `inquiries_count`, `published_at`
) VALUES
(
    1, 4, 1, 'فيلا ملكية فاخرة بتشطيب سوبر VIP في أرقى أحياء حدة',
    'luxury-royal-villa-hadda-sanaa',
    'فرصة استثمارية استثنائية: فيلا ملكية بتصميم هندسي حديث وواجهات حجر دوران مشرب كامل.\nالموقع: صنعاء - حي حدة السكني الراقي، بالقرب من السفارات والخدمات الحيوية.\nالمساحة: 12 لبنة حر معمدة في السجل العقاري.\nتتكون الفيلا من 3 طوابق وبدروم كامل:\n- الطابق الأرضي: أجنحة استقبال واسعة وصالون ضيوف ملكي مع صالة طعام ومطبخ تركي مؤثث.\n- الطوابق العلوية: 6 غرف نوم ماستر فسيحة مع حمامات جاكوزي وشرفات مطلة.\n- البدروم: صالة مناسبات ونادي صحي ومسبح داخلي.\nالملحقات: منظومة طاقة شمسية بقوة 10 كيلوواط، بئر ماء ارتوازي، حديقة منسقة، كراج يتسع لـ 5 سيارات، غرفة حراسة مستقلة مع حمام.\nالوثائق بصيرة حرة معمدة شرعاً ومسجلة في السجل العقاري والتسليم فوري.',
    'sale', 1, 1, 2, 1, 'حي حدة - خلف فندق شيراتون سابقاً',
    850000.00, 'USD', 'total', 1, 12.00, 'labna',
    6, 7, 3, 3, 0, 2023,
    'قبلية غربية', 15.31250000, 44.18520000, 'approved', 1, 1420, 28, NOW()
),
(
    2, 3, 1, 'عمارة استثمارية تجارية وسكنية ركنية دخل شهري ممتاز - بيت بوس',
    'commercial-residential-building-bait-baws-sanaa',
    'للبيع عمارة تجارية استثمارية دخلها الشهري ممتاز ومضمون.\nالموقع: صنعاء - بيت بوس - شارع الـ 24 التجاري النافذ.\nالمساحة: 8 لبن حر معمدة.\nالمواصفات: تتكون من 6 طوابق:\n- الطابق الأرضي: 4 فتحات محلات تجارية مؤجرة + مدخل سيارات واسع وخزان أرضي يتسع لـ 6 وايتات.\n- 10 شقق سكنية واسعة بتشطيب لوكس (كل شقة 4 غرف وصالة وحمامين ومطبخ).\n- مصعد كهربائي إيطالي حمولة 6 ركاب راكب وشغال.\n- واجهة حجر بيضاء فاخرة.\n- البصيرة أصل معمدة ومثبتة رسمياً.\nالدخل الشهري الحالي: 5,500 دولار أمريكي شهرياً، قابلة للزيادة.',
    'sale', 4, 1, 1, 3, 'بيت بوس - قرب جولة الثقافة - شارع الـ 24',
    680000.00, 'USD', 'total', 1, 8.00, 'labna',
    20, 16, 10, 6, 0, 2022,
    'عدنية شرقية', 15.28900000, 44.20140000, 'approved', 1, 2150, 42, NOW()
),
(
    3, 5, NULL, 'شقة تمليك فخمة مطلة على كورنيش ساحل أبين - خور مكسر',
    'luxury-sea-view-apartment-khor-maksar-aden',
    'شقة سكنية راقية للبيع المباشر من المالك دون وسيط.\nالموقع: عدن - خور مكسر - حي السفارات أمام ساحل أبين مباشرة.\nالمساحة: 185 متر مربع.\nتتكون من: 3 غرف نوم (غرفة نوم ماستر بحمام خاص)، صالة معيشة رحبة بإطلالة بحرية بانورامية مفتوحة، مجلس ضيوف منعزل بحمام ومغاسل رخام، مطبخ مجهز بدواليب خشب زان، بلكونة واسعة مطلة على البحر.\nالمبنى مزود بمصعد كهربائي حديث، ومولد كهربائي احتياطي يغطي تشغيل التكييف 24 ساعة، وخزان مياه مركزي، وموقف سيارة خاص مرقم.\nالملكية طابو جاهز للتنازل الفوري.',
    'sale', 2, 3, 13, 11, 'خور مكسر - بالقرب من قاعة وضاح والساحل',
    95000.00, 'USD', 'total', 1, 185.00, 'sqm',
    3, 3, 2, 1, 4, 2021,
    'بحرية جنوبية', 12.80210000, 45.03150000, 'approved', 1, 890, 19, NOW()
),
(
    4, 4, 1, 'شقة عائلية مؤثثة بالكامل للإيجار الشهري الراقي - الأصبحي',
    'furnished-family-apartment-for-rent-asbahi',
    'شقة فاخرة مفروشة بأثاث راقٍ وجديد للإيجار السنوي أو الشهري للعائلات أو موظفي المنظمات والشركات.\nالموقع: صنعاء - حي الأصبحي الهادئ، قريب من السوبرماركت والمدارس.\nالمساحة: 160 متر مربع.\nالمحتويات: مجلس ضيوف مؤثث، صالة جلوس عائلية مع شاشة ذكية، 3 غرف نوم مجهزة بأسرة وخزائن حديثة، حمامين، مطبخ متكامل بجميع الأجهزة الكهربائية (ثلاجة، غسالة أوتوماتيك، فرن غاز، مايكرويف).\nخدمات إضافية: منظومة طاقة شمسية تعمل بكفاءة على مدار الساعة، خدمة وايفاي، خزان ماء وافر، حارس للعمارة، وموقف سيارة.',
    'rent', 2, 1, 1, 4, 'حي الأصبحي - بالقرب من حديقة الأصبحي وشارع المقالح',
    450.00, 'USD', 'per_month', 1, 160.00, 'sqm',
    3, 2, 2, 1, 2, 2020,
    'غربية', 15.30120000, 44.21800000, 'approved', 0, 620, 14, NOW()
),
(
    5, 3, 1, 'أرض سكنية استثمارية مسورة 10 لبن حر في مخطط راقٍ - فج عطان',
    'residential-land-10-labna-fajj-attan-sanaa',
    'أرض سكنية ممتازة للبيع في أرقى مواقع فج عطان السكنية المطلة.\nالمساحة: 10 لبن صنعاني حر.\nالشوارع: تقع على شارع 14 متر نافذ ومسفلت، واجهة عريضة 22 متراً على الشارع.\nالأرض مستوية ومقصوصة جاهزة للبناء فوراً دون الحاجة لحفريات مكلفة.\nموقع هادئ جداً وفلل حديثة مجاورة، واصلة إليها كافة الخدمات (كهرباء، ماء، شبكة إنترنت).\nبصيرة حرة أصل معمدة في المحكمة والسجل العقاري، والتنازل فوري من المالك المباشر.',
    'sale', 11, 1, 1, 6, 'فج عطان - المخطط السكني الجديد',
    180000000.00, 'YER', 'total', 1, 10.00, 'labna',
    0, 0, 0, 0, 0, NULL,
    'شرقية', 15.32100000, 44.17200000, 'approved', 1, 1100, 16, NOW()
),
(
    6, 5, NULL, 'فيلا عصرية دورين وحوش سيارات للإيجار الدبلوماسي - المعلا عدن',
    'modern-two-floor-villa-rent-mualla-aden',
    'فيلا مستقلة راقية للإيجار للشركات الأجنبية أو المنظمات أو العائلات الدبلوماسية.\nالموقع: عدن - مديرية المعلا.\nالمساحة: 400 متر مربع.\nتتكون من دورين:\n- الدور الأول: ريسبشن واسع، مجلس ضيوف، مكتب أعمال، صالة طعام، ومطبخ كبير.\n- الدور الثاني: 4 غرف نوم ماستر مع بلكونات واسعة.\nالمميزات: حديقة خضراء منسقة، كراج يتسع لـ 3 سيارات، منظومة طاقة شمسية + مولد صامت، خزان مياه 10 ألف لتر، حراسة وسور عالي مع سلك شائك وكاميرات مراقبة.',
    'rent', 1, 3, 14, 13, 'المعلا - بالقرب من الميناء والشارع الرئيسي',
    1800.00, 'USD', 'per_month', 1, 400.00, 'sqm',
    4, 5, 2, 2, 0, 2022,
    'بحرية', 12.78900000, 45.01200000, 'approved', 0, 480, 8, NOW()
),
(
    7, 4, 1, 'محل تجاري بموقع استراتيجي على شارع الستين الغربي - صنعاء',
    'prime-commercial-shop-west-sittin-sanaa',
    'محل تجاري بمعرض ميزانين للإيجار السنوي في موقع تجاري نشط جداً على مدار الساعة.\nالموقع: صنعاء - شارع الستين الغربي بالقرب من جولة عصر.\nالمساحة: 60 متر مربع في الدور الأرضي + 40 متر سدة (ميزانين).\nالتجهيزات: واجهة زجاجية سيكوريت عريضة، أرضيات بورسلان، إنارة سبوت لايت، حمام خاص، سحب كهربائي ثلاثي الفاز.\nمناسب لكبرى الشركات، معارض هواتف، صيدلية نموذجية، أو مكاتب صرافة وتحويل أموال.',
    'rent', 8, 1, 3, 7, 'شارع الستين الغربي - بجانب المحلات التجارية الكبرى',
    800.00, 'USD', 'per_month', 1, 100.00, 'sqm',
    0, 1, 0, 1, 0, 2021,
    'غربية على شارع رئيسي', 15.34100000, 44.18000000, 'approved', 0, 750, 11, NOW()
),
(
    8, 5, NULL, 'منزل شعبي حديث بحوش ومدخل سيارة - حي الدائري تعز',
    'modern-independent-house-taiz',
    'منزل دورين مسلح قواعد وبناء حجر نظيف للبيع بمدينة تعز.\nالموقع: تعز - المظفر.\nالمساحة: 200 متر مربع.\nيحتوي على: دور أول شقة كاملة 3 غرف وحمامين ومطبخ وصالة، والدور الثاني شقة مستقلة بنفس التصميم.\nحوش سيارة، خزان أرضي مستقل، موقع قريب من الخدمات والمدارس والشارع العام.\nالبصيرة شرعية حرة ومسجلة.',
    'sale', 3, 4, 20, NULL, 'تعز - المظفر - قرب الدائري',
    85000000.00, 'YER', 'total', 1, 200.00, 'sqm',
    6, 4, 2, 2, 0, 2019,
    'جنوبية شرقية', 13.57950000, 44.02050000, 'approved', 0, 530, 9, NOW()
),
(
    9, 5, NULL, 'شقة تحت التشطيب في حي المنصورة - بانتظار المراجعة (تجربة Admin)',
    'under-construction-apartment-mansoora-aden',
    'شقة سكنية قيد الإنشاء في المنصورة عدن، مناسبة للمعاينة في لوحة تحكم الإدارة كإعلان بانتظار الموافقة والاعتماد.',
    'sale', 2, 3, 16, 15, 'حي كابوتا - المنصورة',
    35000.00, 'USD', 'total', 1, 120.00, 'sqm',
    3, 2, 1, 1, 2, 2024,
    'شمالية', 12.85000000, 44.98000000, 'pending', 0, 12, 0, NULL
);

-- 11. Property Features Mapping
INSERT INTO `property_features` (`property_id`, `feature_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 6), (1, 7), (1, 8), (1, 10), (1, 12), (1, 14),
(2, 2), (2, 4), (2, 5), (2, 6), (2, 7), (2, 9), (2, 10), (2, 12), (2, 13),
(3, 5), (3, 6), (3, 7), (3, 10), (3, 11), (3, 12),
(4, 1), (4, 2), (4, 6), (4, 7), (4, 10), (4, 11),
(5, 8), (5, 9), (5, 12),
(6, 1), (6, 2), (6, 6), (6, 7), (6, 8), (6, 10), (6, 11),
(7, 7), (7, 9), (7, 10);

-- 12. Property Images (Default mock image representations stored cleanly in uploads/properties)
INSERT INTO `property_images` (`property_id`, `image_path`, `is_primary`, `sort_order`) VALUES
(1, 'villa_hadda_1.svg', 1, 1),
(1, 'villa_hadda_2.svg', 0, 2),
(1, 'villa_hadda_3.svg', 0, 3),
(2, 'building_baitbaws_1.svg', 1, 1),
(2, 'building_baitbaws_2.svg', 0, 2),
(3, 'apartment_khor_1.svg', 1, 1),
(3, 'apartment_khor_2.svg', 0, 2),
(4, 'apartment_asbahi_1.svg', 1, 1),
(5, 'land_attan_1.svg', 1, 1),
(6, 'villa_mualla_1.svg', 1, 1),
(7, 'shop_sittin_1.svg', 1, 1),
(8, 'house_taiz_1.svg', 1, 1),
(9, 'apartment_mansoora_1.svg', 1, 1);

-- 13. Subscription Plans
INSERT INTO `subscription_plans` (`id`, `name`, `slug`, `price`, `currency`, `duration_days`, `max_listings`, `max_featured`, `allow_brokers`, `features_json`, `is_active`) VALUES
(1, 'الباقة المجانية (الأفراد والملاك)', 'free-plan', 0.00, 'YER', 365, 3, 0, 0, '["إضافة حتى 3 عقارات", "لوحة تحكم أساسية", "دعم فني عبر البريد"]', 1),
(2, 'باقة الوسيط المحترف (الفضية)', 'silver-broker-plan', 15000.00, 'YER', 30, 25, 3, 0, '["إضافة حتى 25 عقار", "3 عقارات مميزة في الصفحة الأولى", "شارة وسيط موثق", "إحصائيات تفصيلية بالمشاهدات"]', 1),
(3, 'باقة المكاتب والشركات العقارية (الذهبية)', 'gold-office-plan', 45000.00, 'YER', 30, 100, 10, 1, '["إضافة حتى 100 عقار", "إدارة عدد غير محدود من السماسرة", "10 عقارات مميزة ومثبتة", "صفحة خاصة بالمكتب مع الشعار", "تقارير تصدير متقدمة"]', 1);

-- Subscriptions demo
INSERT INTO `subscriptions` (`user_id`, `plan_id`, `starts_at`, `ends_at`, `status`) VALUES
(3, 3, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 'active'),
(4, 2, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 'active'),
(5, 1, NOW(), DATE_ADD(NOW(), INTERVAL 365 DAY), 'active');

-- 14. Demo Favorites & Inquiries
INSERT INTO `favorites` (`user_id`, `property_id`) VALUES
(6, 1),
(6, 3),
(6, 4);

INSERT INTO `inquiries` (`property_id`, `sender_id`, `sender_name`, `sender_phone`, `sender_email`, `message`, `is_read`, `created_at`) VALUES
(1, 6, 'أحمد فؤاد الأهدل', '+967711223344', 'client.yemen@aqar-yemen.com', 'السلام عليكم، هل الفيلا جاهزة للمعاينة الميدانية غداً عصراً؟ وهل السعر قابل للتفاوض البسيط عند الجدية والدفع الكاش؟', 0, NOW()),
(3, 6, 'أحمد فؤاد الأهدل', '+967711223344', 'client.yemen@aqar-yemen.com', 'مرحباً، أود معرفة ما إذا كانت الشقة مؤجرة حالياً أم شاغرة للتسليم الفوري؟ شكراً.', 1, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- 15. Demo Notification
INSERT INTO `notifications` (`user_id`, `title`, `message`, `link`, `is_read`, `type`, `created_at`) VALUES
(4, 'استفسار جديد عن فيلا حدة الملكية', 'تلقيت استفساراً جديداً من العميل أحمد فؤاد حول الفيلا في حي حدة.', '/dashboard/inquiries', 0, 'inquiry', NOW()),
(1, 'عقار جديد بانتظار المراجعة', 'تم إرسال إعلان عقار جديد في عدن - حي المنصورة وهو بانتظار اعتماد الإدارة.', '/admin/properties/pending', 0, 'property_review', NOW());

-- 16. Site Settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_group`) VALUES
('site_name', 'عقار اليمن', 'general'),
('site_slogan', 'عقار اليمن... بيتك يبدأ من هنا', 'general'),
('site_description', 'منصتك العقارية الشاملة للبيع والإيجار والاستثمار في اليمن', 'general'),
('site_phone', '+967 770 000 001', 'contact'),
('site_whatsapp', '+967770000001', 'contact'),
('site_email', 'info@aqar-yemen.com', 'contact'),
('site_address', 'اليمن - صنعاء - شارع حدة / عدن - خور مكسر', 'contact'),
('default_currency', 'YER', 'finance'),
('enable_auto_approve', '0', 'moderation'),
('enable_verification', '1', 'moderation'),
('days_to_reconfirm', '30', 'moderation'),
('chatbot_name', 'مساعد عقار اليمن الذكي', 'ai'),
('chatbot_welcome', 'مرحباً بك في عقار اليمن! أنا مساعدك العقاري الذكي، كيف أستطيع خدمتك اليوم؟ يمكنك سؤالي عن شراء، استئجار أو أسعار العقارات في أي محافظة يمنية.', 'ai');

SET FOREIGN_KEY_CHECKS = 1;
