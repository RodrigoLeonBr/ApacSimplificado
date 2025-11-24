<?php

namespace App\Services;

use App\Models\Faixa;
use App\Models\Apac;

class ApacService
{
    private $faixaModel;
    private $apacModel;
    
    public function __construct()
    {
        $this->faixaModel = new Faixa();
        $this->apacModel = new Apac();
    }
    
    public function gerarNumeroApac($base13Digits)
    {
        if (strlen($base13Digits) != 13) {
            throw new \InvalidArgumentException('O número base deve ter exatamente 13 dígitos');
        }
        
        if (!ctype_digit($base13Digits)) {
            throw new \InvalidArgumentException('O número base deve conter apenas dígitos');
        }
        
        $digitoVerificador = $this->calcularDigitoVerificador($base13Digits);
        
        return $base13Digits . $digitoVerificador;
    }
    
    public function calcularDigitoVerificador($numero13Digitos)
    {
        $sequenciaCiclica = '78900123456';
        $soma = 0;
        
        for ($i = 0; $i < 13; $i++) {
            $digito = (int) $numero13Digitos[$i];
            $multiplicador = (int) $sequenciaCiclica[$i % 11];
            $soma += $digito * $multiplicador;
        }
        
        $resto = $soma % 11;
        
        if ($resto == 0 || $resto == 1) {
            return 0;
        }
        
        return 11 - $resto;
    }
    
    public function obterProximoNumeroDisponivel($faixaId)
    {
        $faixa = $this->faixaModel->findById($faixaId);
        
        if (!$faixa) {
            throw new \Exception('Faixa não encontrada');
        }
        
        if ($faixa['status'] === 'Esgotada') {
            throw new \Exception('Faixa esgotada. Não há números disponíveis.');
        }
        
        $ultimoApac = $this->apacModel->findUltimoApacDaFaixa($faixaId);
        
        if (!$ultimoApac) {
            $proximoNumero = $faixa['numero_inicial'];
        } else {
            $ultimoNumeroSemDV = substr($ultimoApac['numero_apac'], 0, 13);
            $proximoNumero = (string) ((int) $ultimoNumeroSemDV + 1);
            $proximoNumero = str_pad($proximoNumero, 13, '0', STR_PAD_LEFT);
        }
        
        if ($proximoNumero > $faixa['numero_final']) {
            $this->faixaModel->update($faixaId, ['status' => 'Esgotada']);
            throw new \Exception('Faixa esgotada ao tentar obter próximo número');
        }
        
        $numeroCompleto = $this->gerarNumeroApac($proximoNumero);
        
        $quantidadeEmitida = $this->apacModel->countByFaixaId($faixaId);
        $quantidadeTotal = (int) $faixa['quantidade'];
        
        if ($quantidadeEmitida >= $quantidadeTotal - 1) {
            $this->faixaModel->update($faixaId, ['status' => 'Esgotada']);
        } elseif ($quantidadeEmitida > 0) {
            $this->faixaModel->update($faixaId, ['status' => 'Em Uso']);
        }
        
        return $numeroCompleto;
    }
    
    public function validarNumeroApac($numeroApac14Digitos)
    {
        if (strlen($numeroApac14Digitos) != 14) {
            return false;
        }
        
        if (!ctype_digit($numeroApac14Digitos)) {
            return false;
        }
        
        $base13 = substr($numeroApac14Digitos, 0, 13);
        $dvInformado = (int) substr($numeroApac14Digitos, 13, 1);
        $dvCalculado = $this->calcularDigitoVerificador($base13);
        
        return $dvInformado === $dvCalculado;
    }
    
    public function verificarDuplicidade($numeroApac)
    {
        return $this->apacModel->findByNumeroApac($numeroApac) !== null;
    }
}
