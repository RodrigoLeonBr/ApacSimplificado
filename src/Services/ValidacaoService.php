<?php

namespace App\Services;

class ValidacaoService
{
    public static function validarCpf($cpf)
    {
        if (empty($cpf)) {
            return false;
        }
        
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        if (strlen($cpf) != 11) {
            return false;
        }
        
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        
        return true;
    }
    
    public static function validarCns($cns)
    {
        // Garante apenas números
        $cns = preg_replace('/[^0-9]/', '', $cns);
        
        if (strlen($cns) != 15) {
            return false;
        }
        
        $primeiroDigito = substr($cns, 0, 1);
        
        // Validação para Início 1 ou 2 (Geralmente Definitivos/PIS)
        if ($primeiroDigito == '1' || $primeiroDigito == '2') {
            $pis = substr($cns, 0, 11);
            $soma = 0;
            
            for ($i = 0; $i < 11; $i++) {
                $soma += intval(substr($pis, $i, 1)) * (15 - $i);
            }
            
            $resto = $soma % 11;
            $dv = 11 - $resto;
            
            if ($dv == 11) {
                $dv = 0;
            }
            
            if ($dv == 10) {
                $soma += 2;
                $resto = $soma % 11;
                $dv = 11 - $resto;
                $resultado = $pis . "001" . $dv;
            } else {
                $resultado = $pis . "000" . $dv;
            }
            
            return $cns === $resultado;
        }
        
        // Validação para Início 7, 8 ou 9 (Provisórios)
        if ($primeiroDigito == '7' || $primeiroDigito == '8' || $primeiroDigito == '9') {
            $soma = 0;
            for ($i = 0; $i < 15; $i++) {
                $soma += intval(substr($cns, $i, 1)) * (15 - $i);
            }
            return ($soma % 11) == 0;
        }
        
        return false;
    }
    
    public static function validarCep($cep)
    {
        if (empty($cep)) {
            return false;
        }
        
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        if (strlen($cep) != 8) {
            return false;
        }
        
        if (preg_match('/^0{8}$/', $cep)) {
            return false;
        }
        
        return true;
    }
    
    public static function validarData($data)
    {
        if (empty($data)) {
            return false;
        }
        
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $data, $matches)) {
            $ano = (int) $matches[1];
            $mes = (int) $matches[2];
            $dia = (int) $matches[3];
            
            return checkdate($mes, $dia, $ano);
        }
        
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $data, $matches)) {
            $dia = (int) $matches[1];
            $mes = (int) $matches[2];
            $ano = (int) $matches[3];
            
            return checkdate($mes, $dia, $ano);
        }
        
        return false;
    }
    
    public static function validarEmail($email)
    {
        if (empty($email)) {
            return false;
        }
        
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validarCnpj($cnpj)
    {
        if (empty($cnpj)) {
            return false;
        }
        
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        if (strlen($cnpj) != 14) {
            return false;
        }
        
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        
        $tamanho = strlen($cnpj) - 2;
        $numeros = substr($cnpj, 0, $tamanho);
        $digitos = substr($cnpj, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;
        
        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }
        
        $resultado = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
        
        if ($resultado != $digitos[0]) {
            return false;
        }
        
        $tamanho = $tamanho + 1;
        $numeros = substr($cnpj, 0, $tamanho);
        $soma = 0;
        $pos = $tamanho - 7;
        
        for ($i = $tamanho; $i >= 1; $i--) {
            $soma += $numeros[$tamanho - $i] * $pos--;
            if ($pos < 2) {
                $pos = 9;
            }
        }
        
        $resultado = $soma % 11 < 2 ? 0 : 11 - ($soma % 11);
        
        if ($resultado != $digitos[1]) {
            return false;
        }
        
        return true;
    }
    
    public static function limparCpf($cpf)
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }
    
    public static function limparCnpj($cnpj)
    {
        return preg_replace('/[^0-9]/', '', $cnpj);
    }
    
    public static function limparCns($cns)
    {
        return preg_replace('/[^0-9]/', '', $cns);
    }
    
    public static function limparCep($cep)
    {
        return preg_replace('/[^0-9]/', '', $cep);
    }
    
    public static function formatarData($data, $formato = 'Y-m-d')
    {
        if (empty($data)) {
            return null;
        }
        
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $data, $matches)) {
            $dataObj = \DateTime::createFromFormat('d/m/Y', $data);
            if ($dataObj) {
                return $dataObj->format($formato);
            }
        }
        
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $data)) {
            $dataObj = \DateTime::createFromFormat('Y-m-d', $data);
            if ($dataObj) {
                return $dataObj->format($formato);
            }
        }
        
        return null;
    }
}
