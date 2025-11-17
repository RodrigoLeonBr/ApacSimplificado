<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;
    
    private function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        
        try {
            $dsn = sprintf(
                "%s:host=%s;port=%s;dbname=%s",
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database']
            );
            
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection()
    {
        return $this->connection;
    }
    
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database Query Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function execute($sql, $params = [])
    {
        return $this->query($sql, $params);
    }
    
    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
    
    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }
    
    public function commit()
    {
        return $this->connection->commit();
    }
    
    public function rollback()
    {
        return $this->connection->rollBack();
    }
}
