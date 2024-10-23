    <?php
    include_once("requests.php");
    include_once("../../../templates/Loading.php");
    include_once("../../../templates/header.php");
    ?>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style2.css">
    <style>
        body {
            background-color: #f8f9fa;
            /* Fundo claro para um visual moderno */
        }

        .titulo {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid black;
            color: black;
            font-size: 18px;
            /* Aumentei um pouco a fonte */
            font-weight: 600;
        }

        .card {
            background-color: #ffffff;
            /* Fundo branco para as cartas */
            border-radius: 10px;
            /* Bordas arredondadas */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            /* Sombra suave */
            transition: transform 0.2s;
            /* Transição suave para hover */
        }

        .card:hover {
            transform: scale(1.02);
            /* Efeito de zoom ao passar o mouse */
        }

        .card p {
            font-size: 25px;
            font-weight: 600;
        }

        .icon {
            margin-right: 10px;
            /* Espaçamento entre o ícone e o texto */
        }

        .btn-close-custom {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: darkred;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            border: 1px solid black;
            border-radius: 5px;
        }

        .btn-close-custom::before,
        .btn-close-custom::after {
            content: '';
            position: absolute;
            width: 2px;
            height: 70%;
            background-color: white;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(45deg);
        }

        .btn-close-custom::after {
            transform: translate(-50%, -50%) rotate(-45deg);
        }

        #btn-filtros {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            width: 50px;
            height: 50px;
            background-color: #002955;
            border: none;
            border-radius: 50%;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            color: white;
            font-size: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dropdown-checkbox {
            max-height: 400px;
            overflow-y: auto;
        }

        .dropdown:hover {
            color: black;
        }

        .dropdown-toggle:active,
        .dropdown-toggle:focus {
            color: black;
        }
    </style>

    <div class="titulo">
        <i class="ph ph-speedometer"></i> Dashboards
    </div>
    <div class="corpo container mt-4">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center d-flex flex-row align-items-center p-3">
                    <div class="icon">
                        <i class="icon ph-bold ph-t-shirt fa-2x"></i>
                    </div>
                    <div class="justify-content-center" style="width: 100%">
                        <h5 class="card-title">Total de Peças</h5>
                        <p class="card-text" id="total_pecas"></p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <div class="card text-center d-flex flex-row align-items-center p-3">
                    <div class="icon">
                        <i class="icon ph-bold ph-t-shirt fa-2x"></i>
                    </div>
                    <div class="justify-content-center" style="width: 100%">
                        <h5 class="card-title">Total de Ops</h5>
                        <p class="card-text" id="total_ops"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4 mb-3" style="max-height: 70vh; overflow: auto;">
            <div class="col-12">
                <div class="card p-3">
                    <h4 class="card-title" style="position: sticky; top: 0; min-width: 100%; background-color: white; z-index: 1">Carga por Faccionistas</h4>
                    <div id="barChart"></div>
                </div>
            </div>
        </div>
        <div class="row mt-4 mb-3">
            <div class="col-md-6 d-flex align-items-stretch mb-3">
                <div class="card p-3 flex-fill">
                    <h4 class="card-title">Carga por Categorias</h4>
                    <div id="donutChart"></div>
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-stretch mb-3">
                <div class="card p-3 flex-fill">
                    <h4 class="card-title">Carga por Status</h4>
                    <div id="pieChart"></div>
                </div>
            </div>
        </div>
        <button id="btn-filtros" onclick="$('#modal-filtros').modal('show')">
            <i class="icon ph-bold ph-funnel" style="margin-left: 9px;"></i>
        </button>

    </div>

    <div class="modal fade modal-custom" id="modal-filtros" tabindex="-1" aria-labelledby="customModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-top modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customModalLabel" style="color: black;">Filtros</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex" style="align-items: start; text-align: left;">
                    <div class="dropdown mb-4 mr-4" id="dropdown-faccionistas">
                        <button class="btn dropdown-toggle" type="button" id="btn-dropdown-faccionistas" data-bs-toggle="dropdown"
                            aria-expanded="false" style="border: 1px solid black; border-radius: 10px; padding: 10px 10px;">
                            Faccionistas
                        </button>
                        <ul class="dropdown-menu p-3 dropdown-checkbox" aria-labelledby="dropdown-faccionistas" style="width: 300px;">
                            <!-- Campo de Pesquisa -->
                            <li>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" id="search-faccionistas" class="form-control" placeholder="Pesquisar..." aria-label="Pesquisar Faccionistas">
                                </div>
                            </li>
                            <hr>
                            <!-- Aqui as opções serão inseridas dinamicamente -->
                            <div id="faccionistas-options"></div>
                        </ul>
                    </div>
                    <div class="dropdown" id="dropdown-categorias">
                        <button class="btn dropdown-toggle" type="button" id="btn-dropdown-categorias" data-bs-toggle="dropdown"
                            aria-expanded="false" style="border: 1px solid black; border-radius: 10px; padding: 10px 10px;">
                            Categorias
                        </button>
                        <ul class="dropdown-menu p-3 dropdown-checkbox" aria-labelledby="dropdown-categorias" style="width: 300px;">
                            <!-- Campo de Pesquisa -->
                            <li>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" id="search-categorias" class="form-control" placeholder="Pesquisar..." aria-label="Pesquisar Faccionistas">
                                </div>
                            </li>
                            <hr>
                            <!-- Aqui as opções serão inseridas dinamicamente -->
                            <div id="categorias-options"></div>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn" style="background-color: #002955; color: white" onclick="AtualizarDados()">Aplicar</button>
                </div>
            </div>
        </div>

        <?php include_once("../../../templates/footer.php"); ?>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            $(document).ready(async () => {
                await Consultar_Dados(false, "", "");
                await Consultar_Faccionistas();
                Consultar_Categorias();

                $(document).on('change', '.option-checkbox-faccionista', function() {
                    // Se o checkbox for selecionado, desmarcar os outros
                    if (this.checked) {
                        console.log('Checkbox selecionado: ' + this.value); // Log do valor do checkbox
                        $('.option-checkbox-faccionista').not(this).prop('checked', false);
                    }
                });

                $(document).on('change', '.option-checkbox-categoria', function() {
                    // Se o checkbox for selecionado, desmarcar os outros
                    if (this.checked) {
                        console.log('Checkbox selecionado: ' + this.value); // Log do valor do checkbox
                        $('.option-checkbox-categoria').not(this).prop('checked', false);
                    }
                });
            });

            function AtualizarDados() {
                // Captura a checkbox selecionada de faccionistas e categorias
                const checkbox_faccionista = $('.option-checkbox-faccionista:checked').val();
                const checkbox_categoria = $('.option-checkbox-categoria:checked').val();

                // Log dos valores selecionados (se houver)
                console.log('Faccionista selecionado:', checkbox_faccionista);
                console.log('Categoria selecionada:', checkbox_categoria);

                // Chama a função Consultar_Dados passando os valores (ou vazio se nada foi selecionado)
                Consultar_Dados(true, checkbox_categoria || "", checkbox_faccionista || "");

                $('#modal-filtros').modal('hide')
            }




            async function Consultar_Dados(Congelado, Categoria, Faccionista) {
                $('#loadingModal').modal('show');

                try {
                    const requestData = {
                        acao: "Consulta_Dados",
                        dados: {
                            "categoria": Categoria,
                            "congelamento": Congelado,
                            "apelidoFaccionista": Faccionista
                        }
                    };

                    const response = await $.ajax({
                        type: 'POST',
                        url: 'requests.php',
                        contentType: 'application/json',
                        data: JSON.stringify(requestData),
                    });

                    console.log(response);
                    $('#total_pecas').text(parseInt(response[0]['1- TotalPeças:']).toLocaleString());
                    $('#total_ops').text(response[0]['2- TotalOPs']);

                    FunctionGraficoFaccionistas(response[0]["3- Distribuicao:"]);
                    FunctionGraficoCategorias(response[0]["3.1- ResumoCategoria"]);
                    FunctionGraficoStatus(response[0]["3.2- ResumoStatus"]);

                } catch (error) {
                    console.error('Erro na solicitação AJAX:', error);
                    alert('Ocorreu um erro ao buscar os dados. Tente novamente.');
                } finally {
                    $('#loadingModal').modal('hide');
                }
            }


            async function Consultar_Faccionistas() {
                $('#loadingModal').modal('show');

                try {
                    const response = await $.ajax({
                        type: 'GET',
                        url: 'requests.php',
                        dataType: 'json',
                        data: {
                            acao: 'Consultar_Faccionistas',
                        }
                    });

                    console.log(response)

                    // Verifique se os dados foram recebidos corretamente
                    if (response && response.length > 0) {
                        // Limpe as opções anteriores, se houver
                        $('#faccionistas-options').empty();

                        // Itera sobre os dados recebidos da API
                        response.forEach(faccionista => {
                            // Cria uma nova opção de checkbox para cada faccionista
                            const checkboxOption = `
                    <li class="faccionista-item">
                        <div class="form-check">
                            <input class="form-check-input option-checkbox-faccionista" type="checkbox" id="faccionista-${faccionista['02- nome']}" value="${faccionista['02- nome']}">
                            <label class="form-check-label" for="faccionista-${faccionista['02- nome']}">
                                ${faccionista['01- codfaccionista']} - ${faccionista['02- nome']}
                            </label>
                        </div>
                    </li>
                `;

                            // Adiciona cada opção à lista de faccionistas
                            $('#faccionistas-options').append(checkboxOption);
                        });
                    } else {
                        // Caso não haja dados, exiba uma mensagem
                        $('#faccionistas-options').html('<li>Nenhum faccionista encontrado.</li>');
                    }

                } catch (error) {
                    console.error('Erro na solicitação AJAX:', error);
                    alert('Ocorreu um erro ao buscar os dados. Tente novamente.');
                } finally {
                    $('#loadingModal').modal('hide');
                }
            }

            async function Consultar_Categorias() {
                $('#loadingModal').modal('show');

                try {
                    const response = await $.ajax({
                        type: 'GET',
                        url: 'requests.php',
                        dataType: 'json',
                        data: {
                            acao: 'Consultar_Categorias',
                        }
                    });

                    console.log(response)

                    // Verifique se os dados foram recebidos corretamente
                    if (response && response.length > 0) {
                        // Limpe as opções anteriores, se houver
                        $('#categorias-options').empty();

                        // Itera sobre os dados recebidos da API
                        response.forEach(categoria => {
                            // Cria uma nova opção de checkbox para cada categoria
                            const checkboxOption = `
                    <li class="categoria-item">
                        <div class="form-check">
                            <input class="form-check-input option-checkbox-categoria" type="checkbox" id="categoria-${categoria.categoria}" value="${categoria.categoria}">
                            <label class="form-check-label" for="categoria-${categoria.categoria}">
                                ${categoria.categoria}
                            </label>
                        </div>
                    </li>
                `;

                            // Adiciona cada opção à lista de categorias
                            $('#categorias-options').append(checkboxOption);
                        });
                    } else {
                        // Caso não haja dados, exiba uma mensagem
                        $('#categorias-options').html('<li>Nenhuma categoria encontrado.</li>');
                    }

                } catch (error) {
                    console.error('Erro na solicitação AJAX:', error);
                    alert('Ocorreu um erro ao buscar os dados. Tente novamente.');
                } finally {
                    $('#loadingModal').modal('hide');
                }
            }

            // Função de Filtro de Pesquisa
            $(document).on('input', '#search-faccionistas', function() {
                const searchValue = $(this).val().toLowerCase();

                // Filtra as opções com base no valor digitado
                $('#faccionistas-options .faccionista-item').filter(function() {
                    const itemText = $(this).text().toLowerCase();
                    $(this).toggle(itemText.indexOf(searchValue) > -1);
                });
            });

            $(document).on('input', '#search-categorias', function() {
                const searchValue = $(this).val().toLowerCase();

                // Filtra as opções com base no valor digitado
                $('#categorias-options .categoria-item').filter(function() {
                    const itemText = $(this).text().toLowerCase();
                    $(this).toggle(itemText.indexOf(searchValue) > -1);
                });
            });

            let chart = null; // Variável global para o gráfico

            function FunctionGraficoFaccionistas(data) {
                // Verifique se a data está no formato esperado
                if (!Array.isArray(data) || data.length === 0) {
                    console.error("Dados inválidos:", data);
                    return; // Não faz nada se os dados não forem válidos
                }

                // Ordena os dados em ordem decrescente com base na carga
                const sortedData = data.sort((a, b) => b.carga - a.carga);

                const nomes = sortedData.map(item => item.apelidofaccionista);
                const valores = sortedData.map(item => item.carga);

                // Calcula a altura do gráfico com base na quantidade de dados
                const totalHeight = sortedData.length * 50; // Altura da barra ajustada para 50px por item

                const options = {
                    series: [{
                        name: 'Carga',
                        data: valores
                    }],
                    chart: {
                        type: 'bar',
                        height: totalHeight > 400 ? totalHeight : 400, // Define um mínimo de 400px de altura para o gráfico
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: true, // Garantir que as barras sejam horizontais
                            borderRadius: 4
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return `${val.toLocaleString()}`; // Formatação dos valores
                        },
                        offsetX: 5,
                        style: {
                            fontSize: '13px',
                            colors: ['#fff'], // Ajusta a cor do texto para branco
                        },
                    },
                    xaxis: {
                        categories: nomes
                    },
                    colors: ['#002955'],
                    grid: {
                        show: true,
                        strokeDashArray: 4, // Grid de fundo mais leve
                    },
                };

                // Se o gráfico já existir, destrua-o antes de criar um novo
                if (chart !== null) {
                    chart.destroy(); // Destroi o gráfico anterior
                    chart = null; // Redefine a variável
                }

                // Certifica-se de que o container esteja vazio e pronto para o novo gráfico
                const chartContainer = document.querySelector("#barChart");
                chartContainer.innerHTML = ''; // Limpa o container

                // Cria uma nova instância do gráfico
                chart = new ApexCharts(chartContainer, options);

                // Renderiza o gráfico
                chart.render().then(() => {
                    console.log("Gráfico renderizado com sucesso!");
                }).catch(error => {
                    console.error("Erro ao renderizar o gráfico:", error);
                });
            }


            let chartCategorias = null; // Variável global para o gráfico de categorias
            let chartStatus = null; // Variável global para o gráfico de status

            function FunctionGraficoCategorias(data) {
                console.log(data);

                // Ordena os dados em ordem decrescente com base na carga
                const sortedData = data.sort((a, b) => b.carga - a.carga);

                const nomes = sortedData.map(item => item.categoria);
                const valores = sortedData.map(item => item.carga);

                console.log(nomes, valores);

                const options = {
                    series: valores,
                    chart: {
                        width: 380,
                        type: 'pie',
                    },
                    labels: nomes,
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, {
                            seriesIndex
                        }) {
                            return valores[seriesIndex].toLocaleString(); // Formata o valor correspondente
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                // Se o gráfico já existir, destrua-o antes de criar um novo
                if (chartCategorias !== null) {
                    chartCategorias.destroy();
                    chartCategorias = null;
                }

                // Certifique-se de que o container esteja vazio e pronto para o novo gráfico
                const chartContainer = document.querySelector("#donutChart");
                chartContainer.innerHTML = ''; // Limpa o container

                // Cria uma nova instância do gráfico
                chartCategorias = new ApexCharts(chartContainer, options);
                chartCategorias.render();
            }

            function FunctionGraficoStatus(data) {
                console.log(data);

                // Ordena os dados em ordem decrescente com base na carga
                const sortedData = data.sort((a, b) => b.carga - a.carga);

                const nomes = sortedData.map(item => item.status);
                const valores = sortedData.map(item => item.carga);

                console.log(nomes, valores);

                const options = {
                    series: valores,
                    chart: {
                        width: 380,
                        type: 'pie',
                    },
                    labels: nomes,
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, {
                            seriesIndex
                        }) {
                            return valores[seriesIndex].toLocaleString(); // Formata o valor correspondente
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                // Se o gráfico já existir, destrua-o antes de criar um novo
                if (chartStatus !== null) {
                    chartStatus.destroy();
                    chartStatus = null;
                }

                // Certifique-se de que o container esteja vazio e pronto para o novo gráfico
                const chartContainer = document.querySelector("#pieChart");
                chartContainer.innerHTML = ''; // Limpa o container

                // Cria uma nova instância do gráfico
                chartStatus = new ApexCharts(chartContainer, options);
                chartStatus.render();
            }
        </script>