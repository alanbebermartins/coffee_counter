<?php
require 'src/Database.php';

$config = require 'config.php';
$pdo = Src\Database::getConnection($config);

try {
    $sql = file_get_contents(__DIR__ . '/migrations/schema.sql');
    $pdo->exec($sql);
    echo "✅ Tabelas criadas com sucesso!";
} catch (PDOException $e) {
    echo "❌ Erro ao criar tabelas: " . $e->getMessage();
}
