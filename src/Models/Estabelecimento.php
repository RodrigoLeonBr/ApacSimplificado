<?php

namespace App\Models;

class Estabelecimento extends BaseModel
{
    protected $table = 'estabelecimentos';
    
    public function findByCnes($cnes)
    {
        $sql = "SELECT * FROM {$this->table} WHERE cnes = :cnes LIMIT 1";
        return $this->db->fetchOne($sql, ['cnes' => $cnes]);
    }
    
    public function findByCnpj($cnpj)
    {
        $sql = "SELECT * FROM {$this->table} WHERE cnpj = :cnpj LIMIT 1";
        return $this->db->fetchOne($sql, ['cnpj' => $cnpj]);
    }
    
    public function findPaginated($limit = 10, $offset = 0)
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT * FROM {$this->table} ORDER BY razao_social ASC LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql);
    }
    
    public function searchPaginated($term, $limit = 10, $offset = 0)
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $termLike = "%{$term}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE razao_social LIKE :term1 
                   OR cnes LIKE :term2 
                   OR cnpj LIKE :term3 
                ORDER BY razao_social ASC 
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
                WHERE razao_social LIKE :term1 
                   OR cnes LIKE :term2 
                   OR cnpj LIKE :term3";
        
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
    
    public function getLaudos($estabelecimentoId, $limit = 10)
    {
        $estabelecimentoId = (int)$estabelecimentoId;
        $limit = max(1, min(50, (int)$limit));
        $sql = "SELECT l.id, l.numero_laudo, l.data_laudo, l.status, 
                       l.created_at, l.updated_at,
                       p.nome as paciente_nome,
                       COALESCE(a.numero_apac, '') as numero_apac
                FROM laudos l
                LEFT JOIN apacs a ON l.apac_id = a.id
                LEFT JOIN pacientes p ON l.paciente_id = p.id
                WHERE l.estabelecimento_id = :estabelecimento_id
                ORDER BY l.data_laudo DESC, l.created_at DESC
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql, ['estabelecimento_id' => $estabelecimentoId]);
    }
}
