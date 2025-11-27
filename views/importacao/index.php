<?php
use App\Utils\UrlHelper;

$title = 'Importação SIGTAP - Sistema APAC';
ob_start();

function formatarTamanho($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>

<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Importação SIGTAP</h1>
            <p class="text-gray-600">Importe dados oficiais do SUS (CIDs, Procedimentos e Relacionamentos)</p>
        </div>
    </div>
</div>

<!-- Avisos -->
<div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-700">
                <strong>Importante:</strong> A importação pode demorar vários minutos dependendo do tamanho dos arquivos. 
                Não feche esta página durante o processo. Você pode fazer upload de novos arquivos para substituir os existentes.
            </p>
        </div>
    </div>
</div>

<!-- Seção de Upload -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Enviar/Substituir Arquivos</h2>
    <p class="text-gray-600 mb-4">Faça upload de novos arquivos SIGTAP para substituir os arquivos existentes. Um backup automático será criado antes da substituição.</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-400 transition">
            <label class="block cursor-pointer">
                <input type="file" 
                       accept=".txt" 
                       class="hidden" 
                       onchange="uploadArquivo('cids', this.files[0])">
                <div class="text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-700">CIDs</p>
                    <p class="text-xs text-gray-500 mt-1">tb_cid.txt</p>
                </div>
            </label>
        </div>
        
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-400 transition">
            <label class="block cursor-pointer">
                <input type="file" 
                       accept=".txt" 
                       class="hidden" 
                       onchange="uploadArquivo('procedimentos', this.files[0])">
                <div class="text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-700">Procedimentos</p>
                    <p class="text-xs text-gray-500 mt-1">tb_procedimento.txt</p>
                </div>
            </label>
        </div>
        
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-blue-400 transition">
            <label class="block cursor-pointer">
                <input type="file" 
                       accept=".txt" 
                       class="hidden" 
                       onchange="uploadArquivo('relacionamentos', this.files[0])">
                <div class="text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-sm font-medium text-gray-700">Relacionamentos</p>
                    <p class="text-xs text-gray-500 mt-1">rl_procedimento_cid.txt</p>
                </div>
            </label>
        </div>
    </div>
    
    <div id="uploadProgress" class="hidden mt-4">
        <div class="bg-blue-50 border border-blue-200 rounded p-4">
            <div class="flex items-center">
                <svg class="animate-spin h-5 w-5 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="uploadMessage" class="text-blue-800">Enviando arquivo...</span>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Arquivos -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <?php foreach ($arquivos as $tipo => $info): ?>
    <div class="bg-white rounded-lg shadow-md p-6 border-l-4 <?= $info['existe'] ? 'border-green-500' : 'border-red-500' ?>">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($info['nome']) ?></h3>
            <?php if ($info['existe']): ?>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                    Disponível
                </span>
            <?php else: ?>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                    Não encontrado
                </span>
            <?php endif; ?>
        </div>
        
        <div class="space-y-2">
            <div class="text-sm text-gray-600">
                <strong>Arquivo:</strong> <?= htmlspecialchars(basename($info['arquivo'])) ?>
            </div>
            <?php if ($info['existe']): ?>
                <div class="text-sm text-gray-600">
                    <strong>Tamanho:</strong> <?= formatarTamanho($info['tamanho']) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Botões de Importação -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Ações de Importação</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <button onclick="importar('cids')" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center"
                <?= !$arquivos['cids']['existe'] ? 'disabled' : '' ?>>
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            Importar CIDs
        </button>
        
        <button onclick="importar('procedimentos')" 
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center"
                <?= !$arquivos['procedimentos']['existe'] ? 'disabled' : '' ?>>
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            Importar Procedimentos
        </button>
        
        <button onclick="importar('relacionamentos')" 
                class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center"
                <?= !$arquivos['relacionamentos']['existe'] ? 'disabled' : '' ?>>
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            Importar Relacionamentos
        </button>
        
        <button onclick="importar('tudo')" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            Importar Tudo
        </button>
    </div>
</div>

<!-- Área de Progresso -->
<div id="progressArea" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Progresso da Importação</h3>
    <div class="mb-4">
        <div class="flex justify-between text-sm text-gray-600 mb-2">
            <span id="progressText">Iniciando...</span>
            <span id="progressPercent">0%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>
    <div id="progressDetails" class="text-sm text-gray-600 space-y-1"></div>
</div>

<!-- Resultados -->
<div id="resultArea" class="hidden bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Resultado da Importação</h3>
    <div id="resultContent"></div>
</div>

<script>
let importacaoEmAndamento = false;

function uploadArquivo(tipo, arquivo) {
    if (!arquivo) {
        return;
    }
    
    // Validar extensão
    if (!arquivo.name.toLowerCase().endsWith('.txt')) {
        alert('Apenas arquivos .txt são permitidos');
        return;
    }
    
    // Validar tamanho (100MB)
    if (arquivo.size > 100 * 1024 * 1024) {
        alert('Arquivo muito grande. Tamanho máximo: 100MB');
        return;
    }
    
    if (!confirm(`Tem certeza que deseja substituir o arquivo ${tipo}? Um backup será criado automaticamente.`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('arquivo', arquivo);
    formData.append('tipo', tipo);
    
    const progressDiv = document.getElementById('uploadProgress');
    const messageSpan = document.getElementById('uploadMessage');
    
    progressDiv.classList.remove('hidden');
    messageSpan.textContent = 'Enviando arquivo...';
    
    fetch('<?= UrlHelper::url("/importacao/upload") ?>', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        progressDiv.classList.add('hidden');
        
        if (data.success) {
            alert('Arquivo enviado e substituído com sucesso!\n\nTamanho: ' + formatarTamanho(data.data.tamanho) + '\nData: ' + data.data.data_modificacao);
            // Recarregar página para atualizar informações dos arquivos
            location.reload();
        } else {
            alert('Erro ao enviar arquivo: ' + (data.message || 'Erro desconhecido'));
        }
    })
    .catch(error => {
        progressDiv.classList.add('hidden');
        alert('Erro ao enviar arquivo: ' + error.message);
    });
}

function formatarTamanho(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

function importar(tipo) {
    if (importacaoEmAndamento) {
        alert('Uma importação já está em andamento. Aguarde a conclusão.');
        return;
    }

    if (!confirm(`Tem certeza que deseja importar ${tipo === 'tudo' ? 'todos os arquivos' : tipo + 's'}? Esta ação pode demorar vários minutos.`)) {
        return;
    }

    importacaoEmAndamento = true;
    const progressArea = document.getElementById('progressArea');
    const resultArea = document.getElementById('resultArea');
    const progressText = document.getElementById('progressText');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const progressDetails = document.getElementById('progressDetails');
    const resultContent = document.getElementById('resultContent');

    // Mostrar área de progresso
    progressArea.classList.remove('hidden');
    resultArea.classList.add('hidden');
    progressText.textContent = 'Iniciando importação...';
    progressBar.style.width = '0%';
    progressPercent.textContent = '0%';
    progressDetails.innerHTML = '';

    // Determinar endpoint
    let endpoint = '';
    switch(tipo) {
        case 'cids':
            endpoint = '<?= UrlHelper::url("/importacao/cids") ?>';
            break;
        case 'procedimentos':
            endpoint = '<?= UrlHelper::url("/importacao/procedimentos") ?>';
            break;
        case 'relacionamentos':
            endpoint = '<?= UrlHelper::url("/importacao/relacionamentos") ?>';
            break;
        case 'tudo':
            endpoint = '<?= UrlHelper::url("/importacao/tudo") ?>';
            break;
    }

    // Simular progresso (já que não temos progresso real)
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += 2;
        if (progress < 90) {
            progressBar.style.width = progress + '%';
            progressPercent.textContent = progress + '%';
            progressText.textContent = 'Processando...';
        }
    }, 500);

    // Fazer requisição
    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        clearInterval(progressInterval);
        progressBar.style.width = '100%';
        progressPercent.textContent = '100%';
        return response.json();
    })
    .then(data => {
        importacaoEmAndamento = false;
        progressText.textContent = 'Concluído!';

        // Mostrar resultados
        resultArea.classList.remove('hidden');
        
        if (data.success) {
            let html = '<div class="bg-green-50 border border-green-200 rounded p-4 mb-4">';
            html += '<p class="text-green-800 font-semibold">✓ ' + data.message + '</p>';
            html += '</div>';

            if (data.data) {
                html += '<div class="space-y-4">';
                
                if (data.data.importados !== undefined) {
                    html += '<div class="flex justify-between items-center p-3 bg-gray-50 rounded">';
                    html += '<span class="font-medium">Registros importados:</span>';
                    html += '<span class="text-blue-600 font-bold">' + data.data.importados + '</span>';
                    html += '</div>';
                }

                if (data.data.atualizados !== undefined) {
                    html += '<div class="flex justify-between items-center p-3 bg-gray-50 rounded">';
                    html += '<span class="font-medium">Registros atualizados:</span>';
                    html += '<span class="text-yellow-600 font-bold">' + data.data.atualizados + '</span>';
                    html += '</div>';
                }

                if (data.data.erros !== undefined && data.data.erros > 0) {
                    html += '<div class="flex justify-between items-center p-3 bg-red-50 rounded">';
                    html += '<span class="font-medium">Erros:</span>';
                    html += '<span class="text-red-600 font-bold">' + data.data.erros + '</span>';
                    html += '</div>';
                }

                // Se for importação completa
                if (data.stats) {
                    html += '<div class="mt-4 p-4 bg-blue-50 rounded">';
                    html += '<h4 class="font-semibold mb-2">Estatísticas Gerais:</h4>';
                    html += '<div class="space-y-2 text-sm">';
                    
                    if (data.stats.cids) {
                        html += '<div>CIDs: ' + (data.stats.cids.importados || 0) + ' importados, ' + (data.stats.cids.atualizados || 0) + ' atualizados</div>';
                    }
                    if (data.stats.procedimentos) {
                        html += '<div>Procedimentos: ' + (data.stats.procedimentos.importados || 0) + ' importados, ' + (data.stats.procedimentos.atualizados || 0) + ' atualizados</div>';
                    }
                    if (data.stats.relacionamentos) {
                        html += '<div>Relacionamentos: ' + (data.stats.relacionamentos.importados || 0) + ' importados</div>';
                    }
                    
                    html += '</div></div>';
                }

                html += '</div>';
            }

            resultContent.innerHTML = html;
        } else {
            resultContent.innerHTML = '<div class="bg-red-50 border border-red-200 rounded p-4">';
            resultContent.innerHTML += '<p class="text-red-800 font-semibold">✗ Erro: ' + (data.message || 'Erro desconhecido') + '</p>';
            resultContent.innerHTML += '</div>';
        }
    })
    .catch(error => {
        importacaoEmAndamento = false;
        clearInterval(progressInterval);
        progressText.textContent = 'Erro!';
        
        resultArea.classList.remove('hidden');
        resultContent.innerHTML = '<div class="bg-red-50 border border-red-200 rounded p-4">';
        resultContent.innerHTML += '<p class="text-red-800 font-semibold">✗ Erro ao processar importação: ' + error.message + '</p>';
        resultContent.innerHTML += '</div>';
    });
}
</script>

<?php
$content = ob_get_clean();
require VIEWS_PATH . '/layouts/app.php';
?>

