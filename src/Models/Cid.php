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
    
    /**
     * Busca CIDs por tipo de agravo
     * 
     * @param string $agravo Valores: 0, 1, 2
     * @return array
     */
    public function findByAgravo($agravo)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tp_agravo = :agravo ORDER BY codigo ASC";
        return $this->db->fetchAll($sql, ['agravo' => $agravo]);
    }
    
    /**
     * Busca CIDs por tipo de sexo
     * 
     * @param string $sexo Valores: M, F, I
     * @return array
     */
    public function findBySexo($sexo)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tp_sexo = :sexo ORDER BY codigo ASC";
        return $this->db->fetchAll($sql, ['sexo' => $sexo]);
    }
    
    /**
     * Busca CIDs por tipo de estádio
     * 
     * @param string $estadio Valores: S, N
     * @return array
     */
    public function findByEstadio($estadio)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tp_estadio = :estadio ORDER BY codigo ASC";
        return $this->db->fetchAll($sql, ['estadio' => $estadio]);
    }
    
    /**
     * Busca procedimentos relacionados a um CID
     * 
     * @param int $cidId
     * @return array
     */
    public function findRelacionamentosProcedimento($cidId)
    {
        $sql = "SELECT r.*, 
                       p.codigo_procedimento,
                       p.descricao as procedimento_descricao,
                       c.codigo as cid_codigo,
                       c.descricao as cid_descricao
                FROM rl_procedimento_cid r
                INNER JOIN procedimentos p ON r.co_procedimento COLLATE utf8mb4_unicode_ci = p.codigo_procedimento COLLATE utf8mb4_unicode_ci
                INNER JOIN cids c ON r.co_cid COLLATE utf8mb4_unicode_ci = c.codigo COLLATE utf8mb4_unicode_ci
                WHERE c.id = :cid_id
                ORDER BY r.st_principal DESC, p.codigo_procedimento ASC";
        return $this->db->fetchAll($sql, ['cid_id' => $cidId]);
    }
}
