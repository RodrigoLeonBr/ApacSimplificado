<?php

namespace App\Services;

use App\Models\Apac;
use App\Models\Faixa;
use App\Models\Log;
use App\Models\Laudo;
use App\Models\ApacLaudo;
use App\Database\Database;

class EmissaoService
{
    private $apacModel;
    private $faixaModel;
    private $logModel;
    private $laudoModel;
    private $apacLaudoModel;
    private $dvService;
    
    public function __construct()
    {
        $this->apacModel = new Apac();
        $this->faixaModel = new Faixa();
        $this->logModel = new Log();
        $this->laudoModel = new Laudo();
        $this->apacLaudoModel = new ApacLaudo();
        $this->dvService = new DigitoVerificadorService();
    }
    
    /**
     * Emite uma nova APAC vinculada obrigatoriamente a um laudo.
     * 
     * @param int $faixaId ID da faixa de números
     * @param int $usuarioId ID do usuário que está emitindo
     * @param int $laudoId ID do laudo para vincular (obrigatório)
     * @return array Resultado da operação com success, apac_id, numero_14dig e laudo_id
     */
    public function emitirAPAC($faixaId, $usuarioId, $laudoId)
    {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // Validar laudo (obrigatório)
            if (empty($laudoId)) {
                throw new \Exception("Laudo é obrigatório para emissão de APAC.");
            }
            
            $laudo = $this->laudoModel->findById($laudoId);
            
            if (!$laudo) {
                throw new \Exception("Laudo não encontrado.");
            }
            
            // Validar status do laudo
            $status = $laudo['status'] ?? 'rascunho';
            if ($status === 'cancelado') {
                throw new \Exception("Não é possível emitir APAC para um laudo cancelado.");
            }
            
            // Verificar se laudo já possui APAC vinculada
            $apacVinculada = $this->apacLaudoModel->laudoPossuiApac($laudoId);
            if ($apacVinculada) {
                throw new \Exception("Este laudo já possui uma APAC vinculada.");
            }
            
            $faixa = $this->faixaModel->findById($faixaId);
            
            if (!$faixa) {
                throw new \Exception("Faixa não encontrada.");
            }
            
            if ($faixa['status'] === 'esgotada') {
                throw new \Exception("Faixa esgotada.");
            }
            
            $proximoNumero = $this->obterProximoNumeroDisponivel($faixa);
            
            if (!$proximoNumero) {
                $this->faixaModel->update($faixaId, ['status' => 'esgotada']);
                throw new \Exception("Não há mais números disponíveis nesta faixa.");
            }
            
            $numero14dig = $this->dvService->gerarNumeroCompleto($proximoNumero);
            
            $apacId = $this->apacModel->create([
                'numero_apac' => $numero14dig,
                'digito_verificador' => substr($numero14dig, 13, 1),
                'faixa_id' => $faixaId,
                'usuario_id' => $usuarioId
            ]);
            
            $this->faixaModel->update($faixaId, ['status' => 'em_uso']);
            
            // Vincular APAC ao laudo (obrigatório)
            $this->apacLaudoModel->vincular($apacId, $laudoId);
            
            $this->logModel->create([
                'acao' => 'Emissão de APAC vinculada a Laudo',
                'usuario_id' => $usuarioId,
                'tabela' => 'apacs_laudos',
                'registro_id' => $apacId,
                'detalhes' => "APAC {$numero14dig} emitida da faixa {$faixaId} e vinculada ao laudo {$laudoId}"
            ]);
            
            $this->logModel->create([
                'acao' => 'Emissão de APAC',
                'usuario_id' => $usuarioId,
                'tabela' => 'apacs',
                'registro_id' => $apacId,
                'detalhes' => "APAC {$numero14dig} emitida da faixa {$faixaId} e vinculada ao laudo {$laudoId}"
            ]);
            
            $db->commit();
            
            return [
                'success' => true,
                'apac_id' => $apacId,
                'numero_14dig' => $numero14dig,
                'laudo_id' => $laudoId
            ];
            
        } catch (\Exception $e) {
            $db->rollback();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function obterProximoNumeroDisponivel($faixa)
    {
        $inicial = (int) $faixa['numero_inicial'];
        $final = (int) $faixa['numero_final'];
        
        $apacsEmitidas = $this->apacModel->findByFaixaId($faixa['id']);
        
        $numerosEmitidos = [];
        foreach ($apacsEmitidas as $apac) {
            $numerosEmitidos[] = (int) substr($apac['numero_apac'], 0, 13);
        }
        
        for ($i = $inicial; $i <= $final; $i++) {
            if (!in_array($i, $numerosEmitidos)) {
                return str_pad((string) $i, 13, '0', STR_PAD_LEFT);
            }
        }
        
        return null;
    }
    
    public function marcarComoImpresso($apacId, $usuarioId)
    {
        $result = $this->apacModel->update($apacId, ['impresso' => true]);
        
        if ($result) {
            $this->logModel->create([
                'acao' => 'Marcação de impressão',
                'usuario_id' => $usuarioId,
                'tabela' => 'apacs',
                'registro_id' => $apacId,
                'detalhes' => "APAC marcada como impressa"
            ]);
        }
        
        return $result;
    }
}
