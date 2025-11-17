<?php

namespace App\Models;

use App\Database\Database;

class Apac
{
    private $db;
    private $table = 'apacs';
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function findAll()
    {
        $sql = "SELECT a.*, f.inicial_13dig, f.final_13dig, u.nome as emitido_por 
                FROM {$this->table} a
                LEFT JOIN faixas f ON a.faixa_id = f.id
                LEFT JOIN usuarios u ON a.emitido_por_usuario_id = u.id
                ORDER BY a.id DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function findById($id)
    {
        $sql = "SELECT a.*, f.inicial_13dig, f.final_13dig, u.nome as emitido_por 
                FROM {$this->table} a
                LEFT JOIN faixas f ON a.faixa_id = f.id
                LEFT JOIN usuarios u ON a.emitido_por_usuario_id = u.id
                WHERE a.id = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function findByFaixaId($faixaId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE faixa_id = :faixa_id ORDER BY numero_14dig ASC";
        return $this->db->fetchAll($sql, ['faixa_id' => $faixaId]);
    }
    
    public function findByNumero($numero14dig)
    {
        $sql = "SELECT * FROM {$this->table} WHERE numero_14dig = :numero_14dig LIMIT 1";
        return $this->db->fetchOne($sql, ['numero_14dig' => $numero14dig]);
    }
    
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (numero_14dig, digito_verificador, faixa_id, emitido_por_usuario_id, impresso) 
                VALUES (:numero_14dig, :digito_verificador, :faixa_id, :emitido_por_usuario_id, :impresso)";
        
        $impresso = isset($data['impresso']) ? ($data['impresso'] ? 'true' : 'false') : 'false';
        
        $params = [
            'numero_14dig' => $data['numero_14dig'],
            'digito_verificador' => $data['digito_verificador'],
            'faixa_id' => $data['faixa_id'],
            'emitido_por_usuario_id' => $data['emitido_por_usuario_id'] ?? null,
            'impresso' => $impresso
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
            
            if ($key === 'impresso' && is_bool($value)) {
                $params[$key] = $value ? 'true' : 'false';
            } else {
                $params[$key] = $value;
            }
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
    
    public function countTotal()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
    
    public function countImpressas()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE impresso = true";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
}
