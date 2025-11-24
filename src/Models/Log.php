<?php

namespace App\Models;

class Log extends BaseModel
{
    protected $table = 'logs';
    
    public function findAll($limit = 100)
    {
        $sql = "SELECT l.*, u.nome as usuario_nome 
                FROM {$this->table} l
                LEFT JOIN usuarios u ON l.usuario_id = u.id
                ORDER BY l.criada_em DESC LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    public function findById($id)
    {
        $sql = "SELECT l.*, u.nome as usuario_nome 
                FROM {$this->table} l
                LEFT JOIN usuarios u ON l.usuario_id = u.id
                WHERE l.id = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function findByUsuarioId($usuarioId, $limit = 50)
    {
        $sql = "SELECT * FROM {$this->table} WHERE usuario_id = :usuario_id ORDER BY criada_em DESC LIMIT :limit";
        return $this->db->fetchAll($sql, ['usuario_id' => $usuarioId, 'limit' => $limit]);
    }
    
    public function create($data)
    {
        $params = [
            'acao' => $data['acao'],
            'usuario_id' => $data['usuario_id'] ?? null,
            'tabela' => $data['tabela'] ?? $data['tabela_afetada'] ?? null,
            'registro_id' => $data['registro_id'] ?? null,
            'detalhes' => $data['detalhes'] ?? null
        ];
        
        $this->db->execute(
            "INSERT INTO {$this->table} (acao, usuario_id, tabela, registro_id, detalhes) 
             VALUES (:acao, :usuario_id, :tabela, :registro_id, :detalhes)",
            $params
        );
        
        return $this->db->lastInsertId();
    }
    
    public function countTotal()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
}
