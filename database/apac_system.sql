-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/11/2025 às 17:00
-- Versão do servidor: 10.4.32-MariaDB-log
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `apac_system`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `apacs`
--

CREATE TABLE `apacs` (
  `id` bigint(20) NOT NULL,
  `numero_apac` varchar(14) NOT NULL,
  `digito_verificador` char(1) NOT NULL,
  `faixa_id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `impresso` tinyint(1) DEFAULT 0,
  `criada_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizada_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `apacs_laudos`
--

CREATE TABLE `apacs_laudos` (
  `id` bigint(20) NOT NULL,
  `apac_id` bigint(20) NOT NULL,
  `laudo_id` bigint(20) NOT NULL,
  `data_autorizacao` date DEFAULT NULL,
  `data_validade_inicio` date DEFAULT NULL,
  `data_validade_fim` date DEFAULT NULL,
  `autorizador` varchar(255) DEFAULT NULL,
  `impressao_sempre` tinyint(1) DEFAULT 0,
  `data_impressao` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `caracteres_atendimento`
--

CREATE TABLE `caracteres_atendimento` (
  `id` bigint(20) NOT NULL,
  `codigo` varchar(10) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `criada_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cids`
--

CREATE TABLE `cids` (
  `id` bigint(20) NOT NULL,
  `codigo` varchar(4) DEFAULT NULL,
  `descricao` mediumtext NOT NULL,
  `criada_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `tp_agravo` char(1) DEFAULT NULL,
  `tp_sexo` char(1) DEFAULT NULL COMMENT 'M=Masc, F=Fem, I=Indif',
  `tp_estadio` char(1) DEFAULT NULL,
  `vl_campos_irradiados` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estabelecimentos`
--

CREATE TABLE `estabelecimentos` (
  `id` bigint(20) NOT NULL,
  `cnes` varchar(7) DEFAULT NULL,
  `cnpj` varchar(14) DEFAULT NULL,
  `razao_social` varchar(255) DEFAULT NULL,
  `nome_fantasia` varchar(255) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `logradouro` varchar(255) DEFAULT NULL,
  `numero` varchar(50) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(255) DEFAULT NULL,
  `municipio` varchar(255) DEFAULT NULL,
  `uf` char(2) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `cep` varchar(8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `faixas`
--

CREATE TABLE `faixas` (
  `id` bigint(20) NOT NULL,
  `numero_inicial` varchar(13) NOT NULL,
  `numero_final` varchar(13) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `utilizados` int(11) DEFAULT 0,
  `status` enum('disponivel','em_uso','esgotada') DEFAULT 'disponivel',
  `criada_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizada_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `laudos`
--

CREATE TABLE `laudos` (
  `id` bigint(20) NOT NULL,
  `paciente_id` bigint(20) NOT NULL,
  `numero_prontuario` varchar(50) NOT NULL,
  `numero_laudo` varchar(50) NOT NULL,
  `data_laudo` date NOT NULL,
  `cid_id` bigint(20) NOT NULL,
  `procedimento_solicitado_id` bigint(20) NOT NULL,
  `procedimento_autorizado_id` bigint(20) NOT NULL,
  `estabelecimento_solicitante_id` bigint(20) NOT NULL,
  `estabelecimento_executante_id` bigint(20) NOT NULL,
  `profissional_solicitante_id` bigint(20) NOT NULL,
  `carater_atendimento_id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `status` enum('rascunho','emitido','autorizado','cancelado') DEFAULT 'rascunho',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) DEFAULT NULL,
  `acao` varchar(255) NOT NULL,
  `tabela` varchar(255) DEFAULT NULL,
  `registro_id` bigint(20) DEFAULT NULL,
  `detalhes` text DEFAULT NULL,
  `criada_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pacientes`
--

CREATE TABLE `pacientes` (
  `id` bigint(20) NOT NULL,
  `cns` varchar(15) DEFAULT NULL,
  `cpf` varchar(11) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `data_nascimento` date NOT NULL,
  `nome_mae` varchar(255) NOT NULL,
  `sexo` enum('M','F','O') NOT NULL,
  `raca_cor` enum('Branca','Preta','Parda','Amarela','Indigena','Sem Informacao') NOT NULL,
  `logradouro` varchar(255) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `bairro` varchar(255) NOT NULL,
  `cep` varchar(8) NOT NULL,
  `municipio` varchar(255) NOT NULL,
  `nome_responsavel` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `procedimentos`
--

CREATE TABLE `procedimentos` (
  `id` bigint(20) NOT NULL,
  `codigo_procedimento` varchar(10) NOT NULL,
  `descricao` mediumtext NOT NULL,
  `tabela_sus` varchar(50) DEFAULT NULL,
  `criada_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `vl_sa` decimal(12,2) DEFAULT 0.00 COMMENT 'Valor Ambulatorial (Pago na APAC)',
  `vl_sh` decimal(12,2) DEFAULT 0.00 COMMENT 'Valor Hospitalar',
  `vl_sp` decimal(12,2) DEFAULT 0.00 COMMENT 'Valor Profissional',
  `tp_complexidade` char(1) DEFAULT NULL COMMENT 'A=Atenção Básica, M=Média, A=Alta',
  `tp_sexo` char(1) DEFAULT NULL COMMENT 'M=Masc, F=Fem, I=Indif',
  `qt_maxima_execucao` int(11) DEFAULT 1,
  `vl_idade_minima` int(11) DEFAULT 0,
  `vl_idade_maxima` int(11) DEFAULT 999,
  `dt_competencia` char(6) DEFAULT NULL COMMENT 'Mês de vigência ex: 202511'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissionais`
--

CREATE TABLE `profissionais` (
  `id` bigint(20) NOT NULL,
  `cns` varchar(15) DEFAULT NULL,
  `cpf` varchar(11) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `especialidade` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `rl_procedimento_cid`
--

CREATE TABLE `rl_procedimento_cid` (
  `co_procedimento` varchar(10) NOT NULL,
  `co_cid` varchar(4) NOT NULL,
  `st_principal` char(1) DEFAULT 'N' COMMENT 'S=Principal, N=Secundário',
  `dt_competencia` char(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `apacs`
--
ALTER TABLE `apacs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_apac` (`numero_apac`),
  ADD KEY `idx_apacs_faixa_id` (`faixa_id`),
  ADD KEY `idx_apacs_usuario_id` (`usuario_id`),
  ADD KEY `idx_apacs_impresso` (`impresso`);

--
-- Índices de tabela `apacs_laudos`
--
ALTER TABLE `apacs_laudos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `apac_id` (`apac_id`),
  ADD UNIQUE KEY `laudo_id` (`laudo_id`),
  ADD KEY `idx_apacs_laudos_apac_id` (`apac_id`),
  ADD KEY `idx_apacs_laudos_laudo_id` (`laudo_id`);

--
-- Índices de tabela `caracteres_atendimento`
--
ALTER TABLE `caracteres_atendimento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idx_caracteres_atendimento_codigo` (`codigo`);

--
-- Índices de tabela `cids`
--
ALTER TABLE `cids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idx_cids_codigo` (`codigo`),
  ADD KEY `idx_cids_descricao` (`descricao`(255));

--
-- Índices de tabela `estabelecimentos`
--
ALTER TABLE `estabelecimentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`cnes`),
  ADD KEY `idx_estabelecimentos_codigo` (`cnes`),
  ADD KEY `idx_estabelecimentos_nome` (`nome`),
  ADD KEY `idx_estabelecimentos_municipio` (`municipio`);

--
-- Índices de tabela `faixas`
--
ALTER TABLE `faixas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_inicial` (`numero_inicial`,`numero_final`),
  ADD KEY `idx_faixas_numero_inicial` (`numero_inicial`),
  ADD KEY `idx_faixas_numero_final` (`numero_final`),
  ADD KEY `idx_faixas_status` (`status`);

--
-- Índices de tabela `laudos`
--
ALTER TABLE `laudos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_laudos_paciente_id` (`paciente_id`),
  ADD KEY `idx_laudos_cid_id` (`cid_id`),
  ADD KEY `idx_laudos_procedimento_solicitado_id` (`procedimento_solicitado_id`),
  ADD KEY `idx_laudos_procedimento_autorizado_id` (`procedimento_autorizado_id`),
  ADD KEY `idx_laudos_estabelecimento_solicitante_id` (`estabelecimento_solicitante_id`),
  ADD KEY `idx_laudos_estabelecimento_executante_id` (`estabelecimento_executante_id`),
  ADD KEY `idx_laudos_profissional_solicitante_id` (`profissional_solicitante_id`),
  ADD KEY `idx_laudos_carater_atendimento_id` (`carater_atendimento_id`),
  ADD KEY `idx_laudos_numero_prontuario` (`numero_prontuario`),
  ADD KEY `idx_laudos_numero_laudo` (`numero_laudo`),
  ADD KEY `idx_laudos_status` (`status`),
  ADD KEY `idx_laudos_usuario_id` (`usuario_id`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_logs_usuario_id` (`usuario_id`),
  ADD KEY `idx_logs_acao` (`acao`),
  ADD KEY `idx_logs_tabela_registro` (`tabela`,`registro_id`);

--
-- Índices de tabela `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cns` (`cns`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_pacientes_cns` (`cns`),
  ADD KEY `idx_pacientes_cpf` (`cpf`),
  ADD KEY `idx_pacientes_nome` (`nome`),
  ADD KEY `idx_pacientes_municipio` (`municipio`);

--
-- Índices de tabela `procedimentos`
--
ALTER TABLE `procedimentos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_procedimento` (`codigo_procedimento`),
  ADD KEY `idx_procedimentos_codigo` (`codigo_procedimento`),
  ADD KEY `idx_procedimentos_descricao` (`descricao`(255));

--
-- Índices de tabela `profissionais`
--
ALTER TABLE `profissionais`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cns` (`cns`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `idx_profissionais_cns` (`cns`),
  ADD KEY `idx_profissionais_cpf` (`cpf`),
  ADD KEY `idx_profissionais_nome` (`nome`);

--
-- Índices de tabela `rl_procedimento_cid`
--
ALTER TABLE `rl_procedimento_cid`
  ADD PRIMARY KEY (`co_procedimento`,`co_cid`),
  ADD KEY `idx_busca_cid` (`co_cid`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_usuarios_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `apacs`
--
ALTER TABLE `apacs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `apacs_laudos`
--
ALTER TABLE `apacs_laudos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `caracteres_atendimento`
--
ALTER TABLE `caracteres_atendimento`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `cids`
--
ALTER TABLE `cids`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estabelecimentos`
--
ALTER TABLE `estabelecimentos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `faixas`
--
ALTER TABLE `faixas`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `laudos`
--
ALTER TABLE `laudos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `procedimentos`
--
ALTER TABLE `procedimentos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `profissionais`
--
ALTER TABLE `profissionais`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `apacs`
--
ALTER TABLE `apacs`
  ADD CONSTRAINT `apacs_ibfk_1` FOREIGN KEY (`faixa_id`) REFERENCES `faixas` (`id`),
  ADD CONSTRAINT `apacs_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `apacs_laudos`
--
ALTER TABLE `apacs_laudos`
  ADD CONSTRAINT `apacs_laudos_ibfk_1` FOREIGN KEY (`apac_id`) REFERENCES `apacs` (`id`),
  ADD CONSTRAINT `apacs_laudos_ibfk_2` FOREIGN KEY (`laudo_id`) REFERENCES `laudos` (`id`);

--
-- Restrições para tabelas `laudos`
--
ALTER TABLE `laudos`
  ADD CONSTRAINT `laudos_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  ADD CONSTRAINT `laudos_ibfk_2` FOREIGN KEY (`cid_id`) REFERENCES `cids` (`id`),
  ADD CONSTRAINT `laudos_ibfk_3` FOREIGN KEY (`procedimento_solicitado_id`) REFERENCES `procedimentos` (`id`),
  ADD CONSTRAINT `laudos_ibfk_4` FOREIGN KEY (`procedimento_autorizado_id`) REFERENCES `procedimentos` (`id`),
  ADD CONSTRAINT `laudos_ibfk_5` FOREIGN KEY (`estabelecimento_solicitante_id`) REFERENCES `estabelecimentos` (`id`),
  ADD CONSTRAINT `laudos_ibfk_6` FOREIGN KEY (`estabelecimento_executante_id`) REFERENCES `estabelecimentos` (`id`),
  ADD CONSTRAINT `laudos_ibfk_7` FOREIGN KEY (`profissional_solicitante_id`) REFERENCES `profissionais` (`id`),
  ADD CONSTRAINT `laudos_ibfk_8` FOREIGN KEY (`carater_atendimento_id`) REFERENCES `caracteres_atendimento` (`id`),
  ADD CONSTRAINT `laudos_ibfk_9` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
