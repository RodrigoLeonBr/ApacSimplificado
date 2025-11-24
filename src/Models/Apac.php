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
        $sql = "SELECT a.*, f.numero_inicial, f.numero_final, u.nome as emitido_por 
                FROM {$this->table} a
                LEFT JOIN faixas f ON a.faixa_id = f.id
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.id DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function findById($id)
    {
        $sql = "SELECT a.*, f.numero_inicial, f.numero_final, u.nome as emitido_por 
                FROM {$this->table} a
                LEFT JOIN faixas f ON a.faixa_id = f.id
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.id = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function findByFaixaId($faixaId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE faixa_id = :faixa_id ORDER BY numero_apac ASC";
        return $this->db->fetchAll($sql, ['faixa_id' => $faixaId]);
    }
    
    public function findByNumero($numeroApac)
    {
        $sql = "SELECT * FROM {$this->table} WHERE numero_apac = :numero_apac LIMIT 1";
        return $this->db->fetchOne($sql, ['numero_apac' => $numeroApac]);
    }
    
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (numero_apac, digito_verificador, faixa_id, usuario_id, impresso) 
                VALUES (:numero_apac, :digito_verificador, :faixa_id, :usuario_id, :impresso)";
        
        $impresso = isset($data['impresso']) ? ($data['impresso'] ? 1 : 0) : 0;
        
        $params = [
            'numero_apac' => $data['numero_apac'] ?? $data['numero_14dig'] ?? null,
            'digito_verificador' => $data['digito_verificador'],
            'faixa_id' => $data['faixa_id'],
            'usuario_id' => $data['usuario_id'] ?? $data['emitido_por_usuario_id'] ?? null,
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
                $params[$key] = $value ? 1 : 0;
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
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE impresso = 1";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
}
