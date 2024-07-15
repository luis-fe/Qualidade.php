<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
?>
<link rel="stylesheet" href="style.css">
<style>
    #checkboxContainerColecao label {
        display: block;
        margin: 0;
        padding: 0;
    }

    .Corpo {
        width: 100%;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
        overflow: auto;
        background-color: var(--branco);
        padding: 20px;
        height: calc(100% - 50px);
        min-height: calc(97% - 50px);
        max-height: calc(97% - 50px);
    }

    .chart-container {
        margin: 20px 0;
    }

    .detalha-fila {
        position: absolute;
        background-color: lightgray;
        border: 1px solid #ddd;
        padding: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        display: none;
        max-height: 50vh;
        overflow-y: auto;
    }
</style>

<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height d-flex flex-wrap">
        <div class="row col-12">
            <div class="col-12 col-md-4 text-center">
                <div class="dropdown mt-2 col-9 mx-auto">
                    <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="filtroDropdownColecao" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Coleção
                    </button>
                    <div class="dropdown-menu" aria-labelledby="filtroDropdownColecao" style="width: 300px">
                        <div class="p-2">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="search-icon">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="searchInputColecao" class="form-control" placeholder="Pesquisar..." aria-label="Pesquisar" aria-describedby="search-icon">
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAllColecao">
                                <label class="form-check-label" for="selectAllColecao">
                                    Selecionar Todos
                                </label>
                            </div>
                            <div id="checkboxContainerColecao"></div>
                        </div>
                    </div>
                </div>
                <div class="dropdown mt-3 col-9 mx-auto">
                    <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="filtroDropdownCategoria" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Categoria
                    </button>
                    <div class="dropdown-menu" aria-labelledby="filtroDropdownCategoria">
                        <input type="text" id="searchInputCategoria" class="form-control mb-2" placeholder="Pesquisar...">
                        <label><input type="checkbox" id="selectAllCategoria"> Selecionar Todos</label><br>
                        <div id="checkboxContainerCategoria"></div>
                    </div>
                </div>
                <div class="dropdown mt-3 col-9 mx-auto">
                    <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="filtroDropdownTipoOp" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        TipoOp
                    </button>
                    <div class="dropdown-menu" aria-labelledby="filtroDropdownTipoOp">
                        <input type="text" id="searchInputTipoOp" class="form-control mb-2" placeholder="Pesquisar...">
                        <label><input type="checkbox" id="selectAllTipoOp"> Selecionar Todos</label><br>
                        <div id="checkboxContainerTipoOp"></div>
                    </div>
                </div>
                <button class="btn btn-secondary mt-3" type="button" onclick="Filtrar()">
                    Filtrar
                </button>
            </div>
            <div class="col-12 col-md-8" id="Graficos">
                <!-- Gráficos serão inseridos aqui -->
            </div>
        </div>
    </div>
</div>

<div id="detalha-info" class="detalha-fila">
    <div class="col-12" id="Graficos2">
        <!-- Gráficos serão inseridos aqui -->
    </div>
</div>

<?php include_once("../../../templates/footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $(document).ready(() => {
        ConsultaColecao();


        $('#searchInputColecao').on('keyup', function() {
            var searchText = $(this).val().toLowerCase();
            var $container = $('#checkboxContainerColecao');
            $container.find('.filtro').closest('label').each(function() {
                var $label = $(this);
                var labelText = $label.text().toLowerCase();
                if (labelText.includes(searchText)) {
                    $label.show().prependTo($container);
                } else {
                    $label.hide();
                }
            });
        });

        $('#NomeRotina').text('Fila das Fases')

        function fecharDropdowns() {
            $('.dropdown-menu').hide();
        }

        // Evento de clique no documento para fechar dropdowns ao clicar fora
        $(document).click(function(event) {
            var $target = $(event.target);
            if (!$target.closest('.dropdown').length) {
                fecharDropdowns();
            }
        });

        // Evento de clique nos botões de dropdown
        $('.dropdown-toggle').click(function(event) {
            event.stopPropagation();
            var $this = $(this);
            var $dropdownMenu = $this.next('.dropdown-menu');

            // Fechar todos os outros dropdowns
            $('.dropdown-menu').not($dropdownMenu).hide();

            // Toggle do dropdown clicado
            $dropdownMenu.toggle();
        });

    });

    async function ConsultarFila(Colecoes) {
        $('#loadingModal').modal('show');
        try {
            const dados = {
                Colecao: Colecoes
            };

            var requestData = {
                acao: "Consultar_Fila_Fases",
                dados: dados
            };

            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response);
            criarGraficos(response);
            DetalhaFila();
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    const ConsultaColecao = async () => {
        $('#loadingModal').modal('show');
        try {
            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Colecao'
                }
            });
            console.log(response)
            const ColecoesFiltradas = response.filter(item => item.COLECAO !== '-');
            $('#checkboxContainerColecao').empty();
            ColecoesFiltradas.forEach(item => {
                $('#checkboxContainerColecao').append(`<label><input type="checkbox" class="filtro" value="${item["COLECAO"]}"> ${item["COLECAO"]}</label>`);
            });
        } catch (error) {
            console.error('Erro ao consultar chamados:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }



    async function DetalhaFila() {
        try {
            let timeoutId;
            $('.chart-container').hover(function() {
                clearTimeout(timeoutId);
                const index = $(this).index('.chart-container');
                const detalhaInfo = $('#detalha-info');
                const containerTop = $(this).offset().top;
                const windowTop = $(window).scrollTop();

                // Verifica se a modal ultrapassaria o topo da página
                if (containerTop - detalhaInfo.outerHeight() < windowTop) {
                    // Se sim, posiciona a modal abaixo da div
                    detalhaInfo.css({
                        display: 'block',
                        top: containerTop + $(this).outerHeight(),
                        left: $(this).offset().left
                    });
                } else {
                    // Se não, posiciona a modal acima da div
                    detalhaInfo.css({
                        display: 'block',
                        top: containerTop - detalhaInfo.outerHeight(),
                        left: $(this).offset().left
                    });
                }
                const faseAtual = $(this).find('h5').text(); // Obtém o título da fase atual
                ConsultarFilaFases(faseAtual); // Chama a função de consulta passando o título da fase

            }, function() {
                timeoutId = setTimeout(() => {
                    $('#detalha-info').css('display', 'none');
                }, 500);
            });

            $('#detalha-info').hover(function() {
                clearTimeout(timeoutId);
            }, function() {
                $('#detalha-info').css('display', 'none');
            });

        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
        } finally {}
    }


    async function ConsultarFilaFases(faseAtual) {
        try {
            const dados = {
                nomeFase: faseAtual
            };

            var requestData = {
                acao: "Consultar_Detalha_Fila",
                dados: dados
            };

            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });

            criarGraficosHover(response, faseAtual); // Chama a função para criar o modal com base na resposta

        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
        } finally {}
    }

    function criarGraficos(dados) {
        const divGraficos = document.getElementById('Graficos');
        divGraficos.innerHTML = '';

        dados.forEach((item, index) => {
            const chartContainer = document.createElement('div');
            chartContainer.className = 'chart-container d-flex col-12 mb-4 align-items-center justify-content-center';
            chartContainer.style.cursor = 'pointer';
            chartContainer.style.border = '1px solid black';
            chartContainer.id = 'chart' + index;

            const faseTitulo = document.createElement('h5');
            faseTitulo.className = 'col-2 text-center';
            faseTitulo.style.fontSize = '15px';
            faseTitulo.textContent = item.fase;
            chartContainer.appendChild(faseTitulo);

            const chartDiv = document.createElement('div');
            chartDiv.className = 'col-10';
            chartContainer.appendChild(chartDiv);

            divGraficos.appendChild(chartContainer);

            var options = {
                series: [{
                    name: 'Fila',
                    data: [item.Fila]
                }, {
                    name: 'Carga Atual',
                    data: [item['Carga Atual']]
                }],
                chart: {
                    type: 'bar',
                    height: 90,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '100%',
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetX: 10,
                    style: {
                        fontSize: '14px',
                        colors: ['#000']
                    },
                    formatter: function(val) {
                        return val;
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                tooltip: {
                    shared: true,
                    intersect: false
                },
                xaxis: {
                    categories: ['Fila'],
                    labels: {
                        show: false
                    }
                },
                legend: {
                    show: false
                }
            };

            var chart = new ApexCharts(chartDiv, options);
            chart.render();
        });
    }

    function criarGraficosHover(dados, faseAtual) {
        console.log(dados)
        const divGraficos2 = document.getElementById('Graficos2');
        divGraficos2.innerHTML = '';
        const titulo = document.createElement('h2');
        titulo.textContent = faseAtual;

        divGraficos2.append(titulo)

        dados.forEach((item, index) => {
            const chartContainer = document.createElement('div');
            chartContainer.className = 'chart-container d-flex col-12 mb-4 align-items-center justify-content-center';
            chartContainer.style.cursor = 'pointer';
            chartContainer.style.border = '1px solid black';
            chartContainer.id = 'chartHover' + index;

            const faseTitulo = document.createElement('h5');
            faseTitulo.className = 'col-2 text-center';
            faseTitulo.style.fontSize = '15px';
            faseTitulo.textContent = item.faseAtual;
            chartContainer.appendChild(faseTitulo);

            const chartDiv = document.createElement('div');
            chartDiv.className = 'col-10';
            chartContainer.appendChild(chartDiv);

            divGraficos2.appendChild(chartContainer);

            // Configuração do gráfico
            var options = {
                series: [{
                    name: 'Quantidade de Peças',
                    data: [item.pcs] // Utiliza a quantidade de peças do item
                }],
                chart: {
                    type: 'bar',
                    height: 90,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '100%',
                        dataLabels: {
                            position: 'top',
                        },
                    }
                },
                dataLabels: {
                    enabled: true,
                    offsetX: 10,
                    style: {
                        fontSize: '14px',
                        colors: ['#000']
                    },
                    formatter: function(val) {
                        return val;
                    }
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                tooltip: {
                    shared: true,
                    intersect: false
                },
                xaxis: {
                    categories: ['Quantidade'],
                    labels: {
                        show: false
                    }
                },
                legend: {
                    show: false
                }
            };

            // Criação do gráfico
            var chart = new ApexCharts(chartDiv, options);
            chart.render(); // Renderiza o gráfico dentro do elemento chartDiv
        });
    }





    async function Filtrar() {
        var selectedItems = [];
        $('#checkboxContainerColecao .filtro:checked').each(function() {
            selectedItems.push($(this).val());
        });
        ConsultarFila(selectedItems)

    }
</script>