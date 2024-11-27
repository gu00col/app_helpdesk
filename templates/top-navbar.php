<?php 
// Converte a primeira letra de cada palavra para maiúscula 
$nomeFormatado = $_SESSION['usuario']['nome'] ? ucwords($_SESSION['usuario']['nome']) : ''; 
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
            <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link"><?php echo $nomeSobrenome; ?></a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <p class="nav-link mb-0">|</p>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./funcs/deslogar.php"><i class="bi bi-box-arrow-left"></i> Deslogar</a>
                </li>
            </ul>
        </div>
    </div>
</nav>