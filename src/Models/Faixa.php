<?php

namespace App\Models;

class Faixa extends BaseModel
{
    protected $table = 'faixas';
    
    public function findDisponiveis()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status IN ('disponivel', 'em_uso') ORDER BY id DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function create($data)
    {
        $params = [
            'numero_inicial' => $data['numero_inicial'] ?? $data['inicial_13dig'] ?? null,
            'numero_final' => $data['numero_final'] ?? $data['final_13dig'] ?? null,
            'quantidade' => $data['quantidade'],
            'total' => $data['total'] ?? $data['quantidade'],
            'utilizados' => $data['utilizados'] ?? 0,
            'status' => $data['status'] ?? 'disponivel'
        ];
        
        $this->db->execute(
            "INSERT INTO {$this->table} (numero_inicial, numero_final, quantidade, total, utilizados, status) 
             VALUES (:numero_inicial, :numero_final, :quantidade, :total, :utilizados, :status)",
            $params
        );
        
        return $this->db->lastInsertId();
    }
    
    public function countApacsEmitidas($id)
    {
        $sql = "SELECT COUNT(*) as total FROM apacs WHERE faixa_id = :id";
        $result = $this->db->fetchOne($sql, ['id' => $id]);
        return $result['total'] ?? 0;
    }
}
