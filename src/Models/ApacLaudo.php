<?php

namespace App\Models;

class ApacLaudo extends BaseModel
{
    protected $table = 'apacs_laudos';
    
    public function findByApacId($apacId)
    {
        $sql = "SELECT al.*, 
                       l.paciente_id,
                       p.nome as paciente_nome,
                       proc.descricao as procedimento_descricao
                FROM {$this->table} al
                INNER JOIN laudos l ON al.laudo_id = l.id
                LEFT JOIN pacientes p ON l.paciente_id = p.id
                LEFT JOIN procedimentos proc ON l.procedimento_autorizado_id = proc.id
                WHERE al.apac_id = :apac_id";
        return $this->db->fetchAll($sql, ['apac_id' => $apacId]);
    }
    
    public function findByLaudoId($laudoId)
    {
        $sql = "SELECT al.*, 
                       a.numero_apac,
                       a.digito_verificador,
                       a.id as apac_id,
                       a.criada_em as apac_data_emissao,
                       a.impresso as apac_impresso
                FROM {$this->table} al
                INNER JOIN apacs a ON al.apac_id = a.id
                WHERE al.laudo_id = :laudo_id";
        return $this->db->fetchAll($sql, ['laudo_id' => $laudoId]);
    }
    
    public function vincular($apacId, $laudoId)
    {
        $sql = "INSERT INTO {$this->table} (apac_id, laudo_id) 
                VALUES (:apac_id, :laudo_id)";
        $this->db->execute($sql, [
            'apac_id' => $apacId,
            'laudo_id' => $laudoId
        ]);
        return $this->db->lastInsertId();
    }
    
    public function desvincular($apacId, $laudoId)
    {
        $sql = "DELETE FROM {$this->table} 
                WHERE apac_id = :apac_id AND laudo_id = :laudo_id";
        $this->db->execute($sql, [
            'apac_id' => $apacId,
            'laudo_id' => $laudoId
        ]);
        return true;
    }
    
    public function existeVinculo($apacId, $laudoId)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE apac_id = :apac_id AND laudo_id = :laudo_id";
        $result = $this->db->fetchOne($sql, [
            'apac_id' => $apacId,
            'laudo_id' => $laudoId
        ]);
        return ($result['total'] ?? 0) > 0;
    }
    
    /**
     * Verifica se um laudo já possui APAC vinculada.
     * 
     * @param int $laudoId
     * @return array|null Retorna a APAC vinculada ou null se não houver
     */
    public function laudoPossuiApac($laudoId)
    {
        $apacs = $this->findByLaudoId($laudoId);
        return !empty($apacs) ? $apacs[0] : null;
    }
    
    /**
     * Cria um novo registro de vinculação APAC-Laudo.
     * 
     * @param array $data Dados contendo apac_id e laudo_id
     * @return int ID do registro criado
     */
    public function create(array $data)
    {
        return $this->vincular($data['apac_id'], $data['laudo_id']);
    }
}
