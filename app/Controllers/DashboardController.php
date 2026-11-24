<?php
namespace App\Controllers;

use App\Config\App;
use App\Models\Property;
use App\Models\Category;
use App\Models\Location;
use App\Models\Inquiry;
use App\Models\Favorite;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\Office;
use App\Models\User;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Helpers\Uploader;
use App\Helpers\View;
use PDO;

class DashboardController
{
    public function index(): void
    {
        $userId = Session::userId();
        $stats = Property::getUserStats($userId);

        // Recent 5 properties
        $sqlProps = "SELECT p.*, c.name_ar AS category_name, g.name_ar AS governorate_name, d.name_ar AS district_name,
                            (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC LIMIT 1) AS primary_image
                     FROM properties p
                     JOIN property_categories c ON p.category_id = c.id
                     JOIN governorates g ON p.governorate_id = g.id
                     JOIN districts d ON p.district_id = d.id
                     WHERE p.user_id = :uid
                     ORDER BY p.id DESC LIMIT 5";
        $stmt = Property::db()->prepare($sqlProps);
        $stmt->execute([':uid' => $userId]);
        $recentProperties = $stmt->fetchAll();

        // Recent 5 inquiries
        $recentInquiries = array_slice(Inquiry::getForUser($userId), 0, 5);

        View::render('dashboard.index', [
            'title' => 'لوحة التحكم | عقار اليمن',
            'stats' => $stats,
            'recentProperties' => $recentProperties,
            'recentInquiries' => $recentInquiries
        ], 'dashboard');
    }

    public function properties(): void
    {
        $userId = Session::userId();
        $status = $_GET['status'] ?? 'all';

        $where = "p.user_id = :uid";
        $binds = [':uid' => $userId];

        if ($status !== 'all' && in_array($status, ['pending', 'approved', 'rejected', 'reserved', 'sold', 'rented', 'suspended'])) {
            $where .= " AND p.status = :status";
            $binds[':status'] = $status;
        }

        $sql = "SELECT p.*, c.name_ar AS category_name, g.name_ar AS governorate_name, d.name_ar AS district_name,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC LIMIT 1) AS primary_image
                FROM properties p
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                WHERE {$where}
                ORDER BY p.id DESC";
        $stmt = Property::db()->prepare($sql);
        $stmt->execute($binds);
        $properties = $stmt->fetchAll();

        View::render('dashboard.properties.index', [
            'title' => 'عقاراتي | لوحة التحكم',
            'properties' => $properties,
            'currentStatus' => $status
        ], 'dashboard');
    }

    public function create(): void
    {
        $categories = Category::getActive();
        $governorates = Location::getGovernorates(true);

        $featuresSql = "SELECT * FROM features ORDER BY category ASC, id ASC";
        $features = Property::db()->query($featuresSql)->fetchAll();

        View::render('dashboard.properties.create', [
            'title' => 'إضافة عقار جديد | عقار اليمن',
            'categories' => $categories,
            'governorates' => $governorates,
            'features' => $features
        ], 'dashboard');
    }

    public function store(): void
    {
        $validator = new Validator($_POST);
        $isValid = $validator->validate([
            'title' => 'required|min:5|max:200',
            'description' => 'required|min:15',
            'purpose' => 'required|in:sale,rent',
            'category_id' => 'required|numeric',
            'governorate_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'price' => 'required|numeric',
            'currency' => 'required|in:YER,USD,SAR',
            'area_size' => 'required|numeric',
            'area_unit' => 'required|in:sqm,labna,qasaba'
        ]);

        if (!$isValid) {
            Session::setFlash('danger', $validator->firstError());
            header('Location: ' . App::baseUrl('dashboard/properties/create'));
            exit;
        }

        // Duplicate check
        $dup = Property::checkDuplicate(
            $_POST['title'],
            (int)$_POST['governorate_id'],
            (int)$_POST['district_id'],
            (int)$_POST['category_id']
        );
        if ($dup) {
            Session::setFlash('warning', 'تنبيه: يوجد إعلان سابق بنفس العنوان والموقع والتصنيف مسجل في النظام. يرجى تعديل العنوان أو التفاصيل لتجنب التكرار.');
        }

        // Generate slug
        $baseSlug = preg_replace('/[^a-zA-Z0-9\x{0621}-\x{064A}]+/u', '-', trim($_POST['title']));
        $baseSlug = trim($baseSlug, '-');
        if (empty($baseSlug)) {
            $baseSlug = 'property-' . time();
        }
        $slug = $baseSlug . '-' . bin2hex(random_bytes(3));

        $userId = Session::userId();
        $user = Session::user();
        $officeId = $user['office_id'] ?? null;

        $propertyData = [
            'user_id' => $userId,
            'office_id' => $officeId,
            'title' => trim($_POST['title']),
            'slug' => $slug,
            'description' => trim($_POST['description']),
            'purpose' => $_POST['purpose'],
            'category_id' => (int)$_POST['category_id'],
            'governorate_id' => (int)$_POST['governorate_id'],
            'district_id' => (int)$_POST['district_id'],
            'area_id' => !empty($_POST['area_id']) ? (int)$_POST['area_id'] : null,
            'address_details' => !empty($_POST['address_details']) ? trim($_POST['address_details']) : null,
            'price' => (float)$_POST['price'],
            'currency' => $_POST['currency'],
            'price_type' => $_POST['price_type'] ?? 'total',
            'is_negotiable' => isset($_POST['is_negotiable']) ? 1 : 0,
            'area_size' => (float)$_POST['area_size'],
            'area_unit' => $_POST['area_unit'],
            'bedrooms' => !empty($_POST['bedrooms']) ? (int)$_POST['bedrooms'] : 0,
            'bathrooms' => !empty($_POST['bathrooms']) ? (int)$_POST['bathrooms'] : 0,
            'living_rooms' => !empty($_POST['living_rooms']) ? (int)$_POST['living_rooms'] : 0,
            'floors' => !empty($_POST['floors']) ? (int)$_POST['floors'] : 1,
            'floor_number' => !empty($_POST['floor_number']) ? (int)$_POST['floor_number'] : 0,
            'building_year' => !empty($_POST['building_year']) ? (int)$_POST['building_year'] : null,
            'direction' => !empty($_POST['direction']) ? trim($_POST['direction']) : null,
            'latitude' => !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null,
            'longitude' => !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null,
            'status' => 'pending',
            'is_featured' => 0
        ];

        $propertyId = Property::create($propertyData);

        if ($propertyId) {
            // Save features
            if (!empty($_POST['features']) && is_array($_POST['features'])) {
                $featStmt = Property::db()->prepare("INSERT INTO property_features (property_id, feature_id) VALUES (:pid, :fid)");
                foreach ($_POST['features'] as $fid) {
                    $featStmt->execute([':pid' => $propertyId, ':fid' => (int)$fid]);
                }
            }

            // Save images
            if (!empty($_FILES['images'])) {
                $uploadedImages = Uploader::uploadMultipleImages($_FILES['images'], 'properties');
                if (!empty($uploadedImages)) {
                    $imgStmt = Property::db()->prepare("INSERT INTO property_images (property_id, image_path, is_primary, sort_order) VALUES (:pid, :img, :primary, :sort)");
                    foreach ($uploadedImages as $idx => $img) {
                        $imgStmt->execute([
                            ':pid' => $propertyId,
                            ':img' => $img,
                            ':primary' => ($idx === 0) ? 1 : 0,
                            ':sort' => $idx + 1
                        ]);
                    }
                }
            }

            // Record log & admin notification
            ActivityLog::log($userId, 'create_property', 'properties', $propertyId, "إضافة عقار جديد: {$_POST['title']}");
            
            // Send notification to super admins
            $admins = User::where('role', 'super_admin');
            foreach ($admins as $admin) {
                Notification::send(
                    $admin['id'],
                    'إعلان عقار جديد بانتظار المراجعة',
                    "قام المعلن {$user['full_name']} بإضافة عقار جديد: {$_POST['title']}",
                    '/admin/properties/pending',
                    'property_review'
                );
            }

            Session::setFlash('success', 'تم إرسال العقار للمراجعة بنجاح! سيتم مراجعته واعتماده من فريق الإدارة قريباً.');
            header('Location: ' . App::baseUrl('dashboard/properties'));
            exit;
        }

        Session::setFlash('danger', 'حدث خطأ أثناء حفظ العقار. يرجى المحاولة لاحقاً.');
        header('Location: ' . App::baseUrl('dashboard/properties/create'));
        exit;
    }

    public function edit(int $id): void
    {
        $userId = Session::userId();
        $property = Property::findWithDetails($id);

        if (!$property || (!Session::isAdmin() && $property['user_id'] != $userId)) {
            Session::setFlash('danger', 'غير مصرح لك بتعديل هذا العقار.');
            header('Location: ' . App::baseUrl('dashboard/properties'));
            exit;
        }

        $categories = Category::getActive();
        $governorates = Location::getGovernorates(true);
        $districts = Location::getDistricts($property['governorate_id']);
        $areas = $property['district_id'] ? Location::getAreas($property['district_id']) : [];

        $featuresSql = "SELECT * FROM features ORDER BY category ASC, id ASC";
        $features = Property::db()->query($featuresSql)->fetchAll();

        // Selected features ids
        $selectedFeatureIds = array_column($property['features'], 'id');

        View::render('dashboard.properties.edit', [
            'title' => 'تعديل العقار: ' . $property['title'],
            'property' => $property,
            'categories' => $categories,
            'governorates' => $governorates,
            'districts' => $districts,
            'areas' => $areas,
            'features' => $features,
            'selectedFeatureIds' => $selectedFeatureIds
        ], 'dashboard');
    }

    public function update(int $id): void
    {
        $userId = Session::userId();
        $property = Property::find($id);

        if (!$property || (!Session::isAdmin() && $property['user_id'] != $userId)) {
            Session::setFlash('danger', 'غير مصرح لك بتعديل هذا العقار.');
            header('Location: ' . App::baseUrl('dashboard/properties'));
            exit;
        }

        $validator = new Validator($_POST);
        $isValid = $validator->validate([
            'title' => 'required|min:5|max:200',
            'description' => 'required|min:15',
            'purpose' => 'required|in:sale,rent',
            'category_id' => 'required|numeric',
            'governorate_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'price' => 'required|numeric',
            'currency' => 'required|in:YER,USD,SAR',
            'area_size' => 'required|numeric',
            'area_unit' => 'required|in:sqm,labna,qasaba'
        ]);

        if (!$isValid) {
            Session::setFlash('danger', $validator->firstError());
            header('Location: ' . App::baseUrl('dashboard/properties/edit/' . $id));
            exit;
        }

        $newStatus = $property['status'];
        // If it was rejected or revision required, re-submitting sets it to pending
        if (in_array($property['status'], ['rejected', 'revision_required'])) {
            $newStatus = 'pending';
        }

        $updateData = [
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'purpose' => $_POST['purpose'],
            'category_id' => (int)$_POST['category_id'],
            'governorate_id' => (int)$_POST['governorate_id'],
            'district_id' => (int)$_POST['district_id'],
            'area_id' => !empty($_POST['area_id']) ? (int)$_POST['area_id'] : null,
            'address_details' => !empty($_POST['address_details']) ? trim($_POST['address_details']) : null,
            'price' => (float)$_POST['price'],
            'currency' => $_POST['currency'],
            'price_type' => $_POST['price_type'] ?? 'total',
            'is_negotiable' => isset($_POST['is_negotiable']) ? 1 : 0,
            'area_size' => (float)$_POST['area_size'],
            'area_unit' => $_POST['area_unit'],
            'bedrooms' => !empty($_POST['bedrooms']) ? (int)$_POST['bedrooms'] : 0,
            'bathrooms' => !empty($_POST['bathrooms']) ? (int)$_POST['bathrooms'] : 0,
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        Property::update($id, $updateData);

        // Update features
        Property::db()->prepare("DELETE FROM property_features WHERE property_id = :pid")->execute([':pid' => $id]);
        if (!empty($_POST['features']) && is_array($_POST['features'])) {
            $featStmt = Property::db()->prepare("INSERT INTO property_features (property_id, feature_id) VALUES (:pid, :fid)");
            foreach ($_POST['features'] as $fid) {
                $featStmt->execute([':pid' => $id, ':fid' => (int)$fid]);
            }
        }

        // Upload additional images if any
        if (!empty($_FILES['images'])) {
            $uploadedImages = Uploader::uploadMultipleImages($_FILES['images'], 'properties');
            if (!empty($uploadedImages)) {
                $imgStmt = Property::db()->prepare("INSERT INTO property_images (property_id, image_path, is_primary, sort_order) VALUES (:pid, :img, 0, 99)");
                foreach ($uploadedImages as $img) {
                    $imgStmt->execute([':pid' => $id, ':img' => $img]);
                }
            }
        }

        ActivityLog::log($userId, 'update_property', 'properties', $id, "تعديل بيانات العقار: {$_POST['title']}");

        Session::setFlash('success', 'تم حفظ التعديلات بنجاح' . ($newStatus === 'pending' ? ' وإعادة إرسال الإعلان للمراجعة.' : '.'));
        header('Location: ' . App::baseUrl('dashboard/properties'));
        exit;
    }

    public function updateStatus(int $id): void
    {
        $userId = Session::userId();
        $property = Property::find($id);

        if (!$property || (!Session::isAdmin() && $property['user_id'] != $userId)) {
            Session::setFlash('danger', 'غير مصرح لك بتغيير حالة هذا العقار.');
            header('Location: ' . App::baseUrl('dashboard/properties'));
            exit;
        }

        $allowedStatuses = ['reserved', 'sold', 'rented', 'suspended', 'approved'];
        $newStatus = $_POST['status'] ?? '';

        if (!in_array($newStatus, $allowedStatuses)) {
            Session::setFlash('danger', 'الحالة المحددة غير صالحة.');
            header('Location: ' . App::baseUrl('dashboard/properties'));
            exit;
        }

        Property::changeStatus($id, $newStatus, $userId, 'تحديث الحالة من لوحة التحكم');
        ActivityLog::log($userId, 'change_status', 'properties', $id, "تحديث حالة العقار إلى: {$newStatus}");

        Session::setFlash('success', 'تم تحديث حالة العقار بنجاح.');
        header('Location: ' . App::baseUrl('dashboard/properties'));
        exit;
    }

    public function inquiries(): void
    {
        $userId = Session::userId();
        $inquiries = Inquiry::getForUser($userId);

        View::render('dashboard.inquiries.index', [
            'title' => 'الاستفسارات الواردة | لوحة التحكم',
            'inquiries' => $inquiries
        ], 'dashboard');
    }

    public function markInquiryRead(int $id): void
    {
        $userId = Session::userId();
        Inquiry::markAsRead($id, $userId);
        View::json(['success' => true]);
    }

    public function favorites(): void
    {
        $userId = Session::userId();
        $favorites = Favorite::getForUser($userId);

        View::render('dashboard.favorites.index', [
            'title' => 'عقاراتي المفضلة | عقار اليمن',
            'favorites' => $favorites
        ], 'dashboard');
    }

    public function profile(): void
    {
        $userId = Session::userId();
        $user = User::find($userId);

        View::render('dashboard.profile.index', [
            'title' => 'الملف الشخصي | لوحة التحكم',
            'user' => $user
        ], 'dashboard');
    }

    public function updateProfile(): void
    {
        $userId = Session::userId();
        $validator = new Validator($_POST);
        $isValid = $validator->validate([
            'full_name' => 'required|min:3|max:100',
            'phone' => 'required|phone',
            'whatsapp' => 'phone'
        ]);

        if (!$isValid) {
            Session::setFlash('danger', $validator->firstError());
            header('Location: ' . App::baseUrl('dashboard/profile'));
            exit;
        }

        $updateData = [
            'full_name' => trim($_POST['full_name']),
            'phone' => trim($_POST['phone']),
            'whatsapp' => !empty($_POST['whatsapp']) ? trim($_POST['whatsapp']) : trim($_POST['phone']),
            'bio' => !empty($_POST['bio']) ? trim($_POST['bio']) : null,
            'license_number' => !empty($_POST['license_number']) ? trim($_POST['license_number']) : null
        ];

        // Avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $avatar = Uploader::uploadImage($_FILES['avatar'], 'avatars');
            if ($avatar) {
                $updateData['avatar'] = $avatar;
            }
        }

        User::update($userId, $updateData);
        $user = User::find($userId);
        Session::setAuth($user);

        Session::setFlash('success', 'تم تحديث بيانات الملف الشخصي بنجاح.');
        header('Location: ' . App::baseUrl('dashboard/profile'));
        exit;
    }

    public function notifications(): void
    {
        $userId = Session::userId();
        $notifications = Notification::getForUser($userId, 50);
        Notification::markAllRead($userId);

        View::render('dashboard.notifications.index', [
            'title' => 'الإشعارات | لوحة التحكم',
            'notifications' => $notifications
        ], 'dashboard');
    }

    public function office(): void
    {
        $userId = Session::userId();
        $user = Session::user();

        if ($user['role'] !== 'office') {
            Session::setFlash('warning', 'هذا القسم مخصص للمكاتب العقارية فقط.');
            header('Location: ' . App::baseUrl('dashboard'));
            exit;
        }

        $office = Office::findWithStats($user['office_id'] ?? 0);
        if (!$office) {
            // Find by owner
            $officeRow = Office::findWhere('owner_id', $userId);
            if ($officeRow) {
                $office = Office::findWithStats($officeRow['id']);
            }
        }

        View::render('dashboard.office.index', [
            'title' => 'إدارة المكتب العقاري | لوحة التحكم',
            'office' => $office
        ], 'dashboard');
    }
}
