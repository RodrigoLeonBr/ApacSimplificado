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
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT * FROM {$this->table} ORDER BY nome ASC LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql);
    }
    
    public function searchPaginated($term, $limit = 10, $offset = 0)
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $termLike = "%{$term}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE nome LIKE :term1 
                   OR cpf LIKE :term2 
                   OR cns LIKE :term3 
                ORDER BY nome ASC 
                LIMIT {$limit} OFFSET {$offset}";
        
        return $this->db->fetchAll($sql, [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike
        ]);
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
        $pacienteId = (int)$pacienteId;
        $limit = max(1, min(50, (int)$limit));
        $sql = "SELECT l.id, l.numero_laudo, l.data_laudo, l.status, 
                       l.created_at, l.updated_at,
                       COALESCE(a.numero_apac, '') as numero_apac
                FROM laudos l
                LEFT JOIN apacs_laudos al ON l.id = al.laudo_id
                LEFT JOIN apacs a ON al.apac_id = a.id
                WHERE l.paciente_id = :paciente_id
                ORDER BY l.data_laudo DESC, l.created_at DESC
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql, ['paciente_id' => $pacienteId]);
    }
}
