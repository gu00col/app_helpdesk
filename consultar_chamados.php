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
                        echo '<div class="alert alert-' . $_SESSION['alerta_tag'] . ' mt-4 mb-0" role="alert" id="erros">
                                        ' . $_SESSION['alerta'] . '
                                        </div>';
                        // Remove a mensagem de erro da sessão
                        unset($_SESSION['alerta']);
                    }
                    ?>
                    <?php
                    // Importa o arquivo de conexão
                    require './funcs/cnx.php';

                    $status = isset($_GET['status']) ? $_GET['status'] : '';
                    $atendente_id = $_SESSION['usuario']['id'];

                    try {
                        if ((isset($_SESSION['usuario']['adm']) && $_SESSION['usuario']['adm'])) {

                            // É administrador
                            if ($status !== '') {
                                $stmt = $pdo->prepare("SELECT c.*, u.nome as atendente_nome from chamados c 
                                left join usuarios u on u.id = c.atendente_id WHERE status = :status_param and deletado_em is null;");
                                $stmt->bindParam(':status_param', $status, PDO::PARAM_STR);
                            } else {
                                $stmt = $pdo->prepare("SELECT c.*, u.nome as atendente_nome from chamados c 
                                left join usuarios u on u.id = c.atendente_id where deletado_em is null;");
                            }
                            $stmt->execute();
                            $chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } else if ((isset($_SESSION['usuario']['adm']) && !$_SESSION['usuario']['adm']) and (isset($_SESSION['usuario']['atendente']) && $_SESSION['usuario']['atendente'])
                        ) {

                            // É atendente
                            if ($status !== '') {
                                $stmt = $pdo->prepare("SELECT c.*, u.nome as atendente_nome from chamados c 
                                    left join usuarios u on u.id = c.atendente_id WHERE status = :status_param and deletado_em is null;");
                                $stmt->bindParam(':status_param', $status, PDO::PARAM_STR);
                            } else {
                                $stmt = $pdo->prepare("SELECT c.*, u.nome as atendente_nome from chamados c 
                                left join usuarios u on u.id = c.atendente_id where deletado_em is null;");
                            }
                            $stmt->execute();
                            $chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } else if ((isset($_SESSION['usuario']['usuario']) && $_SESSION['usuario']['usuario']) && (isset($_SESSION['usuario']['adm']) && !$_SESSION['usuario']['adm'])  && (isset($_SESSION['usuario']['atendente']) && !$_SESSION['usuario']['atendente'])) {
                            // É usuário
                            $usuario_id = $_SESSION['usuario']['id'];
                            if ($status !== '') {
                                $stmt = $pdo->prepare("SELECT c.*, u.nome as atendente_nome from chamados c 
                                            left join usuarios u on u.id = c.atendente_id WHERE status = :status_param AND usuario_id = :usuario_id and deletado_em is null;");
                                $stmt->bindParam(':status_param', $status, PDO::PARAM_STR);
                                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                            } else {
                                $stmt = $pdo->prepare("SELECT c.*, u.nome as atendente_nome from chamados c 
                                left join usuarios u on u.id = c.atendente_id WHERE usuario_id = :usuario_id and deletado_em is null;");
                                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                            }
                            $stmt->execute();
                            $chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        }
                    } catch (PDOException $e) {
                        echo "Erro ao executar a consulta: " . $e->getMessage();
                    };
                    ?>
                    <div class="mt-3 text-start d-flex d-flex justify-content-between align-items-center">
                        <div>
                            <a
                                href="home.php"
                                class="btn botao-primario">
                                <i class="bi bi-arrow-bar-left"></i> Voltar
                            </a>
                        </div>
                        <a
                            href="abrir_chamado.php"
                            class="btn botao-primario">
                            <i class="bi bi-pencil-square"></i> Abrir chamado
                        </a>
                    </div>
                    <div class="card mt-md-3 mt-2">
                        <div class="card-header bg-primario texto-laranja d-block d-md-flex justify-content-between align-items-center">
                            <div class="fs-5">
                                <i class="bi bi-ticket-detailed-fill"></i> Chamados
                            </div>
                            <div>
                                <select class="form-select" id="status_menu_select">
                                    <option value="consultar_chamados.php" selected>Todos</option>
                                    <option value="consultar_chamados.php?status=aberto">Abertos</option>
                                    <option value="consultar_chamados.php?status=pendente">Pendentes</option>
                                    <option value="consultar_chamados.php?status=fechado">Fechados</option>
                                    <option value="consultar_chamados.php?status=resolvido">Resolvidos</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($chamados) {


                            echo '<div class="table-responsive">
                        <table class="table table-striped table-hover table-responsive">
                                <thead class="text-center">
                                    <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Titulo</th>
                                    <th scope="col">Atribuido</th>
                                    <th scope="col">Categoria</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">';
                        ?>
                            <?php
                            foreach ($chamados as $index => $value) {
                                $mt_cards = $index = 0 ? '' : 'mt-1';
                                $status_badge = 'secondary';
                                if ($value['status'] == 'pendente') {
                                    $status_badge = 'warning';
                                } else if ($value['status'] == 'aberto') {
                                    $status_badge = 'info';
                                } else if ($value['status'] == 'fechado') {
                                    $status_badge = 'dark';
                                } else if ($value['status'] == 'resolvido') {
                                    $status_badge = 'success';
                                };
                                // Converte a primeira letra de cada palavra para maiúscula 
                                $nomeFormatado = $value['atendente_nome'] ? ucwords($value['atendente_nome']) : ''; 
                                // Quebra a string em um array usando o espaço como delimitador 
                                $partesNome = explode(' ', $nomeFormatado); 
                                // Verifica se há mais de uma parte 
                                if (count($partesNome) > 1) { 
                                    // Pega a primeira e a última parte do nome 
                                    $nomeSobrenome = $partesNome[0] . ' ' . end($partesNome); }
                                    else { 
                                        // Se há apenas uma parte, usa apenas essa parte 
                                        $nomeSobrenome = $partesNome[0]; 
                                    }
                                echo ' <tr>
                                    <th scope="row">' . $value['id'] . '</th>
                                    <td>' . ucfirst($value['titulo']) . '</td>
                                    <td>' . ucfirst($nomeSobrenome) . '</td>
                                    <td>' . $value['categoria'] . '</td>
                                    <td><span class="badge text-bg-' . $status_badge . ' ">' . strtoupper($value['status']) . '</span></td>
                                    <td>
                                    <a href="ver_chamado.php?id=' . $value['id'] . '" class="btn botao-transparente ">
                                                <i class="bi bi-eye"></i>
                                     </a>
                                    </td>
                                    </tr>';
                            };
                            ?>
                        <?php
                            echo '</tbody>
                            </table>
                            </div>';
                        } else {
                            echo '<p class="fs-6">Nenhum chamado encontrado.</p>';
                        };
                        ?>

                    </div>


                </div>

            </div>
        </div>
        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectMenu = document.getElementById('status_menu_select');

            selectMenu.addEventListener('change', function() {
                const selectedValue = selectMenu.value;
                window.location.href = selectedValue;
            });

            // Verifica o parâmetro da URL e seleciona a opção correta
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status') || 'todos';
            selectMenu.value = status === 'todos' ? 'consultar_chamados.php' : `consultar_chamados.php?status=${status}`;
        });
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
    </script>
</body>

</html>