<?php

namespace App\Models;

class Laudo extends BaseModel
{
    protected $table = 'laudos';
    
    public function findAll($limit = null, $offset = 0)
    {
        $sql = "SELECT l.*, 
                       p.nome as paciente_nome, 
                       p.cpf as paciente_cpf,
                       proc.descricao as procedimento_descricao,
                       proc.codigo_procedimento as procedimento_codigo,
                       cid.codigo as cid_codigo,
                       cid.descricao as cid_descricao,
                       a.numero_apac as apac_numero,
                       a.id as apac_id
                FROM {$this->table} l
                LEFT JOIN pacientes p ON l.paciente_id = p.id
                LEFT JOIN procedimentos proc ON l.procedimento_autorizado_id = proc.id
                LEFT JOIN cids cid ON l.cid_id = cid.id
                LEFT JOIN apacs_laudos al ON l.id = al.laudo_id
                LEFT JOIN apacs a ON al.apac_id = a.id
                GROUP BY l.id
                ORDER BY l.id DESC";
        
        if ($limit !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Conta o total de laudos.
     * 
     * @param array $conditions Condições opcionais para filtrar a contagem
     * @return int Total de laudos
     */
    public function count($conditions = [])
    {
        // Se não houver condições, usa o método do BaseModel
        if (empty($conditions)) {
            return parent::count($conditions);
        }
        
        // Se houver condições, usa a lógica do BaseModel
        return parent::count($conditions);
    }
    
    /**
     * Busca laudos por termo de pesquisa.
     * 
     * @param string $termo Termo de busca
     * @return array Lista de laudos encontrados
     */
    public function search($termo)
    {
        $termLike = "%{$termo}%";
        $sql = "SELECT l.*, 
                       p.nome as paciente_nome,
                       p.cpf as paciente_cpf,
                       proc.descricao as procedimento_descricao,
                       cid.codigo as cid_codigo
                FROM {$this->table} l
                LEFT JOIN pacientes p ON l.paciente_id = p.id
                LEFT JOIN procedimentos proc ON l.procedimento_autorizado_id = proc.id
                LEFT JOIN cids cid ON l.cid_id = cid.id
                WHERE l.numero_laudo LIKE :term1
                   OR l.numero_prontuario LIKE :term2
                   OR p.nome LIKE :term3
                   OR p.cpf LIKE :term4
                   OR p.cns LIKE :term5
                ORDER BY l.id DESC
                LIMIT 50";
        
        return $this->db->fetchAll($sql, [
            'term1' => $termLike,
            'term2' => $termLike,
            'term3' => $termLike,
            'term4' => $termLike,
            'term5' => $termLike
        ]);
    }
    
    public function findById($id)
    {
        $sql = "SELECT l.*, 
                       p.nome as paciente_nome, 
                       p.cpf as paciente_cpf, 
                       p.cns as paciente_cns,
                       p.data_nascimento as paciente_data_nascimento,
                       p.sexo as paciente_sexo,
                       p.nome_mae as paciente_nome_mae,
                       p.raca_cor as paciente_raca_cor,
                       p.logradouro as paciente_logradouro,
                       p.numero as paciente_numero,
                       p.complemento as paciente_complemento,
                       p.bairro as paciente_bairro,
                       p.cep as paciente_cep,
                       p.municipio as paciente_municipio,
                       proc.codigo_procedimento as procedimento_codigo,
                       proc.descricao as procedimento_descricao,
                       proc_sol.codigo_procedimento as procedimento_solicitado_codigo,
                       proc_sol.descricao as procedimento_solicitado_descricao,
                       cid.codigo as cid_codigo,
                       cid.descricao as cid_descricao,
                       est_sol.razao_social as estabelecimento_solicitante_nome,
                       est_sol.nome_fantasia as estabelecimento_solicitante_nome_fantasia,
                       est_sol.cnes as estabelecimento_solicitante_codigo,
                       est_exec.razao_social as estabelecimento_executante_nome,
                       est_exec.nome_fantasia as estabelecimento_executante_nome_fantasia,
                       est_exec.cnes as estabelecimento_executante_codigo,
                       prof.nome as profissional_solicitante_nome,
                       prof.especialidade as profissional_solicitante_especialidade,
                       car.codigo as carater_atendimento_codigo,
                       car.descricao as carater_atendimento_descricao
                FROM {$this->table} l
                LEFT JOIN pacientes p ON l.paciente_id = p.id
                LEFT JOIN procedimentos proc ON l.procedimento_autorizado_id = proc.id
                LEFT JOIN procedimentos proc_sol ON l.procedimento_solicitado_id = proc_sol.id
                LEFT JOIN cids cid ON l.cid_id = cid.id
                LEFT JOIN estabelecimentos est_sol ON l.estabelecimento_solicitante_id = est_sol.id
                LEFT JOIN estabelecimentos est_exec ON l.estabelecimento_executante_id = est_exec.id
                LEFT JOIN profissionais prof ON l.profissional_solicitante_id = prof.id
                LEFT JOIN caracteres_atendimento car ON l.carater_atendimento_id = car.id
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
                LEFT JOIN procedimentos proc ON l.procedimento_autorizado_id = proc.id
                LEFT JOIN cids cid ON l.cid_id = cid.id
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
    
    /**
     * Busca a APAC vinculada a um laudo específico.
     * 
     * @param int $laudoId
     * @return array|null Retorna a APAC vinculada ou null se não houver
     */
    public function findApacVinculada($laudoId)
    {
        $apacLaudoModel = new ApacLaudo();
        $apacs = $apacLaudoModel->findByLaudoId($laudoId);
        return !empty($apacs) ? $apacs[0] : null;
    }
}
