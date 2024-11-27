<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Inicia a sessão

// Importa o arquivo de conexão
require_once './cnx.php';

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    try {
        // Prepara a consulta SQL para evitar injeção de SQL
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        // Obtém os resultados
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o usuário foi encontrado e se a senha está correta
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Adiciona o usuário à sessão
            $_SESSION['usuario'] = $usuario;
            // Redireciona para home.php se a senha estiver correta
            header("Location: ../home.php");
            exit();
        } else {
            // Define a mensagem de erro na sessão
            $_SESSION['alerta'] = 'Usuário ou senha incorretos';
            $_SESSION['alerta_tag'] = 'danger';
            // Redireciona para index.php com mensagem de erro se o e-mail ou a senha estiverem incorretos
            header("Location: ../index.php");
            exit();

        }
    } catch (PDOException $e) {
        echo "Erro ao executar a consulta: " . $e->getMessage();
    }

    // Fecha a conexão PDO (opcional, já que ela é fechada automaticamente no final do script)
    $pdo = null;
} else {
    // Se não foi enviado via POST, redireciona para index.php
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-Br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abir Chamado</title>
    <style>
        body,
        html {
            height: 100%;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* HTML: <div class="loader"></div> */
        .loader {
            width: 50px;
            padding: 8px;
            aspect-ratio: 1;
            border-radius: 50%;
            background: #264653;
            --_m:
                conic-gradient(#0000 10%, #000),
                linear-gradient(#000 0 0) content-box;
            -webkit-mask: var(--_m);
            mask: var(--_m);
            -webkit-mask-composite: source-out;
            mask-composite: subtract;
            animation: l3 1s infinite linear;
        }

        @keyframes l3 {
            to {
                transform: rotate(1turn)
            }
        }
    </style>
</head>

<body>
    <div class="loader"></div>
</body>

</html>