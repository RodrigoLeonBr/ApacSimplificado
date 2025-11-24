<?php

namespace App\Models;

use App\Database\Database;

class Faixa
{
    private $db;
    private $table = 'faixas';
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function findAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function findDisponiveis()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status IN ('disponivel', 'em_uso') ORDER BY id DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (numero_inicial, numero_final, quantidade, total, utilizados, status) 
                VALUES (:numero_inicial, :numero_final, :quantidade, :total, :utilizados, :status)";
        
        $params = [
            'numero_inicial' => $data['numero_inicial'] ?? $data['inicial_13dig'] ?? null,
            'numero_final' => $data['numero_final'] ?? $data['final_13dig'] ?? null,
            'quantidade' => $data['quantidade'],
            'total' => $data['total'] ?? $data['quantidade'],
            'utilizados' => $data['utilizados'] ?? 0,
            'status' => $data['status'] ?? 'disponivel'
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->db->execute($sql, $params);
        
        return true;
    }
    
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $this->db->execute($sql, ['id' => $id]);
        return true;
    }
    
    public function countApacsEmitidas($id)
    {
        $sql = "SELECT COUNT(*) as total FROM apacs WHERE faixa_id = :id";
        $result = $this->db->fetchOne($sql, ['id' => $id]);
        return $result['total'] ?? 0;
    }
}
