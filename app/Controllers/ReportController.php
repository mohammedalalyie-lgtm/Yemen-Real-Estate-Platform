<?php
namespace App\Controllers;

use App\Models\Report;
use App\Models\Property;
use App\Models\Notification;
use App\Models\User;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Helpers\View;

class ReportController
{
    public function store(): void
    {
        $validator = new Validator($_POST);
        $isValid = $validator->validate([
            'property_id' => 'required|numeric',
            'reason' => 'required|in:incorrect_info,misleading_price,fake_images,duplicate_ad,invalid_phone,suspicious_ad,other',
            'details' => 'required|min:5'
        ]);

        if (!$isValid) {
            View::json(['success' => false, 'error' => $validator->firstError()], 422);
            return;
        }

        $propertyId = (int)$_POST['property_id'];
        $property = Property::find($propertyId);
        if (!$property) {
            View::json(['success' => false, 'error' => 'العقار المحدد غير موجود.'], 404);
            return;
        }

        $reporterId = Session::userId();

        Report::create([
            'property_id' => $propertyId,
            'reporter_id' => $reporterId,
            'reporter_name' => !empty($_POST['reporter_name']) ? trim($_POST['reporter_name']) : (Session::check() ? Session::user()['full_name'] : 'زائر'),
            'reporter_phone' => !empty($_POST['reporter_phone']) ? trim($_POST['reporter_phone']) : (Session::check() ? Session::user()['phone'] : null),
            'reason' => $_POST['reason'],
            'details' => trim($_POST['details']),
            'status' => 'pending'
        ]);

        // Send notification to super admins
        $admins = User::where('role', 'super_admin');
        foreach ($admins as $admin) {
            Notification::send(
                $admin['id'],
                'بلاغ جديد عن إعلان مخالف',
                "تم تسجيل بلاغ جديد عن العقار: {$property['title']}",
                '/admin/reports',
                'report'
            );
        }

        View::json([
            'success' => true,
            'message' => 'تم استلام بلاغك بنجاح وسيقوم فريق المراجعة والتدقيق بالتحقق من الإعلان فوراً.'
        ]);
    }
}
