<?php

namespace App\Models;

class Profissional extends BaseModel
{
    protected $table = 'profissionais';
    
    public function findByCns($cns)
    {
        $cns = preg_replace('/[^0-9]/', '', $cns);
        $sql = "SELECT * FROM {$this->table} WHERE cns = :cns LIMIT 1";
        return $this->db->fetchOne($sql, ['cns' => $cns]);
    }
    
    public function findByCpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $sql = "SELECT * FROM {$this->table} WHERE cpf = :cpf LIMIT 1";
        return $this->db->fetchOne($sql, ['cpf' => $cpf]);
    }
    
    public function findByMatricula($matricula)
    {
        $sql = "SELECT * FROM {$this->table} WHERE matricula = :matricula LIMIT 1";
        return $this->db->fetchOne($sql, ['matricula' => $matricula]);
    }
    
    public function findPaginated($limit = 10, $offset = 0)
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $sql = "SELECT * FROM {$this->table} ORDER BY nome ASC LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql);
    }
    
    public function searchPaginated($term, $limit = 10, $offset = 0)
    {
        $limit = max(1, min(100, (int)$limit));
        $offset = max(0, (int)$offset);
        $termLike = "%{$term}%";
        $sql = "SELECT * FROM {$this->table} 
                WHERE nome LIKE :term1 
                   OR cpf LIKE :term2 
                   OR cns LIKE :term3 
                   OR matricula LIKE :term4
                   OR especialidade LIKE :term5 
                ORDER BY nome ASC 
                LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql, [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike,
            'term4' => $termLike,
            'term5' => $termLike
        ]);
    }
    
    public function searchCount($term)
    {
        $termLike = "%{$term}%";
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE nome LIKE :term1 
                   OR cpf LIKE :term2 
                   OR cns LIKE :term3 
                   OR matricula LIKE :term4
                   OR especialidade LIKE :term5";
        $result = $this->db->fetchOne($sql, [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike,
            'term4' => $termLike,
            'term5' => $termLike
        ]);
        return $result['total'] ?? 0;
    }
    
    public function countTotal()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
    
    public function getLaudos($profissionalId, $limit = 5)
    {
        $profissionalId = (int)$profissionalId;
        $limit = max(1, min(50, (int)$limit));
        $sql = "SELECT l.id, l.numero_laudo, l.data_laudo, l.status, 
                       l.created_at, l.updated_at,
                       p.nome as paciente_nome,
                       COALESCE(a.numero_apac, '') as numero_apac
                FROM laudos l
                LEFT JOIN apacs_laudos al ON l.id = al.laudo_id
                LEFT JOIN apacs a ON al.apac_id = a.id
                LEFT JOIN pacientes p ON l.paciente_id = p.id
                WHERE l.profissional_solicitante_id = :profissional_id
                ORDER BY l.data_laudo DESC, l.created_at DESC
                LIMIT {$limit}";
        
        return $this->db->fetchAll($sql, ['profissional_id' => $profissionalId]);
    }
    
    public function countLaudosPorMes($profissionalId, $mes = null, $ano = null)
    {
        $profissionalId = (int)$profissionalId;
        $where = "WHERE l.profissional_solicitante_id = :profissional_id";
        
        if ($mes && $ano) {
            $where .= " AND MONTH(l.data_laudo) = :mes AND YEAR(l.data_laudo) = :ano";
            $params = ['profissional_id' => $profissionalId, 'mes' => $mes, 'ano' => $ano];
        } elseif ($ano) {
            $where .= " AND YEAR(l.data_laudo) = :ano";
            $params = ['profissional_id' => $profissionalId, 'ano' => $ano];
        } else {
            $params = ['profissional_id' => $profissionalId];
        }
        
        $sql = "SELECT COUNT(*) as total FROM laudos l {$where}";
        $result = $this->db->fetchOne($sql, $params);
        return $result['total'] ?? 0;
    }
    
    public function findByEspecialidade($especialidade)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE especialidade = :especialidade 
                ORDER BY nome ASC";
        return $this->db->fetchAll($sql, ['especialidade' => $especialidade]);
    }
}
