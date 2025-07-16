<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Produtividade-WMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8, #ffffff);
            font-family: 'Segoe UI', sans-serif;
        }

        .titulo-dashboard {
            background-color: #112d7e;
            color: white;
            padding: 3px;
            text-align: center;
            font-weight: bold;
            font-size: 1.8rem;
        }

        .card-kpi {
            background: white;
            border-left: 6px solid #112d7e;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            transition: transform 0.2s ease;
            padding-left: 10px;
        }

        .card-kpi:hover {
            transform: scale(1.02);
        }

        .card-kpi i {
            font-size: 2.5rem;
            color: #112d7e;
            margin-right: 10px;
        }

        .table thead {
            background-color: #112d7e;
            color: white;
        }

        .table thead th {
            font-size: 1.5rem;
            background-color: #112d7e;
            /* azul apenas no cabeçalho */
            color: white;
            padding: 4px 8px;
            line-height: 1.2;
            vertical-align: middle;
        }

        .table tbody td {
            font-size: 30px;
            padding: 4px 8px;
            line-height: 1;
            vertical-align: middle;
            font-weight: 600;
        }

        .table tbody tr {
            background-color: white;
            /* ou remova para manter padrão */
        }


        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.3rem 0.6rem;
            border-radius: 5px;
        }

        @media (min-width: 992px) {
            .tabela-prod {
                display: flex;
                gap: 20px;
            }

            .tabela-prod>div {
                flex: 1;
            }
        }

        .fa-trophy {
            color: yellow;
        }
    </style>
</head>

<body>

    <div class="titulo-dashboard">Dashboard de Produtividade</div>

    <div class="" style="padding-left: 5px; padding-right:5px">
        <!-- Filtros -->
        <div class="row mb-2 mt-2">
            <div class="col-md-5">
                <input type="date" id="dataInicio" class="form-control">
            </div>
            <div class="col-md-5">
                <input type="date" id="dataFim" class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="AtualizarDados()" id="btnFiltrar">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
        </div>

        <!-- KPIs -->
        <div class="row g-4 mb-3">
            <div class="col-md-4">
                <div class="card card-kpi">
                    <div class="d-flex align-items-center ">
                        <i class="fa-solid fa-truck-ramp-box fa-2x me-3"></i> <!-- Ícone à esquerda -->
                        <div class="text-center w-100"> <!-- Centraliza apenas os textos -->
                            <h3 class="mb-0">Retorna</h3>
                            <h3 id="retorna-pc" class="mb-1"></h3>
                            <h3 id="retorna-rs"></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-kpi">
                    <div class="d-flex align-items-center ">
                        <i class="fa-solid fa-check fa-2x me-3"></i> <!-- Ícone à esquerda -->
                        <div class="text-center w-100"> <!-- Centraliza apenas os textos -->
                            <h3 class="mb-0">Pronta Entrega</h3>
                            <h3 id="pronta-entrega-pc"></h3>
                            <h3 id="pronta-entrega-rs"></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-kpi">
                    <div class="d-flex align-items-center ">
                        <i class="fa-solid fa-truck fa-2x me-3"></i> <!-- Ícone à esquerda -->
                        <div class="text-center w-100"> <!-- Centraliza apenas os textos -->
                            <h3 class="mb-0">Faturado</h3>
                            <h3 id="faturado-pc" class="mb-1"></h3>
                            <h3 id="faturado-rs"></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabelas lado a lado -->
        <!-- Tabelas lado a lado e uma abaixo -->
        <div class="tabela-prod mb-3" style="min-height: 65vh; max-height: 65vh;">
            <div class="row g-3">
                <!-- Repositores (lado esquerdo) -->
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm h-100" style="overflow: auto;">
                        <h5 class="d-flex justify-content-between align-items-center" style="font-size: 23px; font-weight: 600" id="repositores"></h5>
                        <div class="table-responsive">
                            <table id="tabelaRepositores" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Ranking</th>
                                        <th>Colaborador</th>
                                        <th>Qtd.</th>
                                        <th>Ritmo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Os dados serão preenchidos aqui -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Separadores (lado direito) -->
                <div class="col-md-6">
                    <div class="card p-3 shadow-sm h-100" style="overflow: auto;">
                        <h5 class="d-flex justify-content-between align-items-center" style="font-size: 23px; font-weight: 600" id="separadores"></h5>
                        <div class="table-responsive">
                            <table id="tabelaSeparadores" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank.</th>
                                        <th style="min-width: 220px; max-width: 220px">Colaborador</th>
                                        <th>Qtd</th>
                                        <th>Qtd. Ped.</th>
                                        <th>Méd. Pçs</th>
                                        <th>Ritmo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Os dados serão preenchidos aqui -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Caixas (embaixo das duas) -->
                <div class="col-12">
                    <div class="card p-3 shadow-sm" style="overflow: auto;">
                        <h5 class="d-flex justify-content-between align-items-center" style="font-size: 23px; font-weight: 600" id="caixas"></h5>
                        <div class="table-responsive">
                            <table id="tabelaCaixas" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Rank.</th>
                                        <th>Colaborador</th>
                                        <th>Qtd. Caixas</th>
                                        <th>Qtd. Peças</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Os dados serão preenchidos aqui -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        let isAtualizando = false;

        $(document).ready(async function() {
            const currentDate = new Date();
            const formattedDate = FormatarData(currentDate);
            $("#dataInicio").val(formattedDate);
            $("#dataFim").val(formattedDate);

            await AtualizarDados();

            // Atualiza os dados a cada 1 minuto, somente se não estiver atualizando
            setInterval(async () => {
                if (!isAtualizando) {
                    await AtualizarDados();
                }
            }, 60000);
        });

        async function AtualizarDados() {
            isAtualizando = true;
            try {
                await Consultar_Faturamentos();
                await Consultar_Produtividade_Separacao('TagsSeparacao');
                await Consultar_Produtividade_Reposicao('TagsReposicao');
                await Consultar_Produtividade_Caixa();
            } catch (error) {
                console.error("Erro ao atualizar dados:", error);
            } finally {
                isAtualizando = false;
            }
        }

        const Consultar_Faturamentos = async () => {
            try {
                const response = await $.ajax({
                    type: 'GET',
                    url: 'requests.php',
                    dataType: 'json',
                    data: {
                        acao: 'Consultar_Faturamentos',
                        "dataInicio": $('#dataInicio').val(),
                        "dataFim": $('#dataFim').val(),
                    },
                });
                console.log(response[0]['Pcs Retorna'])
                $('#retorna-pc').text(response[0]['Pcs Retorna']);
                $('#retorna-rs').text(response[0]['No Retorna']);
                $('#pronta-entrega-pc').text(response[0]['Pç Pronta Entrega']);
                $('#pronta-entrega-rs').text(response[0]['Retorna ProntaEntrega']);
                $('#faturado-pc').text(response[0]['qtdePecas Faturado']);
                $('#faturado-rs').text(response[0]['Total Faturado']);
            } catch (error) {
                console.error('Erro:', error);
            } finally {}
        };

        function FormatarData(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        const Consultar_Produtividade_Reposicao = async (Cosulta) => {
            try {
                const response = await $.ajax({
                    type: 'GET',
                    url: 'requests.php',
                    dataType: 'json',
                    data: {
                        acao: 'Consultar_Produtividade',
                        dataInicio: $('#dataInicio').val(),
                        dataFim: $('#dataFim').val(),
                        Consulta: Cosulta,
                        HoraInicio: "00:01",
                        HoraFim: "23:59"
                    },
                });

                if (!response || !response[0] || !response[0]['3- Ranking Repositores']) {
                    console.log("Nenhum dado encontrado.");
                } else {
                    console.log(response[0]['3- Ranking Repositores']);
                    $('#repositores').html(`
                        <span><i class="fa-solid fa-trophy"></i> ${response[0]['1- Record Repositor']} ${response[0]['1.1- Record qtd']} Pçs</span>
                        <span>Total Reposto: ${response[0]['2 Total Periodo']} Pçs</span>
                    `);
                    let colaboradores = response[0]['3- Ranking Repositores'];
                    colaboradores.sort((a, b) => b.qtde - a.qtde); // Ordena de maior para menor produção

                    // Cria o corpo da tabela com o ranking
                    let tbodyHTML = '';
                    colaboradores.forEach((item, index) => {
                        let ranking = `${index + 1}º`; // A posição começa de 1, então adicionamos 1 ao índice
                        tbodyHTML += `
                    <tr class="text-center">
                        <td>${ranking}</td>
                        <td>${item.nome}</td>
                        <td>${item.qtde.toLocaleString('pt-BR')}</td>
                        <td>${item.ritmo} s</td>
                    </tr>
                `;
                    });

                    // Atualiza o tbody da tabela existente
                    $('#tabelaRepositores tbody').html(tbodyHTML);
                }

            } catch (error) {
                console.error('Erro:', error);
            }
        };

        const Consultar_Produtividade_Separacao = async (Cosulta) => {
            try {
                const response = await $.ajax({
                    type: 'GET',
                    url: 'requests.php',
                    dataType: 'json',
                    data: {
                        acao: 'Consultar_Produtividade',
                        dataInicio: $('#dataInicio').val(),
                        dataFim: $('#dataFim').val(),
                        Consulta: Cosulta,
                        HoraInicio: "00:01",
                        HoraFim: "23:59"
                    },
                });

                if (!response || !response[0] || !response[0]['3- Ranking Repositores']) {
                    console.log("Nenhum dado encontrado.");
                } else {
                    console.log(response[0]['3- Ranking Repositores']);

                    let colaboradores = response[0]['3- Ranking Repositores'];
                    colaboradores.sort((a, b) => b.qtde - a.qtde); // Ordena de maior para menor produção

                    $('#separadores').html(`
                        <span><i class="fa-solid fa-trophy"></i> ${response[0]['1- Record Repositor']} ${response[0]['1.1- Record qtd']} Pçs</span>
                        <span>Total Separado: ${response[0]['2 Total Periodo']} Pçs</span>
                    `);
                    // Cria o corpo da tabela com o ranking
                    let tbodyHTML = '';
                    colaboradores.forEach((item, index) => {
                        let ranking = `${index + 1}º`; // A posição começa de 1, então adicionamos 1 ao índice
                        tbodyHTML += `
                    <tr class="text-center">
                        <td>${ranking}</td>
                        <td>${item.nome}</td>
                        <td>${item.qtde.toLocaleString('pt-BR')}</td>
                        <td>${item["Qtd Pedido"].toLocaleString('pt-BR')}</td>
                        <td>${item["Méd pçs/ped."].toLocaleString('pt-BR')}</td>
                        <td>${item.ritmo} s</td>
                    </tr>
                `;
                    });

                    // Atualiza o tbody da tabela existente
                    $('#tabelaSeparadores tbody').html(tbodyHTML);
                }

            } catch (error) {
                console.error('Erro:', error);
            }
        };

        const Consultar_Produtividade_Caixa = async (Cosulta) => {
            try {
                const response = await $.ajax({
                    type: 'GET',
                    url: 'requests.php',
                    dataType: 'json',
                    data: {
                        acao: 'Consultar_Produtividade_Caixa',
                        dataInicio: $('#dataInicio').val(),
                        dataFim: $('#dataFim').val()
                    },
                });

                if (!response || !response[0] || !response[0]['3- Ranking Carregar Endereco']) {
                    console.log("Nenhum dado encontrado.");
                } else {
                    console.log(response[0]['3- Ranking Carregar Endereco']);

                    let colaboradores = response[0]['3- Ranking Carregar Endereco'];
                    colaboradores.sort((a, b) => b.qtde - a.qtde); // Ordena de maior para menor produção

                    $('#caixas').html(`
                        <span><i class="fa-solid fa-trophy"></i> ${response[0]['1- Record']}: ${response[0]['1.1- Record qtdCaixas']} Caixas</span>
                        <span>Total Caixas: ${response[0]['2 Total Caixas']} Caixas</span>
                        <span>Total Peças: ${response[0]['2.1 Total Pcs']} Caixas</span>
                    `);
                    // Cria o corpo da tabela com o ranking
                    let tbodyHTML = '';
                    colaboradores.forEach((item, index) => {
                        let ranking = `${index + 1}º`; // A posição começa de 1, então adicionamos 1 ao índice
                        tbodyHTML += `
                    <tr class="text-center">
                        <td>${ranking}</td>
                        <td>${item.nome}</td>
                        <td>${item.qtdCaixas.toLocaleString('pt-BR')}</td>
                        <td>${item.qtdPcs.toLocaleString('pt-BR')}</td>
                    </tr>
                `;
                    });

                    // Atualiza o tbody da tabela existente
                    $('#tabelaCaixas tbody').html(tbodyHTML);
                }
            } catch (error) {
                console.error('Erro:', error);
            }
        };
    </script>
</body>
</html>
