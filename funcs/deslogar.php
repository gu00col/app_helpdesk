<?php
session_start(); // Inicia a sessão
unset($_SESSION['usuario']);
unset($_SESSION['alerta']);
header("Location: ../index.php");
exit();
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