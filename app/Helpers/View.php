<?php
namespace App\Helpers;

use App\Config\App;

class View
{
    public static function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data);
        
        $viewPath = dirname(__DIR__, 2) . '/resources/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            die("View not found: " . htmlspecialchars($viewPath));
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout === 'none') {
            echo $content;
            return;
        }

        $layoutPath = dirname(__DIR__, 2) . '/resources/views/layouts/' . $layout . '.php';
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function e(?string $string): string
    {
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }

    public static function formatPrice(float|int|string $price, string $currency = 'YER', ?string $priceType = 'total'): string
    {
        $num = number_format((float)$price, 0);
        $currLabels = [
            'YER' => 'ريال يمني',
            'USD' => '$ دولار',
            'SAR' => 'ر.س سعودي'
        ];
        $currencyText = $currLabels[$currency] ?? $currency;

        $typeSuffix = '';
        if ($priceType === 'per_month') {
            $typeSuffix = ' / شهرياً';
        } elseif ($priceType === 'per_year') {
            $typeSuffix = ' / سنوياً';
        } elseif ($priceType === 'per_labna') {
            $typeSuffix = ' / للبنة';
        } elseif ($priceType === 'per_meter') {
            $typeSuffix = ' / للمتر';
        }

        return "{$num} {$currencyText}{$typeSuffix}";
    }

    public static function formatArea(float|int|string $size, string $unit = 'sqm'): string
    {
        $units = [
            'sqm' => 'م²',
            'labna' => 'لبنة حر',
            'qasaba' => 'قصبة'
        ];
        return number_format((float)$size, 1) . ' ' . ($units[$unit] ?? $unit);
    }

    public static function formatPurpose(string $purpose): string
    {
        return $purpose === 'rent' ? 'للإيجار' : 'للبيع';
    }

    public static function formatStatus(string $status): array
    {
        $statuses = [
            'pending' => ['label' => 'بانتظار المراجعة', 'class' => 'bg-warning text-dark'],
            'approved' => ['label' => 'متاح ومنشور', 'class' => 'bg-success text-white'],
            'rejected' => ['label' => 'مرفوض', 'class' => 'bg-danger text-white'],
            'revision_required' => ['label' => 'يحتاج تعديل', 'class' => 'bg-info text-dark'],
            'reserved' => ['label' => 'محجوز', 'class' => 'bg-secondary text-white'],
            'sold' => ['label' => 'تم البيع', 'class' => 'bg-dark text-white'],
            'rented' => ['label' => 'تم التأجير', 'class' => 'bg-primary text-white'],
            'suspended' => ['label' => 'موقوف مؤقتاً', 'class' => 'bg-secondary text-white'],
            'archived' => ['label' => 'مؤرشف', 'class' => 'bg-light text-dark']
        ];
        return $statuses[$status] ?? ['label' => $status, 'class' => 'bg-secondary text-white'];
    }

    public static function timeAgo(string $datetime): string
    {
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60) {
            return 'الآن';
        }
        $mins = round($diff / 60);
        if ($mins < 60) {
            return "منذ {$mins} دقيقة";
        }
        $hours = round($diff / 3600);
        if ($hours < 24) {
            return "منذ {$hours} ساعة";
        }
        $days = round($diff / 86400);
        if ($days < 30) {
            return "منذ {$days} يوم";
        }
        return date('Y-m-d', $time);
    }
}
