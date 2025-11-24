# Sistema de Emissão de APAC

## Visão Geral
Sistema web desenvolvido em PHP puro (sem frameworks) para gerenciamento e emissão de Autorizações de Procedimentos de Alta Complexidade (APAC) para o Sistema Único de Saúde (SUS) brasileiro.

## Stack Tecnológico
- **Backend**: PHP 8.3 (puro, sem frameworks)
- **Banco de Dados**: MySQL 5.7 (hospedagem remota) com PDO
- **Frontend**: HTML5 + Tailwind CSS (via CDN) + Alpine.js (via CDN)
- **Arquitetura**: MVC simplificado com separação em camadas
- **Servidor**: PHP Built-in Server (porta 5000)

## Estrutura do Projeto

```
sistema-apac/
├── src/
│   ├── Controllers/       # Controladores da aplicação
│   ├── Models/           # Modelos de dados (CRUD)
│   ├── Services/         # Lógica de negócio
│   ├── Database/         # Conexão PDO com PostgreSQL
│   └── Utils/            # Utilitários (Session, Router, Validation)
├── views/
│   ├── layouts/          # Layouts principais
│   ├── components/       # Componentes reutilizáveis
│   ├── auth/             # Views de autenticação
│   ├── dashboard/        # Dashboard
│   ├── faixa/            # Gerenciamento de faixas
│   └── apac/             # Emissão e listagem de APACs
├── public/
│   ├── index.php         # Ponto de entrada (front controller)
│   ├── css/              # Estilos customizados
│   └── js/               # Scripts customizados
├── config/               # Configurações
├── database/             # Schema SQL
└── router.php            # Definição de rotas
```

## Funcionalidades Implementadas (MVP)

### 1. Sistema de Autenticação
- Login e logout de usuários
- Proteção de rotas com middleware
- Gerenciamento de sessões seguro
- **Credenciais padrão**:
  - Email: `admin@apac.com`
  - Senha: `admin123`

### 2. Dashboard
- Visualização de estatísticas em tempo real
- Total de faixas cadastradas e disponíveis
- APACs emitidas e impressas
- Logs recentes de atividades
- APACs emitidas recentemente

### 3. Gerenciamento de Faixas de APAC
- **CRUD Completo**:
  - Cadastrar nova faixa (número inicial e final de 13 dígitos)
  - Listar todas as faixas com status e percentual de uso
  - Visualizar detalhes de uma faixa específica
  - Excluir faixas (apenas se não tiverem APACs emitidas)
- **Controle de Status**:
  - Disponível: Faixa nunca utilizada
  - Em Uso: Faixa com APACs emitidas mas ainda com números disponíveis
  - Esgotada: Todos os números da faixa foram utilizados
- **Cálculo Automático**: Quantidade de números na faixa calculada automaticamente

### 4. Emissão de APAC
- **Emissão Individual**:
  - Seleção de faixa disponível
  - Geração automática do número de 14 dígitos (13 dígitos + DV)
  - Cálculo automático do Dígito Verificador usando algoritmo cíclico
  - Validação para evitar duplicatas
  - Controle sequencial de emissão dentro da faixa
- **Listagem de APACs**:
  - Visualização de todas as APACs emitidas
  - Informações: número completo, DV, faixa de origem, usuário emissor, data
  - Status de impressão (Pendente/Impressa)
  - Ação para marcar como impressa

### 5. Algoritmo de Dígito Verificador
- **Sequência Cíclica**: "78900123456" (11 caracteres)
- **Lógica**: 
  - Pega os 2 últimos dígitos do número de 13 dígitos
  - Calcula o índice: `últimos_2_dígitos % 11`
  - Retorna o caractere na posição do índice na sequência
- **Validação**: Verifica se o DV fornecido corresponde ao calculado
- **Geração em Lote**: Suporte para gerar múltiplas APACs de uma faixa (futuro)

### 6. Sistema de Logs e Auditoria
- Registro automático de todas as ações importantes:
  - Cadastro, edição e exclusão de faixas
  - Emissão de APACs
  - Marcação de impressão
- Rastreabilidade completa: usuário, data/hora, detalhes da ação
- Visualização no dashboard

## Banco de Dados

### Servidor MySQL Remoto
- **Host**: 192.185.213.221 (hospedagem compartilhada)
- **Database**: radlc849_apac
- **Versão**: MySQL 5.7.23-23
- **Charset**: utf8mb4
- **Credenciais**: Armazenadas como secrets seguros (MYSQL_HOST, MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD)

### Tabelas Implementadas (12 tabelas)

**Módulo Core (MVP):**
1. **usuarios**: Usuários do sistema
2. **faixas**: Faixas de números de APAC (13 dígitos)
3. **apacs**: APACs emitidas (14 dígitos com DV)
4. **logs**: Auditoria de ações do sistema

**Módulo Pacientes e Laudos:**
5. **pacientes**: Dados dos pacientes (CPF, CNS, nome, etc.)
6. **laudos**: Laudos médicos vinculados às APACs
7. **apacs_laudos**: Relacionamento N:N entre APACs e Laudos

**Módulo Cadastros Auxiliares:**
8. **procedimentos**: Procedimentos SUS (tabela SIA/SIH)
9. **cids**: Classificação Internacional de Doenças
10. **estabelecimentos**: Estabelecimentos de saúde (CNES)
11. **profissionais**: Profissionais de saúde (CBO, CNS)
12. **caracteres_atendimento**: Tipos de caractere de atendimento

### Relacionamentos
- `apacs.faixa_id` → `faixas.id` (ON DELETE RESTRICT)
- `apacs.usuario_id` → `usuarios.id` (ON DELETE SET NULL)
- `logs.usuario_id` → `usuarios.id` (ON DELETE SET NULL)
- `laudos.paciente_id` → `pacientes.id` (ON DELETE CASCADE)
- `laudos.procedimento_principal_id` → `procedimentos.id`
- `laudos.cid_principal_id` → `cids.id`
- `apacs_laudos.apac_id` → `apacs.id` (ON DELETE CASCADE)
- `apacs_laudos.laudo_id` → `laudos.id` (ON DELETE CASCADE)

### Índices Otimizados
- Índices em campos de busca frequente
- Índices em chaves estrangeiras
- Índices em campos de data para ordenação

## Como Usar

### Primeiro Acesso
1. Acesse a aplicação pela URL do Replit
2. Faça login com as credenciais padrão:
   - Email: `admin@apac.com`
   - Senha: `admin123`

### Fluxo de Trabalho Típico

#### 1. Cadastrar uma Faixa
1. No menu lateral, clique em "Faixas de APAC"
2. Clique no botão "Nova Faixa"
3. Digite o número inicial (13 dígitos): ex: `3525807281110`
4. Digite o número final (13 dígitos): ex: `3525807281120`
5. Clique em "Cadastrar Faixa"
6. O sistema calcula automaticamente a quantidade de números

#### 2. Emitir uma APAC
1. No menu lateral, clique em "Emitir APAC" (botão verde)
2. Selecione uma faixa disponível no dropdown
3. Clique em "Emitir APAC"
4. O sistema:
   - Encontra o próximo número disponível na faixa
   - Calcula o dígito verificador automaticamente
   - Gera o número completo de 14 dígitos
   - Registra a emissão com data/hora e usuário
   - Atualiza o status da faixa

#### 3. Visualizar APACs Emitidas
1. No menu lateral, clique em "APACs Emitidas"
2. Veja a lista completa de APACs com:
   - Número completo (14 dígitos)
   - Dígito verificador
   - Faixa de origem
   - Usuário que emitiu
   - Data e hora da emissão
   - Status de impressão

#### 4. Marcar APAC como Impressa
1. Na listagem de APACs, localize a APAC desejada
2. Clique no botão "Marcar Impressa"
3. O sistema atualiza o status e registra no log

## Segurança Implementada

- **Autenticação**: Sistema de login com senha criptografada (bcrypt)
- **Proteção de Rotas**: Middleware verifica autenticação antes de acessar páginas protegidas
- **SQL Injection**: Uso de prepared statements em todas as queries
- **XSS Prevention**: Sanitização de outputs com `htmlspecialchars()`
- **CSRF**: Sessões com regeneração de ID após login
- **Validação**: Validação de dados no backend antes de processar

## Performance e Otimização

- **Conexão Singleton**: Uma única conexão PDO reutilizada
- **Índices de Banco**: Otimização de queries frequentes
- **Autoload PSR-4**: Carregamento automático de classes
- **Sem Cache de Views**: Desenvolvimento rápido (para produção, implementar cache)

## Próximas Funcionalidades (Backlog)

### P2 - Funcionalidades Importantes
- [ ] Emissão em lote de múltiplas APACs
- [ ] Exportação de APACs em formato TXT
- [ ] Relatórios detalhados de uso por período
- [ ] Filtros e busca avançada de APACs

### P3 - Melhorias e Expansões
- [ ] Dashboard com gráficos interativos (Chart.js)
- [ ] Gestão de prestadores de serviço
- [ ] Vinculação de APAC com prestador
- [ ] Sistema de permissões por role (admin/user)
- [ ] Exportação em PDF
- [ ] API REST para integração externa

## Observações Técnicas

### Migração PostgreSQL → MySQL (Fase 5)
**Data**: 24/11/2025

O sistema foi originalmente desenvolvido em PostgreSQL (Replit) e posteriormente migrado para MySQL remoto para suportar o conjunto completo de funcionalidades.

**Adaptações de Nomenclatura:**

| Tabela | Campo PostgreSQL | Campo MySQL |
|--------|------------------|-------------|
| usuarios | `password` | `senha_hash` |
| faixas | `inicial_13dig` | `numero_inicial` |
| faixas | `final_13dig` | `numero_final` |
| faixas | - | `total` (novo) |
| faixas | - | `utilizados` (novo) |
| apacs | `numero_14dig` | `numero_apac` |
| apacs | `emitido_por_usuario_id` | `usuario_id` |
| apacs | `data_emissao` | `criada_em` |
| logs | `tabela_afetada` | `tabela` |
| logs | `created_at` | `criada_em` |

**Campos Booleanos:**
- PostgreSQL: Usa tipo `boolean` com valores string `'true'/'false'`
- MySQL: Usa tipo `tinyint(1)` com valores inteiros `1/0`

**Arquivos Adaptados:**
- Models: Usuario.php, Faixa.php, Apac.php, Log.php
- Services: AuthService.php, EmissaoService.php
- Views: Todas as views de faixa, apac e dashboard
- Config: database.php (driver pgsql → mysql)

## Desenvolvimento e Manutenção

### Estrutura de Código
- **Namespaces**: `App\Controllers`, `App\Models`, `App\Services`, etc.
- **Autoload**: PSR-4 implementado manualmente
- **Convenções**: CamelCase para classes, snake_case para banco de dados

### Boas Práticas Aplicadas
- Separação clara de responsabilidades (MVC)
- Código modular e reutilizável
- Validação centralizada
- Logs de auditoria automáticos
- Comentários em código crítico

## Credenciais e Acesso

- **Email**: admin@apac.com
- **Senha**: admin123
- **Role**: admin
- **Permissões**: Acesso total ao sistema

## Versão
- **Atual**: 2.0.0 (MVP migrado para MySQL)
- **Data**: 24/11/2025
- **Status**: Migrado para MySQL remoto e pronto para expansão

## Changelog

### v2.0.0 - 24/11/2025
- ✅ Migração completa de PostgreSQL para MySQL remoto
- ✅ Adaptação de todos os Models para nomenclatura MySQL
- ✅ Adaptação de todos os Services e Views
- ✅ Criação de 12 tabelas no banco MySQL (vs 5 do PostgreSQL)
- ✅ Dados iniciais: usuário admin, CIDs, procedimentos, estabelecimentos, profissionais
- ✅ Testes de conectividade e funcionalidades básicas bem-sucedidos

### v1.0.0 - 17/11/2025
- ✅ MVP funcional em PostgreSQL
- ✅ Sistema de autenticação completo
- ✅ CRUD de faixas de APAC
- ✅ Emissão individual de APACs com DV
- ✅ Dashboard com estatísticas
- ✅ Sistema de logs e auditoria
