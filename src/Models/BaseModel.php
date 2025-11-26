<?php

namespace App\Models;

use App\Database\Database;
use PDO;
use PDOException;

abstract class BaseModel
{
    protected $db;
    protected $table;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function findAll()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function getAll()
    {
        return $this->findAll();
    }
    
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function getById($id)
    {
        return $this->findById($id);
    }
    
    public function create(array $data)
    {
        try {
            // Filtrar campos NULL vazios (exceto se explicitamente definidos como null)
            $filteredData = [];
            foreach ($data as $key => $value) {
                // Incluir o campo mesmo se for null (para permitir NULL no banco)
                // Mas remover campos que são strings vazias quando não são obrigatórios
                if ($value !== '' || $value === null) {
                    $filteredData[$key] = $value === '' ? null : $value;
                }
            }
            
            if (empty($filteredData)) {
                throw new \Exception('Nenhum dado fornecido para inserção');
            }
            
            $fields = array_keys($filteredData);
            $placeholders = array_map(function($field) {
                return ":{$field}";
            }, $fields);
            
            $sql = sprintf(
                "INSERT INTO %s (%s) VALUES (%s)",
                $this->table,
                implode(', ', $fields),
                implode(', ', $placeholders)
            );
            
            $this->db->execute($sql, $filteredData);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            $this->tratarErro($e, 'create');
            throw $e;
        }
    }
    
    public function update($id, array $data)
    {
        try {
            $fields = [];
            $params = ['id' => $id];
            
            foreach ($data as $key => $value) {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
            
            $sql = sprintf(
                "UPDATE %s SET %s WHERE id = :id",
                $this->table,
                implode(', ', $fields)
            );
            
            $this->db->execute($sql, $params);
            return true;
        } catch (PDOException $e) {
            $this->tratarErro($e, 'update');
            throw $e;
        }
    }
    
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->execute($sql, ['id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->tratarErro($e, 'delete');
            throw $e;
        }
    }
    
    protected function executarQuery($sql, array $params = [])
    {
        try {
            $pdo = $this->db->getConnection();
            $stmt = $pdo->prepare($sql);
            
            foreach ($params as $key => $value) {
                $this->bindParam($stmt, $key, $value);
            }
            
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            $this->tratarErro($e, 'executarQuery');
            throw $e;
        }
    }
    
    protected function bindParam($stmt, $key, $value)
    {
        if (is_numeric($key)) {
            $param = $key;
        } else {
            $param = (strpos($key, ':') === 0) ? $key : ":{$key}";
        }
        
        if (is_int($value)) {
            $stmt->bindValue($param, $value, PDO::PARAM_INT);
        } elseif (is_bool($value)) {
            $stmt->bindValue($param, $value ? 1 : 0, PDO::PARAM_INT);
        } elseif (is_null($value)) {
            $stmt->bindValue($param, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue($param, $value, PDO::PARAM_STR);
        }
    }
    
    protected function tratarErro(PDOException $e, $operacao = '')
    {
        error_log(sprintf(
            "[%s] Erro na operação '%s' na tabela '%s': %s",
            date('Y-m-d H:i:s'),
            $operacao,
            $this->table,
            $e->getMessage()
        ));
    }
    
    protected function count($conditions = [])
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            
            if (!empty($conditions)) {
                $where = [];
                foreach ($conditions as $field => $value) {
                    $where[] = "{$field} = :{$field}";
                }
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            $stmt = $this->executarQuery($sql, $conditions);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->tratarErro($e, 'count');
            throw $e;
        }
    }
    
    protected function exists($conditions)
    {
        return $this->count($conditions) > 0;
    }
}
