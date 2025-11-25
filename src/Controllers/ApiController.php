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
    
    public function validarCnpj()
    {
        AuthMiddleware::handle();
        
        $cnpj = $_GET['cnpj'] ?? '';
        
        $cnpj = ValidacaoService::limparCnpj($cnpj);
        
        if (strlen($cnpj) !== 14) {
            $this->jsonResponse([
                'valido' => false,
                'message' => 'CNPJ deve ter 14 dígitos'
            ]);
            return;
        }
        
        $valido = ValidacaoService::validarCnpj($cnpj);
        
        $this->jsonResponse([
            'valido' => $valido,
            'message' => $valido ? 'CNPJ válido' : 'CNPJ inválido'
        ]);
    }
    
    public function buscarCep()
    {
        AuthMiddleware::handle();
        
        $cep = $_GET['cep'] ?? '';
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        if (strlen($cep) !== 8) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'CEP deve ter 8 dígitos'
            ], 400);
            return;
        }
        
        // Tentar BrasilAPI primeiro (API gratuita mantida pela comunidade)
        $endpointBrasilAPI = "https://brasilapi.com.br/api/cep/v1/{$cep}";
        
        $ch = curl_init($endpointBrasilAPI);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            
            if (isset($data['street']) || isset($data['neighborhood'])) {
                $this->jsonResponse([
                    'success' => true,
                    'logradouro' => $data['street'] ?? '',
                    'bairro' => $data['neighborhood'] ?? '',
                    'municipio' => $data['city'] ?? '',
                    'uf' => $data['state'] ?? '',
                    'cep' => $data['cep'] ?? $cep
                ]);
                return;
            }
        }
        
        // Fallback para ViaCEP
        $endpointViaCEP = "https://viacep.com.br/ws/{$cep}/json/";
        
        $ch = curl_init($endpointViaCEP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            
            if (!isset($data['erro'])) {
                $this->jsonResponse([
                    'success' => true,
                    'logradouro' => $data['logradouro'] ?? '',
                    'bairro' => $data['bairro'] ?? '',
                    'municipio' => $data['localidade'] ?? '',
                    'uf' => $data['uf'] ?? '',
                    'cep' => $data['cep'] ?? $cep
                ]);
                return;
            }
        }
        
        $this->jsonResponse([
            'success' => false,
            'message' => 'CEP não encontrado'
        ], 404);
    }
}
