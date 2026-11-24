<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Property;
use App\Helpers\View;

class BrokerController
{
    public function index(): void
    {
        $brokers = User::getBrokers(24);
        View::render('brokers.index', [
            'title' => 'دليل السماسرة والوسطاء العقاريين المعتمدين | عقار اليمن',
            'brokers' => $brokers
        ]);
    }

    public function show(int $id): void
    {
        $broker = User::find($id);
        if (!$broker || $broker['role'] !== 'broker' || !$broker['is_active']) {
            http_response_code(404);
            View::render('errors.404', ['title' => 'الوسيط العقاري غير موجود'], 'main');
            return;
        }

        // Broker properties
        $sql = "SELECT p.*, c.name_ar AS category_name, g.name_ar AS governorate_name, d.name_ar AS district_name,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC LIMIT 1) AS primary_image
                FROM properties p
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                WHERE p.user_id = :uid AND p.status = 'approved'
                ORDER BY p.id DESC";
        $stmt = Property::db()->prepare($sql);
        $stmt->execute([':uid' => $broker['id']]);
        $properties = $stmt->fetchAll();

        View::render('brokers.show', [
            'title' => $broker['full_name'] . ' - وسيط عقاري معتمد | عقار اليمن',
            'broker' => $broker,
            'properties' => $properties
        ]);
    }
}
