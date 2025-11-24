<?php

require_once __DIR__ . '/src/Database/Database.php';

try {
    $db = App\Database\Database::getInstance();
    
    echo "Iniciando migração da tabela estabelecimentos...\n";
    
    // Renomear coluna 'codigo' para 'cnes'
    $db->query("ALTER TABLE estabelecimentos CHANGE COLUMN codigo cnes VARCHAR(7)");
    echo "- Coluna 'codigo' renomeada para 'cnes'\n";
    
    // Adicionar novas colunas
    $db->query("ALTER TABLE estabelecimentos 
        ADD COLUMN cnpj VARCHAR(14) AFTER cnes,
        ADD COLUMN razao_social VARCHAR(255) AFTER cnpj,
        ADD COLUMN nome_fantasia VARCHAR(255) AFTER razao_social,
        ADD COLUMN complemento VARCHAR(100) AFTER numero,
        ADD COLUMN uf CHAR(2) AFTER municipio,
        ADD COLUMN telefone VARCHAR(20) AFTER uf,
        ADD COLUMN email VARCHAR(255) AFTER telefone,
        ADD COLUMN status ENUM('ativo', 'inativo') DEFAULT 'ativo' AFTER email");
    echo "- Novas colunas adicionadas\n";
    
    // Copiar dados de 'nome' para 'razao_social'
    $db->query("UPDATE estabelecimentos SET razao_social = nome");
    echo "- Dados copiados de 'nome' para 'razao_social'\n";
    
    echo "\nMigração concluída com sucesso!\n";
    
} catch (Exception $e) {
    echo "Erro na migração: " . $e->getMessage() . "\n";
    exit(1);
}
