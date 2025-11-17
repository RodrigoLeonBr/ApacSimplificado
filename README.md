# Sistema de Emissão de APAC

Sistema web para gerenciamento e emissão de Autorizações de Procedimentos de Alta Complexidade (APAC) do SUS.

## 🚀 Como Usar

### Acesso ao Sistema
1. Acesse a aplicação pela URL do Replit
2. Faça login com as credenciais padrão:
   - **Email**: `admin@apac.com`
   - **Senha**: `admin123`

### Fluxo de Trabalho

#### 1️⃣ Cadastrar Faixa de APAC
- Menu: **Faixas de APAC** → **Nova Faixa**
- Digite o número inicial (13 dígitos): `3525807281110`
- Digite o número final (13 dígitos): `3525807281120`
- Clique em **Cadastrar Faixa**

#### 2️⃣ Emitir APAC
- Menu: **Emitir APAC** (botão verde)
- Selecione uma faixa disponível
- Clique em **Emitir APAC**
- O sistema gera automaticamente o número de 14 dígitos (13 + DV)

#### 3️⃣ Visualizar APACs
- Menu: **APACs Emitidas**
- Veja todas as APACs com número completo, data, usuário e status
- Marque como impressa quando necessário

## 📋 Funcionalidades

- ✅ Autenticação segura com bcrypt
- ✅ Dashboard com estatísticas em tempo real
- ✅ CRUD completo de Faixas de APAC
- ✅ Emissão individual de APAC com DV automático
- ✅ Controle de status (Disponível/Em Uso/Esgotada)
- ✅ Sistema de logs e auditoria
- ✅ Interface responsiva (Tailwind CSS)

## 🔢 Algoritmo de Dígito Verificador

**Sequência Cíclica**: `"78900123456"` (11 caracteres)

**Cálculo**:
1. Pega os 2 últimos dígitos do número de 13 dígitos
2. Calcula: `últimos_2_dígitos % 11`
3. Retorna o caractere na posição do índice

**Exemplo**:
- Número: `3525807281111` → Últimos 2 dig: `11`
- Índice: `11 % 11 = 0`
- DV: `sequencia[0] = '7'`
- APAC completa: `35258072811117`

## 🛠️ Tecnologias

- **Backend**: PHP 8.3 puro (sem frameworks)
- **Banco de Dados**: PostgreSQL com PDO
- **Frontend**: HTML5 + Tailwind CSS + Alpine.js
- **Arquitetura**: MVC simplificado
- **Servidor**: PHP Built-in Server (porta 5000)

## 📁 Estrutura do Projeto

```
sistema-apac/
├── src/
│   ├── Controllers/    # Lógica de controle
│   ├── Models/        # Operações CRUD
│   ├── Services/      # Lógica de negócio
│   ├── Database/      # Conexão PDO
│   └── Utils/         # Utilitários
├── views/             # Camada de apresentação
├── public/            # Ponto de entrada
├── config/            # Configurações
└── database/          # Schema SQL
```

## 🔒 Segurança

- ✅ Prepared statements (SQL Injection)
- ✅ Password hashing (bcrypt)
- ✅ XSS prevention (htmlspecialchars)
- ✅ Proteção de rotas (middleware)
- ✅ Regeneração de sessão após login

## 📊 Banco de Dados

5 tabelas principais:
- `usuarios`: Usuários do sistema
- `faixas`: Faixas de números APAC (13 dígitos)
- `apacs`: APACs emitidas (14 dígitos)
- `logs`: Auditoria de ações
- `prestadores`: Para expansão futura

## 🎯 Próximas Funcionalidades

- Emissão em lote de APACs
- Exportação em formato TXT
- Relatórios por período
- Dashboard com gráficos
- Gestão de prestadores
- Sistema de permissões por role

## 📝 Observações

- Sistema adaptado para PostgreSQL (Replit)
- Todas funcionalidades MVP implementadas
- Código modular e manutenível
- Testes do algoritmo DV: 32/32 passou ✅

---

**Versão**: 1.0.0 | **Status**: Funcional e pronto para uso
