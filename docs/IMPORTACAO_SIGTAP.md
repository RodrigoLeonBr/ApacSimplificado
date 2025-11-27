# Importação SIGTAP - Documentação

## Layout Oficial dos Arquivos

### TB_CID (tb_cid.txt)
- **CO_CID**: Posição 1-4 (4 caracteres)
- **NO_CID**: Posição 5-104 (100 caracteres)
- **TP_AGRAVO**: Posição 105 (1 caractere)
- **TP_SEXO**: Posição 106 (1 caractere)
- **TP_ESTADIO**: Posição 107 (1 caractere)
- **VL_CAMPOS_IRRADIADOS**: Posição 108-111 (4 caracteres numéricos)

### TB_PROCEDIMENTO (tb_procedimento.txt)
- **CO_PROCEDIMENTO**: Posição 1-10 (10 caracteres)
- **NO_PROCEDIMENTO**: Posição 11-260 (250 caracteres)
- **TP_COMPLEXIDADE**: Posição 261 (1 caractere)
- **TP_SEXO**: Posição 262 (1 caractere)
- **QT_MAXIMA_EXECUCAO**: Posição 263-266 (4 caracteres)
- **VL_SH**: Posição 283-292 (10 caracteres - formato 8,2)
- **VL_SA**: Posição 293-302 (10 caracteres - formato 8,2)
- **VL_SP**: Posição 303-312 (10 caracteres - formato 8,2)
- **DT_COMPETENCIA**: Posição 321-326 (6 caracteres - formato YYYYMM)
  - Nota: Se vazio na posição oficial, tenta pegar do final da linha

### RL_PROCEDIMENTO_CID (rl_procedimento_cid.txt)
- **CO_PROCEDIMENTO**: Posição 1-10 (10 caracteres)
- **CO_CID**: Posição 11-14 (4 caracteres)
- **ST_PRINCIPAL**: Posição 15 (1 caractere - S ou N)
- **DT_COMPETENCIA**: Posição 16-21 (6 caracteres - formato YYYYMM)

## Correções Implementadas

### 1. Posições Corrigidas
- ✅ CIDs: Todas as posições corrigidas conforme layout oficial
- ✅ Procedimentos: Valores monetários corrigidos de 12 para 10 caracteres
- ✅ Procedimentos: DT_COMPETENCIA ajustada para verificar também o final da linha
- ✅ Relacionamentos: ST_PRINCIPAL e DT_COMPETENCIA corrigidos conforme layout oficial

### 2. Conversão de Valores Monetários
- Formato: 10 caracteres (8 inteiros + 2 decimais)
- Exemplo: `0000841571` = R$ 8.415,71
- Conversão: Divide por 100 para obter valor em reais

### 3. Tratamento de Encoding
- Arquivos em ISO-8859-1 são convertidos para UTF-8
- Função `limparTexto()` remove caracteres especiais e normaliza encoding

### 4. Normalização de Campos
- **ST_PRINCIPAL**: Espaço ou 'S' vira 'S', caso contrário 'N'
- **DT_COMPETENCIA**: Se vazio na posição oficial, tenta pegar do final da linha

## Testes Realizados

### Teste de Parsing
Execute o script de teste para validar o parsing:
```bash
php scripts/testar_importacao.php cids
php scripts/testar_importacao.php procedimentos
php scripts/testar_importacao.php relacionamentos
```

### Resultados dos Testes
- ✅ **CIDs**: Parsing correto, todas as posições validadas
- ✅ **Procedimentos**: Parsing correto, valores monetários convertidos corretamente
- ✅ **Relacionamentos**: Parsing correto, ST_PRINCIPAL normalizado corretamente

## Como Usar

### Via Interface Web
1. Acesse `/importacao`
2. Verifique se os arquivos estão na pasta `temp/`
3. Clique no botão desejado (CIDs, Procedimentos, Relacionamentos ou Tudo)
4. Aguarde a conclusão e veja os resultados

### Via CLI
```bash
php scripts/importar_sigtap.php cids
php scripts/importar_sigtap.php procedimentos
php scripts/importar_sigtap.php relacionamentos
php scripts/importar_sigtap.php tudo
```

## Observações Importantes

1. **DT_COMPETENCIA em Procedimentos**: Alguns arquivos podem ter a competência no final da linha ao invés da posição oficial. O código trata ambos os casos.

2. **ST_PRINCIPAL**: O arquivo pode conter espaço em branco ou 'S'. O código normaliza para 'S' ou 'N'.

3. **Valores Monetários**: Todos os valores são armazenados em reais (decimal), não em centavos.

4. **Performance**: A importação usa transações em lote (commit a cada 1000-5000 registros) para melhor performance.

5. **Validação**: O código valida se Procedimentos e CIDs existem antes de criar relacionamentos.

## Arquivos Criados

- `src/Services/SigtapImportService.php` - Service principal
- `src/Controllers/ImportacaoController.php` - Controller web
- `views/importacao/index.php` - Interface web
- `scripts/importar_sigtap.php` - Script CLI
- `scripts/testar_importacao.php` - Script de teste
- `scripts/verificar_posicoes.php` - Script de verificação

## Status

✅ Layout validado conforme documentação oficial SIGTAP
✅ Testes de parsing realizados e validados
✅ Correções aplicadas conforme layout oficial
✅ Pronto para importação em produção

