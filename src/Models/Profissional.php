<?php

namespace App\Models;

class Profissional extends BaseModel
{
    protected $table = 'profissionais';
    
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
                   OR especialidade LIKE :term4 
                ORDER BY nome ASC 
                LIMIT 50";
        return $this->db->fetchAll($sql, [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike,
            'term4' => $termLike
        ]);
    }
    
    public function findByEspecialidade($especialidade)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE especialidade = :especialidade 
                ORDER BY nome ASC";
        return $this->db->fetchAll($sql, ['especialidade' => $especialidade]);
    }
}
