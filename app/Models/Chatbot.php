<?php
namespace App\Models;

class Chatbot extends Model
{
    protected static string $table = 'chatbot_conversations';

    public static function processQuery(string $input, ?int $userId = null): array
    {
        $text = trim($input);
        $cleanText = mb_strtolower($text, 'UTF-8');

        // Check for general knowledge / Yemeni terms dictionary
        $termResponse = self::checkRealEstateDictionary($cleanText);
        if ($termResponse) {
            return [
                'type' => 'text',
                'message' => $termResponse,
                'properties' => [],
                'suggestions' => ['أريد شراء عقار', 'أريد استئجار عقار', 'أبحث عن أرض باللبنة', 'كيف أنشر عقاري؟']
            ];
        }

        // Check for procedural questions (how to publish, contact, etc.)
        $faqResponse = self::checkFaq($cleanText);
        if ($faqResponse) {
            return [
                'type' => 'text',
                'message' => $faqResponse,
                'properties' => [],
                'suggestions' => ['تصفح أحدث العقارات', 'البحث في صنعاء', 'البحث في عدن']
            ];
        }

        // Extract parameters for real estate search
        $intent = self::extractIntent($cleanText);

        // Search database with extracted parameters
        $results = self::searchPropertiesForBot($intent);

        if (!empty($results)) {
            $msg = "وجدت لك " . count($results) . " عقاراً مطابقاً لطلبك في قاعدة البيانات:";
            return [
                'type' => 'properties',
                'message' => $msg,
                'properties' => $results,
                'suggestions' => ['بحث متقدم إضافي', 'تواصل مع وسيط عقاري', 'عقارات في صنعاء', 'عقارات في عدن']
            ];
        }

        // If no direct property match
        return [
            'type' => 'text',
            'message' => "أهلاً بك! لقد فهمت بحثك، ولكن لم أجد حالياً عقاراً مطابقاً تماماً لهذه المواصفات المحددة في قاعدة البيانات.\n\nيمكنك تجربة توسيع نطاق السعر، أو اختيار محافظة أخرى، أو زيارة صفحة البحث المتقدم لمشاهدة جميع العقارات المتاحة.",
            'properties' => [],
            'suggestions' => ['عرض جميع العقارات المتاحة', 'عقارات للبيع في صنعاء', 'شقق للإيجار في عدن', 'تواصل مع مكتب عقاري']
        ];
    }

    private static function checkRealEstateDictionary(string $text): ?string
    {
        $terms = [
            'لبنة' => "💡 **اللبنة الصنعانية**: هي وحدة قياس مساحة الأراضي الشائعة في صنعاء وبعض المحافظات الشمالية.\n- 1 لبنة صنعانية = تقريباً 44.44 متر مربع (أو ما يقارب 100 لبنة = 4,444 م²).\n- عند شراء أرض في صنعاء يتم حساب السعر باللبنة، وتكون إما 'حر' (ملك خاص) أو 'وقف'.",
            'قصبة' => "💡 **القصبة**: وحدة قياس مساحة مستخدمة في مناطق تعز وإب وبعض المحافظات الوسطى والجنوبية، وتختلف مساحتها بدقة حسب العرف المحلي في كل منطقة وتتراوح عادة بين 16 إلى 25 متراً مربعاً.",
            'معاد' => "💡 **المعاد**: وحدة قياس مساحة الأراضي الزراعية في سهل تهامة والحديدة وبعض مناطق لحج وأبين، ويعادل المعاد التهامي حوالي 4,400 متر مربع تقريباً.",
            'بصيرة' => "💡 **البصيرة الشرعية**: هي وثيقة الملكية العقارية التقليدية والرسمية في اليمن، وتتضمن حدود العقار، أصل الملكية، أسماء الشهود وتوقيع الأمين الشرعي، ويجب تعميدها وتوثيقها في قلم التوثيق بالمحكمة والسجل العقاري لضمان الحماية القانونية.",
            'حر' => "💡 **الأرض الحر (الملك الحر)**: هي الأرض المملوكة للشخص ملكية خاصة ومطلقة يحق له بيعها وهبتها والتصرف بها، بخلاف أراضي الوقف وأراضي الدولة (أملاك الدولة).",
            'طابو' => "💡 **الطابو / السجل العقاري**: هو سند التسجيل العقاري الصادر من مصلحة السجل العقاري الرسمية (شائع التسمية في عدن والمحافظات الجنوبية) ويعد أقوى سند ملكية قانوني.",
            'قبلي' => "💡 **الاتجاه القبلي**: في العمارة الصنعانية واليمنية، الواجهة 'القبلية' تعني الواجهة الشمالية أو باتجاه الشمال والشمس، وهي مفضلة في المباني للتهوية والإضاءة الطبيعية."
        ];

        foreach ($terms as $term => $definition) {
            if (mb_strpos($text, $term) !== false) {
                return $definition;
            }
        }

        return null;
    }

    private static function checkFaq(string $text): ?string
    {
        if (mb_strpos($text, 'كيف انشر') !== false || mb_strpos($text, 'كيف أنشر') !== false || mb_strpos($text, 'اضافة عقار') !== false || mb_strpos($text, 'إضافة عقار') !== false || mb_strpos($text, 'بيع عقاري') !== false) {
            return "📌 **طريقة نشر عقارك في عقار اليمن:**\n1. سجّل حساباً كـ 'مالك' أو 'وسيط عقاري' أو 'مكتب'.\n2. ادخل إلى لوحة التحكم واختر 'إضافة عقار'.\n3. املأ بيانات العقار (الموقع، السعر، التفاصيل) وارفع صوراً واضحة.\n4. أرسل الإعلان؛ وسيتم مراجعته واعتماده من فريق الإدارة خلال ساعات قليلة ليظهر لآلاف الزوار.";
        }

        if (mb_strpos($text, 'نصائح') !== false || mb_strpos($text, 'قبل الشراء') !== false || mb_strpos($text, 'احتيال') !== false) {
            return "🛡️ **أهم إرشادات الأمان قبل شراء أو استئجار عقار في اليمن:**\n1. لا تدفع أي عربون مالي دون معاينة العقار على أرض الواقع ومقابلة المالك أو الوسيط المعتمد.\n2. تأكد من صحة البصيرة وأصل الملكية وتعميدها لدى المحكمة المختصة والسجل العقاري.\n3. تحقق من حدود العقار ومطابقتها للمخطط العام والشوارع النافذة.\n4. استعن بمهندس أو خبير لمعاينة سلامة البناء وتوفر الخدمات الأساسية (الماء والكهرباء).";
        }

        if (mb_strpos($text, 'وسيط') !== false || mb_strpos($text, 'سمسار') !== false || mb_strpos($text, 'مكتب') !== false) {
            return "🤝 **التواصل مع المكاتب والوسطاء:**\nتضم منصة 'عقار اليمن' نخبة من المكاتب العقارية والوسطاء المعتمدين والموثقين في مختلف المحافظات.\nيمكنك زيارة صفحة 'المكاتب العقارية' أو 'الوسطاء' وتصفح ملفاتهم وأرقام هواتفهم والتواصل معهم مباشرة عبر الاتصال أو الواتساب.";
        }

        return null;
    }

    private static function extractIntent(string $text): array
    {
        $intent = [
            'purpose' => null,
            'gov_id' => null,
            'cat_id' => null,
            'max_price' => null,
            'currency' => null
        ];

        // Purpose
        if (mb_strpos($text, 'شراء') !== false || mb_strpos($text, 'اشتري') !== false || mb_strpos($text, 'للبيع') !== false || mb_strpos($text, 'تمليك') !== false) {
            $intent['purpose'] = 'sale';
        } elseif (mb_strpos($text, 'ايجار') !== false || mb_strpos($text, 'إيجار') !== false || mb_strpos($text, 'استئجار') !== false || mb_strpos($text, 'شقة للايجار') !== false) {
            $intent['purpose'] = 'rent';
        }

        // Governorates
        $govMap = [
            'صنعاء' => 1,
            'حدة' => 1,
            'بيت بوس' => 1,
            'الأصبحي' => 1,
            'عدن' => 3,
            'خور مكسر' => 3,
            'المعلا' => 3,
            'كريتر' => 3,
            'المنصورة' => 3,
            'تعز' => 4,
            'حضرموت' => 5,
            'المكلا' => 5,
            'سيئون' => 5,
            'إب' => 6,
            'الحديدة' => 7,
            'مأرب' => 8,
            'ذمار' => 9
        ];

        foreach ($govMap as $name => $id) {
            if (mb_strpos($text, $name) !== false) {
                $intent['gov_id'] = $id;
                break;
            }
        }

        // Category
        if (mb_strpos($text, 'فيلا') !== false || mb_strpos($text, 'فلل') !== false) {
            $intent['cat_id'] = 1;
        } elseif (mb_strpos($text, 'شقة') !== false || mb_strpos($text, 'شقق') !== false) {
            $intent['cat_id'] = 2;
        } elseif (mb_strpos($text, 'بيت') !== false || mb_strpos($text, 'منزل') !== false) {
            $intent['cat_id'] = 3;
        } elseif (mb_strpos($text, 'عمارة') !== false || mb_strpos($text, 'عماره') !== false) {
            $intent['cat_id'] = 4;
        } elseif (mb_strpos($text, 'محل') !== false || mb_strpos($text, 'معرض') !== false) {
            $intent['cat_id'] = 8;
        } elseif (mb_strpos($text, 'ارض') !== false || mb_strpos($text, 'أرض') !== false) {
            $intent['cat_id'] = 11;
        }

        return $intent;
    }

    private static function searchPropertiesForBot(array $intent): array
    {
        $where = ["p.status = 'approved'"];
        $binds = [];

        if (!empty($intent['purpose'])) {
            $where[] = "p.purpose = :purpose";
            $binds[':purpose'] = $intent['purpose'];
        }

        if (!empty($intent['gov_id'])) {
            $where[] = "p.governorate_id = :gov_id";
            $binds[':gov_id'] = $intent['gov_id'];
        }

        if (!empty($intent['cat_id'])) {
            $where[] = "p.category_id = :cat_id";
            $binds[':cat_id'] = $intent['cat_id'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT p.id, p.title, p.slug, p.purpose, p.price, p.currency, p.price_type,
                       p.area_size, p.area_unit, p.bedrooms, p.bathrooms,
                       c.name_ar AS category_name, g.name_ar AS governorate_name, d.name_ar AS district_name,
                       (SELECT image_path FROM property_images pi WHERE pi.property_id = p.id ORDER BY pi.is_primary DESC LIMIT 1) AS primary_image
                FROM properties p
                JOIN property_categories c ON p.category_id = c.id
                JOIN governorates g ON p.governorate_id = g.id
                JOIN districts d ON p.district_id = d.id
                WHERE {$whereClause}
                ORDER BY p.is_featured DESC, p.id DESC
                LIMIT 5";

        $stmt = self::db()->prepare($sql);
        $stmt->execute($binds);
        return $stmt->fetchAll();
    }
}
