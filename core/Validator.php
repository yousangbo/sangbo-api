<?php
namespace Core;

/**
 * 验证类
 * - 参数验证（必填、类型、长度等）
 * - SQL 注入过滤
 * - XSS 攻击过滤
 * - 邮箱、URL 验证
 */
class Validator
{
    /**
     * 根据规则验证数据
     * 规则示例：
     * [
     *   'username' => 'required|type:string|max:50',
     *   'email'    => 'required|email',
     *   'url'      => 'url',
     * ]
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleStr) {
            $value = $data[$field] ?? null;
            $rulesArr = explode('|', $ruleStr);
            foreach ($rulesArr as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                switch ($name) {
                    case 'required':
                        if ($value === null || $value === '') {
                            $errors[$field][] = '必填项';
                        }
                        break;
                    case 'type':
                        if ($param === 'int' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                            $errors[$field][] = '必须为整数';
                        } elseif ($param === 'string' && !is_string($value)) {
                            $errors[$field][] = '必须为字符串';
                        } elseif ($param === 'array' && !is_array($value)) {
                            $errors[$field][] = '必须为数组';
                        }
                        break;
                    case 'max':
                        $max = (int)$param;
                        if (is_string($value) && mb_strlen($value) > $max) {
                            $errors[$field][] = '长度不能超过 ' . $max;
                        }
                        break;
                    case 'min':
                        $min = (int)$param;
                        if (is_string($value) && mb_strlen($value) < $min) {
                            $errors[$field][] = '长度不能小于 ' . $min;
                        }
                        break;
                    case 'email':
                        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = '邮箱格式不正确';
                        }
                        break;
                    case 'url':
                        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
                            $errors[$field][] = 'URL 格式不正确';
                        }
                        break;
                }
            }
        }
        return $errors;
    }

    /**
     * SQL 注入基础过滤（推荐使用预处理，本方法作为兜底）
     */
    public static function cleanSql(string $value): string
    {
        // 移除危险字符与关键字（简化版）
        $pattern = '/(select|insert|update|delete|drop|truncate|;|--|\\\\|\')/i';
        return (string)preg_replace($pattern, '', $value);
    }

    /**
     * XSS 过滤
     */
    public static function cleanXss(string $value): string
    {
        $value = strip_tags($value);
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
