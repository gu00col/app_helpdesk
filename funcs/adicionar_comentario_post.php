<?php 
require_once 'valida_sessao.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Importa o arquivo de conexão
require_once './cnx.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// id,chamado_id,usuario_id,comentario,criado_em
$chamado_id = $_POST['chamado_id'];
$usuario_id = $_SESSION['usuario']['id'];
$comentario = $_POST['descricao'];
$criado_em = date("Y-m-d H:i:s");

if (empty(trim($comentario))) {
    // Define a mensagem de erro na sessão
    $_SESSION['alerta'] = 'Descrição Obrigatória!';
    $_SESSION['alerta_tag'] = 'warning';
    // Redireciona para abrir_chamado.php com mensagem de erro
    header("Location: ../ver_chamado.php?id=$chamado_id");
    exit();
};

try {
    // Prepara a consulta SQL para evitar injeção de SQL
    $stmt = $pdo->prepare("INSERT INTO chamado_comentarios
                                    (chamado_id, usuario_id, comentario, criado_em)
                                    VALUES(:chamado_id, :usuario_id, :comentario, :criado_em);");
    $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
    $stmt->bindParam(':criado_em', $criado_em, PDO::PARAM_STR);
    $stmt->execute();

    // Obtém a última ID autoincrementada inserida 
    $comentario_id = $pdo->lastInsertId();

} catch (PDOException $e) {
    echo "Erro ao executar a consulta: " . $e->getMessage();
}

if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
    // O arquivo foi enviado com sucesso
    $diretorioDestino = '../chamado_arquivos/';
    $nomeArquivo = basename($_FILES['arquivo']['name']);
    $caminhoArquivo = $diretorioDestino . $nomeArquivo;

    if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminhoArquivo)) {
        $_SESSION['alerta'] = 'Erro ao mover o arquivo para o diretório de destino.';
        $_SESSION['alerta_tag'] = 'error';
        $stmt = $pdo->prepare("DELETE FROM chamado_comentarios WHERE id=:comentario_id;");
        $stmt->bindParam(':comentario_id', $comentario_id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: ../ver_chamado.php?id=$chamado_id");
        exit();
    };
    try {
        // Prepara a consulta SQL para evitar injeção de SQL
    $stmt = $pdo->prepare("INSERT INTO chamado_arquivos
        (chamado_id, url, criado_em, abertura,comentario_id)
        VALUES(:chamado_id, :caminhoArquivo, :criado_em, 0, :comentario_id);");

    $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
    $stmt->bindParam(':caminhoArquivo', $nomeArquivo, PDO::PARAM_STR);
    $stmt->bindParam(':criado_em', $criado_em, PDO::PARAM_STR);
    $stmt->bindParam(':comentario_id', $comentario_id, PDO::PARAM_INT);
    $stmt->execute();
    
    } catch (PDOException $e) {
        echo "Erro ao executar a consulta: " . $e->getMessage();
    }
};


$_SESSION['alerta'] = 'Comentario criado com sucesso.';
$_SESSION['alerta_tag'] = 'success';
header("Location: ../ver_chamado.php?id=$chamado_id");
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