<?php

namespace App\Models;

class Paciente extends BaseModel
{
    protected $table = 'pacientes';
    
    public function findByCns($cns)
    {
        $sql = "SELECT * FROM {$this->table} WHERE cns = :cns LIMIT 1";
        return $this->db->fetchOne($sql, ['cns' => $cns]);
    }
    
    public function findByCpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $sql = "SELECT * FROM {$this->table} WHERE cpf = :cpf LIMIT 1";
        return $this->db->fetchOne($sql, ['cpf' => $cpf]);
    }
    
    public function findPaginated($limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY nome ASC";
        $stmt = $this->executarQuery($sql);
        $stmt->execute();
        
        $allResults = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_slice($allResults, $offset, $limit);
    }
    
    public function searchPaginated($term, $limit = 10, $offset = 0)
    {
        $termLike = "%{$term}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE nome LIKE :term1 
                   OR cpf LIKE :term2 
                   OR cns LIKE :term3 
                ORDER BY nome ASC";
        
        $stmt = $this->executarQuery($sql, [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike
        ]);
        
        $allResults = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_slice($allResults, $offset, $limit);
    }
    
    public function searchCount($term)
    {
        $termLike = "%{$term}%";
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE nome LIKE :term1 
                   OR cpf LIKE :term2 
                   OR cns LIKE :term3";
        $result = $this->db->fetchOne($sql, [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike
        ]);
        return $result['total'] ?? 0;
    }
    
    public function countTotal()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
    
    public function getLaudos($pacienteId, $limit = 5)
    {
        $sql = "SELECT l.id, l.numero_laudo, l.data_laudo, l.status, 
                       l.created_at, l.updated_at,
                       COALESCE(a.numero_apac, '') as numero_apac
                FROM laudos l
                LEFT JOIN apacs a ON l.apac_id = a.id
                WHERE l.paciente_id = :paciente_id
                ORDER BY l.data_laudo DESC, l.created_at DESC";
        
        $stmt = $this->executarQuery($sql, [
            'paciente_id' => (int)$pacienteId
        ]);
        
        $allResults = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_slice($allResults, 0, $limit);
    }
}
