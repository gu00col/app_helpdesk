<?php
try {
    // Conectar ao banco de dados SQLite
    try {
        $pdo = new PDO('sqlite:../db/banco.db');
    } catch (\Throwable $th) {
        $pdo = new PDO('sqlite:./db/banco.db');
    }

    // Configurar o PDO para lançar exceções em caso de erros
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // // Cria a tabela usuarios se não existir
    // $pdo->exec("
    //     CREATE TABLE IF NOT EXISTS usuarios (
    //         id INTEGER PRIMARY KEY AUTOINCREMENT,
    //         nome TEXT NOT NULL,
    //         senha TEXT NOT NULL,
    //         email TEXT NOT NULL UNIQUE
    //     )
    // ");
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}

// A variável $pdo está agora disponível para outros scripts
?>
