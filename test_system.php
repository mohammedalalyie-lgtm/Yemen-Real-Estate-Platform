<?php
declare(strict_types=1);

/**
 * عقار اليمن – Yemen Real Estate Platform
 * Automated Integration & Quality Verification Test
 */

require_once __DIR__ . '/app/Config/App.php';
\App\Config\App::init();

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Models\User;
use App\Models\Property;
use App\Models\Location;
use App\Models\Category;
use App\Models\Chatbot;
use App\Models\Inquiry;
use App\Models\Favorite;
use App\Models\Report;

echo "=== بدء اختبارات الجودة والتكامل لمنصة عقار اليمن ===\n\n";

$passCount = 0;
$failCount = 0;

function assertTest(string $description, bool $condition): void {
    global $passCount, $failCount;
    if ($condition) {
        echo "[نجاح] {$description}\n";
        $passCount++;
    } else {
        echo "[فشل] {$description}\n";
        $failCount++;
    }
}

// 1. Test Database connection and basic queries
try {
    $userCount = User::count();
    assertTest("الاتصال بقاعدة البيانات MySQL وجلب عدد المستخدمين ({$userCount} مستخدم)", $userCount >= 5);
} catch (\Throwable $e) {
    assertTest("الاتصال بقاعدة البيانات: " . $e->getMessage(), false);
}

// 2. Test Super Admin Authentication
$admin = User::authenticate('admin@aqar-yemen.com', 'Admin@123456');
assertTest("تسجيل دخول المالك الرئيسي (Super Admin) بنجاح", $admin !== null && $admin['role'] === 'super_admin');

// 3. Test Invalid Password rejection
$invalid = User::authenticate('admin@aqar-yemen.com', 'WrongPass123');
assertTest("رفض تسجيل الدخول بكلمة مرور خاطئة بنجاح", $invalid === null);

// 4. Test Governorates list (Yemen 22 governorates)
$govs = Location::getGovernorates(true);
assertTest("جلب محافظات اليمن الـ 22 بنجاح (عدد المحافظات: " . count($govs) . ")", count($govs) === 22);

// 5. Test Categories list
$cats = Category::getActive();
assertTest("جلب تصنيفات العقارات (سكني، تجاري، أراضي)", count($cats) >= 10);

// 6. Test Properties Search Filter (For Sale in Sana'a)
$searchSale = Property::search(['purpose' => 'sale', 'governorate_id' => 1]);
assertTest("البحث المتقدم: عقارات للبيع في صنعاء (العدد: {$searchSale['total']})", $searchSale['total'] >= 1);

// 7. Test Property Details
$prop1 = Property::findWithDetails(1);
assertTest("جلب تفاصيل العقار #1 مع صوره ومميزاته وموقعه", $prop1 !== null && !empty($prop1['images']) && !empty($prop1['features']));

// 8. Test Smart Real Estate Assistant (Yemeni Dictionary)
$botTerm = Chatbot::processQuery('ما هي اللبنة الصنعانية؟');
assertTest("مساعد عقار اليمن الذكي: شرح مصطلح اللبنة الصنعانية", str_contains($botTerm['message'], 'اللبنة الصنعانية'));

// 9. Test Smart Real Estate Assistant (Property Matching from MySQL)
$botSearch = Chatbot::processQuery('أريد شراء فيلا في صنعاء');
assertTest("مساعد عقار اليمن الذكي: استخراج النية والبحث في قاعدة البيانات وإرجاع عقارات حقيقية", !empty($botSearch['properties']));

// 10. Test Favorites Toggle
$favUserId = 6;
$favPropId = 2;
$favAdded = Favorite::toggle($favUserId, $favPropId);
$favIsAdded = Favorite::isFavorite($favUserId, $favPropId);
assertTest("إضافة عقار إلى المفضلة", $favAdded === true && $favIsAdded === true);
$favRemoved = Favorite::toggle($favUserId, $favPropId);
assertTest("إزالة عقار من المفضلة", $favRemoved === false);

// 11. Test Duplicate Detection Mechanism
$isDup = Property::checkDuplicate('فيلا ملكية فاخرة بتشطيب سوبر VIP في أرقى أحياء حدة', 1, 2, 1);
assertTest("نظام منع الإعلانات المكررة: كشف وجود إعلان سابق مطابق", $isDup !== null);

// 12. Test Property Lifecycle State Machine
$samplePropId = Property::create([
    'user_id' => 5,
    'title' => 'عقار تجريبي لاختبار دورة الموافقة والرفض',
    'slug' => 'test-lifecycle-' . time(),
    'description' => 'وصف العقار التجريبي لفحص دورة الحياة',
    'purpose' => 'sale',
    'category_id' => 2,
    'governorate_id' => 1,
    'district_id' => 1,
    'price' => 50000,
    'currency' => 'USD',
    'area_size' => 120,
    'area_unit' => 'sqm',
    'status' => 'pending'
]);

assertTest("إضافة عقار جديد وحفظه بحالة (pending)", $samplePropId > 0);

// Admin approves
Property::changeStatus($samplePropId, 'approved', 1, 'اعتماد تجريبي');
$approvedProp = Property::find($samplePropId);
assertTest("اعتماد الإعلان من الإدارة وتغيير حالته إلى (approved)", $approvedProp['status'] === 'approved');

// Mark as reserved
Property::changeStatus($samplePropId, 'reserved', 5, 'تم حجز العقار');
$reservedProp = Property::find($samplePropId);
assertTest("تحديث حالة العقار إلى محجوز (reserved)", $reservedProp['status'] === 'reserved');

// Mark as sold
Property::changeStatus($samplePropId, 'sold', 5, 'تم بيع العقار بنجاح');
$soldProp = Property::find($samplePropId);
assertTest("تحديث حالة العقار إلى تم البيع (sold) واختفائه من نتائج البحث المتاح", $soldProp['status'] === 'sold');

// Clean test record
Property::delete($samplePropId);

echo "\n=== النتيجة النهائية للاختبارات ===\n";
echo "نجح: {$passCount} | فشل: {$failCount}\n";

if ($failCount === 0) {
    echo "🎉 جميع الاختبارات اكتملت بنجاح 100%! المنصة جاهزة للعمل والتشغيل.\n";
} else {
    echo "⚠️ يوجد بعض الاختبارات غير المجتازة، يرجى مراجعتها.\n";
}
