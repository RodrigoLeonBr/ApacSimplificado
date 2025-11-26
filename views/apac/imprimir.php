<?php
use App\Utils\UrlHelper;

// Formatação de data
function formatarData($data) {
    if (empty($data)) return '';
    return date('d/m/Y', strtotime($data));
}

// Formatação de CPF
function formatarCPF($cpf) {
    if (empty($cpf)) return '';
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
}

// Formatação de CNS
function formatarCNS($cns) {
    if (empty($cns)) return '';
    return preg_replace('/(\d{3})(\d{4})(\d{4})(\d{4})/', '$1 $2 $3 $4', $cns);
}

$numeroApac = $apac['numero_apac'] ?? '';
$dataEmissao = formatarData($apac['criada_em'] ?? date('Y-m-d'));
$dataValidadeInicio = formatarData($laudoVinculado[0]['data_validade_inicio'] ?? $dataEmissao);
$dataValidadeFim = formatarData($laudoVinculado[0]['data_validade_fim'] ?? date('Y-m-d', strtotime('+3 months')));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APAC - Autorização de Procedimento Ambulatorial</title>
    <style>
        @media print {
            .no-print { display: none; }
            @page { margin: 0; size: A4; }
            body { margin: 0; padding: 0; }
            .container { page-break-after: always; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 5mm;
            page-break-after: always;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .header .via {
            font-size: 10pt;
            margin-bottom: 5px;
        }
        
        .numero-apac {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .section {
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 3px;
        }
        
        .row {
            display: flex;
            margin-bottom: 3px;
        }
        
        .col {
            flex: 1;
            padding: 0 5px;
        }
        
        .col-2 {
            flex: 2;
        }
        
        .label {
            font-weight: bold;
            display: inline-block;
            min-width: 80px;
        }
        
        .value {
            display: inline-block;
        }
        
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 8pt;
        }
        
        .actions {
            text-align: center;
            margin: 20px 0;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Botões de ação (não aparecem na impressão) -->
    <div class="actions no-print">
        <button onclick="window.print()" class="btn">Imprimir</button>
        <a href="<?= UrlHelper::url('/laudos/' . $laudo['id']) ?>" class="btn">Voltar ao Laudo</a>
    </div>

    <!-- 1ª Via -->
    <div class="container">
        <div class="header">
            <div class="via">1ª Via</div>
            <h1>SUS</h1>
            <h2>AUTORIZAÇÃO DE PROCEDIMENTO AMBULATORIAL</h2>
        </div>
        
        <div class="numero-apac">
            N° <?= htmlspecialchars($numeroApac) ?>
        </div>
        
        <div class="section">
            <div class="section-title">Órgão Emissor:</div>
            <div class="value"><?= htmlspecialchars($estabelecimentoSolicitante['cnes'] ?? '') ?></div>
            <div class="value"><?= htmlspecialchars($estabelecimentoSolicitante['razao_social'] ?? '') ?></div>
        </div>
        
        <div class="section">
            <div class="row">
                <div class="col">
                    <span class="label">Estabelecimento Solicitante:</span>
                    <span class="value"><?= htmlspecialchars($estabelecimentoSolicitante['cnes'] ?? '') ?></span>
                </div>
            </div>
            <div class="value"><?= htmlspecialchars($estabelecimentoSolicitante['razao_social'] ?? '') ?></div>
        </div>
        
        <div class="section">
            <div class="row">
                <div class="col">
                    <span class="label">Estabelecimento Executante:</span>
                    <span class="value"><?= htmlspecialchars($estabelecimentoExecutante['cnes'] ?? '') ?></span>
                </div>
            </div>
            <div class="value"><?= htmlspecialchars($estabelecimentoExecutante['razao_social'] ?? '') ?></div>
        </div>
        
        <div class="section">
            <div class="section-title">Paciente</div>
            <div class="row">
                <div class="col-2">
                    <span class="label">Nome:</span>
                    <span class="value"><?= htmlspecialchars($paciente['nome'] ?? $laudo['paciente_nome'] ?? '') ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="label">Sexo:</span>
                    <span class="value"><?= htmlspecialchars(strtoupper($paciente['sexo'] ?? '')) ?></span>
                </div>
                <div class="col">
                    <span class="label">Nascimento:</span>
                    <span class="value"><?= formatarData($paciente['data_nascimento'] ?? '') ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="label">CNS:</span>
                    <span class="value"><?= htmlspecialchars($paciente['cns'] ?? $laudo['paciente_cns'] ?? '') ?></span>
                </div>
                <div class="col">
                    <span class="label">CPF:</span>
                    <span class="value"><?= formatarCPF($paciente['cpf'] ?? $laudo['paciente_cpf'] ?? '') ?></span>
                </div>
            </div>
            <?php if (!empty($paciente['nome_mae'])): ?>
            <div class="row">
                <div class="col-2">
                    <span class="label">Nome da Mãe:</span>
                    <span class="value"><?= htmlspecialchars($paciente['nome_mae']) ?></span>
                </div>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col">
                    <span class="label">Município:</span>
                    <span class="value"><?= htmlspecialchars($paciente['municipio'] ?? '') ?></span>
                </div>
                <div class="col">
                    <span class="label">CEP:</span>
                    <span class="value"><?= htmlspecialchars($paciente['cep'] ?? '') ?></span>
                </div>
            </div>
            <?php if (!empty($paciente['cor_raca'])): ?>
            <div class="row">
                <div class="col">
                    <span class="label">Cor/Raça:</span>
                    <span class="value"><?= htmlspecialchars($paciente['cor_raca']) ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <div class="section-title">Solicitação / Autorização</div>
            <?php if (!empty($profissional['cns'])): ?>
            <div class="row">
                <div class="col-2">
                    <span class="label">CNS do Solicitante:</span>
                    <span class="value"><?= htmlspecialchars($profissional['cns']) ?> <?= htmlspecialchars($profissional['nome'] ?? '') ?></span>
                </div>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-2">
                    <span class="label">Procedimento Autorizado:</span>
                    <span class="value"><?= htmlspecialchars($laudo['procedimento_codigo'] ?? '') ?> <?= htmlspecialchars($laudo['procedimento_descricao'] ?? '') ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="label">CID:</span>
                    <span class="value"><?= htmlspecialchars($laudo['cid_codigo'] ?? '') ?> <?= htmlspecialchars($laudo['cid_descricao'] ?? '') ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="label">N° Laudo:</span>
                    <span class="value"><?= htmlspecialchars($laudo['numero_laudo'] ?? '') ?></span>
                </div>
                <div class="col">
                    <span class="label">N° Prontuário:</span>
                    <span class="value"><?= htmlspecialchars($laudo['numero_prontuario'] ?? '') ?></span>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Autorizador</div>
            <div class="row">
                <div class="col">
                    <span class="label">Data:</span>
                    <span class="value"><?= $dataEmissao ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="label">Validade:</span>
                    <span class="value"><?= $dataValidadeInicio ?> à <?= $dataValidadeFim ?></span>
                </div>
            </div>
            <?php if (!empty($profissional['cpf'])): ?>
            <div class="row">
                <div class="col">
                    <span class="label">CPF:</span>
                    <span class="value"><?= formatarCPF($profissional['cpf']) ?> <?= htmlspecialchars($profissional['nome'] ?? '') ?></span>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($profissional['cns'])): ?>
            <div class="row">
                <div class="col">
                    <span class="label">CNS:</span>
                    <span class="value"><?= htmlspecialchars($profissional['cns']) ?></span>
                </div>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col">
                    <span class="label">Assinatura e carimbo (N° do registro do conselho de classe):</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2ª Via -->
    <div class="container">
        <div class="header">
            <div class="via">2ª Via</div>
            <h1>SUS</h1>
            <h2>AUTORIZAÇÃO DE PROCEDIMENTO AMBULATORIAL</h2>
        </div>
        
        <div class="numero-apac">
            N° <?= htmlspecialchars($numeroApac) ?>
        </div>
        
        <div class="section">
            <div class="section-title">Órgão Emissor:</div>
            <div class="value"><?= htmlspecialchars($estabelecimentoSolicitante['cnes'] ?? '') ?></div>
            <div class="value"><?= htmlspecialchars($estabelecimentoSolicitante['razao_social'] ?? '') ?></div>
        </div>
        
        <div class="section">
            <div class="row">
                <div class="col">
                    <span class="label">Estabelecimento Solicitante:</span>
                    <span class="value"><?= htmlspecialchars($estabelecimentoSolicitante['cnes'] ?? '') ?></span>
                </div>
            </div>
            <div class="value"><?= htmlspecialchars($estabelecimentoSolicitante['razao_social'] ?? '') ?></div>
        </div>
        
        <div class="section">
            <div class="row">
                <div class="col">
                    <span class="label">Estabelecimento Executante:</span>
                    <span class="value"><?= htmlspecialchars($estabelecimentoExecutante['cnes'] ?? '') ?></span>
                </div>
            </div>
            <div class="value"><?= htmlspecialchars($estabelecimentoExecutante['razao_social'] ?? '') ?></div>
        </div>
        
        <div class="section">
            <div class="section-title">Paciente</div>
            <div class="row">
                <div class="col-2">
                    <span class="label">Nome:</span>
                    <span class="value"><?= htmlspecialchars($paciente['nome'] ?? $laudo['paciente_nome'] ?? '') ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="label">Sexo:</span>
                    <span class="value"><?= htmlspecialchars(strtoupper($paciente['sexo'] ?? '')) ?></span>
                </div>
                <div class="col">
                    <span class="label">Nascimento:</span>
                    <span class="value"><?= formatarData($paciente['data_nascimento'] ?? '') ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="label">CNS:</span>
                    <span class="value"><?= htmlspecialchars($paciente['cns'] ?? $laudo['paciente_cns'] ?? '') ?></span>
                </div>
                <div class="col">
                    <span class="label">Prontuário:</span>
                    <span class="value"><?= htmlspecialchars($laudo['numero_prontuario'] ?? '') ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="label">CNS:</span>
                    <span class="value"><?= htmlspecialchars($paciente['cns'] ?? $laudo['paciente_cns'] ?? '') ?></span>
                </div>
                <div class="col">
                    <span class="label">CPF:</span>
                    <span class="value"><?= formatarCPF($paciente['cpf'] ?? $laudo['paciente_cpf'] ?? '') ?></span>
                </div>
            </div>
            <?php if (!empty($paciente['nome_mae'])): ?>
            <div class="row">
                <div class="col-2">
                    <span class="label">Nome da Mãe:</span>
                    <span class="value"><?= htmlspecialchars($paciente['nome_mae']) ?></span>
                </div>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col">
                    <span class="label">Município:</span>
                    <span class="value"><?= htmlspecialchars($paciente['municipio'] ?? '') ?></span>
                </div>
                <div class="col">
                    <span class="label">CEP:</span>
                    <span class="value"><?= htmlspecialchars($paciente['cep'] ?? '') ?></span>
                </div>
            </div>
            <?php if (!empty($paciente['cor_raca'])): ?>
            <div class="row">
                <div class="col">
                    <span class="label">Cor/Raça:</span>
                    <span class="value"><?= htmlspecialchars($paciente['cor_raca']) ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <div class="section-title">Solicitação / Autorização</div>
            <?php if (!empty($profissional['cns'])): ?>
            <div class="row">
                <div class="col-2">
                    <span class="label">CNS do Solicitante:</span>
                    <span class="value"><?= htmlspecialchars($profissional['cns']) ?> <?= htmlspecialchars($profissional['nome'] ?? '') ?></span>
                </div>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col-2">
                    <span class="label">Procedimento Autorizado:</span>
                    <span class="value"><?= htmlspecialchars($laudo['procedimento_codigo'] ?? '') ?> <?= htmlspecialchars($laudo['procedimento_descricao'] ?? '') ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="label">CID:</span>
                    <span class="value"><?= htmlspecialchars($laudo['cid_codigo'] ?? '') ?> <?= htmlspecialchars($laudo['cid_descricao'] ?? '') ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="label">N° Laudo:</span>
                    <span class="value"><?= htmlspecialchars($laudo['numero_laudo'] ?? '') ?></span>
                </div>
                <div class="col">
                    <span class="label">N° Prontuário:</span>
                    <span class="value"><?= htmlspecialchars($laudo['numero_prontuario'] ?? '') ?></span>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Autorizador</div>
            <div class="row">
                <div class="col">
                    <span class="label">Data:</span>
                    <span class="value"><?= $dataEmissao ?></span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <span class="label">Validade:</span>
                    <span class="value"><?= $dataValidadeInicio ?> à <?= $dataValidadeFim ?></span>
                </div>
            </div>
            <?php if (!empty($profissional['cpf'])): ?>
            <div class="row">
                <div class="col">
                    <span class="label">CPF:</span>
                    <span class="value"><?= formatarCPF($profissional['cpf']) ?> <?= htmlspecialchars($profissional['nome'] ?? '') ?></span>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($profissional['cns'])): ?>
            <div class="row">
                <div class="col">
                    <span class="label">CNS:</span>
                    <span class="value"><?= htmlspecialchars($profissional['cns']) ?></span>
                </div>
            </div>
            <?php endif; ?>
            <div class="row">
                <div class="col">
                    <span class="label">Assinatura e carimbo (N° do registro do conselho de classe):</span>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Marcar como impresso quando a página for impressa
        window.addEventListener('beforeprint', function() {
            fetch('<?= UrlHelper::url('/apacs/' . $apac['id'] . '/imprimir') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            }).catch(function(error) {
                console.error('Erro ao marcar APAC como impressa:', error);
            });
        });
    </script>
</body>
</html>

