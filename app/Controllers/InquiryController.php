<?php
namespace App\Controllers;

use App\Models\Inquiry;
use App\Models\Property;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Helpers\View;

class InquiryController
{
    public function store(): void
    {
        $validator = new Validator($_POST);
        $isValid = $validator->validate([
            'property_id' => 'required|numeric',
            'sender_name' => 'required|min:3|max:100',
            'sender_phone' => 'required|phone',
            'message' => 'required|min:5'
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

        $senderId = Session::userId();

        $inquiryId = Inquiry::create([
            'property_id' => $propertyId,
            'sender_id' => $senderId,
            'sender_name' => trim($_POST['sender_name']),
            'sender_phone' => trim($_POST['sender_phone']),
            'sender_email' => !empty($_POST['sender_email']) ? trim($_POST['sender_email']) : null,
            'message' => trim($_POST['message']),
            'is_read' => 0
        ]);

        // Increment property inquiry counter
        Property::db()->prepare("UPDATE properties SET inquiries_count = inquiries_count + 1 WHERE id = :id")->execute([':id' => $propertyId]);

        // Notify property owner
        Notification::send(
            $property['user_id'],
            'استفسار جديد عن عقارك',
            "تلقيت استفساراً جديداً من {$_POST['sender_name']} بخصوص: {$property['title']}",
            '/dashboard/inquiries',
            'inquiry'
        );

        if ($senderId) {
            ActivityLog::log($senderId, 'send_inquiry', 'properties', $propertyId, 'إرسال استفسار حول العقار');
        }

        View::json([
            'success' => true,
            'message' => 'تم إرسال استفسارك بنجاح إلى صاحب الإعلان! سيتواصل معك في أقرب وقت.'
        ]);
    }
}
