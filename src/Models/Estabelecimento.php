<?php

namespace App\Models;

class Estabelecimento extends BaseModel
{
    protected $table = 'estabelecimentos';
    
    public function findByCodigo($codigo)
    {
        $sql = "SELECT * FROM {$this->table} WHERE codigo = :codigo LIMIT 1";
        return $this->db->fetchOne($sql, ['codigo' => $codigo]);
    }
    
    public function search($term)
    {
        $termLike = "%{$term}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE nome LIKE :term1 
                   OR codigo LIKE :term2 
                   OR municipio LIKE :term3 
                ORDER BY nome ASC 
                LIMIT 50";
        return $this->db->fetchAll($sql, [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike
        ]);
    }
    
    public function findByMunicipio($municipio)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE municipio = :municipio 
                ORDER BY nome ASC";
        return $this->db->fetchAll($sql, ['municipio' => $municipio]);
    }
    
    public function findByTipo($tipo)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE tipo = :tipo 
                ORDER BY nome ASC";
        return $this->db->fetchAll($sql, ['tipo' => $tipo]);
    }
}
