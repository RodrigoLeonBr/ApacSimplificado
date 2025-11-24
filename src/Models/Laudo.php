<?php

namespace App\Models;

class Laudo extends BaseModel
{
    protected $table = 'laudos';
    
    public function findAll()
    {
        $sql = "SELECT l.*, 
                       p.nome as paciente_nome, 
                       p.cpf as paciente_cpf,
                       proc.descricao as procedimento_descricao,
                       cid.codigo as cid_codigo,
                       cid.descricao as cid_descricao
                FROM {$this->table} l
                LEFT JOIN pacientes p ON l.paciente_id = p.id
                LEFT JOIN procedimentos proc ON l.procedimento_principal_id = proc.id
                LEFT JOIN cids cid ON l.cid_principal_id = cid.id
                ORDER BY l.id DESC";
        return $this->db->fetchAll($sql);
    }
    
    public function findById($id)
    {
        $sql = "SELECT l.*, 
                       p.nome as paciente_nome, 
                       p.cpf as paciente_cpf, 
                       p.cns as paciente_cns,
                       proc.codigo as procedimento_codigo,
                       proc.descricao as procedimento_descricao,
                       cid.codigo as cid_codigo,
                       cid.descricao as cid_descricao
                FROM {$this->table} l
                LEFT JOIN pacientes p ON l.paciente_id = p.id
                LEFT JOIN procedimentos proc ON l.procedimento_principal_id = proc.id
                LEFT JOIN cids cid ON l.cid_principal_id = cid.id
                WHERE l.id = :id 
                LIMIT 1";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function findByPacienteId($pacienteId)
    {
        $sql = "SELECT l.*,
                       proc.descricao as procedimento_descricao,
                       cid.codigo as cid_codigo
                FROM {$this->table} l
                LEFT JOIN procedimentos proc ON l.procedimento_principal_id = proc.id
                LEFT JOIN cids cid ON l.cid_principal_id = cid.id
                WHERE l.paciente_id = :paciente_id
                ORDER BY l.created_at DESC";
        return $this->db->fetchAll($sql, ['paciente_id' => $pacienteId]);
    }
    
    public function countByPaciente($pacienteId)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE paciente_id = :paciente_id";
        $result = $this->db->fetchOne($sql, ['paciente_id' => $pacienteId]);
        return $result['total'] ?? 0;
    }
}
