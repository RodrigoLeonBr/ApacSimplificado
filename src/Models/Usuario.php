<?php

namespace App\Models;

use App\Database\Database;

class Usuario
{
    private $db;
    private $table = 'usuarios';
    
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
    
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        return $this->db->fetchOne($sql, ['email' => $email]);
    }
    
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (email, senha_hash, nome, role, ativo) 
                VALUES (:email, :senha_hash, :nome, :role, :ativo)";
        
        $ativo = isset($data['ativo']) ? ($data['ativo'] ? 1 : 0) : 1;
        
        $params = [
            'email' => $data['email'],
            'senha_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'nome' => $data['nome'],
            'role' => $data['role'] ?? 'user',
            'ativo' => $ativo
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $fields[] = "senha_hash = :senha_hash";
                $params['senha_hash'] = password_hash($value, PASSWORD_BCRYPT);
            } else {
                $fields[] = "{$key} = :{$key}";
                
                if ($key === 'ativo' && is_bool($value)) {
                    $params[$key] = $value ? 1 : 0;
                } else {
                    $params[$key] = $value;
                }
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
}
