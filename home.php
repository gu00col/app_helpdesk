<?php
require_once './funcs/valida_sessao.php';
$usuario_id = $_SESSION['usuario']['id'];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>


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
                    // echo $_SESSION['usuario']['atendente'];
                    ?>

                    <?php

                    // Importa o arquivo de conexão
                    require './funcs/cnx.php';
                    // Definindo o fuso horário
                    date_default_timezone_set('America/Sao_Paulo');

                    // Obtendo a data e hora atuais
                    $dataAtual = date('d/m/y H:i');

                    // Separando o dia, mês e ano
                    $dia = date('d');
                    $mes = date('m');
                    $ano = date('Y');


                    try {
                        $query = "SELECT
                                    count(id) as qtd
                                from
                                    chamados c
                                where
                                    c.usuario_id = :usuario_id
                                    and status = 'aberto';";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $seusChamadosAbertos = $stmt->fetch(PDO::FETCH_ASSOC);

                        $query = "SELECT
                                    count(id) as qtd
                                from
                                    chamados c
                                where
                                    c.usuario_id = :usuario_id
                                    and status = 'pendente';";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $seusChamadosPendente = $stmt->fetch(PDO::FETCH_ASSOC);

                        $query = "SELECT
                                    count(id) as qtd
                                from
                                    chamados c
                                where
                                    c.atendente_id = :usuario_id
                                    and status = 'aberto';";
                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $seusAtendimentosAbertos = $stmt->fetch(PDO::FETCH_ASSOC);

                        $query = "SELECT
                                    count(id) as qtd
                                from
                                    chamados c
                                where
                                    status = 'pendente';";

                        $stmt = $pdo->prepare($query);
                        $stmt->execute();
                        $AtendimentosPendente = $stmt->fetch(PDO::FETCH_ASSOC);

                        $query = "SELECT count(c.categoria) AS total, c.categoria
                                    FROM chamados c
                                    WHERE strftime('%Y', c.atualizado_em) = :ano
                                    AND strftime('%m', c.atualizado_em) = :mes
                                    AND c.status = 'aberto' or c.status = 'fechado' or c.status = 'resolvido' or c.status = 'pendente'
                                    GROUP BY c.categoria;";
                        // Dashboard

                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(':ano', $ano, PDO::PARAM_STR);
                        $stmt->bindParam(':mes', $mes, PDO::PARAM_STR);
                        $stmt->execute();
                        $categoriasMes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $categoriasMesJson = json_encode($categoriasMes);

                        $query = "SELECT
                                    count(c.id) as total,
                                    u.nome as atendente_nome
                                FROM
                                    chamados c
                                JOIN usuarios u ON
                                    c.atendente_id = u.id
                                WHERE
                                    c.status = 'resolvido'
                                    AND strftime('%Y', c.atualizado_em) = :ano
                                    AND strftime('%m', c.atualizado_em) = :mes
                                GROUP BY
                                    c.atendente_id
                                ORDER BY
                                    total DESC
                                LIMIT 5;

                                        ";

                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(':ano', $ano, PDO::PARAM_STR);
                        $stmt->bindParam(':mes', $mes, PDO::PARAM_STR);
                        $stmt->execute();
                        $resolvidosAtendenteMes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $resolvidosAtendenteMesJson = json_encode($resolvidosAtendenteMes);


                        $query = "SELECT
                                count(c.id) AS total,
                                u.nome AS cliente_nome
                            FROM
                                chamados c
                            JOIN usuarios u ON
                                c.usuario_id = u.id
                            WHERE
                                (c.status = 'resolvido' OR c.status = 'aberto' OR c.status = 'pendente')
                                AND strftime('%Y', c.atualizado_em) = :ano
                                AND strftime('%m', c.atualizado_em) = :mes
                            GROUP BY
                                u.nome
                            ORDER BY
                                total DESC
                            LIMIT 5;"
                            ;

                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(':ano', $ano, PDO::PARAM_STR);
                        $stmt->bindParam(':mes', $mes, PDO::PARAM_STR);
                        $stmt->execute();
                        $abertosClienteMes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        $abertosClienteMesJson = json_encode($abertosClienteMes);

                        $query = "SELECT 
                            ROUND(MIN((julianday(atualizado_em) - julianday(criado_em)) * 24 * 60)) AS menor_tma,
                            ROUND(AVG((julianday(atualizado_em) - julianday(criado_em)) * 24 * 60)) AS media_tma,
                            ROUND(MAX((julianday(atualizado_em) - julianday(criado_em)) * 24 * 60)) AS maior_tma
                            FROM 
                            chamados c
                            WHERE 
                            (c.status = 'resolvido' OR c.status = 'fechado')
                            and strftime('%Y', c.atualizado_em) = :ano
                            AND strftime('%m', c.atualizado_em) = :mes;
                        ";

                        $stmt = $pdo->prepare($query);
                        $stmt->bindParam(':ano', $ano, PDO::PARAM_STR);
                        $stmt->bindParam(':mes', $mes, PDO::PARAM_STR);
                        $stmt->execute();
                        $tmaMes = $stmt->fetch(PDO::FETCH_ASSOC);
                        $tmaMesJson = json_encode($tmaMes);
                    } catch (PDOException $e) {
                        echo "Erro ao executar a consulta: " . $e->getMessage();
                    }

                    ?>
                    <div class="row mt-3 d-none d-md-flex">

                        <div class="col-md-3">
                            <div class="card border-primario border-2  texto-primario cursor" onclick="irParaChamados('aberto')">
                                <div class="card-header h6 border-0 bg-transparent text-center">
                                    Meus chamados (Abertos)
                                </div>
                                <div class="card-body text-center">
                                    <h3 class="card-text"><?php echo $seusChamadosAbertos['qtd'];  ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-primario border-2  texto-primario cursor" onclick="irParaChamados('pendente')">
                                <div class="card-header h6 border-0 bg-transparent text-center">
                                    Meus chamados (Pendentes)
                                </div>
                                <div class="card-body text-center">
                                    <h3 class="card-text"><?php echo $seusChamadosPendente['qtd'];  ?></h3>
                                </div>

                            </div>
                        </div>
                        <?php
                        if ((isset($_SESSION["usuario"]["atendente"]) && $_SESSION["usuario"]["atendente"]) || (isset($_SESSION["usuario"]["adm"]) && $_SESSION["usuario"]["adm"])) {
                        ?>
                            <div class="col-md-3">
                                <div class="card border-primario border-2  texto-primario  cursor" onclick="irParaChamados('aberto')">
                                    <div class="card-header h6 border-0 bg-transparent text-center">
                                        Meus atendimentos (Abertos)
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 class="card-text"><?php echo $seusAtendimentosAbertos['qtd'];  ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-primario border-2  texto-primario  cursor" onclick="irParaChamados('pendente')">
                                    <div class="card-header h6 border-0 bg-transparent text-center">
                                        Chamados (Pendentes)
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 class="card-text"><?php echo $AtendimentosPendente['qtd'];  ?></h3>
                                    </div>
                                </div>
                            </div>
                        <?php
                        };
                        ?>
                    </div>
                    <?php
                    if ((isset($_SESSION["usuario"]["atendente"]) && $_SESSION["usuario"]["atendente"]) || (isset($_SESSION["usuario"]["adm"]) && $_SESSION["usuario"]["adm"])) {
                    ?>
                        <div class="card mt-md-3 mt-3">

                            <div class="card-header bg-primario texto-laranja"><i class="bi bi-speedometer2"></i> Dashboards</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card border-primario border-0  texto-primario cursor h-100">
                                            <div class="card-header h6 border-0 bg-transparent text-center">
                                                Top Categorias (mês)
                                            </div>
                                            <div class="card-body text-center">
                                                <canvas id="categoriaMes" ></canvas>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-8">
                                        <div class="card border-primario border-0  texto-primario cursor h-100">
                                            <div class="card-header h6 border-0 bg-transparent text-center">
                                                Top Clientes (mês)
                                            </div>
                                            <div class="card-body text-center">
                                                <canvas id="myBarChart3"></canvas>

                                            </div>
                                        </div>

                                    </div>
                                    <hr>
                                    <div class="col-md-7">
                                        <div class="card border-primario border-0  texto-primario cursor">
                                            <div class="card-header h6 border-0 bg-transparent text-center">
                                                Resolvidos Atendentes (mês)
                                            </div>
                                            <div class="card-body text-center">
                                                <canvas id="myBarChart2"></canvas>

                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-5">
                                        <div class="card border-primario border-0  texto-primario cursor h-100">
                                            <div class="card-header h6 border-0 bg-transparent text-center">
                                                TMA Minutos (mês)
                                            </div>
                                            <div class="card-body text-center">
                                                <canvas id="tmaBarChart"></canvas>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    };
                    ?>

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

        function irParaChamados(status) {
            window.location.href = `consultar_chamados.php?status=${status}`
        }
    </script>
    <script>
        // Obtenha os dados do PHP
        const categoriasMes = <?php echo $categoriasMesJson; ?>;

        // Prepare os dados para Chart.js
        const categoriasMesLabels = categoriasMes.map(item => item.categoria);
        const categoriasMesData = categoriasMes.map(item => item.total);

        const categoriasMesCtx = document.getElementById('categoriaMes').getContext('2d');
        const categoriasMesPieChart = new Chart(categoriasMesCtx, {
            type: 'pie',
            data: {
                labels: categoriasMesLabels,
                datasets: [{
                    data: categoriasMesData,
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffcd56', '#4bc0c0', '#9966ff', '#ff9f40'], // Cores para cada fatia do gráfico
                    hoverBackgroundColor: ['#ff6384', '#36a2eb', '#ffcd56', '#4bc0c0', '#9966ff', '#ff9f40']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return `${tooltipItem.label}: ${tooltipItem.raw}`;
                            }
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        anchor: 'center',
                        align: 'start',
                        backgroundColor: (context) => {
                            return context.dataset.backgroundColor;
                        },
                        font: {
                            weight: 'bold',
                            size: '14'
                        },
                        formatter: (value, context) => {
                            return context.chart.data.labels[context.dataIndex] + '\n' + value;
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>
    <script>
        // Suponha que os dados já estejam mapeados em labels e data
        const resolvidosAtendentesMes = <?php echo $resolvidosAtendenteMesJson; ?>;

        // Função para gerar cores aleatórias
        function generateRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        // Gerar cores dinamicamente para cada barra
        const backgroundColors = resolvidosAtendentesMes.map(() => generateRandomColor());
        const borderColors = backgroundColors.map(color => color);

        // Prepare os dados para Chart.js
        const resolvidosAtendentesMesLabels = resolvidosAtendentesMes.map(item => item.atendente_nome);
        const resolvidosAtendentesMesData = resolvidosAtendentesMes.map(item => item.total);

        const resolvidosAtendentesMesCtx = document.getElementById('myBarChart2').getContext('2d');

        const resolvidosAtendentesMesChart = new Chart(resolvidosAtendentesMesCtx, {
            type: 'bar',
            data: {
                labels: resolvidosAtendentesMesLabels,
                datasets: [{
                    data: resolvidosAtendentesMesData,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Este é o ajuste para barras horizontais
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return `${tooltipItem.label}: ${tooltipItem.raw}`;
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'center',
                        align: 'center',
                        formatter: (value, context) => value,
                        color: 'black',
                        font: {
                            weight: 'bold',
                            size: 12
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>


    <script>
        // Suponha que os dados já estejam mapeados em labels e data
        const abertosClientesMes = <?php echo $abertosClienteMesJson; ?>;
        // Prepare os dados para Chart.js
        const abertosClientesMesLabels = abertosClientesMes.map(item => item.cliente_nome);
        const abertosClientesMesData = abertosClientesMes.map(item => item.total);

        const abertosClientesMesCtx = document.getElementById('myBarChart3').getContext('2d');

        const abertosClientesMesChart = new Chart(abertosClientesMesCtx, {
            type: 'bar',
            data: {
                labels: abertosClientesMesLabels,
                datasets: [{
                    data: abertosClientesMesData,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return `${tooltipItem.label}: ${tooltipItem.raw}`;
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'center',
                        align: 'center',
                        formatter: (value, context) => value,
                        color: 'black',
                        font: {
                            weight: 'bold',
                            size: 12
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    </script>
   <script>
    // Suponha que os dados já estejam mapeados em labels e data
    const tmasMes = <?php echo $tmaMesJson; ?>;
    // Labels e dados para o gráfico
    const tmaLabels = ["Menor TMA", "Média TMA", "Maior TMA"];
    const tmaData = [tmasMes.menor_tma, tmasMes.media_tma, tmasMes.maior_tma];

    // Cores para as barras
    const backgroundColors2 = ['#ff6384', '#36a2eb', '#ffcd56'];
    const borderColors2 = ['#ff6384', '#36a2eb', '#ffcd56'];

    const tmaCtx = document.getElementById('tmaBarChart').getContext('2d');
    const tmaBarChart = new Chart(tmaCtx, {
        type: 'bar',
        data: {
            labels: tmaLabels,
            datasets: [{
                data: tmaData,
                backgroundColor: backgroundColors2,
                borderColor: borderColors2,
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y', // Este é o ajuste para barras horizontais
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Tempo Médio de Atendimento (Minutos)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return `${tooltipItem.label}: ${tooltipItem.raw} minutos`;
                        }
                    }
                },
                datalabels: {
                    anchor: 'center',
                    align: 'center',
                    formatter: (value, context) => Number(value).toFixed(0) + ' minutos',
                    color: 'black',
                    font: {
                        weight: 'bold',
                        size: 12
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>


</body>

</html>