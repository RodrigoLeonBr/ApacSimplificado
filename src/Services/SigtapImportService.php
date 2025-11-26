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
     * Formato SUS: 12 dígitos, últimos 2 são centavos
     * Exemplo: 000000008415 = 8415.71 (84.1571 / 100)
     * 
     * @param string $valor Valor no formato SUS (12 dígitos)
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
     * Layout estimado:
     * - Posição 1-4: Código CID (4 caracteres)
     * - Posição 5-104: Descrição (100 caracteres)
     * - Posição 105: tp_agravo (1 caractere)
     * - Posição 106: tp_sexo (1 caractere)
     * - Posição 107: tp_estadio (1 caractere)
     * - Posição 108-111: vl_campos_irradiados (4 caracteres numéricos)
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
                    $codigo = $this->extrairCampo($line, 1, 4);
                    if (empty($codigo)) {
                        continue;
                    }

                    $descricao = $this->extrairCampo($line, 5, 100);
                    $tp_agravo = $this->extrairCampo($line, 105, 1);
                    $tp_sexo = $this->extrairCampo($line, 106, 1);
                    $tp_estadio = $this->extrairCampo($line, 107, 1);
                    $vl_campos_irradiados = (int)$this->extrairCampo($line, 108, 4);

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
     * Layout estimado:
     * - Posição 1-10: Código Procedimento (10 caracteres)
     * - Posição 11-260: Descrição (250 caracteres)
     * - Posição 261: tp_complexidade (1 caractere)
     * - Posição 262: tp_sexo (1 caractere)
     * - Posição 263-266: qt_maxima_execucao (4 caracteres)
     * - Posição 283-294: vl_sh (12 caracteres monetários)
     * - Posição 295-306: vl_sa (12 caracteres monetários)
     * - Posição 307-318: vl_sp (12 caracteres monetários)
     * - Posição 331-336: dt_competencia (6 caracteres)
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
                    $codigo = $this->extrairCampo($line, 1, 10);
                    if (empty($codigo)) {
                        continue;
                    }

                    $descricao = $this->extrairCampo($line, 11, 250);
                    $tp_complexidade = $this->extrairCampo($line, 261, 1);
                    $tp_sexo = $this->extrairCampo($line, 262, 1);
                    $qt_maxima_execucao = (int)$this->extrairCampo($line, 263, 4);
                    $vl_sh = $this->converterValorMonetario($this->extrairCampo($line, 283, 12));
                    $vl_sa = $this->converterValorMonetario($this->extrairCampo($line, 295, 12));
                    $vl_sp = $this->converterValorMonetario($this->extrairCampo($line, 307, 12));
                    $dt_competencia = $this->extrairCampo($line, 331, 6);

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
     * Layout estimado:
     * - Posição 1-10: co_procedimento (10 caracteres)
     * - Posição 11-14: co_cid (4 caracteres)
     * - Posição 15: st_principal (1 caractere) - S ou espaço
     * - Posição 16-21: dt_competencia (6 caracteres)
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
                    $co_procedimento = $this->extrairCampo($line, 1, 10);
                    $co_cid = $this->extrairCampo($line, 11, 4);
                    
                    if (empty($co_procedimento) || empty($co_cid)) {
                        continue;
                    }

                    $st_principal = $this->extrairCampo($line, 15, 1);
                    $dt_competencia = $this->extrairCampo($line, 16, 6);

                    // Normaliza st_principal: S ou espaço vira 'S', caso contrário 'N'
                    if (strtoupper($st_principal) === 'S') {
                        $st_principal = 'S';
                    } else {
                        $st_principal = 'N';
                    }

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

