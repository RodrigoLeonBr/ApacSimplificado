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
    
    public function search($term)
    {
        $termLike = "%{$term}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE nome LIKE :term1 
                   OR cpf LIKE :term2 
                   OR cns LIKE :term3 
                ORDER BY nome ASC 
                LIMIT 50";
        return $this->db->fetchAll($sql, [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike
        ]);
    }
    
    public function countTotal()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
}
