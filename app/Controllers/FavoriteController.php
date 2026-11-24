<?php
namespace App\Controllers;

use App\Models\Favorite;
use App\Models\Property;
use App\Helpers\Session;
use App\Helpers\View;

class FavoriteController
{
    public function toggle(): void
    {
        if (!Session::check()) {
            View::json(['success' => false, 'error' => 'يرجى تسجيل الدخول لحفظ العقار في المفضلة.'], 401);
            return;
        }

        $propertyId = (int)($_POST['property_id'] ?? 0);
        if (!$propertyId || !Property::find($propertyId)) {
            View::json(['success' => false, 'error' => 'العقار غير صالح.'], 404);
            return;
        }

        $userId = Session::userId();
        $isFavorited = Favorite::toggle($userId, $propertyId);

        View::json([
            'success' => true,
            'is_favorited' => $isFavorited,
            'message' => $isFavorited ? 'تمت إضافة العقار إلى قائمة المفضلة.' : 'تمت إزالة العقار من قائمة المفضلة.'
        ]);
    }
}
