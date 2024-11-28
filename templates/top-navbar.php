<?php
// Converte a primeira letra de cada palavra para maiúscula 
$nomeFormatado = $_SESSION['usuario']['nome'] ? ucwords($_SESSION['usuario']['nome']) : '';
// Quebra a string em um array usando o espaço como delimitador 
$partesNome = explode(' ', $nomeFormatado);
// Verifica se há mais de uma parte 
if (count($partesNome) > 1) {
    // Pega a primeira e a última parte do nome 
    $nomeSobrenome = $partesNome[0] . ' ' . end($partesNome);
} else {
    // Se há apenas uma parte, usa apenas essa parte 
    $nomeSobrenome = $partesNome[0];
}
require_once('./config.php')
?>
<nav
    class="navbar navbar-expand-sm navbar-dark bg-primario">
    <div class="container">
        <a class="navbar-brand" href="home.php"><i class="bi bi-headset texto-laranja"></i> Helpdesk</a>
        <button
            class="navbar-toggler d-lg-none"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#collapsibleNavId"
            aria-controls="collapsibleNavId"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="home.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Chamados
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="abrir_chamado.php"><i class="bi bi-pencil-square"></i> Novo Chamado</a></li>
                        <li><a class="dropdown-item" href="consultar_chamados.php"><i class="bi bi-search"></i> Listar Chamados</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $nomeSobrenome; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <?php 
                        if(isset($HOMOLOG) and !$HOMOLOG){
                            echo '<li><a class="dropdown-item" href="./alterar_senha.php"><i class="bi bi-shield-lock"></i> Alterar Senha</a></li>';
                        }
                        ?>
                        <li><a class="dropdown-item" href="./funcs/deslogar.php"><i class="bi bi-box-arrow-left"></i> Sair</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>