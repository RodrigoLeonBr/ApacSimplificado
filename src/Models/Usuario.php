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
        $sql = "INSERT INTO {$this->table} (email, password, nome, role, ativo) 
                VALUES (:email, :password, :nome, :role, :ativo)";
        
        $ativo = isset($data['ativo']) ? ($data['ativo'] ? 'true' : 'false') : 'true';
        
        $params = [
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
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
                $fields[] = "password = :password";
                $params['password'] = password_hash($value, PASSWORD_BCRYPT);
            } else {
                $fields[] = "{$key} = :{$key}";
                
                if ($key === 'ativo' && is_bool($value)) {
                    $params[$key] = $value ? 'true' : 'false';
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
