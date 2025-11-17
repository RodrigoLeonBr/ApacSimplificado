-- Sistema de Emissão de APAC
-- Schema para PostgreSQL

-- Tabela de usuários do sistema
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices para tabela usuarios
CREATE INDEX IF NOT EXISTS idx_usuarios_email ON usuarios(email);
CREATE INDEX IF NOT EXISTS idx_usuarios_role ON usuarios(role);

-- Tabela de faixas de números de APAC
CREATE TABLE IF NOT EXISTS faixas (
    id SERIAL PRIMARY KEY,
    inicial_13dig VARCHAR(13) UNIQUE NOT NULL,
    final_13dig VARCHAR(13) UNIQUE NOT NULL,
    quantidade INTEGER NOT NULL,
    status VARCHAR(50) DEFAULT 'disponivel',
    criada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices para tabela faixas
CREATE INDEX IF NOT EXISTS idx_faixas_inicial ON faixas(inicial_13dig);
CREATE INDEX IF NOT EXISTS idx_faixas_final ON faixas(final_13dig);
CREATE INDEX IF NOT EXISTS idx_faixas_status ON faixas(status);

-- Tabela de APACs emitidas
CREATE TABLE IF NOT EXISTS apacs (
    id SERIAL PRIMARY KEY,
    numero_14dig VARCHAR(14) UNIQUE NOT NULL,
    digito_verificador CHAR(1) NOT NULL,
    faixa_id INTEGER NOT NULL,
    impresso BOOLEAN DEFAULT FALSE,
    data_emissao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    emitido_por_usuario_id INTEGER,
    FOREIGN KEY (faixa_id) REFERENCES faixas(id) ON DELETE RESTRICT,
    FOREIGN KEY (emitido_por_usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Índices para tabela apacs
CREATE INDEX IF NOT EXISTS idx_apacs_numero ON apacs(numero_14dig);
CREATE INDEX IF NOT EXISTS idx_apacs_faixa_id ON apacs(faixa_id);
CREATE INDEX IF NOT EXISTS idx_apacs_data_emissao ON apacs(data_emissao);
CREATE INDEX IF NOT EXISTS idx_apacs_impresso ON apacs(impresso);

-- Tabela de logs de auditoria
CREATE TABLE IF NOT EXISTS logs (
    id SERIAL PRIMARY KEY,
    acao VARCHAR(255) NOT NULL,
    usuario_id INTEGER,
    tabela_afetada VARCHAR(100),
    registro_id INTEGER,
    detalhes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Índices para tabela logs
CREATE INDEX IF NOT EXISTS idx_logs_usuario_id ON logs(usuario_id);
CREATE INDEX IF NOT EXISTS idx_logs_created_at ON logs(created_at);
CREATE INDEX IF NOT EXISTS idx_logs_tabela_afetada ON logs(tabela_afetada);

-- Tabela de prestadores (para expansão futura)
CREATE TABLE IF NOT EXISTS prestadores (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cnpj VARCHAR(18) UNIQUE,
    endereco VARCHAR(255),
    telefone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices para tabela prestadores
CREATE INDEX IF NOT EXISTS idx_prestadores_cnpj ON prestadores(cnpj);

-- Trigger para atualizar updated_at automaticamente (usuarios e prestadores)
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Trigger para atualizar atualizada_em (faixas)
CREATE OR REPLACE FUNCTION update_atualizada_em_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.atualizada_em = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

DROP TRIGGER IF EXISTS update_usuarios_updated_at ON usuarios;
CREATE TRIGGER update_usuarios_updated_at BEFORE UPDATE ON usuarios
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_faixas_atualizada_em ON faixas;
CREATE TRIGGER update_faixas_atualizada_em BEFORE UPDATE ON faixas
    FOR EACH ROW EXECUTE FUNCTION update_atualizada_em_column();

DROP TRIGGER IF EXISTS update_prestadores_updated_at ON prestadores;
CREATE TRIGGER update_prestadores_updated_at BEFORE UPDATE ON prestadores
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Inserir usuário admin padrão (senha: admin123)
INSERT INTO usuarios (email, password, nome, role, ativo)
VALUES ('admin@apac.com', '$2y$10$7npeS2HCEomoFaF8MZQAt..p8gbKZJakM8IGyRd12.e01rxZkC4se', 'Administrador', 'admin', TRUE)
ON CONFLICT (email) DO NOTHING;
