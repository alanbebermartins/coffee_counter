<?php
require 'src/Database.php';

// Carrega a configuração do banco
$config = require 'config.php';

try {
    // Tenta criar a conexão
    $pdo = Src\Database::getConnection($config);
    echo "✅ Conexão com o banco de dados estabelecida com sucesso!";
} catch (PDOException $e) {
    // Se der erro, mostra a mensagem
    echo "❌ Erro ao conectar: " . $e->getMessage();
}
?>