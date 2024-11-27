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
    <script src="https://cdn.ckeditor.com/4.25.0-lts/standard/ckeditor.js"></script>
</head>

<body style="height: calc(100vh - 93px);" class="">
    <header>
        <?php include('./templates/top-navbar.php'); ?>
    </header>
    <main class=" h-100  ">
        <div class="container h-100">
            <div class="row">
                <div class="col-md-12 ">
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
                    <div class="mt-3 text-start">
                        <a
                            href="home.php"
                            class="btn botao-primario">
                            <i class="bi bi-arrow-bar-left"></i> Voltar
                        </a>
                    </div>
                    <div class="card mt-md-3 mt-3">
                        <div class="card-header bg-primario texto-laranja fs-5"><i class="bi bi-pencil-square"></i> Abertura de chamado</div>
                        <div class="card-body mt-4 mb-4">
                            <form action="./funcs/abrir_chamado_post.php" method="POST" id="form_abrir_chamado" enctype="multipart/form-data">
                                <div class="form-floating mb-3">
                                    <input
                                        type="text"
                                        class="form-control"
                                        name="titulo"
                                        id="titulo"
                                        placeholder="" required>
                                    <label for="titulo">Titulo</label>
                                </div>
                                <div class="mb-3">
                                    <label for="categoria" class="form-label">Categoria</label>
                                    <select
                                        class="form-select form-select-lg"
                                        name="categoria"
                                        id="categoria">
                                        <option selected value="default">Selecione uma categoria</option>
                                        <option value="bug">Bugs/Erros</option>
                                        <option value="duvida">Dúvida</option>
                                        <option value="financeiro">Financeiro</option>
                                    </select>
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

                                <div class="mb-3">
                                    <label for="editor" class="form-label">Descrição</label>
                                    <textarea name="descricao" id="editor" rows="10" cols="80" required></textarea>
                                </div>
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                    <button class="btn botao-primario me-md-2 " type="submit"><i class="bi bi-send-plus-fill"></i> Abrir chamado</button>
                                    <button class="btn btn-secondary" type="reset"><i class="bi bi-trash3-fill"></i> Limpar</button>
                                </div>

                            </form>
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
    </script>

</body>

</html>