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
                            echo '<div class="alert alert-'. $_SESSION['alerta_tag'] . ' mt-4 mb-0" role="alert" id="erros">
                                        ' . $_SESSION['alerta'] . '
                                        </div>';
                            // Remove a mensagem de erro da sessão
                            unset($_SESSION['alerta']);
                        }
                        // echo $_SESSION['usuario']['atendente'];
                        ?>
                    <div class="card mt-md-5 mt-3">
                        
                        <div class="card-header bg-primario texto-laranja fs-5"><i class="bi bi-menu-button-fill"></i> Menu</div>
                        <div class="card-body mt-4 mb-4">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a class="btn botao-primario me-md-5 btn fs-4" href="abrir_chamado.php"><i class="bi bi-pencil-square"></i> Novo Chamado</a>
                                <a class="btn botao-primario btn fs-4" href="consultar_chamados.php"><i class="bi bi-search"></i> Listar Chamados</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
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