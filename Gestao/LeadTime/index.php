    <?php
    include_once('requests.php');

    if (isset($_SESSION['usuario']) && isset($_SESSION['empresa'])) {
        include_once("../../templates/header.php");
        include_once("../../templates/loading.php");
    } else {
        include_once("../../templates/header1.php");
        include_once("../../templates/loading1.php");
    }
    ?>
    <link rel="stylesheet" href="style1.css">
    <style>
        @media (min-width: 700px) {
            .responsive-container {
                background-color: white;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
                border-radius: 5px;
                padding: 0;
                margin: auto;
                overflow: auto;
            }
        }

        @media (max-width: 700px) {
            .responsive-container {
                min-width: 100% !important;
                width: 100% !important;
                margin: 0;
                margin-right: 0px;
                padding: 0;
                overflow: auto;
            }

            .titulo {
                width: 90%;
                padding: auto;
                margin: auto;
                margin-top: -10px;
                margin-bottom: 10px;
            }

        }

        @media (min-width: 768px) {
            #container-table-categoria {
                max-height: 60vh;
                overflow: auto;
            }

            #container-2-tabelas {
                max-height: 60vh;
                overflow: auto;
            }

            #table-lead-time-faccionistas {
                min-height: 40vh;
                max-height: 40vh;
                overflow: auto;
            }

            #table-lead-time-fases {
                min-height: 40vh;
                max-height: 40vh;
                overflow: auto;
            }

            .table-container {
                min-height: 40vh;
                max-height: 40vh;
                overflow: auto;
            }

        }

        @media (min-width: 1900px) {
            #container-table-categoria {
                max-height: 70vh;
            }

            #table-lead-time-faccionistas {
                min-height: 40vh;
                max-height: 40vh;
                overflow: auto;
            }

            #table-lead-time-fases {
                min-height: 40vh;
                max-height: 40vh;
                overflow: auto;
            }

            .table-container {
                min-height: 40vh;
                max-height: 40vh;
                overflow: auto;
            }
        }

        .loading-icon {
            display: none;
        }
    </style>


    <label for="" class="d-flex flex-start col-12 titulo">Lead Time</label>
    <div class="responsive-container row col-12" id="teste">
        <div class="row col-12" style="min-width: 100%; margin: 0; padding: 0">
            <div class="col-12 col-md-3" style="padding: 0; margin: 0">
                <div class="col-12" style="padding: 0; margin: 0">
                    <div class="tipoOp col-12" style="border: 1px solid black; border-radius: 5px; max-height: 200px; min-height: 200px; padding: 0; overflow:auto">
                        <h2 style="font-size: 18px; background-color: var(--CorMenu); color: white; position: sticky; top: 0">Tipo de Op</h2>
                        <div>
                            <input type="checkbox" id="select-all" style="margin-left: 5px;">
                            <label for="select-all">Selecionar Tudo</label>
                        </div>
                        <div id="checkbox-container"></div>
                    </div>
                </div>
                <div class="col-12 mt-3" style="padding: 0; margin: 0">
                    <div class="col-12" style="border: 1px solid black; border-radius: 5px; padding: 0; max-height: 100px; min-height: 100px; text-align: center; justify-content: center; text-align: center">
                        <h2 style="font-size: 18px; background-color: var(--CorMenu); color: white;">Período</h2>
                        <input type="date" id="data-inicio">
                        <input type="date" id="data-fim">
                    </div>
                </div>
            </div>
            <div class="col-md-9 col-12 row" style="padding: 0; margin: 0">
                <div class=" row col-12 cards" style="justify-content: space-around;">
                    <div class="card text-center d-flex flex-row align-items-center p-3 shadow-sm" style="max-height: 100px; min-width: 250px; max-width: 250px">
                        <i class="fa-solid fa-spinner fa-spin loading-icon"></i>
                        <div class="img mr-3">
                            <i class="fa-solid fa-shirt fa-2x"></i>
                        </div>
                        <div class="justify-content-center">
                            <h5 class="card-title">Total de Peças</h5>
                            <p class="card-text" id="qtd-pecas"></p>
                        </div>
                    </div>
                    <div class="card text-center d-flex flex-row align-items-center p-3 shadow-sm" style="max-height: 100px; min-width: 250px; max-width: 250px">
                        <i class="fa-solid fa-spinner fa-spin loading-icon"></i>
                        <div class="img mr-3">
                            <i class="fa-solid fa-clock fa-2x"></i>
                        </div>
                        <div class="justify-content-center">
                            <h5 class="card-title">Lead Time</h5>
                            <p class="card-text" id="lead-time"></p>
                        </div>
                    </div>
                </div>
                <div class="table-container col-12 col-md-6 mt-3" id="container-table-categoria" style="border: 1px solid black; border-radius: 7px; max-width: 100%; overflow: auto">
                    <i class="fa-solid fa-spinner fa-spin loading-icon"></i>
                    <h5>Lead Time por Categoria</h5>
                    <table id="table-lead-time-categorias" class="table table-custom table-striped" style="overflow: auto; min-width: 100%">
                        <thead id="fixed-header">
                            <tr>
                                <th scope="col">Categoria</th>
                                <th scope="col">Qnt. Peças</th>
                                <th scope="col">Meta</th>
                                <th scope="col">Realizado</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tables col-12 col-md-6 mt-3" id="container-2-tabelas" style="border: 1px solid black; border-radius: 7px;">
                    <div class="table-container" style="max-width: 100%; overflow: auto">
                        <i class="fa-solid fa-spinner fa-spin loading-icon"></i>
                        <h5>Lead Time por Fase</h5>
                        <table id="table-lead-time-fases" class="table table-custom table-striped" style="overflow: auto; min-width: 100%;">
                            <thead id="fixed-header">
                                <tr>
                                    <th scope="col">Cód. Fase</th>
                                    <th scope="col">Nome Fase</th>
                                    <th scope="col">Realizado</th>
                                    <th scope="col">Lead Time</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="table-container" style="max-width: 100%; overflow: auto">
                        <i class="fa-solid fa-spinner fa-spin loading-icon"></i>
                        <h5>Lead Time por Faccionista</h5>
                        <table id="table-lead-time-faccionistas" class="table table-custom table-striped" style="overflow: auto; min-width: 100%;">
                            <thead id="fixed-header">
                                <tr>
                                    <th scope="col">Cód. Faccionista</th>
                                    <th scope="col">Faccionista</th>
                                    <th scope="col">Realizado</th>
                                    <th scope="col">Lead Time</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>
<?php include_once("../../templates/footer1.php"); ?>
    <script>
        $(document).ready(async () => {
            const hoje = new Date();
            const primeiroDiaDoMes = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
            const ultimoDiaDoMes = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);
            const formatDate = (date) => date.toISOString().split('T')[0];

            document.getElementById('data-inicio').value = formatDate(primeiroDiaDoMes);
            document.getElementById('data-fim').value = formatDate(ultimoDiaDoMes);
            await Consulta_Tipos_Op();
            await Consultar_Lead_Time(false);
            await Consultar_Lead_Time_Fases(false);
            Consultar_Lead_Time_Faccionistas(false)
        })

        const Consulta_Tipos_Op = async () => {
            $('#loadingModal').modal('show');
            try {
                const data = await $.ajax({
                    type: 'GET',
                    url: 'requests.php',
                    dataType: 'json',
                    data: {
                        acao: 'Consultar_Tipo_Op',
                    }
                });

                // Certifique-se de que `data` é um array com as opções de checkbox
                const container = document.getElementById('checkbox-container');
                const selectAllCheckbox = document.getElementById('select-all');
                container.innerHTML = ''; // Limpa o conteúdo anterior

                data.forEach(item => {
                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.id = item.tipoOP;
                    checkbox.className = 'tipo-checkbox'; // Adiciona uma classe para fácil seleção
                    checkbox.style.marginLeft = '5px'

                    const label = document.createElement('label');
                    label.htmlFor = item.tipoOP; // Associa o label ao checkbox
                    label.textContent = `${item.tipoOP}`;

                    container.appendChild(checkbox);
                    container.appendChild(label);
                    container.appendChild(document.createElement('br')); // Adiciona quebra de linha
                });

                // Marca todos os checkboxes por padrão
                document.querySelectorAll('.tipo-checkbox').forEach(checkbox => checkbox.checked = true);
                selectAllCheckbox.checked = true;

            } catch (error) {
                console.error('Erro ao consultar chamados:', error);
            } finally {
                $('#loadingModal').modal('hide');
            }
        };

        // Adiciona um evento para a checkbox "Selecionar Tudo"
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.tipo-checkbox').forEach(checkbox => checkbox.checked = this.checked);
        });


        async function Consultar_Lead_Time(congelado) {
            $('#loadingModal').modal('show');
            try {
                // Coleta todas as opções de Tipo Op que estão marcadas
                const arrayTipoOp = Array.from(document.querySelectorAll('.tipo-checkbox:checked')).map(checkbox => checkbox.id);

                const dados = {
                    dataIncio: $('#data-inicio').val(),
                    dataFim: $('#data-fim').val(),
                    arrayTipoOP: arrayTipoOp,
                };

                const requestData = {
                    acao: "Consultar_Lead_Time",
                    dados: dados
                };

                const response = await $.ajax({
                    type: 'POST',
                    url: 'requests.php',
                    contentType: 'application/json',
                    data: JSON.stringify(requestData),
                });
                console.log(response[0]['04-LeadTimeCategorias']);
                TabelaLeadTimes(response[0]['04-LeadTimeCategorias']);
                $('#qtd-pecas').html(parseInt(response[0]['03-TotalPeças']).toLocaleString() + ' Pçs')
                $('#lead-time').html(parseInt(response[0]['02-LeadTimeMediaPonderada']).toLocaleString() + ' dias')
            } catch (error) {
                console.error('Erro na solicitação AJAX:', error);
            } finally {
                $('#loadingModal').modal('hide');
            }
        }

        async function Consultar_Lead_Time_Fases(congelado) {
            $('#loadingModal').modal('show');
            try {
                // Coleta todas as opções de Tipo Op que estão marcadas
                const arrayTipoOp = Array.from(document.querySelectorAll('.tipo-checkbox:checked')).map(checkbox => checkbox.id);

                const dados = {
                    dataInicio: $('#data-inicio').val(),
                    dataFim: $('#data-fim').val(),
                    arrayTipoOP: arrayTipoOp,
                    arrayCategorias: [],
                    congelado: congelado
                };

                const requestData = {
                    acao: "Consultar_Lead_Time_Fases",
                    dados: dados
                };

                const response = await $.ajax({
                    type: 'POST',
                    url: 'requests.php',
                    contentType: 'application/json',
                    data: JSON.stringify(requestData),
                });
                TabelaLeadTimeFase(response);
            } catch (error) {
                console.error('Erro na solicitação AJAX:', error);
            } finally {
                $('#loadingModal').modal('hide');
            }
        }

        async function Consultar_Lead_Time_Faccionistas(congelado) {
            $('#loadingModal').modal('show');
            try {
                // Coleta todas as opções de Tipo Op que estão marcadas
                const arrayTipoOp = Array.from(document.querySelectorAll('.tipo-checkbox:checked')).map(checkbox => checkbox.id);

                const dados = {
                    dataInicio: $('#data-inicio').val(),
                    dataFim: $('#data-fim').val(),
                    arrayTipoOP: arrayTipoOp,
                    arrayCategorias: [],
                    congelado: congelado
                };

                const requestData = {
                    acao: "Consultar_Lead_Time_Faccionistas",
                    dados: dados
                };

                const response = await $.ajax({
                    type: 'POST',
                    url: 'requests.php',
                    contentType: 'application/json',
                    data: JSON.stringify(requestData),
                });
                TabelaLeadTimeFaccionistas(response);
            } catch (error) {
                console.error('Erro na solicitação AJAX:', error);
            } finally {
                $('#loadingModal').modal('hide');
            }
        }

        async function Consultar_Lead_Time2(congelado) {

            try {
                const arrayTipoOp = Array.from(document.querySelectorAll('.tipo-checkbox:checked')).map(checkbox => checkbox.id);

                const dados = {
                    dataIncio: $('#data-inicio').val(),
                    dataFim: $('#data-fim').val(),
                    arrayTipoOP: arrayTipoOp,
                    congelado: true
                };

                const requestData = {
                    acao: "Consultar_Lead_Time",
                    dados: dados
                };

                const response = await $.ajax({
                    type: 'POST',
                    url: 'requests.php',
                    contentType: 'application/json',
                    data: JSON.stringify(requestData),
                });

                TabelaLeadTimes(response[0]['04-LeadTimeCategorias']);
                $('#qtd-pecas').html(parseInt(response[0]['03-TotalPeças']).toLocaleString() + ' Pçs');
                $('#lead-time').html(parseInt(response[0]['02-LeadTimeMediaPonderada']).toLocaleString() + ' dias');

            } catch (error) {
                console.error('Erro na solicitação AJAX:', error);
            } finally {}
        }

        async function Consultar_Lead_Time_Faccionistas2(congelado) {

            try {
                const arrayTipoOp = Array.from(document.querySelectorAll('.tipo-checkbox:checked')).map(checkbox => checkbox.id);

                const dados = {
                    dataInicio: $('#data-inicio').val(),
                    dataFim: $('#data-fim').val(),
                    arrayTipoOP: arrayTipoOp,
                    arrayCategorias: [],
                    congelado: congelado
                };

                const requestData = {
                    acao: "Consultar_Lead_Time_Faccionistas",
                    dados: dados
                };

                const response = await $.ajax({
                    type: 'POST',
                    url: 'requests.php',
                    contentType: 'application/json',
                    data: JSON.stringify(requestData),
                });
                TabelaLeadTimeFaccionistas(response);
            } catch (error) {
                console.error('Erro na solicitação AJAX:', error);
            } finally {}
        }


        function TabelaLeadTimes(listaMetas) {

            if ($.fn.DataTable.isDataTable('#table-lead-time-categorias')) {
                $('#table-lead-time-categorias').DataTable().destroy();
            }

            const tabela = $('#table-lead-time-categorias').DataTable({
                paging: false,
                info: false,
                searching: true,
                colReorder: true,
                data: listaMetas,
                lengthChange: false,
                pageLength: 10,
                fixedHeader: true,
                columns: [{
                        data: 'categoria'
                    },
                    {
                        data: 'Realizado'
                    },
                    {
                        data: 'meta'
                    },
                    {
                        data: 'LeadTimePonderado(diasCorridos)',
                        render: function(data, type, row) {
                            const realizado = parseInt(data);
                            const meta = parseInt(row['meta']);
                            let icon = '';

                            if (realizado <= meta) {
                                icon = ' <i class="fas fa-trophy" style="color: #FFCC33; float: right;"></i>';

                            } else if (realizado > meta) {
                                icon = ' <i class="fas fa-bomb" style="color: red; float: right;"></i>';
                            }

                            return realizado.toLocaleString() + icon;
                        }
                    }
                ],
            });

        }

        function TabelaLeadTimeFase(listaMetas) {

            if ($.fn.DataTable.isDataTable('#table-lead-time-fases')) {
                $('#table-lead-time-fases').DataTable().destroy();
            }

            const tabela = $('#table-lead-time-fases').DataTable({
                paging: false,
                info: false,
                searching: true,
                colReorder: true,
                data: listaMetas,
                lengthChange: false,
                pageLength: 10,
                fixedHeader: true,
                columns: [{
                        data: 'codfase'
                    },
                    {
                        data: 'nomeFase'
                    },
                    {
                        data: 'Realizado',
                        render: function(data) {
                            return parseInt(data).toLocaleString();
                        }
                    },
                    {
                        data: 'LeadTime(PonderadoPorQtd)',
                    }
                ],
            });

        }

        function TabelaLeadTimeFaccionistas(listaMetas) {

            if ($.fn.DataTable.isDataTable('#table-lead-time-faccionistas')) {
                $('#table-lead-time-faccionistas').DataTable().destroy();
            }

            const tabela = $('#table-lead-time-faccionistas').DataTable({
                paging: false,
                info: false,
                searching: true,
                colReorder: true,
                data: listaMetas,
                lengthChange: false,
                pageLength: 10,
                fixedHeader: true,
                columns: [{
                        data: 'codfaccionista'
                    },
                    {
                        data: 'apelidofaccionista'
                    },
                    {
                        data: 'Realizado',
                        render: function(data) {
                            return parseInt(data).toLocaleString();
                        }
                    },
                    {
                        data: 'LeadTime(PonderadoPorQtd)',
                    }
                ],
            });

        }

        $('input[type="date"]').on('change', function() {
            requestQueue.push({
                callback: () => Consultar_Lead_Time2(true), // Função que retorna uma promessa
                onComplete: () => {} // Função a ser chamada quando a requisição for concluída
            });

            requestQueue.push({
                callback: () => Consultar_Lead_Time_Faccionistas2(true), // Função que retorna uma promessa
                onComplete: () => {} // Função a ser chamada quando a requisição for concluída
            });

            processQueue(); // Inicia o processamento da fila se não estiver em andamento
        });


        const requestQueue = [];
        let isProcessingQueue = false;

        function processQueue() {
            if (isProcessingQueue || requestQueue.length === 0) {
                return; // Se já está processando ou não há requisições na fila, não faz nada
            }

            isProcessingQueue = true;
            const {
                callback,
                onComplete
            } = requestQueue.shift(); // Remove o primeiro item da fila

            $('.loading-icon').show();

            callback()
                .then(() => {
                    $('.loading-icon').hide();
                })
                .catch((error) => {
                    console.error('Erro ao consultar lead time:', error);
                    $('.loading-icon').hide();
                })
                .finally(() => {
                    isProcessingQueue = false;
                    onComplete();
                    processQueue(); // Processa a próxima requisição na fila
                });
        }

        $(document).on('change', '.tipo-checkbox', function() {
            requestQueue.push({
                callback: () => Consultar_Lead_Time2(true), // Função que retorna uma promessa
                onComplete: () => {} // Função a ser chamada quando a requisição for concluída
            });

            requestQueue.push({
                callback: () => Consultar_Lead_Time_Faccionistas2(true), // Função que retorna uma promessa
                onComplete: () => {} // Função a ser chamada quando a requisição for concluída
            });

            processQueue(); // Inicia o processamento da fila se não estiver em andamento
        });
    </script>
