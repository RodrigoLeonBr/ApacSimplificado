<?php

namespace App\Models;

class CaraterAtendimento extends BaseModel
{
    protected $table = 'caracteres_atendimento';
    
    public function findByCodigo($codigo)
    {
        $sql = "SELECT * FROM {$this->table} WHERE codigo = :codigo LIMIT 1";
        return $this->db->fetchOne($sql, ['codigo' => $codigo]);
    }
    
    public function findAtivos()
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE ativo = 1 
                ORDER BY codigo ASC";
        return $this->db->fetchAll($sql);
    }
    
    public function search($term)
    {
        $termLike = "%{$term}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE codigo LIKE :term1 
                   OR descricao LIKE :term2 
                ORDER BY codigo ASC";
        return $this->db->fetchAll($sql, ['term1' => $termLike, 'term2' => $termLike]);
    }
}
