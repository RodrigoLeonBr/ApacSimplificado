<?php

namespace App\Models;

class Procedimento extends BaseModel
{
    protected $table = 'procedimentos';
    
    public function findByCodigo($codigo)
    {
        $sql = "SELECT * FROM {$this->table} WHERE codigo_procedimento = :codigo LIMIT 1";
        return $this->db->fetchOne($sql, ['codigo' => $codigo]);
    }
    
    public function search($term)
    {
        $termLike = "%{$term}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE codigo_procedimento LIKE :term1 
                   OR descricao LIKE :term2 
                ORDER BY codigo_procedimento ASC 
                LIMIT 50";
        return $this->db->fetchAll($sql, ['term1' => $termLike, 'term2' => $termLike]);
    }
    
    public function findByTabela($tabelaSus)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE tabela_sus = :tabela_sus 
                ORDER BY descricao ASC";
        return $this->db->fetchAll($sql, ['tabela_sus' => $tabelaSus]);
    }
}
