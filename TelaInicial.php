<?php
include_once("./utils/session.php");
include_once("./templates/headers.php");
?>

<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/TelaInicial.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.29.0/dist/apexcharts.min.js"></script>
</head>
<style>
    * {
    font-family: Arial, sans-serif;
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}

:root {
    --cor1: rgb(153, 153, 153);
    --cor2: rgb(17, 45, 126);
    --cor3: rgb(93, 140, 233);
    --cor4: rgb(210, 210, 210);
    --cor5: rgb(0, 0, 0);
}


.container {
            padding: 20px;
            width: 100%;
            height: 90vh;
            display: block;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

.title{
    display: flex;
    border-bottom: 2px solid var(--cor2);
    padding: 20px;
    background-color: var(--cor4);
}

.title i {
    flex-shrink: 0;
    font-size: 25px;
    cursor: pointer;
    color: black;
}

.title h3 {
    margin: 0;
    text-align: left;
    flex-grow: 1;  
    font-size: 25px; 
}



.Datas input[type="date"]{
    margin-left: 10px;
    margin-top: 10px;
    margin-bottom: 10px;
    padding: 10px;
    border-top: none;
    border-right: none;
    border-left: none;
    background-color: transparent;
    border-bottom: 2px solid black;
}

.Datas button{
    padding: 10px;
    border-radius: 10px;
    background-color: var(--cor2);
    color: white;
    cursor: pointer;
}
.Corpo {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            width: 100%;
            flex-grow: 1;
            overflow-x: auto;
            max-height: 70vh;
        }

        .left, .right {
            width: calc(50% - 20px);
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            max-height: 100%;
            overflow-y: auto;
        }

        .chart-container {
            min-height: 300px;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            position: relative;
        }

        .loading-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            color: #245580;
            display: none;
        }
</style>
<body>
    <div class="container">
        <div class="title">
            <h3>INDICADORES</h3>
        </div>
        <div class="Datas">
            <input type="date" id="dataInicial">
            <input type="date" id="dataFinal">
            <button onclick="initializeCharts()">Atualizar</button>
        </div>
        <div class="Corpo">
            <div class="left">
            <div id="PercentualQualidade">
                <h2>Percentual 2ª Qualidade</h2>
                <div id="chartQualidade">
                    <div id="chartQualidadeLabel"></div>
                </div>
            </div>
            </div>
            <div class="right">
                <div class="top">
                <h2>Ranking Defeitos</h2>
                <div id="chart"></div>
                </div>
                <div class="bottom">
                <h2>Ranking Defeitos - FACCIONISTA / FORNECEDOR</h2>
                <div id="chartFaccionista"></div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
        const ApiMotivos = 'http://10.162.0.190:5000/api/MotivosAgrupado';
        const ApiQualidade = 'http://10.162.0.190:5000/api/AcompanhamentoQualidade';
        const ApiTerceirizados = 'http://10.162.0.190:5000/api/OrigemAgrupado';

        async function fetchData(apiUrl, dataInicial, dataFinal) {
            try {
                const response = await fetch(`${apiUrl}?DataIncial=${dataInicial}&DataFinal=${dataFinal}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': "a40016aabcx9"
                    },
                });

                if (!response.ok) {
                    throw new Error('Erro no retorno da API');
                }

                return response.json();
            } catch (error) {
                console.error(error);
                return null;
            }
        }

        async function fetchData1(apiUrl, dataInicial, dataFinal) {
            try {
                const response = await fetch(`${apiUrl}?DataIncial=${dataInicial}&DataFinal=${dataFinal}&origem=COSTURA`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': "a40016aabcx9"
                    },
                });

                if (!response.ok) {
                    throw new Error('Erro no retorno da API');
                }

                return response.json();
            } catch (error) {
                console.error(error);
                return null;
            }
        }

        async function renderBarChart(data) {
            const chartHeight = Math.max(300, data.length * 50); // Calcula a altura com base na quantidade de dados

            const chartOptions = {
                chart: {
                    type: 'bar',
                    height: `${chartHeight}px`,
                    width: '100%',
                },
                series: [{
                    name: 'Quantidade',
                    data: data.map(item => item.qtde)
                }],
                xaxis: {
                    categories: data.map(item => item.motivo2Qualidade),
                    labels: {
                        rotate: -90,
                        align: 'center',
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        barHeight: 80,
                        horizontal: true,
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#chart"), chartOptions);
            chart.render();
        }

        async function renderBarChartTerceirizados(data) {
            const chartHeight = Math.max(300, data.length * 50); // Calcula a altura com base na quantidade de dados
            const chartOptions = {
                chart: {
                    type: 'bar',
                    height: `${chartHeight}px`,
                    width: '100%',
                },
                series: [{
                    name: 'Quantidade',
                    data: data.map(item => item.qtde)
                }],
                xaxis: {
                    categories: data.map(item => item.Origem),
                    labels: {
                        rotate: -90,
                        align: 'center',
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        barHeight: 80,
                        horizontal: true,
                    }
                }
            };

            const chart = new ApexCharts(document.querySelector("#chartFaccionista"), chartOptions);
            chart.render();
        }

        async function renderDonutChart(data) {
    if (data.length > 0) {
        const totalPecas = parseFloat(data[0]["2- Total Peças Baixadas periodo"]);
        const pecas2Qualidade = parseFloat(data[0]["1- Peças com Motivo de 2Qual."]);

        if (!isNaN(totalPecas) && !isNaN(pecas2Qualidade)) {
            const porcentagem2Qualidade = (pecas2Qualidade / totalPecas) * 100;
            const porcentagemDiferenca = 100 - porcentagem2Qualidade;

            const chartOptions = {
                chart: {
                    type: 'donut',
                    width: '80%',
                },
                series: [porcentagem2Qualidade, porcentagemDiferenca],
                labels: ['Peças 2ª Qualidade', 'Peças 1ª Qualidade'],
                colors: ['#FF4560', '#008FFB'],
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200,
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }],
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    show: true
                                },
                                value: {
                                    show: true,
                                    formatter: function (val) {
                                        return val.toFixed(2) + "%"; // Mostra o valor com duas casas decimais
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total 2ª Qualidade',
                                    formatter: function () {
                                        return porcentagem2Qualidade.toFixed(2) + '%'; // Exibe o percentual de segunda qualidade no centro do donut
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: true,
                    position: 'bottom'
                }
            };

            const chart = new ApexCharts(document.querySelector("#chartQualidade"), chartOptions);
            chart.render();

            const legendContainer = document.getElementById("legendQualidade");
            chartOptions.labels.forEach((label, index) => {
                const legendItem = document.createElement("div");
                legendItem.classList.add("legend-item");

                const legendColor = document.createElement("div");
                legendColor.classList.add("legend-color");
                legendColor.style.backgroundColor = chartOptions.colors[index];

                const legendLabel = document.createElement("div");
                legendLabel.innerText = label;

                legendItem.appendChild(legendColor);
                legendItem.appendChild(legendLabel);

                legendContainer.appendChild(legendItem);
            });
        } else {
            throw new Error('Valores inválidos para criar o gráfico');
        }
    } else {
        throw new Error('Nenhum dado disponível para criar o gráfico');
    }
}

async function initializeCharts() {
    // Limpa o conteúdo dos elementos onde os gráficos serão renderizados
    document.getElementById("chart").innerHTML = '';
    document.getElementById("chartQualidade").innerHTML = '';
    document.getElementById("chartFaccionista").innerHTML = '';

    const dataInicial = document.getElementById("dataInicial").value.split("-").reverse().join("/");
    const dataFinal = document.getElementById("dataFinal").value.split("-").reverse().join("/");

    const dataMotivos = await fetchData1(ApiMotivos, dataInicial, dataFinal);
    if (dataMotivos) {
        renderBarChart(dataMotivos);
    }

    const dataQualidade = await fetchData(ApiQualidade, dataInicial, dataFinal);
    if (dataQualidade) {
        renderDonutChart(dataQualidade);
    }


    const dataTerceirizados = await fetchData1(ApiTerceirizados, dataInicial, dataFinal);
    if (dataTerceirizados) {
        renderBarChartTerceirizados(dataTerceirizados);
    }

    
}


document.addEventListener("DOMContentLoaded", function() {
    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0]; // Obtém a data de hoje no formato 'aaaa-mm-dd'

    document.getElementById("dataInicial").value = formattedDate; // Define a data de hoje para a entrada de data inicial
    document.getElementById("dataFinal").value = formattedDate; // Define a data de hoje para a entrada de data final
    initializeCharts(); // Inicia os gráficos com os dados da data de hoje
});

</script>
</html>
