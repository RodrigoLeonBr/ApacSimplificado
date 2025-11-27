<?php

namespace App\Services;

use App\Database\Database;
use PDO;
use PDOException;

/**
 * Service para importação de arquivos posicionais do SIGTAP (SUS)
 * 
 * Arquivos suportados:
 * - tb_cid.txt: Códigos CID (Classificação Internacional de Doenças)
 * - tb_procedimento.txt: Procedimentos do SUS
 * - rl_procedimento_cid.txt: Relacionamento entre Procedimentos e CIDs
 */
class SigtapImportService
{
    private $db;
    private $errors = [];
    private $stats = [
        'cids' => ['importados' => 0, 'atualizados' => 0, 'erros' => 0],
        'procedimentos' => ['importados' => 0, 'atualizados' => 0, 'erros' => 0],
        'relacionamentos' => ['importados' => 0, 'erros' => 0]
    ];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Limpa e converte texto de ISO-8859-1 para UTF-8
     */
    private function limparTexto($texto)
    {
        $texto = trim($texto);
        if (empty($texto)) {
            return '';
        }
        // Converte de ISO-8859-1 (padrão SUS) para UTF-8
        return mb_convert_encoding($texto, 'UTF-8', 'ISO-8859-1');
    }

    /**
     * Extrai campo posicional de uma linha
     * 
     * @param string $linha Linha completa do arquivo
     * @param int $inicio Posição inicial (1-based, como no layout SUS)
     * @param int $tamanho Tamanho do campo
     * @return string Campo extraído e limpo
     */
    private function extrairCampo($linha, $inicio, $tamanho)
    {
        // Converte de 1-based para 0-based
        $start = $inicio - 1;
        $campo = substr($linha, $start, $tamanho);
        return $this->limparTexto($campo);
    }

    /**
     * Converte valor monetário do formato SUS para decimal
     * Formato SUS: 10 dígitos (formato 8,2 - 8 inteiros + 2 decimais)
     * Exemplo: 0000841571 = 8415.71 (841571 / 100)
     * 
     * @param string $valor Valor no formato SUS (10 dígitos)
     * @return float Valor decimal
     */
    private function converterValorMonetario($valor)
    {
        $valor = trim($valor);
        if (empty($valor) || !is_numeric($valor)) {
            return 0.00;
        }
        // Divide por 100 para converter centavos em reais
        return (float)($valor / 100);
    }

    /**
     * Importa CIDs do arquivo tb_cid.txt
     * 
     * Layout oficial SIGTAP:
     * - Posição 1-4: CO_CID (4 caracteres)
     * - Posição 5-104: NO_CID (100 caracteres)
     * - Posição 105: TP_AGRAVO (1 caractere)
     * - Posição 106: TP_SEXO (1 caractere)
     * - Posição 107: TP_ESTADIO (1 caractere)
     * - Posição 108-111: VL_CAMPOS_IRRADIADOS (4 caracteres numéricos)
     */
    public function importarCids($arquivo)
    {
        if (!file_exists($arquivo)) {
            throw new \Exception("Arquivo não encontrado: {$arquivo}");
        }

        $handle = fopen($arquivo, 'r');
        if (!$handle) {
            throw new \Exception("Não foi possível abrir o arquivo: {$arquivo}");
        }

        $sql = "INSERT INTO cids (codigo, descricao, tp_agravo, tp_sexo, tp_estadio, vl_campos_irradiados, criada_em)
                VALUES (:codigo, :descricao, :tp_agravo, :tp_sexo, :tp_estadio, :vl_campos_irradiados, NOW())
                ON DUPLICATE KEY UPDATE
                    descricao = VALUES(descricao),
                    tp_agravo = VALUES(tp_agravo),
                    tp_sexo = VALUES(tp_sexo),
                    tp_estadio = VALUES(tp_estadio),
                    vl_campos_irradiados = VALUES(vl_campos_irradiados)";

        $stmt = $this->db->getConnection()->prepare($sql);
        
        $this->db->beginTransaction();
        $count = 0;
        $batchSize = 1000;

        try {
            while (($line = fgets($handle)) !== false) {
                $line = rtrim($line, "\r\n");
                
                // Validação mínima: linha deve ter pelo menos 4 caracteres (código CID)
                if (strlen($line) < 4) {
                    continue;
                }

                try {
                    // Layout oficial SIGTAP: CO_CID (1-4), NO_CID (5-104), TP_AGRAVO (105), TP_SEXO (106), TP_ESTADIO (107), VL_CAMPOS_IRRADIADOS (108-111)
                    $tamanhoLinha = strlen($line);
                    if ($tamanhoLinha < 4) {
                        continue; // Linha muito curta
                    }
                    
                    $codigo = trim(substr($line, 0, 4));
                    if (empty($codigo)) {
                        continue;
                    }

                    // Descrição: posição 5-104 (100 caracteres)
                    $descricao = $this->limparTexto(substr($line, 4, 100));
                    
                    // Campos finais conforme layout oficial
                    $tp_agravo = $tamanhoLinha >= 105 ? trim(substr($line, 104, 1)) : null;
                    $tp_sexo = $tamanhoLinha >= 106 ? trim(substr($line, 105, 1)) : null;
                    $tp_estadio = $tamanhoLinha >= 107 ? trim(substr($line, 106, 1)) : null;
                    $vl_campos_irradiados = $tamanhoLinha >= 111 ? (int)trim(substr($line, 107, 4)) : 0;

                    // Verifica se é atualização ou inserção
                    $existe = $this->db->fetchOne(
                        "SELECT id FROM cids WHERE codigo = :codigo",
                        ['codigo' => $codigo]
                    );

                    $stmt->execute([
                        ':codigo' => $codigo,
                        ':descricao' => $descricao,
                        ':tp_agravo' => $tp_agravo ?: null,
                        ':tp_sexo' => $tp_sexo ?: null,
                        ':tp_estadio' => $tp_estadio ?: null,
                        ':vl_campos_irradiados' => $vl_campos_irradiados
                    ]);

                    if ($existe) {
                        $this->stats['cids']['atualizados']++;
                    } else {
                        $this->stats['cids']['importados']++;
                    }

                    $count++;
                    
                    // Commit a cada lote para melhor performance
                    if ($count % $batchSize === 0) {
                        $this->db->commit();
                        $this->db->beginTransaction();
                    }
                } catch (PDOException $e) {
                    $this->stats['cids']['erros']++;
                    $this->errors[] = "Erro ao importar CID linha {$count}: " . $e->getMessage();
                    continue;
                }
            }

            $this->db->commit();
            fclose($handle);
            
            return [
                'sucesso' => true,
                'importados' => $this->stats['cids']['importados'],
                'atualizados' => $this->stats['cids']['atualizados'],
                'erros' => $this->stats['cids']['erros']
            ];
        } catch (\Exception $e) {
            $this->db->rollback();
            fclose($handle);
            throw $e;
        }
    }

    /**
     * Importa Procedimentos do arquivo tb_procedimento.txt
     * 
     * Layout oficial SIGTAP:
     * - Posição 1-10: CO_PROCEDIMENTO (10 caracteres)
     * - Posição 11-260: NO_PROCEDIMENTO (250 caracteres)
     * - Posição 261: TP_COMPLEXIDADE (1 caractere)
     * - Posição 262: TP_SEXO (1 caractere)
     * - Posição 263-266: QT_MAXIMA_EXECUCAO (4 caracteres)
     * - Posição 267-270: QT_DIAS_PERMANENCIA (4 caracteres) - não usado no banco
     * - Posição 271-274: QT_PONTOS (4 caracteres) - não usado no banco
     * - Posição 275-278: VL_IDADE_MINIMA (4 caracteres) - não usado no banco
     * - Posição 279-282: VL_IDADE_MAXIMA (4 caracteres) - não usado no banco
     * - Posição 283-292: VL_SH (10 caracteres monetários - formato 8,2)
     * - Posição 293-302: VL_SA (10 caracteres monetários - formato 8,2)
     * - Posição 303-312: VL_SP (10 caracteres monetários - formato 8,2)
     * - Posição 313-314: CO_FINANCIAMENTO (2 caracteres) - não usado no banco
     * - Posição 315-320: CO_RUBRICA (6 caracteres) - não usado no banco
     * - Posição 321-326: DT_COMPETENCIA (6 caracteres - formato YYYYMM)
     */
    public function importarProcedimentos($arquivo)
    {
        if (!file_exists($arquivo)) {
            throw new \Exception("Arquivo não encontrado: {$arquivo}");
        }

        $handle = fopen($arquivo, 'r');
        if (!$handle) {
            throw new \Exception("Não foi possível abrir o arquivo: {$arquivo}");
        }

        $sql = "INSERT INTO procedimentos (
                    codigo_procedimento, descricao, tabela_sus, tp_complexidade, tp_sexo,
                    qt_maxima_execucao, vl_sh, vl_sa, vl_sp, dt_competencia, criada_em
                ) VALUES (
                    :codigo, :descricao, 'SIGTAP', :tp_complexidade, :tp_sexo,
                    :qt_maxima_execucao, :vl_sh, :vl_sa, :vl_sp, :dt_competencia, NOW()
                )
                ON DUPLICATE KEY UPDATE
                    descricao = VALUES(descricao),
                    tp_complexidade = VALUES(tp_complexidade),
                    tp_sexo = VALUES(tp_sexo),
                    qt_maxima_execucao = VALUES(qt_maxima_execucao),
                    vl_sh = VALUES(vl_sh),
                    vl_sa = VALUES(vl_sa),
                    vl_sp = VALUES(vl_sp),
                    dt_competencia = VALUES(dt_competencia)";

        $stmt = $this->db->getConnection()->prepare($sql);
        
        $this->db->beginTransaction();
        $count = 0;
        $batchSize = 1000;

        try {
            while (($line = fgets($handle)) !== false) {
                $line = rtrim($line, "\r\n");
                
                // Validação mínima: linha deve ter pelo menos 10 caracteres (código procedimento)
                if (strlen($line) < 10) {
                    continue;
                }

                try {
                    $codigo = trim(substr($line, 0, 10));
                    if (empty($codigo)) {
                        continue;
                    }

                    $tamanhoLinha = strlen($line);
                    
                    // Layout oficial SIGTAP:
                    // CO_PROCEDIMENTO (1-10), NO_PROCEDIMENTO (11-260), TP_COMPLEXIDADE (261), TP_SEXO (262)
                    // QT_MAXIMA_EXECUCAO (263-266), VL_SH (283-292), VL_SA (293-302), VL_SP (303-312), DT_COMPETENCIA (321-326)
                    
                    // Descrição: posição 11-260 (250 caracteres)
                    $descricao = $this->limparTexto(substr($line, 10, 250));
                    
                    // Campos posicionais conforme layout oficial SIGTAP
                    $tp_complexidade = $tamanhoLinha >= 261 ? trim(substr($line, 260, 1)) : null;
                    $tp_sexo = $tamanhoLinha >= 262 ? trim(substr($line, 261, 1)) : null;
                    $qt_maxima_execucao = $tamanhoLinha >= 266 ? (int)trim(substr($line, 262, 4)) : 1;
                    
                    // Valores monetários: formato 8,2 (10 caracteres, últimos 2 são decimais)
                    $vl_sh = $tamanhoLinha >= 292 ? $this->converterValorMonetario(trim(substr($line, 282, 10))) : 0.00;
                    $vl_sa = $tamanhoLinha >= 302 ? $this->converterValorMonetario(trim(substr($line, 292, 10))) : 0.00;
                    $vl_sp = $tamanhoLinha >= 312 ? $this->converterValorMonetario(trim(substr($line, 302, 10))) : 0.00;
                    
                    // DT_COMPETENCIA: posição 321-326 (formato YYYYMM)
                    // Nota: Alguns arquivos podem ter a competência no final (últimos 6 caracteres)
                    if ($tamanhoLinha >= 326) {
                        $dt_competencia = trim(substr($line, 320, 6));
                        // Se estiver vazio, tenta pegar do final
                        if (empty($dt_competencia) && $tamanhoLinha >= 6) {
                            $dt_competencia = trim(substr($line, -6));
                        }
                    } elseif ($tamanhoLinha >= 6) {
                        // Se a linha for menor, pega os últimos 6 caracteres
                        $dt_competencia = trim(substr($line, -6));
                    } else {
                        $dt_competencia = null;
                    }

                    // Valores padrão
                    if ($qt_maxima_execucao <= 0) {
                        $qt_maxima_execucao = 1;
                    }

                    // Verifica se é atualização ou inserção
                    $existe = $this->db->fetchOne(
                        "SELECT id FROM procedimentos WHERE codigo_procedimento = :codigo",
                        ['codigo' => $codigo]
                    );

                    $stmt->execute([
                        ':codigo' => $codigo,
                        ':descricao' => $descricao,
                        ':tp_complexidade' => $tp_complexidade ?: null,
                        ':tp_sexo' => $tp_sexo ?: null,
                        ':qt_maxima_execucao' => $qt_maxima_execucao,
                        ':vl_sh' => $vl_sh,
                        ':vl_sa' => $vl_sa,
                        ':vl_sp' => $vl_sp,
                        ':dt_competencia' => $dt_competencia ?: null
                    ]);

                    if ($existe) {
                        $this->stats['procedimentos']['atualizados']++;
                    } else {
                        $this->stats['procedimentos']['importados']++;
                    }

                    $count++;
                    
                    // Commit a cada lote
                    if ($count % $batchSize === 0) {
                        $this->db->commit();
                        $this->db->beginTransaction();
                    }
                } catch (PDOException $e) {
                    $this->stats['procedimentos']['erros']++;
                    $this->errors[] = "Erro ao importar Procedimento linha {$count}: " . $e->getMessage();
                    continue;
                }
            }

            $this->db->commit();
            fclose($handle);
            
            return [
                'sucesso' => true,
                'importados' => $this->stats['procedimentos']['importados'],
                'atualizados' => $this->stats['procedimentos']['atualizados'],
                'erros' => $this->stats['procedimentos']['erros']
            ];
        } catch (\Exception $e) {
            $this->db->rollback();
            fclose($handle);
            throw $e;
        }
    }

    /**
     * Importa relacionamentos Procedimento x CID do arquivo rl_procedimento_cid.txt
     * 
     * Layout oficial SIGTAP:
     * - Posição 1-10: CO_PROCEDIMENTO (10 caracteres)
     * - Posição 11-14: CO_CID (4 caracteres)
     * - Posição 15: ST_PRINCIPAL (1 caractere) - S ou N
     * - Posição 16-21: DT_COMPETENCIA (6 caracteres - formato YYYYMM)
     */
    public function importarRelacionamentos($arquivo)
    {
        if (!file_exists($arquivo)) {
            throw new \Exception("Arquivo não encontrado: {$arquivo}");
        }

        $handle = fopen($arquivo, 'r');
        if (!$handle) {
            throw new \Exception("Não foi possível abrir o arquivo: {$arquivo}");
        }

        $sql = "INSERT INTO rl_procedimento_cid (co_procedimento, co_cid, st_principal, dt_competencia)
                VALUES (:co_procedimento, :co_cid, :st_principal, :dt_competencia)
                ON DUPLICATE KEY UPDATE
                    st_principal = VALUES(st_principal),
                    dt_competencia = VALUES(dt_competencia)";

        $stmt = $this->db->getConnection()->prepare($sql);
        
        $this->db->beginTransaction();
        $count = 0;
        $batchSize = 5000;

        try {
            while (($line = fgets($handle)) !== false) {
                $line = rtrim($line, "\r\n");
                
                // Validação mínima: linha deve ter pelo menos 14 caracteres
                if (strlen($line) < 14) {
                    continue;
                }

                try {
                    // Layout: 10 chars procedimento + 4 chars CID + 1 char principal + 6 chars competência = 21 chars
                    // Exemplo: "0201010038C73 S202511"
                    $co_procedimento = trim(substr($line, 0, 10));
                    $co_cid = trim(substr($line, 10, 4));
                    
                    if (empty($co_procedimento) || empty($co_cid)) {
                        continue;
                    }

                    // Layout oficial SIGTAP: CO_PROCEDIMENTO (1-10), CO_CID (11-14), ST_PRINCIPAL (15), DT_COMPETENCIA (16-21)
                    $tamanhoLinha = strlen($line);
                    if ($tamanhoLinha < 15) {
                        continue; // Linha muito curta
                    }
                    
                    $st_principal = trim(substr($line, 14, 1));
                    // Normaliza: S ou espaço vira 'S', caso contrário 'N'
                    if (strtoupper($st_principal) === 'S' || $st_principal === ' ') {
                        $st_principal = 'S';
                    } else {
                        $st_principal = 'N';
                    }
                    
                    // DT_COMPETENCIA: posição 16-21 (formato YYYYMM)
                    $dt_competencia = $tamanhoLinha >= 21 ? trim(substr($line, 15, 6)) : null;

                    // Validação: verifica se procedimento e CID existem
                    $procExiste = $this->db->fetchOne(
                        "SELECT id FROM procedimentos WHERE codigo_procedimento = :codigo",
                        ['codigo' => $co_procedimento]
                    );

                    $cidExiste = $this->db->fetchOne(
                        "SELECT id FROM cids WHERE codigo = :codigo",
                        ['codigo' => $co_cid]
                    );

                    if (!$procExiste || !$cidExiste) {
                        // Ignora relacionamentos com FK inválida (dados inconsistentes do SIGTAP)
                        continue;
                    }

                    $stmt->execute([
                        ':co_procedimento' => $co_procedimento,
                        ':co_cid' => $co_cid,
                        ':st_principal' => $st_principal,
                        ':dt_competencia' => $dt_competencia ?: null
                    ]);

                    $this->stats['relacionamentos']['importados']++;
                    $count++;
                    
                    // Commit a cada lote
                    if ($count % $batchSize === 0) {
                        $this->db->commit();
                        $this->db->beginTransaction();
                    }
                } catch (PDOException $e) {
                    $this->stats['relacionamentos']['erros']++;
                    // Ignora erros de FK (dados inconsistentes)
                    if (strpos($e->getMessage(), 'foreign key') === false) {
                        $this->errors[] = "Erro ao importar Relacionamento linha {$count}: " . $e->getMessage();
                    }
                    continue;
                }
            }

            $this->db->commit();
            fclose($handle);
            
            return [
                'sucesso' => true,
                'importados' => $this->stats['relacionamentos']['importados'],
                'erros' => $this->stats['relacionamentos']['erros']
            ];
        } catch (\Exception $e) {
            $this->db->rollback();
            fclose($handle);
            throw $e;
        }
    }

    /**
     * Retorna estatísticas da última importação
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * Retorna erros da última importação
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Limpa estatísticas e erros
     */
    public function reset()
    {
        $this->stats = [
            'cids' => ['importados' => 0, 'atualizados' => 0, 'erros' => 0],
            'procedimentos' => ['importados' => 0, 'atualizados' => 0, 'erros' => 0],
            'relacionamentos' => ['importados' => 0, 'erros' => 0]
        ];
        $this->errors = [];
    }
}

