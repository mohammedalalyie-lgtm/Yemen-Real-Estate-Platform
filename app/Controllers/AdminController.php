<?php
namespace App\Controllers;

use App\Config\App;
use App\Models\Property;
use App\Models\User;
use App\Models\Location;
use App\Models\Category;
use App\Models\Report;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Helpers\View;

class AdminController
{
    public function index(): void
    {
        $stats = Property::getAdminStats();
        $userCounts = User::getCountsByRole();
        $recentActivities = ActivityLog::getRecent(8);
        $pendingProperties = Property::where('status', 'pending');
        $pendingReports = Report::where('status', 'pending');

        // Distribution by Governorate for Chart
        $govSql = "SELECT g.name_ar, COUNT(p.id) as count
                   FROM governorates g
                   LEFT JOIN properties p ON g.id = p.governorate_id AND p.status = 'approved'
                   GROUP BY g.id, g.name_ar
                   ORDER BY count DESC LIMIT 7";
        $govDistribution = Property::db()->query($govSql)->fetchAll();

        // Distribution by Category for Chart
        $catSql = "SELECT c.name_ar, COUNT(p.id) as count
                   FROM property_categories c
                   LEFT JOIN properties p ON c.id = p.category_id AND p.status = 'approved'
                   GROUP BY c.id, c.name_ar
                   ORDER BY count DESC LIMIT 6";
        $catDistribution = Property::db()->query($catSql)->fetchAll();

        View::render('admin.index', [
            'title' => 'لوحة الإدارة الرئيسية | عقار اليمن',
            'stats' => $stats,
            'userCounts' => $userCounts,
            'recentActivities' => $recentActivities,
            'pendingCount' => count($pendingProperties),
            'pendingReportsCount' => count($pendingReports),
            'govDistribution' => $govDistribution,
            'catDistribution' => $catDistribution
        ], 'admin');
    }

    public function properties(): void
    {
        $status = $_GET['status'] ?? '';
        $where = "1=1";
        $binds = [];

        if (!empty($status)) {
            $where .= " AND p.status = :status";
            $binds[':status'] = $status;
        }

        $sql = "SELECT p.*, c.name_ar AS category_name, g.name_ar AS governorate_name, d.name_ar AS district_name,
                       u.full_name AS user_name, u.phone AS user_phone, u.role AS user_role,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC LIMIT 1) AS primary_image
                FROM properties p
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                JOIN users u ON p.user_id = u.id
                WHERE {$where}
                ORDER BY p.id DESC";
        $stmt = Property::db()->prepare($sql);
        $stmt->execute($binds);
        $properties = $stmt->fetchAll();

        View::render('admin.properties.index', [
            'title' => 'إدارة جميع العقارات | لوحة الإدارة',
            'properties' => $properties,
            'selectedStatus' => $status
        ], 'admin');
    }

    public function pending(): void
    {
        $sql = "SELECT p.*, c.name_ar AS category_name, g.name_ar AS governorate_name, d.name_ar AS district_name,
                       u.full_name AS user_name, u.phone AS user_phone, u.role AS user_role,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC LIMIT 1) AS primary_image
                FROM properties p
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                JOIN users u ON p.user_id = u.id
                WHERE p.status = 'pending'
                ORDER BY p.id ASC";
        $stmt = Property::db()->query($sql);
        $pendingProperties = $stmt->fetchAll();

        View::render('admin.properties.pending', [
            'title' => 'مراجعة وتدقيق الإعلانات المعلقة | لوحة الإدارة',
            'properties' => $pendingProperties
        ], 'admin');
    }

    public function approveProperty(int $id): void
    {
        $property = Property::find($id);
        if (!$property) {
            Session::setFlash('danger', 'العقار غير موجود.');
            header('Location: ' . App::baseUrl('admin/properties/pending'));
            exit;
        }

        $adminId = Session::userId();
        Property::changeStatus($id, 'approved', $adminId, 'تمت الموافقة والاعتماد من قبل الإدارة.');

        // Notify property owner
        Notification::send(
            $property['user_id'],
            'تهانينا! تمت الموافقة على إعلان عقارك',
            "تمت مراجعة واعتماد عقارك '{$property['title']}' وأصبح متاحاً للبحث ولجميع الزوار.",
            '/property/' . $property['id'],
            'property_approved'
        );

        ActivityLog::log($adminId, 'approve_property', 'properties', $id, "الموافقة على نشر العقار: {$property['title']}");

        Session::setFlash('success', 'تم اعتماد ونشر الإعلان بنجاح!');
        header('Location: ' . App::baseUrl('admin/properties/pending'));
        exit;
    }

    public function rejectProperty(int $id): void
    {
        $property = Property::find($id);
        if (!$property) {
            Session::setFlash('danger', 'العقار غير موجود.');
            header('Location: ' . App::baseUrl('admin/properties/pending'));
            exit;
        }

        $reason = trim($_POST['rejection_reason'] ?? '');
        if (empty($reason)) {
            Session::setFlash('danger', 'يجب كتابة سبب الرفض لتوضيحه للمعلن.');
            header('Location: ' . App::baseUrl('admin/properties/pending'));
            exit;
        }

        $adminId = Session::userId();
        Property::changeStatus($id, 'rejected', $adminId, $reason);

        // Notify property owner
        Notification::send(
            $property['user_id'],
            'تنبيه: تم رفض إعلان العقار',
            "نعتذر، لم يتم اعتماد عقارك '{$property['title']}' للسبب التالي: {$reason}. يمكنك تعديل الإعلان وإعادة إرساله.",
            '/dashboard/properties/edit/' . $property['id'],
            'property_rejected'
        );

        ActivityLog::log($adminId, 'reject_property', 'properties', $id, "رفض العقار: {$property['title']} - السبب: {$reason}");

        Session::setFlash('info', 'تم رفض الإعلان وإرسال إشعار بالسبب للمعلن.');
        header('Location: ' . App::baseUrl('admin/properties/pending'));
        exit;
    }

    public function requestRevision(int $id): void
    {
        $property = Property::find($id);
        if (!$property) {
            Session::setFlash('danger', 'العقار غير موجود.');
            header('Location: ' . App::baseUrl('admin/properties/pending'));
            exit;
        }

        $reason = trim($_POST['rejection_reason'] ?? '');
        if (empty($reason)) {
            Session::setFlash('danger', 'يجب كتابة المطلوب تعديله.');
            header('Location: ' . App::baseUrl('admin/properties/pending'));
            exit;
        }

        $adminId = Session::userId();
        Property::changeStatus($id, 'revision_required', $adminId, $reason);

        Notification::send(
            $property['user_id'],
            'مطلوب تعديل على إعلان عقارك',
            "يرجى تعديل الإعلان '{$property['title']}': {$reason}. ثم إعادة إرساله للاعتماد.",
            '/dashboard/properties/edit/' . $property['id'],
            'property_revision'
        );

        ActivityLog::log($adminId, 'request_revision', 'properties', $id, "طلب تعديل العقار: {$property['title']} - التفاصيل: {$reason}");

        Session::setFlash('warning', 'تم إرسال طلب التعديل للمعلن.');
        header('Location: ' . App::baseUrl('admin/properties/pending'));
        exit;
    }

    public function toggleFeatured(int $id): void
    {
        $property = Property::find($id);
        if ($property) {
            $newFeatured = $property['is_featured'] ? 0 : 1;
            Property::update($id, ['is_featured' => $newFeatured]);
            ActivityLog::log(Session::userId(), 'toggle_featured', 'properties', $id, "تغيير حالة التمييز إلى: {$newFeatured}");
            Session::setFlash('success', $newFeatured ? 'تم تمييز العقار بنجاح في الصفحة الرئيسية.' : 'تم إلغاء تمييز العقار.');
        }
        header('Location: ' . App::baseUrl('admin/properties'));
        exit;
    }

    public function users(): void
    {
        $role = $_GET['role'] ?? '';
        $where = "1=1";
        $binds = [];

        if (!empty($role)) {
            $where .= " AND role = :role";
            $binds[':role'] = $role;
        }

        $sql = "SELECT u.*, 
                       (SELECT COUNT(*) FROM properties p WHERE p.user_id = u.id) as properties_count
                FROM users u
                WHERE {$where}
                ORDER BY u.id DESC";
        $stmt = User::db()->prepare($sql);
        $stmt->execute($binds);
        $users = $stmt->fetchAll();

        View::render('admin.users.index', [
            'title' => 'إدارة المستخدمين والصلاحيات | لوحة الإدارة',
            'users' => $users,
            'selectedRole' => $role
        ], 'admin');
    }

    public function updateUserStatus(int $id): void
    {
        // Protect Super Admin from being edited/deactivated by normal admin
        $targetUser = User::find($id);
        if (!$targetUser) {
            Session::setFlash('danger', 'المستخدم غير موجود.');
            header('Location: ' . App::baseUrl('admin/users'));
            exit;
        }

        if ($targetUser['role'] === 'super_admin' && !Session::isSuperAdmin()) {
            Session::setFlash('danger', 'ممنوع تعديل حساب المالك الرئيسي للنظام.');
            header('Location: ' . App::baseUrl('admin/users'));
            exit;
        }

        $newActive = $targetUser['is_active'] ? 0 : 1;
        User::update($id, ['is_active' => $newActive]);

        ActivityLog::log(Session::userId(), 'toggle_user_active', 'users', $id, "تغيير حالة تفعيل المستخدم إلى: {$newActive}");

        Session::setFlash('success', 'تم تحديث حالة المستخدم بنجاح.');
        header('Location: ' . App::baseUrl('admin/users'));
        exit;
    }

    public function updateUserRole(int $id): void
    {
        if (!Session::isSuperAdmin()) {
            Session::setFlash('danger', 'تغيير الصلاحيات متاح للمالك الرئيسي Super Admin فقط.');
            header('Location: ' . App::baseUrl('admin/users'));
            exit;
        }

        $targetUser = User::find($id);
        if (!$targetUser) {
            Session::setFlash('danger', 'المستخدم غير موجود.');
            header('Location: ' . App::baseUrl('admin/users'));
            exit;
        }

        // Prevent modifying primary super admin id 1
        if ($targetUser['id'] == 1 && $_POST['role'] !== 'super_admin') {
            Session::setFlash('danger', 'لا يمكن تغيير دور المالك الرئيسي الأساسي للمنصة.');
            header('Location: ' . App::baseUrl('admin/users'));
            exit;
        }

        $newRole = $_POST['role'] ?? '';
        $allowedRoles = ['super_admin', 'admin', 'office', 'broker', 'owner', 'customer'];
        if (in_array($newRole, $allowedRoles)) {
            User::update($id, ['role' => $newRole]);
            ActivityLog::log(Session::userId(), 'change_user_role', 'users', $id, "تغيير دور المستخدم {$targetUser['full_name']} إلى {$newRole}");
            Session::setFlash('success', 'تم تحديث دور وصلاحيات المستخدم بنجاح.');
        }

        header('Location: ' . App::baseUrl('admin/users'));
        exit;
    }

    public function locations(): void
    {
        $governorates = Location::getGovernorates(false);
        View::render('admin.locations.governorates', [
            'title' => 'إدارة المحافظات والمواقع | لوحة الإدارة',
            'governorates' => $governorates
        ], 'admin');
    }

    public function categories(): void
    {
        $categories = Category::all('sort_order ASC, id ASC');
        View::render('admin.categories.index', [
            'title' => 'إدارة تصنيفات العقارات | لوحة الإدارة',
            'categories' => $categories
        ], 'admin');
    }

    public function reports(): void
    {
        $reports = Report::getPendingReports();
        View::render('admin.reports.index', [
            'title' => 'إدارة البلاغات ومكافحة الاحتيال | لوحة الإدارة',
            'reports' => $reports
        ], 'admin');
    }

    public function resolveReport(int $id): void
    {
        $action = $_POST['action'] ?? 'dismiss';
        $report = Report::find($id);

        if ($report) {
            $adminId = Session::userId();
            if ($action === 'suspend_property') {
                Property::changeStatus($report['property_id'], 'suspended', $adminId, 'إيقاف الإعلان بسبب بلاغ احتيال أو مخالفة.');
                Report::update($id, ['status' => 'action_taken', 'resolved_by' => $adminId, 'admin_note' => 'تم إيقاف العقار المخالف.']);
                Session::setFlash('success', 'تم إيقاف الإعلان ومعالجة البلاغ بنجاح.');
            } else {
                Report::update($id, ['status' => 'dismissed', 'resolved_by' => $adminId, 'admin_note' => 'تم فحص الإعلان ورفض البلاغ لعدم المخالفة.']);
                Session::setFlash('info', 'تم حفظ وتجاهل البلاغ.');
            }
        }

        header('Location: ' . App::baseUrl('admin/reports'));
        exit;
    }

    public function plans(): void
    {
        $plansSql = "SELECT * FROM subscription_plans ORDER BY id ASC";
        $plans = Property::db()->query($plansSql)->fetchAll();

        View::render('admin.plans.index', [
            'title' => 'إدارة باقات الاشتراكات | لوحة الإدارة',
            'plans' => $plans
        ], 'admin');
    }

    public function logs(): void
    {
        $logs = ActivityLog::getRecent(50);
        View::render('admin.logs.index', [
            'title' => 'سجل العمليات والتدقيق الأمني | لوحة الإدارة',
            'logs' => $logs
        ], 'admin');
    }

    public function settings(): void
    {
        $settingsGrouped = Setting::getAllGrouped();
        View::render('admin.settings.index', [
            'title' => 'إعدادات المنصة | لوحة الإدارة',
            'settings' => $settingsGrouped
        ], 'admin');
    }

    public function updateSettings(): void
    {
        foreach ($_POST as $key => $val) {
            if ($key !== 'csrf_token') {
                Setting::set($key, trim($val));
            }
        }
        ActivityLog::log(Session::userId(), 'update_settings', 'settings', null, 'تحديث إعدادات المنصة العامة');
        Session::setFlash('success', 'تم حفظ جميع الإعدادات بنجاح.');
        header('Location: ' . App::baseUrl('admin/settings'));
        exit;
    }
}
