<?php
require_once './funcs/valida_sessao.php';
?>
<!doctype html>
<html lang="pt-Br">

<head>
    <title>Helpdesk</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <?php
    include_once('./funcs/scripts_head.php');
    ?>

    <style>
        .nav-pills .nav-link.active,
        .nav-pills .show>.nav-link {
            color: #264653 !important;
            background-color: #f4a261 !important;
        }

        .nav {
            --bs-nav-link-padding-x: 1rem;
            --bs-nav-link-padding-y: 0.5rem;
            --bs-nav-link-font-weight: ;
            --bs-nav-link-color: #f4a261;
            --bs-nav-link-hover-color: #e9c46a;
            --bs-nav-link-disabled-color: var(--bs-secondary-color);
            display: flex;
            flex-wrap: wrap;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
        }
    </style>


</head>

<body style="height: calc(100vh - 93px);" class="">
    <header>
        <?php include('./templates/top-navbar.php'); ?>
    </header>
    <main class=" h-100 ">
        <div class="container h-100">
            <div class="row">
                <div class="col-md-12">
                    <?php


                    // Exibe a mensagem de erro se existir
                    if (isset($_SESSION['alerta'])) {
                        echo '<div class="alert alert-' . $_SESSION['alerta_tag'] . '  mt-4 mb-0" role="alert" id="erros">
                                        ' . $_SESSION['alerta'] . '
                                        </div>';
                        // Remove a mensagem de erro da sessão
                        unset($_SESSION['alerta']);
                    }
                    ?>
                    <?php
                    // Importa o arquivo de conexão
                    require './funcs/cnx.php';

                    $chamado_id = isset($_GET['id']) ? $_GET['id'] : null;
                    if ($chamado_id == null) {
                        $_SESSION['alerta'] = 'Chamado não existe.';
                        $_SESSION['alerta_tag'] = 'warning';
                        header("Location: ./consultar_chamados.php");
                        exit();
                    };

                    $atendente_id = $_SESSION['usuario']['id'];

                    try {
                        if ((isset($_SESSION['usuario']['adm']) && $_SESSION['usuario']['adm']) || (isset($_SESSION['usuario']['atendente']) && $_SESSION['usuario']['atendente'])) {


                            $stmt = $pdo->prepare("SELECT * FROM chamados WHERE id = :chamado_id;");
                            $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_STR);
                            $stmt->execute();
                            $chamado = $stmt->fetch(PDO::FETCH_ASSOC);
                        } else if ((isset($_SESSION['usuario']['usuario']) && $_SESSION['usuario']['usuario']) && (isset($_SESSION['usuario']['adm']) && !$_SESSION['usuario']['adm'])  && (isset($_SESSION['usuario']['atendente']) && !$_SESSION['usuario']['atendente'])) {
                            // É usuário
                            $usuario_id = $_SESSION['usuario']['id'];

                            $stmt = $pdo->prepare("SELECT * FROM chamados WHERE id = :chamado_id AND usuario_id = :usuario_id;");
                            $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_STR);
                            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                            $stmt->execute();
                            $chamado = $stmt->fetch(PDO::FETCH_ASSOC);
                        };
                        if (!$chamado) {
                            $_SESSION['alerta'] = 'Você não pode acessar esse chamado ou ele não existe.';
                            $_SESSION['alerta_tag'] = 'warning';
                            header("Location: ./consultar_chamados.php");
                            exit();
                        };

                        $stmt = $pdo->prepare("SELECT * FROM chamado_arquivos ca WHERE chamado_id = :chamado_id and abertura = 1;");
                        $stmt->bindParam(':chamado_id', $chamado_id, PDO::PARAM_STR);
                        $stmt->execute();
                        $arquivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        echo "Erro ao executar a consulta: " . $e->getMessage();
                    };
                    ?>
                    <div class="mt-3 text-start d-flex d-flex justify-content-between align-items-center">
                        <div>
                            <a
                                href="consultar_chamados.php"
                                class="btn botao-primario">

                                <i class="bi bi-arrow-bar-left"></i> Voltar
                            </a>
                        </div>
                        <?php
                        if (
                            ($_SESSION['usuario']['adm'] || $_SESSION['usuario']['atendente']) &&
                            (
                                ($chamado['status'] == 'pendente' && $chamado['atendente_id'] == null) ||
                                ($chamado['status'] == 'aberto' && $chamado['atendente_id'] != $_SESSION['usuario']['id'])
                            )
                        ) {
                            echo '<div>
                            <a
                        href="./funcs/acoes_chamado.php?acao=assumir_chamado&id=' . $chamado['id'] . '"
                        class="btn botao-secundario">
                        Assumir chamado
                            </a>
                            </div>';
                        };


                        ?>
                    </div>
                    <div class="card mt-md-3 mt-2">
                        <?php
                        $status_badge = 'secondary';
                        if ($chamado['status'] == 'pendente') {
                            $status_badge = 'warning';
                        } else if ($chamado['status'] == 'aberto') {
                            $status_badge = 'info';
                        } else if ($chamado['status'] == 'fechado') {
                            $status_badge = 'dark';
                        } else if ($chamado['status'] == 'resolvido') {
                            $status_badge = 'success';
                        };
                        ?>
                        <div class="card-header bg-primario texto-laranja  d-md-flex justify-content-between align-items-center">
                            <div class="">
                                <i class="bi bi-ticket-detailed-fill"></i> Chamado: <?php echo $chamado['id']; ?> - <?php echo $chamado['titulo'] ?>
                            </div>
                            <div>
                            <?php echo '<span class="badge text-bg-info">' . strtoupper($chamado['categoria']) . '</span>' ?>
                            </div>
                            <div>
                                <?php echo '<span class="badge text-bg-' . $status_badge . '">' . strtoupper($chamado['status']) . '</span>' ?>
                            </div>
                            <?php
                            if ($chamado['status'] == 'pendente' || $chamado['status'] == 'aberto') {

                            ?>
                                <div>
                                    <div class="dropdown">
                                        <button class="btn botao-primario dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Ações
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php
                                            if ($_SESSION['usuario']['adm'] || $_SESSION['usuario']['atendente']) {
                                            ?>
                                                <li><a class="dropdown-item" href="#" onclick="atribuirChamado()">Atribuir chamado</a></li>
                                                <?php
                                                if ($chamado['status'] == 'aberto') {
                                                ?>
                                                    <li><a class="dropdown-item" href="#" onclick="alteraStatusChamado('resolvido')">Resolver</a></li>
                                                <?php
                                                }
                                                ?>
                                                <li><a class="dropdown-item" href="#" onclick="alteraStatusChamado('fechado')">Fechar</a></li>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if ($_SESSION['usuario']['adm']) {
                                            ?>
                                                <li><a class="dropdown-item" href="#" onclick="alteraStatusChamado('deletado')">Deletar</a></li>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            if ($_SESSION['usuario']['id'] == $chamado['usuario_id']) {
                                            ?>
                                                <li><a class="dropdown-item" href="#" onclick="alteraStatusChamado('fechado')">Cancelar</a></li>
                                            <?php
                                            }
                                            ?>

                                        </ul>
                                    </div>


                                </div>
                            <?php
                            };
                            ?>
                        </div>
                        <div class="bg-light pt-3 pb-3 ps-3 pe-3 d-md-flex justify-content-between align-items-center border-primario border-bottom">
                            <?php
                            // Suponha que $chamado['criado_em'] tem o valor '2024-11-25 13:28:01'
                            $timestamp = $chamado['criado_em'];

                            // Converter para o formato dd/mm/aa hh:mm
                            $dataFormatada = date('d/m/y H:i', strtotime($timestamp));

                            // Data e hora atuais para cálculo do tempo decorrido (substitua pelo valor real quando necessário)
                            $dataAtual = date("Y-m-d H:i:s"); // Exemplo
                            $dataAtualTimestamp = strtotime($dataAtual);

                            // Calcular o tempo decorrido
                            $tempoDecorridoSegundos = $dataAtualTimestamp - strtotime($timestamp);

                            // Função para calcular o tempo decorrido em um formato legível
                            function calcularTempoDecorrido($segundos)
                            {
                                $minutos = floor($segundos / 60);
                                $horas = floor($minutos / 60);
                                $dias = floor($horas / 24);
                                $meses = floor($dias / 30); // Aproximação
                                $anos = floor($meses / 12);

                                if ($anos > 0) {
                                    $meses = $meses % 12;
                                    $dias = $dias % 30;
                                    return "<span class=''>há $anos anos, $meses meses e $dias dias</span>";
                                } elseif ($meses > 0) {
                                    $dias = $dias % 30;
                                    return "<span class=''>há $meses meses e $dias dias</span>";
                                } elseif ($dias > 0) {
                                    return "<span class=''>há $dias dias</span>";
                                } elseif ($horas > 0) {
                                    return "<span class=''>há $horas horas</span>";
                                } else {
                                    return "<span class=''>há $minutos minutos</span>";
                                }
                            };
                            $tempoDecorrido = calcularTempoDecorrido($tempoDecorridoSegundos);
                            ?>
                            <div>
                                <p class="small mb-0 texto-primario">
                                    <?php
                                    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :usuario_id;");
                                    $stmt->bindParam(':usuario_id', $chamado['usuario_id'], PDO::PARAM_INT);
                                    $stmt->execute();
                                    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
                                    echo '<span class="fw-bold">Aberto por: </span> ' . $cliente['nome'];
                                    ?>
                                </p>
                            </div>
                            <div>
                                <p class="small mb-0 texto-primario">
                                    <?php
                                    if ($chamado['atendente_id']) {
                                        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :atendente_id;");
                                        $stmt->bindParam(':atendente_id', $chamado['atendente_id'], PDO::PARAM_INT);
                                        $stmt->execute();
                                        $atendente = $stmt->fetch(PDO::FETCH_ASSOC);
                                        if ($atendente) {
                                            echo '<span class="fw-bold">Atribuido para: </span> ' . $atendente['nome'];
                                        };
                                    };


                                    ?>
                                </p>
                            </div>
                            <div>
                                <p class="small mb-0 texto-primario "><?php echo '<span class="fw-bold">Criado em: </span>' . $dataFormatada; ?> <?php echo $tempoDecorrido; ?></p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div>
                                <p><?php echo $chamado['descricao'] ?></p>
                            </div>
                            <div class="mb-3">

                                <?php
                                $iconeArquivo = 'bi bi-archive-fill';
                                // Lista de extensões para diferentes tipos de arquivo
                                $extensoesImagem = ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'tiff', 'svg'];
                                $extensoesPDF = ['pdf'];
                                $extensoesWord = ['doc', 'docx'];
                                $extensoesExcel = ['xls', 'xlsx', 'xlsm'];
                                $extensoesTexto = ['txt'];
                                $extensoesZip = ['zip', 'rar', '7z', 'tar', 'gz'];

                                if ($arquivos) {
                                    echo '<h6>Anexo:</h6>';
                                    foreach ($arquivos as $arquivo) {
                                        if ($arquivo['abertura']) {
                                            // Dividir a URL do anexo para obter a extensão do arquivo
                                            $partesArquivo = explode(".", $arquivo['url']);
                                            $extensaoArquivo = end($partesArquivo);
                                            // Verificação das extensões
                                            if (in_array($extensaoArquivo, $extensoesImagem)) {
                                                $iconeArquivo = 'bi bi-file-earmark-image';
                                            } elseif (in_array($extensaoArquivo, $extensoesPDF)) {
                                                $iconeArquivo = 'bi bi-file-earmark-pdf';
                                            } elseif (in_array($extensaoArquivo, $extensoesWord)) {
                                                $iconeArquivo = 'bi bi-file-earmark-word';
                                            } elseif (in_array($extensaoArquivo, $extensoesExcel)) {
                                                $iconeArquivo = 'bi bi-file-earmark-excel';
                                            } elseif (in_array($extensaoArquivo, $extensoesTexto)) {
                                                $iconeArquivo = 'bi bi-file-earmark-text';
                                            } elseif (in_array($extensaoArquivo, $extensoesZip)) {
                                                $iconeArquivo = 'bi bi-file-earmark-zip';
                                            } else {
                                                $iconeArquivo = 'bi bi-file-earmark';
                                            }
                                            echo '<a class="btn fs-2" href="./chamado_arquivos/' . $chamado['id'] . '/' . $arquivo['url'] . '  " download="' . $arquivo['url'] . '"><i class=" ' . $iconeArquivo . ' "></i></a>';
                                        };
                                    };
                                };
                                ?>

                            </div>
                        </div>
                    </div>
                    <!-- Final CARD abertura -->

                    <?php
                    #consulta
                    $stmt = $pdo->prepare("SELECT
                                                        cc.id as comentario_id,
                                                        cc.chamado_id as chamado_id,
                                                        u.nome as usuario_nome,
                                                        u.id as usuario_id,
                                                        cc.comentario as comentario,
                                                        cc.criado_em as criado_em,
                                                        ca.url as anexo_url
                                                    FROM
                                                        chamado_comentarios cc
                                                    join usuarios u on
                                                        u.id = cc.usuario_id
                                                    left join chamado_arquivos ca on
                                                        ca.comentario_id = cc.id
                                                    where
                                                        cc.chamado_id = :chamado_id 
                                                    ORDER by cc.id asc;");
                    $stmt->bindParam(':chamado_id', $chamado['id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $chamadoComentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if ($chamadoComentarios) {
                    ?>
                        <!-- Card Comentarios -->
                        <div class="card mt-1">
                            <div class="card-header bg-primario texto-laranja "><i class="bi bi-chat-left-text"></i> Comentários</div>
                            <div class="card-body mb-3">
                                <div class="">

                                    <?php

                                    if ($chamadoComentarios) {
                                        foreach ($chamadoComentarios as $comentario) {

                                            // Suponha que $chamado['criado_em'] tem o valor '2024-11-25 13:28:01'
                                            $timestamp = $comentario['criado_em'];

                                            // Converter para o formato dd/mm/aa hh:mm
                                            $dataFormatada = date('d/m/y H:i', strtotime($timestamp));

                                            // Data e hora atuais para cálculo do tempo decorrido (substitua pelo valor real quando necessário)
                                            $dataAtual = date("Y-m-d H:i:s"); // Exemplo
                                            $dataAtualTimestamp = strtotime($dataAtual);

                                            // Calcular o tempo decorrido
                                            $tempoDecorridoSegundos = $dataAtualTimestamp - strtotime($timestamp);

                                            // Função para calcular o tempo decorrido em um formato legível
                                            $tempoDecorrido = calcularTempoDecorrido($tempoDecorridoSegundos);

                                            // Dividir a URL do anexo para obter a extensão do arquivo
                                            $partesArquivo = explode(".", $comentario['anexo_url']);
                                            $extensaoArquivo = end($partesArquivo);

                                            // Verificação das extensões
                                            if (in_array($extensaoArquivo, $extensoesImagem)) {
                                                $iconeArquivo = 'bi bi-file-earmark-image';
                                            } elseif (in_array($extensaoArquivo, $extensoesPDF)) {
                                                $iconeArquivo = 'bi bi-file-earmark-pdf';
                                            } elseif (in_array($extensaoArquivo, $extensoesWord)) {
                                                $iconeArquivo = 'bi bi-file-earmark-word';
                                            } elseif (in_array($extensaoArquivo, $extensoesExcel)) {
                                                $iconeArquivo = 'bi bi-file-earmark-excel';
                                            } elseif (in_array($extensaoArquivo, $extensoesTexto)) {
                                                $iconeArquivo = 'bi bi-file-earmark-text';
                                            } elseif (in_array($extensaoArquivo, $extensoesZip)) {
                                                $iconeArquivo = 'bi bi-file-earmark-zip';
                                            } else {
                                                $iconeArquivo = 'bi bi-file-earmark';
                                            }

                                    ?>

                                            <div class="border-bottom mb-2 pb-2">
                                                <div class="bg-light pt-2 pb-2 ps-3 pe-3 d-md-flex justify-content-between align-items-center border-primario border-bottom">
                                                    <div>
                                                        <p class="mb-0 small mb-0 texto-primario"><span class="fw-bold">Por: </span> <?php echo $comentario['usuario_nome']; ?> em: <?php echo $dataFormatada; ?> <?php echo $tempoDecorrido; ?> </p>
                                                    </div>
                                                    <div>
                                                        <?php

                                                        if ($comentario['anexo_url']) {
                                                            echo '<span class="fw-bold small">Anexo</span>';
                                                            echo '<a class="btn fs-5" href="./chamado_arquivos/' . $chamado['id'] . '/' . $comentario['anexo_url'] . ' " download="' . $arquivo['url'] . '"><i class=" ' . $iconeArquivo . ' "></i></a>';
                                                        };
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="mt-4 ms-3 mb-2">
                                                    <?php
                                                    echo $comentario['comentario'];
                                                    ?>
                                                </div>
                                            </div>

                                    <?php
                                        }
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php
                    };
                    ?>

                    <!-- Comentario -->
                    <?php
                    if (($chamado['atendente_id'] == $_SESSION['usuario']['id'] && $chamado['status'] == 'aberto')  || ($chamado['usuario_id'] == $_SESSION['usuario']['id'] and $chamado['status'] == 'aberto') || ($chamado['usuario_id'] == $_SESSION['usuario']['id'] and $chamado['status'] == 'pendente')) {
                    ?>
                        <div class="card mt-1">
                            <div class="card-header bg-primario texto-laranja "> <i class="bi bi-pencil-square"></i> Comentário</div>
                            <div class="card-body">
                                <form action="./funcs/adicionar_comentario_post.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="chamado_id" value="<?php echo $chamado['id']; ?>">

                                    <div class="mb-3">
                                        <label for="editor" class="form-label">Descrição</label>
                                        <textarea name="descricao" id="editor" rows="10" cols="80" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="arquivo" class="form-label">Arquivo</label>
                                        <input
                                            type="file"
                                            class="form-control"
                                            name="arquivo"
                                            id="arquivo"
                                            placeholder=""
                                            aria-describedby="fileHelpId" accept=".jpg,.jpeg,.png"/>
                                        <div id="fileHelpId" class="form-text">Opcional</div>
                                    </div>
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                        <button class="btn botao-primario me-md-2 " type="submit"><i class="bi bi-send-plus-fill"></i> Adicionar comentario</button>
                                        <button class="btn btn-secondary" type="reset"><i class="bi bi-trash3-fill"></i> Limpar</button>
                                    </div>


                                </form>
                            </div>
                        </div>
                    <?php
                    };
                    ?>
                </div>
            </div>
        </div>
    </main>
    <!-- Modal -->
    <div class="modal fade" id="alterar_status_modal" tabindex="-1" aria-labelledby="alterar_status_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="alterar_status_modalLabel">Altera chamado</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="./funcs/acoes_chamado.php?id=<?php echo $chamado['id'] ?>" method="post" id="form_alterar_status">
                        <input type="hidden" name="acao" value="alterar_status">
                        <input type="hidden" name="status" value="" id="formulario_acao">
                        <div class="mb-3">
                            <div class="mb-3">
                                <label for="motivo" class="form-label">Motivo</label>
                                <textarea name="motivo" id="motivo" rows="10" cols="80"></textarea>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn botao-primario" onclick="alterarStatus()">Gravar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="atribuir_chamado_modal" tabindex="-1" aria-labelledby="atribuir_chamado_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="atribuir_chamado_modalLabel">Atribuir chamado</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="./funcs/acoes_chamado.php?id=<?php echo $chamado['id'] ?>" method="post" id="form_atribuir_chamado">
                        <input type="hidden" name="acao" value="atribuir_chamado">
                       <div class="mb-3">
                        <?php 
                        $stmt = $pdo->prepare("SELECT * from usuarios u 
                        where u.atendente = 1 or u.adm = 1;");
                        $stmt->execute();
                        $lista_atendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($lista_atendentes){
                        ?>
                        
                        <label for="atendentes" class="form-label">Selecione o usuário</label>
                        <select
                            class="form-select"
                            name="atendente"
                            id="atendentes"
                        >
                            <?php 
                            foreach ($lista_atendentes as $key => $atendente) {
                               if ($atendente['id'] == $chamado['atendente_id']){
                                    echo '<option selected value="'.$atendente['id'].'">'.$atendente['nome'].'</option>';
                               } else {
                                echo '<option value="'.$atendente['id'].'">'.$atendente['nome'].'</option>';
                               }                               
                            }
                            ?>
                        </select>

                        <?php 
                        }
                        ?>
                       </div>
                       

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn botao-primario" onclick="btnAtribuirChamado()">Atribuir</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // JavaScript para fechar o alerta automaticamente após 10 segundos 
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alertElement = document.getElementById('erros');
                if (alertElement) {
                    alertElement.classList.add('fade');
                    setTimeout(function() {
                        alertElement.remove();
                    }, 150); // Tempo para a animação de fade out 
                }
            }, 5000); // 5000 milissegundos = 5 segundos 
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Função para pegar o valor de um parâmetro de consulta da URL
            function getQueryParam(param) {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get(param) || 'todos';
            }

            // Pega o status da URL ou define como 'todos'
            const status = getQueryParam('status');

            // Pega o elemento ul com id 'status_menu'
            const statusMenu = document.getElementById('status_menu');

            if (statusMenu) {
                // Itera sobre cada li e a dentro de status_menu
                const menuItems = statusMenu.querySelectorAll('li > a');
                menuItems.forEach(function(item) {
                    // Verifica se o data-status do item corresponde ao status da query
                    if (item.getAttribute('data-status') === status) {
                        // Remove a classe 'active' de todos os itens
                        menuItems.forEach(function(menuItem) {
                            menuItem.classList.remove('active');
                        });
                        // Adiciona a classe 'active' ao item correspondente
                        item.classList.add('active');
                    }
                });
            }
        });
        CKEDITOR.on('instanceReady', function(event) {
            delete event.editor.plugins.notification;
        });
        CKEDITOR.replace('editor', {
            on: {
                instanceReady: function(event) {
                    var editor = event.editor;
                    if (editor.plugins.notification) {
                        delete editor.plugins.notification;
                    }
                }
            },
            removePlugins: 'image, image2, easyimage, cloudservices'
        });
        CKEDITOR.replace('motivo', {
            on: {
                instanceReady: function(event) {
                    var editor = event.editor;
                    if (editor.plugins.notification) {
                        delete editor.plugins.notification;
                    }
                }
            },
            removePlugins: 'image, image2, easyimage, cloudservices'
        });


        function alteraStatusChamado(status) {
            const modalId = $('#alterar_status_modal')
            const inputAcao = $('#formulario_acao')

            inputAcao.val(status)
            modalId.modal('show')


        }

        function alterarStatus() {
            const formulario = $('#form_alterar_status')
            formulario.submit()
        }

        function atribuirChamado() {
            const modalId = $('#atribuir_chamado_modal')
            modalId.modal('show')

        }
        function btnAtribuirChamado() {
            const formulario = $('#form_atribuir_chamado')
            formulario.submit()
        }

    </script>
    <!-- Bootstrap JavaScript Libraries -->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>