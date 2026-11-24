<?php
namespace App\Helpers;

use App\Config\Database;
use PDO;

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function validate(array $rules): bool
    {
        foreach ($rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;
            $rulesList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($rulesList as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $this->applyRule($field, $value, $rule, $params);
            }
        }

        return empty($this->errors);
    }

    private function applyRule(string $field, mixed $value, string $rule, array $params): void
    {
        switch ($rule) {
            case 'required':
                if ($value === null || trim((string)$value) === '') {
                    $this->addError($field, "حقل {$this->getFieldLabel($field)} مطلوب.");
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "يرجى إدخال بريد إلكتروني صحيح.");
                }
                break;

            case 'min':
                $min = (int)($params[0] ?? 0);
                if (!empty($value) && mb_strlen((string)$value) < $min) {
                    $this->addError($field, "يجب ألا يقل {$this->getFieldLabel($field)} عن {$min} أحرف/أرقام.");
                }
                break;

            case 'max':
                $max = (int)($params[0] ?? 255);
                if (!empty($value) && mb_strlen((string)$value) > $max) {
                    $this->addError($field, "يجب ألا يتجاوز {$this->getFieldLabel($field)} {$max} حرفاً.");
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, "يجب أن تكون قيمة {$this->getFieldLabel($field)} رقماً.");
                }
                break;

            case 'phone':
                // Yemeni phone regex: checks standard format (e.g. 77xxxxxxx, 73xxxxxxx, 71xxxxxxx, 70xxxxxxx, or with +967)
                if (!empty($value)) {
                    $cleanPhone = preg_replace('/[^0-9+]/', '', (string)$value);
                    if (!preg_match('/^(\+?967)?[7][01378][0-9]{7}$/', $cleanPhone)) {
                        $this->addError($field, "رقم الهاتف غير صحيح. يرجى إدخال رقم يمني صالح (مثال: 771234567 أو +967771234567).");
                    }
                }
                break;

            case 'confirmed':
                $confirmField = $field . '_confirmation';
                $confirmValue = $this->data[$confirmField] ?? null;
                if ($value !== $confirmValue) {
                    $this->addError($field, "تأكيد كلمة المرور غير متطابق.");
                }
                break;

            case 'unique':
                // table,column,exceptId
                $table = $params[0] ?? '';
                $column = $params[1] ?? $field;
                $exceptId = $params[2] ?? null;

                if (!empty($value) && $table) {
                    $db = Database::connect();
                    $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :val";
                    $binds = [':val' => $value];
                    if ($exceptId !== null) {
                        $sql .= " AND id != :except_id";
                        $binds[':except_id'] = $exceptId;
                    }
                    $stmt = $db->prepare($sql);
                    $stmt->execute($binds);
                    if ($stmt->fetchColumn() > 0) {
                        $this->addError($field, "قيمة {$this->getFieldLabel($field)} مسجلة مسبقاً في النظام.");
                    }
                }
                break;

            case 'in':
                if (!empty($value) && !in_array($value, $params)) {
                    $this->addError($field, "القيمة المحددة في {$this->getFieldLabel($field)} غير صالحة.");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }

    private function getFieldLabel(string $field): string
    {
        $labels = [
            'full_name' => 'الاسم الكامل',
            'email' => 'البريد الإلكتروني',
            'phone' => 'رقم الهاتف',
            'password' => 'كلمة المرور',
            'password_confirmation' => 'تأكيد كلمة المرور',
            'role' => 'نوع الحساب',
            'title' => 'عنوان الإعلان',
            'description' => 'وصف العقار',
            'purpose' => 'نوع العملية',
            'category_id' => 'تصنيف العقار',
            'governorate_id' => 'المحافظة',
            'district_id' => 'المديرية',
            'price' => 'السعر',
            'currency' => 'العملة',
            'area_size' => 'المساحة',
            'message' => 'الرسالة / الاستفسار',
            'reason' => 'سبب البلاغ',
            'rejection_reason' => 'سبب الرفض'
        ];

        return $labels[$field] ?? $field;
    }
}
