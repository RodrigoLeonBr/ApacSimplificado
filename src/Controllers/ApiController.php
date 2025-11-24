<?php

namespace App\Controllers;

use App\Services\ValidacaoService;
use App\Middleware\AuthMiddleware;

class ApiController extends BaseController
{
    public function validarCns()
    {
        AuthMiddleware::handle();
        
        $cns = $_GET['cns'] ?? '';
        
        $cns = ValidacaoService::limparCns($cns);
        
        if (strlen($cns) !== 15) {
            $this->jsonResponse([
                'valido' => false,
                'message' => 'CNS deve ter 15 dígitos'
            ]);
            return;
        }
        
        $valido = ValidacaoService::validarCns($cns);
        
        $this->jsonResponse([
            'valido' => $valido,
            'message' => $valido ? 'CNS válido' : 'CNS inválido'
        ]);
    }
    
    public function validarCpf()
    {
        AuthMiddleware::handle();
        
        $cpf = $_GET['cpf'] ?? '';
        
        $cpf = ValidacaoService::limparCpf($cpf);
        
        if (strlen($cpf) !== 11) {
            $this->jsonResponse([
                'valido' => false,
                'message' => 'CPF deve ter 11 dígitos'
            ]);
            return;
        }
        
        $valido = ValidacaoService::validarCpf($cpf);
        
        $this->jsonResponse([
            'valido' => $valido,
            'message' => $valido ? 'CPF válido' : 'CPF inválido'
        ]);
    }
    
    public function validarCep()
    {
        AuthMiddleware::handle();
        
        $cep = $_GET['cep'] ?? '';
        
        $cep = ValidacaoService::limparCep($cep);
        
        if (strlen($cep) !== 8) {
            $this->jsonResponse([
                'valido' => false,
                'message' => 'CEP deve ter 8 dígitos'
            ]);
            return;
        }
        
        $valido = ValidacaoService::validarCep($cep);
        
        $this->jsonResponse([
            'valido' => $valido,
            'message' => $valido ? 'CEP válido' : 'CEP inválido'
        ]);
    }
    
    public function validarEmail()
    {
        AuthMiddleware::handle();
        
        $email = $_GET['email'] ?? '';
        
        $valido = ValidacaoService::validarEmail($email);
        
        $this->jsonResponse([
            'valido' => $valido,
            'message' => $valido ? 'E-mail válido' : 'E-mail inválido'
        ]);
    }
}
