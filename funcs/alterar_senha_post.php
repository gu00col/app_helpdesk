<?php 
require_once 'valida_sessao.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Importa o arquivo de conexão
require_once './cnx.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// id,chamado_id,usuario_id,comentario,criado_em
$usuario_id = $_SESSION['usuario']['id'];
$senha = $_POST['senha'];
$password_hash = password_hash($senha, PASSWORD_DEFAULT);
echo $password_hash;
try {
    // Prepara a consulta SQL para evitar injeção de SQL
    $stmt = $pdo->prepare("UPDATE usuarios
                    SET senha=:senha WHERE id=:usuario_id;");
    $stmt->bindParam(':senha', $password_hash, PDO::PARAM_STR);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();


} catch (PDOException $e) {
    echo "Erro ao executar a consulta: " . $e->getMessage();
}
unset($_SESSION['usuario']);
$_SESSION['alerta'] = 'Senha alterada com sucesso.';
$_SESSION['alerta_tag'] = 'success';
header("Location: ../index.php");
exit();



};
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