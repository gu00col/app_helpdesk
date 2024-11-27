<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Inicia a sessão
// Importa o arquivo de conexão
require_once './cnx.php';

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
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
        if ($usuario) {
            // Define a mensagem de erro na sessão
            $_SESSION['alerta'] = 'Usuário já existe no sistema!';
            // Redireciona para index.php com mensagem de erro se o e-mail ou a senha estiverem incorretos
            header("Location: ../registrar.php");
            exit();
        };

        $password_hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, senha, email)
                                VALUES (:nome, :senha, :email);");
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':senha', $password_hash, PDO::PARAM_STR);
        $stmt->execute();
        $_SESSION['alerta'] = 'Usuário criado com sucesso!';
        $_SESSION['alerta_tag'] = 'success';
        header("Location: ../index.php");
        exit();
        
        
    } catch (PDOException $e) {
        echo "Erro ao executar a consulta: " . $e->getMessage();
    }

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