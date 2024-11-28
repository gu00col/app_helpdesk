<?php
require_once 'valida_sessao.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Importa o arquivo de conexão
require_once './cnx.php';

$chamado_id = isset($_GET['id']) ? $_GET['id'] : null;
if ($chamado_id == null) {
    $_SESSION['alerta'] = 'Chamado não existe.';
    $_SESSION['alerta_tag'] = 'warning';
    header("Location: ../consultar_chamados.php");
    exit();
};
$data_agora = date("Y-m-d H:i:s");
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $usuario_id = $_SESSION['usuario']['id'];
    

    if (isset($_GET['acao']) && $_GET['acao'] == 'assumir_chamado') {

        if ($_SESSION['usuario']['adm'] || $_SESSION['usuario']['atendente']) {
            try {
                $stmt = $pdo->prepare("UPDATE chamados SET status='aberto', atendente_id=:usuario_id , atualizado_em=:data_agora WHERE id=:chamado_id;");
                $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_STR);
                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt->bindParam(':data_agora', $data_agora, PDO::PARAM_STR);
                $stmt->execute();
                
                $comentario = $_SESSION['usuario']['nome'] . ' Assumiu o chamado.';

                $stmt = $pdo->prepare("INSERT INTO chamado_comentarios
                                    (chamado_id, usuario_id, comentario, criado_em)
                                    VALUES(:chamado_id, :usuario_id, :comentario, :criado_em);");
                $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
                $stmt->bindParam(':criado_em', $data_agora, PDO::PARAM_STR);
                $stmt->execute();





                $_SESSION['alerta'] = 'Chamado atribuído com sucesso.';
                $_SESSION['alerta_tag'] = 'success';
                header("Location: ../ver_chamado.php?id=$chamado_id");
                exit();
            } catch (PDOException $e) {
                echo "Erro ao executar a consulta: " . $e->getMessage();
            }
            
        };
    };
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario']['id'];

    if (isset($_POST['acao']) && $_POST['acao'] == 'alterar_status') {
        if ($_SESSION['usuario']['adm'] || $_SESSION['usuario']['atendente']) {
            $status = $_POST['status'];
            $deletado_em = null;
            if ($status == 'deletado'){
                $deletado_em = $data_agora;
            };

            $comentario = $_SESSION['usuario']['nome'] . ', Alterou o status do chamado para ' . $status . ' <br><strong>Motivo:</strong><br>' . $_POST['motivo'];

            try {
                $stmt = $pdo->prepare("UPDATE chamados SET status=:status_param, atendente_id=:usuario_id , atualizado_em=:atualizado_em, deletado_em=:deletado_em WHERE id=:chamado_id;");
                $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_STR);
                $stmt->bindParam(':status_param', $status, PDO::PARAM_STR);
                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt->bindParam(':atualizado_em', $data_agora, PDO::PARAM_STR);
                $stmt->bindParam(':deletado_em', $deletado_em, PDO::PARAM_STR);
                $stmt->execute();
                

                $stmt = $pdo->prepare("INSERT INTO chamado_comentarios
                                    (chamado_id, usuario_id, comentario, criado_em)
                                    VALUES(:chamado_id, :usuario_id, :comentario, :criado_em);");
                $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
                $stmt->bindParam(':criado_em', $data_agora, PDO::PARAM_STR);
                $stmt->execute();

                $_SESSION['alerta'] = 'Status alterado para '. $status . ' com sucesso.';
                $_SESSION['alerta_tag'] = 'success';
                header("Location: ../consultar_chamados.php");
                exit();
            } catch (PDOException $e) {
                echo "Erro ao executar a consulta: " . $e->getMessage();
            }
        }else{
            $usuario_id = $_SESSION['usuario']['id'];
            $data_agora = date("Y-m-d H:i:s");

            $stmt = $pdo->prepare("SELECT * FROM chamados WHERE id = :chamado_id;");
            $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_STR);
            $stmt->execute();
            $chamado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($chamado && $chamado['usuario_id'] == $usuario_id){
                
                $comentario = $_SESSION['usuario']['nome'] . ', Alterou o status do chamado para ' . 'fechado' . ' <br><strong>Motivo:</strong><br>' . $_POST['motivo'];
                $status = 'fechado';

                try {
                    $stmt = $pdo->prepare("UPDATE chamados SET status=:status_param, atendente_id=:usuario_id , atualizado_em=:atualizado_em WHERE id=:chamado_id;");
                    $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
                    $stmt->bindParam(':status_param', $status, PDO::PARAM_STR);
                    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                    $stmt->bindParam(':atualizado_em', $data_agora, PDO::PARAM_STR);
                    $stmt->execute();
                    
    
                    $stmt = $pdo->prepare("INSERT INTO chamado_comentarios
                                        (chamado_id, usuario_id, comentario, criado_em)
                                        VALUES(:chamado_id, :usuario_id, :comentario, :criado_em);");
                    $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
                    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                    $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
                    $stmt->bindParam(':criado_em', $data_agora, PDO::PARAM_STR);
                    $stmt->execute();
    
                    $_SESSION['alerta'] = 'Status alterado para '. $status . ' com sucesso.';
                    $_SESSION['alerta_tag'] = 'success';
                    header("Location: ../consultar_chamados.php");
                    exit();
                } catch (PDOException $e) {
                    echo "Erro ao executar a consulta: " . $e->getMessage();
                }
            }
        }
    } else if (isset($_POST['acao']) && $_POST['acao'] == 'atribuir_chamado'){
        $usuario_id = $_SESSION['usuario']['id'];
        $data_agora = date("Y-m-d H:i:s");
        $atendente = $_POST["atendente"];
        $status = 'aberto';

        $stmt = $pdo->prepare("SELECT * from usuarios u 
        where u.id = :atendente_id;");
        $stmt->bindParam(':atendente_id', $atendente, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $usuario_nome = $usuario ? $usuario['nome'] : '';
        
        if ($_SESSION['usuario']['adm'] || $_SESSION['usuario']['atendente']) {
            try {
                $stmt = $pdo->prepare("UPDATE chamados SET status=:status_param, atendente_id=:usuario_id , atualizado_em=:atualizado_em WHERE id=:chamado_id;");
                $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
                $stmt->bindParam(':status_param', $status, PDO::PARAM_STR);
                $stmt->bindParam(':usuario_id', $atendente, PDO::PARAM_INT);
                $stmt->bindParam(':atualizado_em', $data_agora, PDO::PARAM_STR);
                $stmt->execute();

                $comentario = $_SESSION['usuario']['nome'] . ', Atribuiu o chamado para  ' . $usuario_nome;

                $stmt = $pdo->prepare("INSERT INTO chamado_comentarios
                                    (chamado_id, usuario_id, comentario, criado_em)
                                    VALUES(:chamado_id, :usuario_id, :comentario, :criado_em);");
                $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_INT);
                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt->bindParam(':comentario', $comentario, PDO::PARAM_STR);
                $stmt->bindParam(':criado_em', $data_agora, PDO::PARAM_STR);
                $stmt->execute();

                $_SESSION['alerta'] = 'Chamado atribuido com sucesso.';
                $_SESSION['alerta_tag'] = 'success';
                header("Location: ../consultar_chamados.php");
                exit();
            } catch (PDOException $e) {
                echo "Erro ao executar a consulta: " . $e->getMessage();
            }
        }

    }
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