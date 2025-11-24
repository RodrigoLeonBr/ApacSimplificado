<?php

namespace App\Models;

class Usuario extends BaseModel
{
    protected $table = 'usuarios';
    
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        return $this->db->fetchOne($sql, ['email' => $email]);
    }
    
    public function create($data)
    {
        $ativo = isset($data['ativo']) ? ($data['ativo'] ? 1 : 0) : 1;
        
        $params = [
            'email' => $data['email'],
            'senha_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'nome' => $data['nome'],
            'role' => $data['role'] ?? 'user',
            'ativo' => $ativo
        ];
        
        $this->db->execute(
            "INSERT INTO {$this->table} (email, senha_hash, nome, role, ativo) 
             VALUES (:email, :senha_hash, :nome, :role, :ativo)",
            $params
        );
        
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
                $params[$key] = ($key === 'ativo' && is_bool($value)) ? ($value ? 1 : 0) : $value;
            }
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->db->execute($sql, $params);
        
        return true;
    }
}
