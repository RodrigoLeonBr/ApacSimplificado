-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 24/11/2025 às 15:17
-- Versão do servidor: 5.7.23-23
-- Versão do PHP: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `radlc849_apac`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `apacs`
--

CREATE TABLE `apacs` (
  `id` bigint(20) NOT NULL,
  `numero_apac` varchar(14) COLLATE utf8_unicode_ci NOT NULL,
  `digito_verificador` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `faixa_id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `impresso` tinyint(1) DEFAULT '0',
  `criada_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizada_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
  `autorizador` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `impressao_sempre` tinyint(1) DEFAULT '0',
  `data_impressao` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `caracteres_atendimento`
--

CREATE TABLE `caracteres_atendimento` (
  `id` bigint(20) NOT NULL,
  `codigo` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `criada_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `caracteres_atendimento`
--

INSERT INTO `caracteres_atendimento` (`id`, `codigo`, `descricao`, `criada_em`) VALUES
(1, '01', 'Eletivo', '2025-11-24 18:10:44'),
(2, '02', 'Urgência', '2025-11-24 18:10:44'),
(3, '03', 'Emergência', '2025-11-24 18:10:44'),
(4, '04', 'Acidente de Trabalho', '2025-11-24 18:10:44'),
(5, '05', 'Acidente de Trânsito', '2025-11-24 18:10:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cids`
--

CREATE TABLE `cids` (
  `id` bigint(20) NOT NULL,
  `codigo` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8_unicode_ci NOT NULL,
  `criada_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `cids`
--

INSERT INTO `cids` (`id`, `codigo`, `descricao`, `criada_em`) VALUES
(1, 'A00', 'Cólera', '2025-11-24 18:10:10'),
(2, 'B20', 'Doença pelo vírus da imunodeficiência humana [HIV], resultando em doenças infecciosas e parasitárias', '2025-11-24 18:10:10'),
(3, 'C00', 'Neoplasia maligna do lábio', '2025-11-24 18:10:10'),
(4, 'I10', 'Hipertensão essencial (primária)', '2025-11-24 18:10:10'),
(5, 'J45', 'Asma', '2025-11-24 18:10:10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `estabelecimentos`
--

CREATE TABLE `estabelecimentos` (
  `id` bigint(20) NOT NULL,
  `codigo` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tipo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logradouro` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numero` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `municipio` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cep` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `estabelecimentos`
--

INSERT INTO `estabelecimentos` (`id`, `codigo`, `nome`, `tipo`, `logradouro`, `numero`, `bairro`, `municipio`, `cep`, `created_at`, `updated_at`) VALUES
(1, '1234567', 'Hospital Municipal Central', 'Hospital Geral', 'Rua Principal', '1000', 'Centro', 'Cidade Exemplo', '12345000', '2025-11-24 18:10:27', '2025-11-24 18:10:27'),
(2, '7654321', 'Clínica Saúde da Família', 'Clínica', 'Av. Secundária', '500', 'Bairro Novo', 'Cidade Exemplo', '12346000', '2025-11-24 18:10:27', '2025-11-24 18:10:27'),
(3, '9876543', 'Laboratório Análises Clínicas', 'Laboratório', 'Rua do Laboratório', '123', 'Vila Velha', 'Cidade Exemplo', '12347000', '2025-11-24 18:10:27', '2025-11-24 18:10:27'),
(4, '1122334', 'UPA 24h', 'Pronto Atendimento', 'Av. de Urgência', '789', 'Zona Sul', 'Cidade Exemplo', '12348000', '2025-11-24 18:10:27', '2025-11-24 18:10:27'),
(5, '5566778', 'Centro de Especialidades', 'Ambulatório', 'Praça da Saúde', '45', 'Centro', 'Cidade Exemplo', '12349000', '2025-11-24 18:10:27', '2025-11-24 18:10:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `faixas`
--

CREATE TABLE `faixas` (
  `id` bigint(20) NOT NULL,
  `numero_inicial` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  `numero_final` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  `quantidade` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `utilizados` int(11) DEFAULT '0',
  `status` enum('disponivel','em_uso','esgotada') COLLATE utf8_unicode_ci DEFAULT 'disponivel',
  `criada_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizada_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `laudos`
--

CREATE TABLE `laudos` (
  `id` bigint(20) NOT NULL,
  `paciente_id` bigint(20) NOT NULL,
  `numero_prontuario` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `numero_laudo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `data_laudo` date NOT NULL,
  `cid_id` bigint(20) NOT NULL,
  `procedimento_solicitado_id` bigint(20) NOT NULL,
  `procedimento_autorizado_id` bigint(20) NOT NULL,
  `estabelecimento_solicitante_id` bigint(20) NOT NULL,
  `estabelecimento_executante_id` bigint(20) NOT NULL,
  `profissional_solicitante_id` bigint(20) NOT NULL,
  `carater_atendimento_id` bigint(20) NOT NULL,
  `observacoes` text COLLATE utf8_unicode_ci,
  `status` enum('rascunho','emitido','autorizado','cancelado') COLLATE utf8_unicode_ci DEFAULT 'rascunho',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` bigint(20) NOT NULL,
  `usuario_id` bigint(20) DEFAULT NULL,
  `acao` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tabela` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registro_id` bigint(20) DEFAULT NULL,
  `detalhes` text COLLATE utf8_unicode_ci,
  `criada_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pacientes`
--

CREATE TABLE `pacientes` (
  `id` bigint(20) NOT NULL,
  `cns` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cpf` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data_nascimento` date NOT NULL,
  `nome_mae` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sexo` enum('M','F','O') COLLATE utf8_unicode_ci NOT NULL,
  `raca_cor` enum('Branca','Preta','Parda','Amarela','Indigena','Sem Informacao') COLLATE utf8_unicode_ci NOT NULL,
  `logradouro` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `numero` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `complemento` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cep` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `municipio` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nome_responsavel` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `procedimentos`
--

CREATE TABLE `procedimentos` (
  `id` bigint(20) NOT NULL,
  `codigo_procedimento` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8_unicode_ci NOT NULL,
  `tabela_sus` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `criada_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `procedimentos`
--

INSERT INTO `procedimentos` (`id`, `codigo_procedimento`, `descricao`, `tabela_sus`, `criada_em`) VALUES
(1, '0301010070', 'Consulta médica em atenção básica', 'SIA/SUS', '2025-11-24 18:10:18'),
(2, '0401010020', 'Hemodiálise', 'SIA/SUS', '2025-11-24 18:10:18'),
(3, '0204010010', 'Radiografia de tórax', 'SIA/SUS', '2025-11-24 18:10:18'),
(4, '0701010010', 'Transplante de rim', 'SIA/SUS', '2025-11-24 18:10:18'),
(5, '0801010010', 'Quimioterapia', 'SIA/SUS', '2025-11-24 18:10:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissionais`
--

CREATE TABLE `profissionais` (
  `id` bigint(20) NOT NULL,
  `cns` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cpf` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `especialidade` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Despejando dados para a tabela `profissionais`
--

INSERT INTO `profissionais` (`id`, `cns`, `cpf`, `nome`, `especialidade`, `created_at`, `updated_at`) VALUES
(1, '700000000000001', '11122233344', 'Dr. João da Silva', 'Clínico Geral', '2025-11-24 18:10:35', '2025-11-24 18:10:35'),
(2, '700000000000002', '22233344455', 'Dra. Maria Souza', 'Cardiologista', '2025-11-24 18:10:35', '2025-11-24 18:10:35'),
(3, '700000000000003', '33344455566', 'Dr. Pedro Santos', 'Cirurgião', '2025-11-24 18:10:35', '2025-11-24 18:10:35'),
(4, '700000000000004', '44455566677', 'Dra. Ana Costa', 'Pediatra', '2025-11-24 18:10:35', '2025-11-24 18:10:35'),
(5, '700000000000005', '55566677788', 'Dr. Carlos Oliveira', 'Oncologista', '2025-11-24 18:10:35', '2025-11-24 18:10:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `senha_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nome` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idx_estabelecimentos_codigo` (`codigo`),
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
  ADD KEY `idx_laudos_status` (`status`);

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `cids`
--
ALTER TABLE `cids`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `estabelecimentos`
--
ALTER TABLE `estabelecimentos`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `profissionais`
--
ALTER TABLE `profissionais`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  ADD CONSTRAINT `laudos_ibfk_8` FOREIGN KEY (`carater_atendimento_id`) REFERENCES `caracteres_atendimento` (`id`);

--
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
