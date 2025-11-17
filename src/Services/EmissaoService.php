<?php

namespace App\Services;

use App\Models\Apac;
use App\Models\Faixa;
use App\Models\Log;
use App\Database\Database;

class EmissaoService
{
    private $apacModel;
    private $faixaModel;
    private $logModel;
    private $dvService;
    
    public function __construct()
    {
        $this->apacModel = new Apac();
        $this->faixaModel = new Faixa();
        $this->logModel = new Log();
        $this->dvService = new DigitoVerificadorService();
    }
    
    public function emitirAPAC($faixaId, $usuarioId)
    {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
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
                'numero_14dig' => $numero14dig,
                'digito_verificador' => substr($numero14dig, 13, 1),
                'faixa_id' => $faixaId,
                'emitido_por_usuario_id' => $usuarioId
            ]);
            
            $this->faixaModel->update($faixaId, ['status' => 'em_uso']);
            
            $this->logModel->create([
                'acao' => 'Emissão de APAC',
                'usuario_id' => $usuarioId,
                'tabela_afetada' => 'apacs',
                'registro_id' => $apacId,
                'detalhes' => "APAC {$numero14dig} emitida da faixa {$faixaId}"
            ]);
            
            $db->commit();
            
            return [
                'success' => true,
                'apac_id' => $apacId,
                'numero_14dig' => $numero14dig
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
        $inicial = (int) $faixa['inicial_13dig'];
        $final = (int) $faixa['final_13dig'];
        
        $apacsEmitidas = $this->apacModel->findByFaixaId($faixa['id']);
        
        $numerosEmitidos = [];
        foreach ($apacsEmitidas as $apac) {
            $numerosEmitidos[] = (int) substr($apac['numero_14dig'], 0, 13);
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
                'tabela_afetada' => 'apacs',
                'registro_id' => $apacId,
                'detalhes' => "APAC marcada como impressa"
            ]);
        }
        
        return $result;
    }
}
