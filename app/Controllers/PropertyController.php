<?php
namespace App\Controllers;

use App\Models\Property;
use App\Models\Category;
use App\Models\Location;
use App\Models\Favorite;
use App\Helpers\View;
use App\Helpers\Session;

class PropertyController
{
    public function index(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        if ($page < 1) $page = 1;

        $filters = [
            'purpose' => $_GET['purpose'] ?? '',
            'governorate_id' => $_GET['governorate_id'] ?? '',
            'district_id' => $_GET['district_id'] ?? '',
            'area_id' => $_GET['area_id'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'currency' => $_GET['currency'] ?? '',
            'bedrooms' => $_GET['bedrooms'] ?? '',
            'bathrooms' => $_GET['bathrooms'] ?? '',
            'keyword' => $_GET['keyword'] ?? '',
            'sort' => $_GET['sort'] ?? 'latest'
        ];

        $results = Property::search($filters, $page, 12);
        $categories = Category::getActive();
        $governorates = Location::getGovernorates(true);

        $districts = [];
        if (!empty($filters['governorate_id'])) {
            $districts = Location::getDistricts((int)$filters['governorate_id']);
        }

        View::render('properties.index', [
            'title' => 'البحث المتقدم عن العقارات في اليمن | عقار اليمن',
            'properties' => $results['items'],
            'pagination' => $results,
            'filters' => $filters,
            'categories' => $categories,
            'governorates' => $governorates,
            'districts' => $districts
        ]);
    }

    public function apiSearch(): void
    {
        $page = (int)($_GET['page'] ?? 1);
        $filters = [
            'purpose' => $_GET['purpose'] ?? '',
            'governorate_id' => $_GET['governorate_id'] ?? '',
            'district_id' => $_GET['district_id'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
            'min_price' => $_GET['min_price'] ?? '',
            'max_price' => $_GET['max_price'] ?? '',
            'currency' => $_GET['currency'] ?? '',
            'bedrooms' => $_GET['bedrooms'] ?? '',
            'bathrooms' => $_GET['bathrooms'] ?? '',
            'keyword' => $_GET['keyword'] ?? '',
            'sort' => $_GET['sort'] ?? 'latest'
        ];

        $results = Property::search($filters, $page, 12);
        View::json($results);
    }

    public function show(string|int $id): void
    {
        $property = Property::findWithDetails($id);

        if (!$property) {
            http_response_code(404);
            View::render('errors.404', ['title' => 'العقار غير موجود'], 'main');
            return;
        }

        // Increment views
        Property::incrementViews($property['id']);

        $isFavorited = false;
        if (Session::check()) {
            $isFavorited = Favorite::isFavorite(Session::userId(), $property['id']);
        }

        $similarProperties = Property::getSimilar($property['id'], $property['category_id'], $property['governorate_id'], 4);

        View::render('properties.show', [
            'title' => $property['title'] . ' | عقار اليمن',
            'property' => $property,
            'isFavorited' => $isFavorited,
            'similarProperties' => $similarProperties
        ]);
    }

    public function apiDistricts(int $governorateId): void
    {
        $districts = Location::getDistricts($governorateId, true);
        View::json($districts);
    }

    public function apiAreas(int $districtId): void
    {
        $areas = Location::getAreas($districtId, true);
        View::json($areas);
    }
}
