<?php

namespace App\Models;

class Apac extends BaseModel
{
    protected $table = 'apacs';
    
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
        $impresso = isset($data['impresso']) ? ($data['impresso'] ? 1 : 0) : 0;
        
        $params = [
            'numero_apac' => $data['numero_apac'] ?? $data['numero_14dig'] ?? null,
            'digito_verificador' => $data['digito_verificador'],
            'faixa_id' => $data['faixa_id'],
            'usuario_id' => $data['usuario_id'] ?? $data['emitido_por_usuario_id'] ?? null,
            'impresso' => $impresso
        ];
        
        $this->db->execute(
            "INSERT INTO {$this->table} (numero_apac, digito_verificador, faixa_id, usuario_id, impresso) 
             VALUES (:numero_apac, :digito_verificador, :faixa_id, :usuario_id, :impresso)",
            $params
        );
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data)
    {
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
            $params[$key] = ($key === 'impresso' && is_bool($value)) ? ($value ? 1 : 0) : $value;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $this->db->execute($sql, $params);
        
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
    
    /**
     * Busca APAC através do ID do laudo (via tabela apacs_laudos).
     * 
     * @param int $laudoId
     * @return array|null Retorna a APAC vinculada ou null se não houver
     */
    public function findByLaudoId($laudoId)
    {
        $sql = "SELECT a.*, f.numero_inicial, f.numero_final, u.nome as emitido_por 
                FROM {$this->table} a
                INNER JOIN apacs_laudos al ON a.id = al.apac_id
                LEFT JOIN faixas f ON a.faixa_id = f.id
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE al.laudo_id = :laudo_id
                LIMIT 1";
        return $this->db->fetchOne($sql, ['laudo_id' => $laudoId]);
    }
}
