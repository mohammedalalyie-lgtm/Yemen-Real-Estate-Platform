<?php
namespace App\Controllers;

use App\Models\Office;
use App\Models\Property;
use App\Helpers\View;

class OfficeController
{
    public function index(): void
    {
        $offices = Office::getOffices(24);
        View::render('offices.index', [
            'title' => 'دليل المكاتب العقارية المعتمدة في اليمن | عقار اليمن',
            'offices' => $offices
        ]);
    }

    public function show(string|int $id): void
    {
        $office = Office::findWithStats($id);
        if (!$office) {
            http_response_code(404);
            View::render('errors.404', ['title' => 'المكتب العقاري غير موجود'], 'main');
            return;
        }

        // Office properties
        $sql = "SELECT p.*, c.name_ar AS category_name, g.name_ar AS governorate_name, d.name_ar AS district_name,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC LIMIT 1) AS primary_image
                FROM properties p
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                WHERE p.office_id = :oid AND p.status = 'approved'
                ORDER BY p.id DESC";
        $stmt = Property::db()->prepare($sql);
        $stmt->execute([':oid' => $office['id']]);
        $properties = $stmt->fetchAll();

        View::render('offices.show', [
            'title' => $office['name'] . ' | عقار اليمن',
            'office' => $office,
            'properties' => $properties
        ]);
    }
}
