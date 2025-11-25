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
    
    public function findPaginated($limit = 10, $offset = 0)
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT * FROM {$this->table} ORDER BY codigo ASC LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql);
    }
    
    public function searchPaginated($term, $limit = 10, $offset = 0)
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $termLike = "%{$term}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE codigo LIKE :term1 
                   OR descricao LIKE :term2 
                ORDER BY codigo ASC 
                LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql, [
            'term1' => $termLike,
            'term2' => $termLike
        ]);
    }
    
    public function searchCount($term)
    {
        $termLike = "%{$term}%";
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE codigo LIKE :term1 
                   OR descricao LIKE :term2";
        $result = $this->db->fetchOne($sql, [
            'term1' => $termLike,
            'term2' => $termLike
        ]);
        return $result['total'] ?? 0;
    }
    
    public function countTotal()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
}
