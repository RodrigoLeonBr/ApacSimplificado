<?php

require_once __DIR__ . '/src/Database/Database.php';

try {
    $db = App\Database\Database::getInstance();
    
    echo "Iniciando migração da tabela profissionais...\n";
    
    // Verificar se as colunas já existem antes de adicionar
    $columns = $db->fetchAll("SHOW COLUMNS FROM profissionais");
    $columnNames = array_column($columns, 'Field');
    
    // Adicionar matrícula se não existir
    if (!in_array('matricula', $columnNames)) {
        $db->execute("ALTER TABLE profissionais ADD COLUMN matricula VARCHAR(50) AFTER cns");
        echo "- Coluna 'matricula' adicionada\n";
    }
    
    // Adicionar telefone se não existir
    if (!in_array('telefone', $columnNames)) {
        $db->execute("ALTER TABLE profissionais ADD COLUMN telefone VARCHAR(20) AFTER cpf");
        echo "- Coluna 'telefone' adicionada\n";
    }
    
    // Adicionar email se não existir
    if (!in_array('email', $columnNames)) {
        $db->execute("ALTER TABLE profissionais ADD COLUMN email VARCHAR(255) AFTER telefone");
        echo "- Coluna 'email' adicionada\n";
    }
    
    // Adicionar uf se não existir
    if (!in_array('uf', $columnNames)) {
        $db->execute("ALTER TABLE profissionais ADD COLUMN uf CHAR(2) AFTER especialidade");
        echo "- Coluna 'uf' adicionada\n";
    }
    
    // Adicionar municipio se não existir
    if (!in_array('municipio', $columnNames)) {
        $db->execute("ALTER TABLE profissionais ADD COLUMN municipio VARCHAR(255) AFTER uf");
        echo "- Coluna 'municipio' adicionada\n";
    }
    
    // Adicionar status se não existir
    if (!in_array('status', $columnNames)) {
        $db->execute("ALTER TABLE profissionais ADD COLUMN status ENUM('ativo', 'inativo') DEFAULT 'ativo' AFTER municipio");
        echo "- Coluna 'status' adicionada\n";
    }
    
    echo "\nMigração concluída com sucesso!\n";
    
} catch (Exception $e) {
    echo "Erro na migração: " . $e->getMessage() . "\n";
    exit(1);
}

