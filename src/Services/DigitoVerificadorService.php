<?php

namespace App\Services;

class DigitoVerificadorService
{
    private $sequencia = "78900123456";
    
    public function calcularDV(string $numero13dig): string
    {
        if (!is_numeric($numero13dig) || strlen($numero13dig) !== 13) {
            throw new \InvalidArgumentException("O número deve conter exatamente 13 dígitos numéricos.");
        }
        
        $ultimosDoisDigitos = (int) substr($numero13dig, -2);
        $indice = $ultimosDoisDigitos % strlen($this->sequencia);
        
        return $this->sequencia[$indice];
    }
    
    public function validarAPACCompleta(string $numero14dig): bool
    {
        if (!is_numeric($numero14dig) || strlen($numero14dig) !== 14) {
            throw new \InvalidArgumentException("O número deve conter exatamente 14 dígitos numéricos.");
        }
        
        $numero13dig = substr($numero14dig, 0, 13);
        $dvFornecido = substr($numero14dig, 13, 1);
        
        try {
            $dvCalculado = $this->calcularDV($numero13dig);
            return $dvFornecido === $dvCalculado;
        } catch (\InvalidArgumentException $e) {
            return false;
        }
    }
    
    public function gerarSequenciaFaixa(string $inicio13, string $fim13): array
    {
        if (!is_numeric($inicio13) || strlen($inicio13) !== 13 || !is_numeric($fim13) || strlen($fim13) !== 13) {
            throw new \InvalidArgumentException("Os números de início e fim da faixa devem conter exatamente 13 dígitos numéricos.");
        }
        
        $inicioNum = (int) $inicio13;
        $fimNum = (int) $fim13;
        
        if ($inicioNum > $fimNum) {
            throw new \InvalidArgumentException("O número inicial da faixa não pode ser maior que o número final.");
        }
        
        $apacsGeradas = [];
        for ($i = $inicioNum; $i <= $fimNum; $i++) {
            $numero13dig = str_pad((string) $i, 13, '0', STR_PAD_LEFT);
            $dv = $this->calcularDV($numero13dig);
            $apacsGeradas[] = $numero13dig . $dv;
        }
        
        return $apacsGeradas;
    }
    
    public function gerarNumeroCompleto(string $numero13dig): string
    {
        $dv = $this->calcularDV($numero13dig);
        return $numero13dig . $dv;
    }
}
