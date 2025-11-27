<?php

namespace App\Models;

class RelacionamentoProcedimentoCid extends BaseModel
{
    protected $table = 'rl_procedimento_cid';
    
    /**
     * Codifica chave composta para uso em URLs
     * 
     * @param string $co_procedimento
     * @param string $co_cid
     * @return string
     */
    public static function codificarChave($co_procedimento, $co_cid)
    {
        $chave = base64_encode($co_procedimento . '|' . $co_cid);
        // URL encode para garantir compatibilidade com rotas
        return urlencode($chave);
    }
    
    /**
     * Decodifica chave composta de URLs
     * 
     * @param string $chaveCodificada
     * @return array ['co_procedimento' => string, 'co_cid' => string]
     */
    public static function decodificarChave($chaveCodificada)
    {
        // URL decode primeiro
        $chaveCodificada = urldecode($chaveCodificada);
        $decodificado = base64_decode($chaveCodificada);
        list($co_procedimento, $co_cid) = explode('|', $decodificado, 2);
        return [
            'co_procedimento' => $co_procedimento,
            'co_cid' => $co_cid
        ];
    }
    
    /**
     * Adiciona campo virtual 'id' aos resultados
     * 
     * @param array|array[] $results
     * @return array|array[]
     */
    private function adicionarIdVirtual($results)
    {
        if (empty($results)) {
            return $results;
        }
        
        // Se for um único resultado (array associativo)
        if (isset($results['co_procedimento'])) {
            $results['id'] = self::codificarChave($results['co_procedimento'], $results['co_cid']);
            return $results;
        }
        
        // Se for array de resultados
        foreach ($results as &$result) {
            if (isset($result['co_procedimento']) && isset($result['co_cid'])) {
                $result['id'] = self::codificarChave($result['co_procedimento'], $result['co_cid']);
            }
        }
        
        return $results;
    }
    
    /**
     * Busca relacionamento por chave composta (codificada ou não)
     * 
     * @param string|array $id Chave codificada em base64 ou array com co_procedimento e co_cid
     * @return array|null
     */
    public function findById($id)
    {
        // Se for array, já tem as chaves
        if (is_array($id)) {
            $co_procedimento = $id['co_procedimento'];
            $co_cid = $id['co_cid'];
        } else {
            // Tenta decodificar se for string
            try {
                $chaves = self::decodificarChave($id);
                $co_procedimento = $chaves['co_procedimento'];
                $co_cid = $chaves['co_cid'];
            } catch (\Exception $e) {
                // Se falhar ao decodificar, retorna null
                return null;
            }
        }
        
        $sql = "SELECT r.*, 
                       p.id as procedimento_id,
                       p.codigo_procedimento,
                       p.descricao as procedimento_descricao,
                       c.id as cid_id,
                       c.codigo as cid_codigo,
                       c.descricao as cid_descricao
                FROM {$this->table} r
                INNER JOIN procedimentos p ON r.co_procedimento COLLATE utf8mb4_unicode_ci = p.codigo_procedimento COLLATE utf8mb4_unicode_ci
                INNER JOIN cids c ON r.co_cid COLLATE utf8mb4_unicode_ci = c.codigo COLLATE utf8mb4_unicode_ci
                WHERE r.co_procedimento COLLATE utf8mb4_unicode_ci = :co_procedimento COLLATE utf8mb4_unicode_ci
                  AND r.co_cid COLLATE utf8mb4_unicode_ci = :co_cid COLLATE utf8mb4_unicode_ci
                LIMIT 1";
        $result = $this->db->fetchOne($sql, [
            'co_procedimento' => $co_procedimento,
            'co_cid' => $co_cid
        ]);
        
        return $this->adicionarIdVirtual($result);
    }
    
    /**
     * Busca relacionamentos por procedimento
     * 
     * @param string $codigoProcedimento
     * @return array
     */
    public function findByProcedimento($codigoProcedimento)
    {
        $sql = "SELECT r.*, 
                       c.codigo as cid_codigo,
                       c.descricao as cid_descricao
                FROM {$this->table} r
                INNER JOIN cids c ON r.co_cid COLLATE utf8mb4_unicode_ci = c.codigo COLLATE utf8mb4_unicode_ci
                WHERE r.co_procedimento COLLATE utf8mb4_unicode_ci = :codigo COLLATE utf8mb4_unicode_ci
                ORDER BY r.st_principal DESC, c.codigo ASC";
        $results = $this->db->fetchAll($sql, ['codigo' => $codigoProcedimento]);
        return $this->adicionarIdVirtual($results);
    }
    
    /**
     * Busca relacionamentos por CID
     * 
     * @param string $codigoCid
     * @return array
     */
    public function findByCid($codigoCid)
    {
        $sql = "SELECT r.*, 
                       p.codigo_procedimento,
                       p.descricao as procedimento_descricao
                FROM {$this->table} r
                INNER JOIN procedimentos p ON r.co_procedimento COLLATE utf8mb4_unicode_ci = p.codigo_procedimento COLLATE utf8mb4_unicode_ci
                WHERE r.co_cid COLLATE utf8mb4_unicode_ci = :codigo COLLATE utf8mb4_unicode_ci
                ORDER BY r.st_principal DESC, p.codigo_procedimento ASC";
        $results = $this->db->fetchAll($sql, ['codigo' => $codigoCid]);
        return $this->adicionarIdVirtual($results);
    }
    
    /**
     * Busca relacionamento específico por procedimento e CID
     * 
     * @param string $codigoProcedimento
     * @param string $codigoCid
     * @return array|null
     */
    public function findByProcedimentoECid($codigoProcedimento, $codigoCid)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE co_procedimento COLLATE utf8mb4_unicode_ci = :proc COLLATE utf8mb4_unicode_ci 
                  AND co_cid COLLATE utf8mb4_unicode_ci = :cid COLLATE utf8mb4_unicode_ci
                LIMIT 1";
        $result = $this->db->fetchOne($sql, [
            'proc' => $codigoProcedimento,
            'cid' => $codigoCid
        ]);
        return $this->adicionarIdVirtual($result);
    }
    
    /**
     * Lista paginada com filtros
     * 
     * @param int $limit
     * @param int $offset
     * @param array $filtros ['procedimento_id', 'cid_id', 'st_principal']
     * @return array
     */
    public function findPaginated($limit = 10, $offset = 0, $filtros = [])
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        
        $sql = "SELECT r.*, 
                       p.id as procedimento_id,
                       p.codigo_procedimento,
                       p.descricao as procedimento_descricao,
                       c.id as cid_id,
                       c.codigo as cid_codigo,
                       c.descricao as cid_descricao
                FROM {$this->table} r
                INNER JOIN procedimentos p ON r.co_procedimento COLLATE utf8mb4_unicode_ci = p.codigo_procedimento COLLATE utf8mb4_unicode_ci
                INNER JOIN cids c ON r.co_cid COLLATE utf8mb4_unicode_ci = c.codigo COLLATE utf8mb4_unicode_ci
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['procedimento_id'])) {
            $sql .= " AND p.id = :procedimento_id";
            $params['procedimento_id'] = $filtros['procedimento_id'];
        }
        
        if (!empty($filtros['cid_id'])) {
            $sql .= " AND c.id = :cid_id";
            $params['cid_id'] = $filtros['cid_id'];
        }
        
        if (isset($filtros['st_principal']) && $filtros['st_principal'] !== '') {
            $sql .= " AND r.st_principal = :st_principal";
            $params['st_principal'] = $filtros['st_principal'];
        }
        
        $sql .= " ORDER BY r.st_principal DESC, p.codigo_procedimento ASC, c.codigo ASC
                  LIMIT {$limit} OFFSET {$offset}";
        
        $results = $this->db->fetchAll($sql, $params);
        return $this->adicionarIdVirtual($results);
    }
    
    /**
     * Busca paginada com termo de pesquisa
     * 
     * @param string $term
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function searchPaginated($term, $limit = 10, $offset = 0)
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $termLike = "%{$term}%";
        
        $sql = "SELECT r.*, 
                       p.id as procedimento_id,
                       p.codigo_procedimento,
                       p.descricao as procedimento_descricao,
                       c.id as cid_id,
                       c.codigo as cid_codigo,
                       c.descricao as cid_descricao
                FROM {$this->table} r
                INNER JOIN procedimentos p ON r.co_procedimento COLLATE utf8mb4_unicode_ci = p.codigo_procedimento COLLATE utf8mb4_unicode_ci
                INNER JOIN cids c ON r.co_cid COLLATE utf8mb4_unicode_ci = c.codigo COLLATE utf8mb4_unicode_ci
                WHERE p.codigo_procedimento LIKE :term1
                   OR p.descricao LIKE :term2
                   OR c.codigo LIKE :term3
                   OR c.descricao LIKE :term4
                ORDER BY r.st_principal DESC, p.codigo_procedimento ASC, c.codigo ASC
                LIMIT {$limit} OFFSET {$offset}";
        
        $results = $this->db->fetchAll($sql, [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike,
            'term4' => $termLike
        ]);
        return $this->adicionarIdVirtual($results);
    }
    
    /**
     * Conta total com filtros
     * 
     * @param array $filtros
     * @return int
     */
    public function countWithFilters($filtros = [])
    {
        $sql = "SELECT COUNT(*) as total
                FROM {$this->table} r
                INNER JOIN procedimentos p ON r.co_procedimento COLLATE utf8mb4_unicode_ci = p.codigo_procedimento COLLATE utf8mb4_unicode_ci
                INNER JOIN cids c ON r.co_cid COLLATE utf8mb4_unicode_ci = c.codigo COLLATE utf8mb4_unicode_ci
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filtros['procedimento_id'])) {
            $sql .= " AND p.id = :procedimento_id";
            $params['procedimento_id'] = $filtros['procedimento_id'];
        }
        
        if (!empty($filtros['cid_id'])) {
            $sql .= " AND c.id = :cid_id";
            $params['cid_id'] = $filtros['cid_id'];
        }
        
        if (isset($filtros['st_principal']) && $filtros['st_principal'] !== '') {
            $sql .= " AND r.st_principal = :st_principal";
            $params['st_principal'] = $filtros['st_principal'];
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }
    
    /**
     * Conta total de resultados da busca
     * 
     * @param string $term
     * @return int
     */
    public function searchCount($term)
    {
        $termLike = "%{$term}%";
        $sql = "SELECT COUNT(*) as total
                FROM {$this->table} r
                INNER JOIN procedimentos p ON r.co_procedimento COLLATE utf8mb4_unicode_ci = p.codigo_procedimento COLLATE utf8mb4_unicode_ci
                INNER JOIN cids c ON r.co_cid COLLATE utf8mb4_unicode_ci = c.codigo COLLATE utf8mb4_unicode_ci
                WHERE p.codigo_procedimento LIKE :term1
                   OR p.descricao LIKE :term2
                   OR c.codigo LIKE :term3
                   OR c.descricao LIKE :term4";
        
        $result = $this->db->fetchOne($sql, [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike,
            'term4' => $termLike
        ]);
        
        return $result['total'] ?? 0;
    }
    
    /**
     * Conta total de relacionamentos
     * 
     * @return int
     */
    public function countTotal()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
    
    /**
     * Cria novo relacionamento
     * 
     * @param array $data
     * @return int|false ID do relacionamento criado ou false em caso de erro
     */
    public function create(array $data)
    {
        try {
            // Verificar se já existe relacionamento com mesmo procedimento e CID
            $existente = $this->findByProcedimentoECid(
                $data['co_procedimento'],
                $data['co_cid']
            );
            
            if ($existente) {
                // Se já existe, atualiza ao invés de criar
                return $this->update([
                    'co_procedimento' => $existente['co_procedimento'],
                    'co_cid' => $existente['co_cid']
                ], $data);
            }
            
            // dt_competencia é obrigatório, usar data atual se não fornecido
            $dt_competencia = $data['dt_competencia'] ?? null;
            if (empty($dt_competencia)) {
                // Formato YYYYMM (ano + mês atual)
                $dt_competencia = date('Ym');
            }
            
            $sql = "INSERT INTO {$this->table} (co_procedimento, co_cid, st_principal, dt_competencia)
                    VALUES (:co_procedimento, :co_cid, :st_principal, :dt_competencia)";
            
            $this->db->query($sql, [
                'co_procedimento' => $data['co_procedimento'],
                'co_cid' => $data['co_cid'],
                'st_principal' => $data['st_principal'] ?? 'N',
                'dt_competencia' => $dt_competencia
            ]);
            
            // Retorna chave codificada ao invés de ID
            return self::codificarChave($data['co_procedimento'], $data['co_cid']);
        } catch (\Exception $e) {
            error_log('Erro ao criar relacionamento: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualiza relacionamento
     * 
     * @param string|array $id Chave codificada ou array com co_procedimento e co_cid
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        try {
            // Obter chaves antigas
            if (is_array($id)) {
                $co_procedimento_old = $id['co_procedimento'];
                $co_cid_old = $id['co_cid'];
            } else {
                $chaves = self::decodificarChave($id);
                $co_procedimento_old = $chaves['co_procedimento'];
                $co_cid_old = $chaves['co_cid'];
            }
            
            // Se as chaves mudaram, precisa deletar o antigo e criar novo
            if ($co_procedimento_old !== $data['co_procedimento'] || $co_cid_old !== $data['co_cid']) {
                // Deletar relacionamento antigo
                $this->delete($id);
                // Criar novo relacionamento
                $novaChave = $this->create($data);
                return $novaChave; // Retorna a nova chave codificada
            }
            
            // Se as chaves não mudaram, apenas atualiza
            // dt_competencia é obrigatório, usar data atual se não fornecido
            $dt_competencia = $data['dt_competencia'] ?? null;
            if (empty($dt_competencia)) {
                // Formato YYYYMM (ano + mês atual)
                $dt_competencia = date('Ym');
            }
            
            $sql = "UPDATE {$this->table} 
                    SET st_principal = :st_principal,
                        dt_competencia = :dt_competencia
                    WHERE co_procedimento COLLATE utf8mb4_unicode_ci = :co_procedimento COLLATE utf8mb4_unicode_ci
                      AND co_cid COLLATE utf8mb4_unicode_ci = :co_cid COLLATE utf8mb4_unicode_ci";
            
            $this->db->query($sql, [
                'co_procedimento' => $co_procedimento_old,
                'co_cid' => $co_cid_old,
                'st_principal' => $data['st_principal'] ?? 'N',
                'dt_competencia' => $dt_competencia
            ]);
            
            return true;
        } catch (\Exception $e) {
            error_log('Erro ao atualizar relacionamento: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Deleta relacionamento
     * 
     * @param string|array $id Chave codificada ou array com co_procedimento e co_cid
     * @return bool
     */
    public function delete($id)
    {
        try {
            // Obter chaves
            if (is_array($id)) {
                $co_procedimento = $id['co_procedimento'];
                $co_cid = $id['co_cid'];
            } else {
                $chaves = self::decodificarChave($id);
                $co_procedimento = $chaves['co_procedimento'];
                $co_cid = $chaves['co_cid'];
            }
            
            $sql = "DELETE FROM {$this->table} 
                    WHERE co_procedimento COLLATE utf8mb4_unicode_ci = :co_procedimento COLLATE utf8mb4_unicode_ci
                      AND co_cid COLLATE utf8mb4_unicode_ci = :co_cid COLLATE utf8mb4_unicode_ci";
            
            $stmt = $this->db->query($sql, [
                'co_procedimento' => $co_procedimento,
                'co_cid' => $co_cid
            ]);
            
            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            error_log('Erro ao deletar relacionamento: ' . $e->getMessage());
            return false;
        }
    }
}

