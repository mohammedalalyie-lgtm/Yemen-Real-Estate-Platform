<?php
namespace App\Controllers;

use App\Models\Property;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Models\Office;
use App\Helpers\View;

class HomeController
{
    public function index(): void
    {
        $featuredProperties = Property::getFeatured(6);
        $latestProperties = Property::getLatest(6);
        $categories = Category::getActive();
        $governorates = Location::getGovernorates(true);
        $topBrokers = User::getBrokers(4);
        $topOffices = Office::getOffices(4);

        $stats = [
            'properties_count' => Property::count("status = 'approved'"),
            'governorates_count' => count($governorates),
            'brokers_count' => User::count("role = 'broker' AND is_active = 1"),
            'offices_count' => Office::count("is_active = 1")
        ];

        View::render('home.index', [
            'title' => 'عقار اليمن - بيتك يبدأ من هنا | المنصة العقارية الأولى في اليمن',
            'featuredProperties' => $featuredProperties,
            'latestProperties' => $latestProperties,
            'categories' => $categories,
            'governorates' => $governorates,
            'topBrokers' => $topBrokers,
            'topOffices' => $topOffices,
            'stats' => $stats
        ]);
    }
}
