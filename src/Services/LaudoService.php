<?php

namespace App\Services;

use App\Database\Database;
use App\Models\Paciente;
use App\Models\Laudo;
use App\Models\Apac;
use App\Models\ApacLaudo;
use App\Services\ApacService;

class LaudoService
{
    private $db;
    private $pacienteModel;
    private $laudoModel;
    private $apacModel;
    private $apacLaudoModel;
    private $apacService;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->pacienteModel = new Paciente();
        $this->laudoModel = new Laudo();
        $this->apacModel = new Apac();
        $this->apacLaudoModel = new ApacLaudo();
        $this->apacService = new ApacService();
    }
    
    public function emitirLaudo(array $data)
    {
        try {
            $erros = $this->validarDadosLaudo($data);
            if (!empty($erros)) {
                return [
                    'sucesso' => false,
                    'mensagem' => implode(', ', $erros),
                    'dados' => []
                ];
            }
            
            $this->db->beginTransaction();
            
            $paciente = $this->pacienteModel->findById($data['paciente_id']);
            if (!$paciente) {
                $this->db->rollBack();
                return [
                    'sucesso' => false,
                    'mensagem' => 'Paciente não encontrado',
                    'dados' => []
                ];
            }
            
            $laudoData = [
                'paciente_id' => $data['paciente_id'],
                'cid_id' => $data['cid_id'] ?? null,
                'procedimento_id' => $data['procedimento_id'] ?? null,
                'estabelecimento_id' => $data['estabelecimento_id'] ?? null,
                'profissional_id' => $data['profissional_id'] ?? null,
                'carater_atendimento_id' => $data['carater_atendimento_id'] ?? null,
                'data_solicitacao' => $data['data_solicitacao'] ?? date('Y-m-d'),
                'observacoes' => $data['observacoes'] ?? null,
                'usuario_id' => $data['usuario_id'] ?? null
            ];
            
            $laudoId = $this->laudoModel->create($laudoData);
            
            if (!$laudoId) {
                $this->db->rollBack();
                return [
                    'sucesso' => false,
                    'mensagem' => 'Erro ao criar laudo',
                    'dados' => []
                ];
            }
            
            if (!empty($data['faixa_id'])) {
                try {
                    $numeroApac = $this->apacService->obterProximoNumeroDisponivel($data['faixa_id']);
                    
                    $apacData = [
                        'numero_apac' => $numeroApac,
                        'faixa_id' => $data['faixa_id'],
                        'paciente_id' => $data['paciente_id'],
                        'data_emissao' => date('Y-m-d'),
                        'status_impressao' => 'Não Impresso',
                        'usuario_id' => $data['usuario_id'] ?? null
                    ];
                    
                    $apacId = $this->apacModel->create($apacData);
                    
                    if (!$apacId) {
                        $this->db->rollBack();
                        return [
                            'sucesso' => false,
                            'mensagem' => 'Erro ao gerar APAC',
                            'dados' => []
                        ];
                    }
                    
                    $apacLaudoData = [
                        'apac_id' => $apacId,
                        'laudo_id' => $laudoId
                    ];
                    
                    $apacLaudoId = $this->apacLaudoModel->create($apacLaudoData);
                    
                    if (!$apacLaudoId) {
                        $this->db->rollBack();
                        return [
                            'sucesso' => false,
                            'mensagem' => 'Erro ao vincular APAC ao laudo',
                            'dados' => []
                        ];
                    }
                    
                } catch (\Exception $e) {
                    $this->db->rollBack();
                    return [
                        'sucesso' => false,
                        'mensagem' => 'Erro ao processar APAC: ' . $e->getMessage(),
                        'dados' => []
                    ];
                }
            }
            
            $this->db->commit();
            
            $laudo = $this->laudoModel->findById($laudoId);
            
            return [
                'sucesso' => true,
                'mensagem' => 'Laudo emitido com sucesso' . (isset($numeroApac) ? ' - APAC: ' . $numeroApac : ''),
                'dados' => [
                    'laudo_id' => $laudoId,
                    'laudo' => $laudo,
                    'numero_apac' => $numeroApac ?? null,
                    'apac_id' => $apacId ?? null
                ]
            ];
            
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao emitir laudo: ' . $e->getMessage(),
                'dados' => []
            ];
        }
    }
    
    public function validarDadosLaudo(array $data)
    {
        $erros = [];
        
        if (empty($data['paciente_id'])) {
            $erros[] = 'Paciente é obrigatório';
        }
        
        if (empty($data['cid_id'])) {
            $erros[] = 'CID é obrigatório';
        }
        
        if (empty($data['procedimento_id'])) {
            $erros[] = 'Procedimento é obrigatório';
        }
        
        if (empty($data['estabelecimento_id'])) {
            $erros[] = 'Estabelecimento é obrigatório';
        }
        
        if (empty($data['profissional_id'])) {
            $erros[] = 'Profissional é obrigatório';
        }
        
        if (empty($data['carater_atendimento_id'])) {
            $erros[] = 'Caráter de atendimento é obrigatório';
        }
        
        if (empty($data['data_solicitacao'])) {
            $erros[] = 'Data de solicitação é obrigatória';
        }
        
        return $erros;
    }
}
