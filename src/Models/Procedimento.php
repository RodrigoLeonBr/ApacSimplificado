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
    
    public function findPaginated($limit = 10, $offset = 0)
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT * FROM {$this->table} ORDER BY codigo_procedimento ASC LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql);
    }
    
    public function searchPaginated($term, $limit = 10, $offset = 0)
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $termLike = "%{$term}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE codigo_procedimento LIKE :term1 
                   OR descricao LIKE :term2 
                   OR tabela_sus LIKE :term3
                ORDER BY codigo_procedimento ASC 
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
                WHERE codigo_procedimento LIKE :term1 
                   OR descricao LIKE :term2 
                   OR tabela_sus LIKE :term3";
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
    
    /**
     * Busca procedimentos por tipo de complexidade
     * 
     * @param string $complexidade Valores: 0, 1, 2, 3
     * @return array
     */
    public function findByComplexidade($complexidade)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tp_complexidade = :complexidade ORDER BY codigo_procedimento ASC";
        return $this->db->fetchAll($sql, ['complexidade' => $complexidade]);
    }
    
    /**
     * Busca procedimentos por tipo de sexo
     * 
     * @param string $sexo Valores: M, F, I, N
     * @return array
     */
    public function findBySexo($sexo)
    {
        $sql = "SELECT * FROM {$this->table} WHERE tp_sexo = :sexo ORDER BY codigo_procedimento ASC";
        return $this->db->fetchAll($sql, ['sexo' => $sexo]);
    }
    
    /**
     * Busca procedimento por código com todos os campos SIGTAP
     * 
     * @param string $codigo
     * @return array|null
     */
    public function findByCodigoCompleto($codigo)
    {
        return $this->findByCodigo($codigo);
    }
    
    /**
     * Busca CIDs relacionados a um procedimento
     * 
     * @param int $procedimentoId
     * @return array
     */
    public function findRelacionamentosCid($procedimentoId)
    {
        $sql = "SELECT r.*, 
                       c.codigo as cid_codigo, 
                       c.descricao as cid_descricao,
                       p.codigo_procedimento,
                       p.descricao as procedimento_descricao
                FROM rl_procedimento_cid r
                INNER JOIN procedimentos p ON r.co_procedimento COLLATE utf8mb4_unicode_ci = p.codigo_procedimento COLLATE utf8mb4_unicode_ci
                INNER JOIN cids c ON r.co_cid COLLATE utf8mb4_unicode_ci = c.codigo COLLATE utf8mb4_unicode_ci
                WHERE p.id = :procedimento_id
                ORDER BY r.st_principal DESC, c.codigo ASC";
        return $this->db->fetchAll($sql, ['procedimento_id' => $procedimentoId]);
    }
}
