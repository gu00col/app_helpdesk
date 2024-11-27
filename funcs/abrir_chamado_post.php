<?php
require_once 'valida_sessao.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Importa o arquivo de conexão
require_once './cnx.php';

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $categoria = $_POST['categoria'];
    $descricao = $_POST['descricao'];
    $criado_em = date("Y-m-d H:i:s");
    $usuario_id = $_SESSION["usuario"]["id"];



    if (empty(trim($titulo))) {
        // Define a mensagem de erro na sessão
        $_SESSION['alerta'] = 'Titulo é Obrigatório!';
        $_SESSION['alerta_tag'] = 'warning';
        // Redireciona para abrir_chamado.php com mensagem de erro
        header("Location: ../abrir_chamado.php");
        exit();
    };


    if ($categoria == 'default') {
        // Define a mensagem de erro na sessão
        $_SESSION['alerta'] = 'Selecione uma categoria!';
        $_SESSION['alerta_tag'] = 'warning';

        header("Location: ../abrir_chamado.php");
        exit();
    };

    if (empty(trim($descricao))) {
        // Define a mensagem de erro na sessão
        $_SESSION['alerta'] = 'Descrição Obrigatória!';
        $_SESSION['alerta_tag'] = 'warning';
        // Redireciona para abrir_chamado.php com mensagem de erro
        header("Location: ../abrir_chamado.php");
        exit();
    };

    try {
        // Prepara a consulta SQL para evitar injeção de SQL
        $stmt = $pdo->prepare("INSERT INTO chamados
                                        (titulo, categoria, status, descricao, criado_em, usuario_id)
                                        VALUES(:titulo, :categoria, 'pendente', :descricao, :criado_em, :usuario_id);");
        $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':criado_em', $criado_em, PDO::PARAM_STR);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();

        // Obtém a última ID autoincrementada inserida 
        $chamado_id = $pdo->lastInsertId();
    } catch (PDOException $e) {
        echo "Erro ao executar a consulta: " . $e->getMessage();
    }


    if (isset($_FILES['arquivo']) && $_FILES['arquivo']['error'] === UPLOAD_ERR_OK) {
        // O arquivo foi enviado com sucesso
        $diretorioDestino = '../chamado_arquivos/'.$chamado_id.'/';
        // Verifica se o diretório existe 
        if (!is_dir($diretorioDestino)) 
        { // Cria o diretório, incluindo diretórios pais se necessário 
            mkdir($diretorioDestino, 0777, true);
        }

        $nomeArquivo = basename($_FILES['arquivo']['name']);
        $caminhoArquivo = $diretorioDestino . $nomeArquivo;

        if (!move_uploaded_file($_FILES['arquivo']['tmp_name'], $caminhoArquivo)) {
            $_SESSION['alerta'] = 'Erro ao mover o arquivo para o diretório de destino.';
            $_SESSION['alerta_tag'] = 'error';
            $stmt = $pdo->prepare("DELETE FROM chamados WHERE id=:chamado_id;");
            $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
            $stmt->execute();
            header("Location: ../abrir_chamado.php");
            exit();
        };
        try {
            // Prepara a consulta SQL para evitar injeção de SQL
            $stmt = $pdo->prepare("INSERT INTO chamado_arquivos
                                    (chamado_id, url, criado_em, abertura)
                                    VALUES(:chamado_id, :caminhoArquivo, :criado_em, 1);");
            $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
            $stmt->bindParam(':caminhoArquivo', $nomeArquivo, PDO::PARAM_STR);
            $stmt->bindParam(':criado_em', $criado_em, PDO::PARAM_STR);
            $stmt->execute();
            
        } catch (PDOException $e) {
            echo "Erro ao executar a consulta: " . $e->getMessage();
        };
    };


    $_SESSION['alerta'] = 'Chamado criado com sucesso.';
    $_SESSION['alerta_tag'] = 'success';
    header("Location: ../consultar_chamados.php");
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