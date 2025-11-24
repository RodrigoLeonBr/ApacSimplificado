<?php

namespace App\Models;

class Cid extends BaseModel
{
    protected $table = 'cids';
    
    public function findByCodigo($codigo)
    {
        $sql = "SELECT * FROM {$this->table} WHERE codigo = :codigo LIMIT 1";
        return $this->db->fetchOne($sql, ['codigo' => $codigo]);
    }
    
    public function search($term)
    {
        $termLike = "%{$term}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE codigo LIKE :term1 
                   OR descricao LIKE :term2 
                ORDER BY codigo ASC 
                LIMIT 50";
        return $this->db->fetchAll($sql, ['term1' => $termLike, 'term2' => $termLike]);
    }
    
    public function findByCategoria($categoria)
    {
        $categoria = strtoupper(substr($categoria, 0, 1));
        $sql = "SELECT * FROM {$this->table} 
                WHERE codigo LIKE :categoria 
                ORDER BY codigo ASC";
        return $this->db->fetchAll($sql, ['categoria' => $categoria . '%']);
    }
}
