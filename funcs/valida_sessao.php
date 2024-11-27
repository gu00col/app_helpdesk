<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    // Define a mensagem de erro na sessão
    $_SESSION['alerta'] = 'Você precisa estar logado para acessar o sistema.';
    $_SESSION['alerta_tag'] = 'danger';
    header("Location: index.php");
    exit();
} else {
    // Se a sessão existe, atualiza a sessão

    $usuario_id = $_SESSION['usuario']['id'];

    // Importa o arquivo de conexão
    $cnxPath = './cnx.php';
    if (!file_exists($cnxPath)) {
        $cnxPath = './funcs/cnx.php';
    }
    require_once $cnxPath;

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :usuario_id;");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $_SESSION['usuario'] = $usuario;
    }
}
?>
