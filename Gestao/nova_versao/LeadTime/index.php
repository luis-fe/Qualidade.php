<?php
include_once('requests.php');
include_once('../../../templates/LoadingGestao.php');
include_once('../../../templates/headerGestao.php');
?>
<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
    .menus {
        display: flex;
        justify-content: start;
        padding: 0px 10px;
        margin-top: 15px;
    }

    slider-container {
        margin-top: 20px;
    }

    .slider-label {
        font-size: 14px;
        margin-bottom: 5px;
    }

    .ui-slider-range {
        background: #10045a;
    }

    .ui-slider-handle {
        background: #FF5722;
        border: 2px solid #FFC107;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        cursor: pointer;
    }

    .custom-dropdown {
        position: relative;
        width: 300px;
    }

    .select-tipo-op {
        width: 100%;
        padding: 10px 15px;
        font-size: 16px;
        background-color: white;
        border: 1px solid #ced4da;
        border-radius: 4px;
        text-align: left;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .select-tipo-op::after {
        content: "▼";
        font-size: 12px;
        margin-left: 10px;
    }

    /* Menu do dropdown */
    .menu-tipo-op {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 999;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 10px;
        width: 300px;
        max-height: 300px;
        overflow-x: hidden;
        overflow-y: auto;
    }

    /* Itens do dropdown */
    .dropdown-item {
        padding: 10px 15px;
        display: flex;
        align-items: center;
        cursor: pointer;
        font-size: 14px;
    }

    .dropdown-item input {
        margin-right: 10px;
    }

    .dropdown-item:hover {
        background-color: #f1f1f1;
    }

    .btn-tabelas {
        padding: 2px 10px !important;
        border: 1px solid black !important;
        background-color: white !important;
        margin-left: 5px !important;
        margin-top: 10px;
        margin-bottom: 10px;
        border-radius: 5px !important;
    }
</style>

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-clock-history"></i></span> Lead Time
</div>

<div class="mt-3">
    <div class="row ">
        <!-- Data Inputs Section -->
        <div class="col-12 col-lg-4 mb-3">
            <div id="datas" class="d-flex flex-wrap col-12">
                <div class="mb-3 col-12 col-lg-6">
                    <label for="data-inicial" class="form-label fw-bold">Data Inicial</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <input type="date" class="form-control border-secondary rounded-end-3" id="data-inicial">
                    </div>
                </div>
                <div class="mb-3 col-12 col-lg-6">
                    <label for="data-final" class="form-label fw-bold">Data Final</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-secondary">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <input type="date" class="form-control border-secondary rounded-end-3" id="data-final">
                    </div>
                </div>
            </div>
            <div class="slider-container mt-2 w-100">
                <div id="slider"></div>
            </div>
        </div>

        <!-- Dropdown Section -->
        <div class="col-12 col-lg-2 mb-3 d-flex justify-content-end align-items-center">
            <div class="custom-dropdown w-100">
                <button class="select-tipo-op w-100" id="dropdownToggle">
                    Tipos de Op
                </button>
                <div class="menu-tipo-op" id="menu-tipo-op">
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-1 mb-3 d-flex justify-content-start align-items-center">
            <button class="btn btn-geral" onclick="async function atualizar(){await Consulta_Lead_Time_Categoria(); await Consulta_Lead_Time_Faccionistas(); await Consulta_Lead_Time_Fases()}; atualizar()">Filtrar</button>
        </div>

        <div class="col-12 col-lg-5 mt-3 d-flex flex-wrap justify-content-center justify-content-lg-start">
            <div class="cards d-flex flex-wrap justify-content-center">
                <div class="card text-center d-flex flex-row align-items-center p-3 shadow-sm m-2" style="max-width: 250px; min-width: 250px;">
                    <div class="img mr-3">
                        <i class="fa-solid fa-shirt fa-2x"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-center align-items-center" style="text-align: center; flex-grow: 1;">
                        <h5 class="card-title">Total de Peças</h5>
                        <p class="card-text" id="qtd-pecas"></p>
                    </div>
                </div>
                <div class="card text-center d-flex flex-row align-items-center p-3 shadow-sm m-2" style="max-width: 250px; min-width: 250px;">
                    <div class="img mr-3">
                        <i class="fa-solid fa-clock fa-2x"></i>
                    </div>
                    <div class="d-flex flex-column justify-content-center align-items-center" style="text-align: center; flex-grow: 1;">
                        <h5 class="card-title">Lead Time</h5>
                        <p class="card-text" id="lead-time"></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="mt-3" style="padding: 10px;">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6 mb-3 gap-3" style="border: 1px solid black; border-radius: 8px;" id="div-tabela-categorias">
            <h2 class="mt-2 ml-2" style="font-size: 18px; font-weight: bold;">Lead Time por categoria</h2>
            <div class="div-tabela-fases mt-3" style="max-width: 100%; overflow: auto;">
                <table class="table table-bordered table-striped" id="table-categorias" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Categoria<br><input type="search" class="search-input search-table-categorias"></th>
                            <th>Realizado Peças<br><input type="search" class="search-input search-table-categorias"></th>
                            <th>Meta<br><input type="search" class="search-input search-table-categorias"></th>
                            <th>Lead Time Realizado<br><input type="search" class="search-input search-table-categorias"></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabelas à direita (serão empilhadas em telas menores) -->
        <div class="col-12 col-lg-6 d-flex flex-column gap-3">
            <div class="div-tabela-fases" style="max-width: 100%; max-height: 300px; overflow: auto; border: 1px solid black; border-radius: 8px;">
                <h2 class="mt-2" style="font-size: 18px; font-weight: bold; margin-left: 10px">Lead Time por fase</h2>
                <table class="table table-bordered table-striped mt-3" id="table-fases">
                    <thead>
                        <tr>
                            <th>Cód. Fase<br><input type="search" class="search-input search-table-fases"></th>
                            <th>Nome Fase<br><input type="search" class="search-input search-table-fases"></th>
                            <th>Realizado Peças<br><input type="search" class="search-input search-table-fases"></th>
                            <th>Meta Lead Time<br><input type="search" class="search-input search-table-fases"></th>
                            <th>Realizado Lead Time<br><input type="search" class="search-input search-table-fases"></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="div-tabela-faccionista" style="max-width: 100%; max-height: 300px; overflow: auto; border: 1px solid black; border-radius: 8px;">
                <h2 class="mt-2 ml-2" style="font-size: 18px; font-weight: bold; margin-left: 10px">Lead Time por faccionista</h2>
                <table class="table table-bordered table-striped" id="table-faccionistas" style="width: 100%; ">
                    <thead>
                        <tr>
                            <th>Cód. Faccionista<br><input type="search" class="search-input search-table-faccionistas"></th>
                            <th>Nome Faccionista<br><input type="search" class="search-input search-table-faccionistas"></th>
                            <th>Realizado<br><input type="search" class="search-input search-table-faccionistas"></th>
                            <th>Lead Time<br><input type="search" class="search-input search-table-faccionistas"></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>




<?php
include_once('../../../templates/footerGestao.php');
?>
<script>
    $(document).ready(async function() {
        const hoje = new Date();
        const primeiroDiaMes = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
        const ultimoDiaMes = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0);

        const formatarData = (data) => data.toISOString().split('T')[0];

        const limiteInicio = new Date(primeiroDiaMes);
        limiteInicio.setFullYear(limiteInicio.getFullYear() - 1);
        const limiteFinal = ultimoDiaMes;

        $('#data-inicial').val(formatarData(primeiroDiaMes));
        $('#data-final').val(formatarData(ultimoDiaMes));

        $('#slider').slider({
            range: true,
            min: limiteInicio.getTime(),
            max: limiteFinal.getTime(),
            step: 86400000,
            values: [primeiroDiaMes.getTime(), ultimoDiaMes.getTime()],
            slide: function(event, ui) {
                const dataInicial = new Date(ui.values[0]);
                const dataFinal = new Date(ui.values[1]);

                $('#data-inicial').val(formatarData(dataInicial));
                $('#data-final').val(formatarData(dataFinal));
            },
        });

        $('#data-inicial, #data-final').on('change', function() {
            const novaInicial = new Date($('#data-inicial').val()).getTime();
            const novaFinal = new Date($('#data-final').val()).getTime();

            if (novaInicial >= limiteInicio.getTime() && novaInicial <= novaFinal) {
                $('#slider').slider('values', 0, novaInicial);
            }
            if (novaFinal <= limiteFinal.getTime() && novaFinal >= novaInicial) {
                $('#slider').slider('values', 1, novaFinal);
            }
        });

        const $menu = $("#menu-tipo-op");

        $("#dropdownToggle").on("click", function(event) {
            event.preventDefault();
            event.stopPropagation();

            const $button = $(this);
            const offset = $button.offset();

            if ($menu.is(":visible")) {
                $menu.hide();
            } else {
                $menu
                    .appendTo("body")
                    .css({
                        position: "absolute",
                        top: offset.top + $button.outerHeight(),
                        left: offset.left,
                        display: "block",
                    });
            }
        });

        $menu.on("click", function(event) {
            event.stopPropagation();
        });

        // Fecha o menu ao clicar fora
        $(document).on("click", function() {
            $menu.hide();
        });

        await Consulta_Tipos_Op();
        await Consulta_Lead_Time_Categoria();
        await Consulta_Lead_Time_Fases();
        Consulta_Lead_Time_Faccionistas()
    });

    const Consulta_Tipos_Op = async () => {
        try {
            $('#loadingModal').modal('show');

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consulta_Tipos_Op'
                },
            });
            const $menu = $("#menu-tipo-op");
            $menu.empty();
            response.forEach(item => {
                const $option = $("<li>")
                    .addClass("dropdown-item")
                    .html(`
                        <label>
                            <input type="checkbox" value="${item.tipoOP}" checked />
                            ${item.tipoOP}
                        </label>
                    `);

                $menu.append($option);
            });
        } catch (error) {
            console.error('Erro:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    };

    async function Consulta_Lead_Time_Categoria() {
        $('#loadingModal').modal('show');
        const dados = {
            "dataIncio": $('#data-inicial').val(),
            "dataFim": $('#data-final').val(),
            "arrayTipoOP": $("#menu-tipo-op input[type='checkbox']:checked")
                .map(function() {
                    return $(this).val();
                })
                .get()
        };

        var requestData = {
            acao: "Consulta_Lead_Time_Categoria",
            dados: dados
        };

        try {
            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            TabelaCategorias(response['resposta'][0]['04-LeadTimeCategorias']);
            $('#qtd-pecas').text(parseInt(response['resposta'][0]['03-TotalPeças']).toLocaleString() + ' Pçs');
            $('#lead-time').text(parseInt(response['resposta'][0]['02-LeadTimeMediaPonderada']).toLocaleString() + ' dias');
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error); // Exibir erro se ocorrer
            $('#loadingModal').modal('hide');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    async function Consulta_Lead_Time_Fases() {
        $('#loadingModal').modal('show');
        const dados = {
            "dataInicio": $('#data-inicial').val(),
            "dataFim": $('#data-final').val(),
            "congelado": false,
            "arrayTipoOP": $("#menu-tipo-op input[type='checkbox']:checked")
                .map(function() {
                    return $(this).val();
                })
                .get()
        };

        var requestData = {
            acao: "Consulta_Lead_Time_Fases",
            dados: dados
        };

        try {
            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            TabelaFases(response.resposta);
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error); // Exibir erro se ocorrer
            $('#loadingModal').modal('hide');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    async function Consulta_Lead_Time_Faccionistas() {
        $('#loadingModal').modal('show');
        const dados = {
            "dataInicio": $('#data-inicial').val(),
            "dataFim": $('#data-final').val(),
            "congelado": false,
            "arrayTipoOP": $("#menu-tipo-op input[type='checkbox']:checked")
                .map(function() {
                    return $(this).val();
                })
                .get()
        };

        var requestData = {
            acao: "Consulta_Lead_Time_Faccionistas",
            dados: dados
        };

        try {
            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response);
            TabelaFaccionistas(response.resposta)
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error); // Exibir erro se ocorrer
            $('#loadingModal').modal('hide');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }



    function TabelaCategorias(listaCategorias) {
        if ($.fn.DataTable.isDataTable('#table-categorias')) {
            $('#table-categorias').DataTable().destroy();
        }

        const tabela = $('#table-categorias').DataTable({
            searching: true,
            paging: false,
            lengthChange: false,
            info: false,
            pageLength: 10,
            data: listaCategorias,
            columns: [{
                    data: 'categoria'
                },
                {
                    data: 'Realizado',
                    render: data => parseInt(data).toLocaleString()
                },
                {
                    data: 'meta'
                },
                {
                    data: 'LeadTimePonderado(diasCorridos)'
                }
            ],
            language: {
                paginate: {
                    previous: '<i class="fa-solid fa-backward-step"></i>',
                    next: '<i class="fa-solid fa-forward-step"></i>'
                },
                info: "Página _PAGE_ de _PAGES_",
                emptyTable: "Nenhum dado disponível na tabela",
                zeroRecords: "Nenhum registro encontrado"
            },
        });

        $('.search-table-categorias').on('input', function() {
            tabela.column($(this).closest('th').index()).search($(this).val()).draw();
        });
    }

    function TabelaFases(listaFases) {
        if ($.fn.DataTable.isDataTable('#table-fases')) {
            $('#table-fases').DataTable().destroy();
        }

        const tabela = $('#table-fases').DataTable({
            searching: true,
            paging: false,
            lengthChange: false,
            info: false,
            pageLength: 10,
            data: listaFases,
            columns: [{
                    data: 'codfase'
                },
                {
                    data: 'nomeFase'
                },
                {
                    data: 'Realizado',
                    render: data => parseInt(data).toLocaleString()
                },
                {
                    data: 'metaLeadTime'
                },
                {
                    data: 'LeadTime(PonderadoPorQtd)'
                }
            ],
            language: {
                paginate: {
                    previous: '<i class="fa-solid fa-backward-step"></i>',
                    next: '<i class="fa-solid fa-forward-step"></i>'
                },
                info: "Página _PAGE_ de _PAGES_",
                emptyTable: "Nenhum dado disponível na tabela",
                zeroRecords: "Nenhum registro encontrado"
            },
        });

        $('.search-table-fases').on('input', function() {
            tabela.column($(this).closest('th').index()).search($(this).val()).draw();
        });
    }

    function TabelaFaccionistas(listaFaccionistas) {
        if ($.fn.DataTable.isDataTable('#table-faccionistas')) {
            $('#table-faccionistas').DataTable().destroy();
        }

        const tabela = $('#table-faccionistas').DataTable({
            searching: true,
            paging: false,
            lengthChange: false,
            info: false,
            pageLength: 10,
            data: listaFaccionistas,
            columns: [{
                    data: 'codfaccionista'
                },
                {
                    data: 'apelidofaccionista'
                },
                {
                    data: 'Realizado',
                    render: data => parseInt(data).toLocaleString()
                },
                {
                    data: 'LeadTime(PonderadoPorQtd)'
                }
            ],
            language: {
                paginate: {
                    previous: '<i class="fa-solid fa-backward-step"></i>',
                    next: '<i class="fa-solid fa-forward-step"></i>'
                },
                info: "Página _PAGE_ de _PAGES_",
                emptyTable: "Nenhum dado disponível na tabela",
                zeroRecords: "Nenhum registro encontrado"
            },
        });

        $('.search-table-faccionistas').on('input', function() {
            tabela.column($(this).closest('th').index()).search($(this).val()).draw();
        });
    }
</script>
