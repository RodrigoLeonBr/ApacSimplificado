<?php

namespace App\Utils;

class Validation
{
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validateRequired($value)
    {
        return !empty(trim($value));
    }
    
    public static function validateMinLength($value, $min)
    {
        return strlen(trim($value)) >= $min;
    }
    
    public static function validateMaxLength($value, $max)
    {
        return strlen(trim($value)) <= $max;
    }
    
    public static function validateNumeric($value)
    {
        return is_numeric($value);
    }
    
    public static function validateAPAC13($number)
    {
        return is_numeric($number) && strlen($number) === 13;
    }
    
    public static function validateAPAC14($number)
    {
        return is_numeric($number) && strlen($number) === 14;
    }
    
    public static function sanitize($value)
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    
    public static function validatePassword($password)
    {
        return strlen($password) >= 6;
    }
    
    public static function errors($data, $rules)
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? '';
            $ruleList = explode('|', $rule);
            
            foreach ($ruleList as $r) {
                if ($r === 'required' && !self::validateRequired($value)) {
                    $errors[$field] = "O campo é obrigatório.";
                    break;
                } elseif (strpos($r, 'min:') === 0) {
                    $min = (int) substr($r, 4);
                    if (!self::validateMinLength($value, $min)) {
                        $errors[$field] = "O campo deve ter no mínimo {$min} caracteres.";
                        break;
                    }
                } elseif (strpos($r, 'max:') === 0) {
                    $max = (int) substr($r, 4);
                    if (!self::validateMaxLength($value, $max)) {
                        $errors[$field] = "O campo deve ter no máximo {$max} caracteres.";
                        break;
                    }
                } elseif ($r === 'email' && !self::validateEmail($value)) {
                    $errors[$field] = "O e-mail informado é inválido.";
                    break;
                } elseif ($r === 'numeric' && !self::validateNumeric($value)) {
                    $errors[$field] = "O campo deve conter apenas números.";
                    break;
                }
            }
        }
        
        return $errors;
    }
}
