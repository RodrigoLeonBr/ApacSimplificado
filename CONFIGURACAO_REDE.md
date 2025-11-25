# Configuração de Acesso via Rede Local - ApacSimplificado

## Objetivo
Permitir que o sistema ApacSimplificado seja acessível por outros computadores na rede local através de `http://192.168.5.130/ApacSimplificado/`.

## Informações do Servidor

- **IP do Servidor:** 192.168.5.130
- **URL de Acesso:** http://192.168.5.130/ApacSimplificado/
- **Diretório do Projeto:** C:\xampp\htdocs\ApacSimplificado
- **Porta HTTP:** 80
- **Porta MySQL:** 3306

## 1. Modificações no Código (Já Implementadas)

### Arquivos Modificados:
- ✅ `config/constants.php` - Adicionada detecção automática de BASE_URL
- ✅ `config/app.php` - Adicionado base_url na configuração
- ✅ `src/Utils/Router.php` - Suporte para subdiretório implementado
- ✅ `src/Utils/UrlHelper.php` - Helper criado para geração de URLs
- ✅ `public/.htaccess` - Configurado para funcionar em subdiretório
- ✅ `.htaccess` (raiz) - Criado para redirecionar para public/

### Funcionalidades Implementadas:
- Detecção automática do base path (`/ApacSimplificado` ou raiz)
- Router ajusta REQUEST_URI removendo base path antes de fazer match
- Helper de URLs para facilitar geração de links relativos
- Compatibilidade mantida com acesso local e via rede

## 2. Configurações do XAMPP/Apache

### 2.1 Verificar httpd.conf

**Arquivo:** `C:\xampp\apache\conf\httpd.conf`

Verificar e garantir as seguintes configurações:

```apache
# DocumentRoot deve apontar para htdocs
DocumentRoot "C:/xampp/htdocs"

# Diretório do DocumentRoot
<Directory "C:/xampp/htdocs">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

# Módulo mod_rewrite deve estar habilitado (descomentado)
LoadModule rewrite_module modules/mod_rewrite.so
```

**Ações necessárias:**
1. Abrir `C:\xampp\apache\conf\httpd.conf` no editor de texto
2. Verificar se `LoadModule rewrite_module` está descomentado
3. Verificar se `AllowOverride All` está configurado para o diretório htdocs
4. Salvar e reiniciar o Apache

### 2.2 Verificar .htaccess

O arquivo `.htaccess` já foi criado na raiz do projeto e configurado para redirecionar para `public/`.

**Arquivo:** `C:\xampp\htdocs\ApacSimplificado\.htaccess`

### 2.3 Configuração do MySQL

**Arquivo:** `C:\xampp\mysql\bin\my.ini` ou `C:\xampp\mysql\bin\my.cnf`

Para permitir conexões externas ao MySQL (se necessário):

```ini
[mysqld]
# Comentar ou alterar bind-address para permitir conexões externas
# bind-address = 127.0.0.1
bind-address = 0.0.0.0
```

**Atenção:** Por padrão, o MySQL do XAMPP já aceita conexões locais. Se precisar de acesso externo ao MySQL, descomente e ajuste o bind-address.

**Ações necessárias:**
1. Abrir o arquivo de configuração do MySQL
2. Localizar `bind-address`
3. Se necessário, alterar para `0.0.0.0` ou comentar a linha
4. Reiniciar o MySQL

### 2.4 Verificar Permissões

Garantir que o Apache tem permissão de leitura em todos os arquivos do projeto e permissão de escrita nos diretórios:
- `logs/`
- `temp/`

## 3. Configurações de Firewall do Windows

### 3.1 Criar Regra para Apache (Porta 80)

**Método 1: Via Interface Gráfica**

1. Abrir "Firewall do Windows Defender com Segurança Avançada"
   - Pressionar `Win + R`
   - Digitar `wf.msc` e pressionar Enter

2. Criar Nova Regra de Entrada:
   - Clicar em "Regras de Entrada" → "Nova Regra..."
   - Selecionar "Porta" → Avançar
   - Selecionar "TCP" e "Portas locais específicas"
   - Digitar `80` → Avançar
   - Selecionar "Permitir a conexão" → Avançar
   - Marcar todos os perfis (Domínio, Privada, Pública) → Avançar
   - Nome: "Apache HTTP Server (Porta 80)" → Concluir

**Método 2: Via PowerShell (Executar como Administrador)**

```powershell
New-NetFirewallRule -DisplayName "Apache HTTP Server (Porta 80)" -Direction Inbound -Protocol TCP -LocalPort 80 -Action Allow
```

### 3.2 Criar Regra para MySQL (Porta 3306) - Opcional

**Atenção:** Apenas se precisar de acesso externo ao MySQL.

**Via PowerShell (Executar como Administrador):**

```powershell
New-NetFirewallRule -DisplayName "MySQL Server (Porta 3306)" -Direction Inbound -Protocol TCP -LocalPort 3306 -Action Allow -RemoteAddress 192.168.5.0/24
```

Isso permite acesso MySQL apenas da rede local (192.168.5.0/24).

### 3.3 Verificar Regras Existentes

Verificar se já existem regras para Apache e MySQL:
- Abrir "Firewall do Windows Defender com Segurança Avançada"
- Verificar em "Regras de Entrada" se há regras para Apache e MySQL
- Se já existirem, verificar se estão habilitadas

## 4. Testes e Verificações

### 4.1 Teste Local

1. **Parar o servidor PHP built-in** (se estiver rodando):
   ```bash
   # Pressionar Ctrl+C no terminal onde está rodando
   ```

2. **Iniciar Apache e MySQL no XAMPP Control Panel**

3. **Acessar localmente:**
   - Abrir navegador
   - Acessar: `http://localhost/ApacSimplificado/`
   - Verificar se a página carrega corretamente
   - Testar login e navegação

### 4.2 Teste via Rede Local

1. **Verificar IP da máquina:**
   ```powershell
   ipconfig
   ```
   Confirmar que o IP é `192.168.5.130`

2. **Acessar de outro computador na rede:**
   - Abrir navegador em outro computador
   - Acessar: `http://192.168.5.130/ApacSimplificado/`
   - Verificar se a página carrega

3. **Testar funcionalidades:**
   - Login/autenticação
   - Navegação entre páginas
   - Formulários e submissões
   - Requisições AJAX
   - Uploads de arquivos (se houver)

### 4.3 Troubleshooting

**Problema: Página não carrega via rede**

- Verificar se Apache está rodando
- Verificar regras de firewall
- Verificar se IP está correto (`ipconfig`)
- Verificar se outros computadores estão na mesma rede

**Problema: Erro 404 nas rotas**

- Verificar se `.htaccess` está funcionando
- Verificar se `mod_rewrite` está habilitado
- Verificar logs do Apache em `C:\xampp\apache\logs\error.log`

**Problema: CSS/JS não carregam**

- Verificar se caminhos estão relativos
- Verificar permissões de arquivos
- Verificar console do navegador para erros

**Problema: Sessões não funcionam**

- Verificar configuração de sessões no PHP
- Verificar se cookies estão sendo aceitos
- Verificar `session.cookie_path` no php.ini

## 5. Comandos Úteis

### Verificar Status do Apache
```powershell
# Verificar se Apache está rodando
Get-Service | Where-Object {$_.Name -like "*apache*"}
```

### Verificar Portas em Uso
```powershell
# Verificar porta 80
netstat -ano | findstr :80

# Verificar porta 3306
netstat -ano | findstr :3306
```

### Verificar Regras de Firewall
```powershell
# Listar regras de entrada para porta 80
Get-NetFirewallRule | Where-Object {$_.DisplayName -like "*Apache*" -or $_.DisplayName -like "*80*"}

# Listar regras de entrada para porta 3306
Get-NetFirewallRule | Where-Object {$_.DisplayName -like "*MySQL*" -or $_.DisplayName -like "*3306*"}
```

### Reiniciar Apache via XAMPP
- Abrir XAMPP Control Panel
- Clicar em "Stop" e depois "Start" no Apache

## 6. Segurança

### Recomendações:

1. **MySQL:** Se não precisar de acesso externo ao MySQL, mantenha `bind-address = 127.0.0.1`
2. **Firewall:** Restrinja regras de firewall apenas para a rede local quando possível
3. **Autenticação:** Garanta que o sistema de autenticação está funcionando corretamente
4. **HTTPS:** Considere configurar HTTPS para comunicação segura (requer certificado SSL)

## 7. Manutenção

### Logs Importantes:

- **Apache Error Log:** `C:\xampp\apache\logs\error.log`
- **Apache Access Log:** `C:\xampp\apache\logs\access.log`
- **PHP Error Log:** `C:\xampp\php\logs\php_error_log`
- **Aplicação Logs:** `C:\xampp\htdocs\ApacSimplificado\logs\`

### Backup:

Antes de fazer alterações nas configurações do Apache ou MySQL, faça backup:
- `C:\xampp\apache\conf\httpd.conf`
- `C:\xampp\mysql\bin\my.ini` ou `my.cnf`

## 8. Suporte

Em caso de problemas:

1. Verificar logs do Apache e PHP
2. Verificar console do navegador (F12)
3. Verificar se todas as configurações foram aplicadas corretamente
4. Testar acesso local antes de testar via rede

## Checklist Final

- [ ] Apache iniciado e rodando
- [ ] MySQL iniciado e rodando
- [ ] `mod_rewrite` habilitado no Apache
- [ ] `AllowOverride All` configurado
- [ ] `.htaccess` criado e funcionando
- [ ] Regra de firewall para porta 80 criada
- [ ] Acesso local funcionando (`http://localhost/ApacSimplificado/`)
- [ ] Acesso via rede funcionando (`http://192.168.5.130/ApacSimplificado/`)
- [ ] Todas as rotas funcionando corretamente
- [ ] AJAX e formulários funcionando
- [ ] Autenticação e sessões funcionando

