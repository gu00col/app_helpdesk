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
    <main class=" h-100">
        <div class="container h-100">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-center align-items-center ">
                    <div class="w-md-50 w-sm-100 mt-5 rounded-4">

                        <?php
                        // Exibe a mensagem de erro se existir
                        if (isset($_SESSION['alerta'])) {
                        echo '<div class="alert alert-'. $_SESSION['alerta_tag'] . ' " role="alert" id="erros">
                            ' . $_SESSION['alerta'] . '
                        </div>';
                        // Remove a mensagem de erro da sessão
                        unset($_SESSION['alerta']);
                        }
                        ?>

                        <form action="./funcs/alterar_senha_post.php" class="" method="post">

                            <div class="text-center mb-5">
                                <h2 class="h1"><i class="bi bi-headset texto-laranja"></i></h2>
                                <h3>Helpdesk</h3>
                                <p class="fw-5">Altere a senha.</p>
                            </div>

                            <div class="form-floating mb-3">
                                <input
                                    type="password"
                                    class="form-control"
                                    name="senha"
                                    id="senha" required
                                    placeholder="Digite a nova senha" />
                                <label for="senha">Nova senha</label>
                            </div>

                            <button
                                type="submit"
                                class="btn botao-primario w-100 btn-lg mt-3">
                                <i class="bi bi-shield-lock"></i> Alterar senha
                            </button>

                        </form>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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