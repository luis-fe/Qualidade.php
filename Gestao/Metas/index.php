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
<!-- <link rel="stylesheet" href="style.css"> -->
<style>
    body {
        font-family: Arial, sans-serif;
    }

    .container-tabela {
        width: 100%;
        max-height: 80%;
        overflow: auto;
        background-color: white;
        margin-left: auto;
        margin-right: auto;
    }

    .select2-dropdown {
        max-height: 400px;
        overflow-y: auto;
    }

    .table {
        width: 100%;
        overflow: auto;
        border: none !important;
    }

    .table th, .table td {
        white-space: nowrap;
        border-bottom: 1px solid var(--Cinza);
        vertical-align: middle;
        border: 1px solid #dee2e6;
        font-size: 1rem;
    }

    .table tbody tr:hover {
        background-color: lightblue;
    }

    .table th {
        background-color: var(--CorMenu) !important;
        color: var(--Branco) !important;
        text-align: center;
    }

    .table td {
        color: var(--Preto);
        font-size: 0.9rem;
        font-weight: 600;
    }

    .table-custom td:nth-child(1),
    .table-custom th:nth-child(1) {
        min-width: 150px;
        word-wrap: break-word;
        white-space: normal;
    }

    .table-custom td:nth-child(2),
    .table-custom th:nth-child(2),
    .table-custom td:nth-child(3),
    .table-custom th:nth-child(3),
    .table-custom td:nth-child(4),
    .table-custom th:nth-child(4),
    .table-custom td:nth-child(5),
    .table-custom th:nth-child(5),
    .table-custom td:nth-child(6),
    .table-custom th:nth-child(6) {
        min-width: 100px;
        word-wrap: break-word;
        white-space: normal;
    }

    #fixed-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: white;
    }

    .dataTables_wrapper .dataTables_filter {
        display: none;
    }

    .selected-row {
        background-color: lightblue !important;
    }

    .ButtonModal {
        margin-top: 5px;
        border-radius: 5px !important;
    }

    .ButtonModal i {
        font-size: 20px;
        color: var(--CorMenu);
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .container {
            padding: 10px;
        }

        .table th, .table td {
            font-size: 0.8rem;
        }

        .table-custom td:nth-child(1),
        .table-custom th:nth-child(1) {
            min-width: 100px;
        }

        .table-custom td:nth-child(2),
        .table-custom th:nth-child(2),
        .table-custom td:nth-child(3),
        .table-custom th:nth-child(3),
        .table-custom td:nth-child(4),
        .table-custom th:nth-child(4),
        .table-custom td:nth-child(5),
        .table-custom th:nth-child(5),
        .table-custom td:nth-child(6),
        .table-custom th:nth-child(6) {
            min-width: 80px;
        }

        .modal-dialog {
            max-width: 90%;
        }
    }

    @media (min-width: 769px) and (max-width: 992px) {
        .container {
            padding: 20px;
        }

        .table th, .table td {
            font-size: 0.9rem;
        }

        .modal-dialog {
            max-width: 80%;
        }
    }

    @media (min-width: 993px) {
        .container {
            padding: 30px;
        }

        .table th, .table td {
            font-size: 1rem;
        }
    }

    /* Ajustes para TVs e telas muito grandes */
    @media (min-width: 1200px) {
        .container {
            padding: 40px;
        }
    }
    #fixed-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: white;
    }

    .dataTables_wrapper .dataTables_filter {
        display: none;
    }

    .selected-row {
        background-color: lightblue !important;
    }


    .ButtonModal {
        margin-top: 5px;
        border-radius: 5px !important;
    }


    .ButtonModal i {
        font-size: 20px;
        color: var(--CorMenu);
    }
</style>


<label for="" style="font-size: 25px; font-weight: 700; color: black; border-bottom: 1px solid var(--CorMenu)" class="d-flex flex-start col-12">Metas</label>
<div class="container" id="teste" style="background-color: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); border-radius: 5px; height: calc(100vh - 170px); max-height: calc(100vh - 170px); overflow: auto; width: 100%; min-width: 100%; padding-top: 15px; padding-left: 30px;">
    <div class="row text-align-start justify-content-start mb-1">
        <div class="row mt-3 mb-3 align-items-end">
            <div class="col-12 col-md-2 d-flex align-items-center">
                <div class="input-group">
                    <input type="search" id="codigoPlano" class="form-control" placeholder="Plano" style="font-size: 0.9rem" onkeydown="if (event.key === 'Enter') {event.preventDefault(); ConsultaLote()}">
                    <span class="input-group-text" id="search-icon" onclick="Consulta_Planos_Disponiveis()" style="font-size: 0.9rem; cursor:pointer">
                        <i class="lni lni-search-alt"></i>
                    </span>
                </div>
            </div>
            <div class="col-12 col-md-5 d-flex align-items-center mt-3 mt-md-0">
                <div class="input-group">
                    <input type="search" id="DescPlano" readonly class="form-control" placeholder="Descrição" style="font-size: 0.9rem">
                </div>
            </div>
        </div>


        <div class="row mt-3 col-12 mb-3 align-items-end d-none" id="selects">
            <div class="col-12 col-md-3">
                <label for="Lote" class="form-label">Lote</label>
                <select class="form-select" id="SelectLote" style="font-size: 0.9rem" onchange="SelecaoLote()">
                </select>
            </div>
            <div class="col-12 col-md-3"></div>
            <div class="col-12 col-md-6 d-flex justify-content-end align-items-center">
                <div class="input-group col-12 d-none" id="campo-search">
                    <input type="search" id="search" class="form-control" placeholder="Buscar" aria-label="Pesquisar" aria-describedby="search-icon" style="font-size: 0.9rem">
                    <span class="input-group-text" id="search-icon" onclick="document.getElementById('search').focus()" style="font-size: 0.9rem">
                        <i class="lni lni-search-alt"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="container-tabela">
            <table id="table" class="table table-custom table-striped d-none" style="width: 100%;">
                <thead id="fixed-header">
                    <tr>
                        <th scope="col">Sequencia</th>
                        <th scope="col">Cod. Fase <span><i class="fa-solid fa-chevron-down no-sort" style="font-size: 8px; cursor: pointer" onclick="console.log('teste')"></i></span></th>
                        <th scope="col">Nome Fase</th>
                        <th scope="col">Previsão de Peças</th>
                        <th scope="col">Falta Programar</th>
                        <th scope="col">Carga</th>
                        <th scope="col">Fila</th>
                        <th scope="col">Falta Produzir</th>
                        <th scope="col">Qtd. Dias</th>
                        <th scope="col">Meta Dia</th>
                        <th scope="col">Realizado</th>
                        <th scope="col">Efic %</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="container3" style="margin-top: 1rem; width: 100%">
            <div class="col-12 align-items-center text-align-center justify-content-center" id="Paginacao">
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="ModalPlanosDisponiveis" tabindex="-1" role="dialog" aria-labelledby="ModalPlanosDisponiveis" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="max-height: 80vh">
            <div class="modal-header">
                <h5 class="modal-title">Planos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 75vh; overflow: auto">
                <div class="input-group col-md-6 col-12 mb-3 ms-auto" style="width: auto;">
                    <input type="search" id="search-planos" class="form-control" placeholder="Pesquisar" style="background-color: white; color: black">
                    <span class="input-group-text" id="search-icon" style="background-color: white; color: black">
                        <i class="lni lni-search-alt"></i>
                    </span>
                </div>
                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                    <table class="table table-bordered table-striped" id="table-planos-disponiveis" style="width: 100%; min-width: 100%">
                        <thead id="fixed-header" style="position: sticky; top: 0; background: white; z-index: 1;">
                            <tr>
                                <th>Código do Plano</th>
                                <th>Descrição do Plano</th>
                                <th>selecionar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Conteúdo da tabela -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="filtrosModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="data-inicial">Data Início</label>
                        <input type="date" class="form-control" id="data-inicial" name="data-inicial">
                    </div>
                    <div class="form-group">
                        <label for="data-final">Data Fim</label>
                        <input type="date" class="form-control" id="data-final" name="data-final">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="$('#filtrosModal').modal('hide'); ConsultarMetas(true)">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCronogramas" tabindex="-1" aria-labelledby="modalCronogramas" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cronograma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="data-inicial">Data Início</label>
                        <input type="date" class="form-control" id="data-inicial-cronograma" name="data-inicial-cronograma" readonly>
                    </div>
                    <div class="form-group">
                        <label for="data-final">Data Fim</label>
                        <input type="date" class="form-control" id="data-final-cronograma" name="data-final-cronograma" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <!-- <button type="button" class="btn btn-primary" onclick="$('#filtrosModal').modal('hide'); ConsultarMetas()">Aplicar Filtros</button> -->
            </div>
        </div>
    </div>
</div>

<?php include_once("../../templates/footer1.php"); ?>

<!-- <script src="script.js"> -->

</script>

<script>
    const Consulta_Planos_Disponiveis = async () => {
        $('#loadingModal').modal('show');
        try {
            const data = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consulta_Planos_Disponiveis',
                }
            });
            await Tabela_Planos(data);
            $('#ModalPlanosDisponiveis').modal('show')
        } catch (error) {
            console.error('Erro ao consultar chamados:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    };

    function FormatarData(date) {
        const parts = date.split('/');
        return `${parts[2]}-${parts[1]}-${parts[0]}`;
    }

    const Consulta_Cronograma_Fase = async (codPlano, codFase) => {
        $('#loadingModal').modal('show');
        try {
            const data = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consulta_Cronograma_Fase',
                    codPlano: codPlano,
                    codFase: codFase
                }
            });
            console.log(data[0]['DataInicio'])
            $('#data-inicial-cronograma').val(FormatarData(data[0]['DataInicio']));
            $('#data-final-cronograma').val(FormatarData(data[0]['DataFim']));
            $('#modalCronogramas').modal('show')
        } catch (error) {
            console.error('Erro ao consultar chamados:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    };

    function Tabela_Planos(listaPlanos) {
        if ($.fn.DataTable.isDataTable('#table-planos-disponiveis')) {
            $('#table-planos-disponiveis').DataTable().destroy();
        }

        const tabela = $('#table-planos-disponiveis').DataTable({
            searching: true,
            paging: false,
            lengthChange: false,
            info: false,
            pageLength: 10,
            data: listaPlanos,
            columns: [{
                    data: '01- Codigo Plano'
                },
                {
                    data: '02- Descricao do Plano'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                        <div" style="display: flex; justify-content: space-around; align-items: center; height: 100%;">
                            <button class="btn" style="background-color: var(--CorMenu); color: var(--Branco);" onclick="$('#codigoPlano').val('${row['01- Codigo Plano']}'); ConsultaLote(); $('#ModalPlanosDisponiveis').modal('hide')">Selecionar</button>
                        </div>
                    `;
                    }
                }
            ],
            language: {
                emptyTable: "Nenhum dado disponível na tabela",
                zeroRecords: "Nenhum registro encontrado"
            },
        });

        // Associar a busca ao campo de pesquisa do modal
        $('#search-planos').on('keyup', function() {
            tabela.search(this.value).draw();
        });
    }

    $(document).ready(() => {

        $('#NomeRotina').text('Metas');
        $('#loadingModal').modal('hide')
        const hoje = new Date().toISOString().split('T')[0];
        document.getElementById('data-inicial').value = hoje;
        document.getElementById('data-final').value = hoje;
    });


    async function ConsultarMetas(congelado) {
        $('#loadingModal').modal('show');
        try {
            const dados = {
                codigoPlano: $('#codigoPlano').val(),
                arrayCodLoteCsw: [$('#SelectLote').val()],
                dataMovFaseIni: $('#data-inicial').val(),
                dataMovFaseFim: $('#data-final').val(),
                congelado: congelado
            };

            const requestData = {
                acao: "Consultar_Metas",
                dados: dados
            };

            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response);
            CriarTabelaMetas(response[0]['1-Detalhamento']);
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    function CriarTabelaMetas(listaMetas) {
        if ($.fn.DataTable.isDataTable('#table')) {
            $('#table').DataTable().destroy();
        }

        const tabela = $('#table').DataTable({
            paging: false,
            info: false,
            searching: true,
            colReorder: true,
            data: listaMetas,
            lengthChange: false,
            pageLength: 10,
            fixedHeader: true,
            ordering: false,
            dom: 'Bfrtip',
            buttons: [{
                text: '<i class="fa-solid fa-filter"></i>',
                className: 'ButtonModal',
                action: function(e, dt, node, config) {
                    $('#filtrosModal').modal('show');
                },
                attr: {
                    title: 'Filtros'
                }
            }],
            columns: [{
                    data: 'apresentacao',
                    visible: false
                },
                {
                    data: 'codFase',
                    visible: false
                },
                {
                    data: 'nomeFase'
                },
                {
                    data: 'previsao',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'FaltaProgramar',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'Carga Atual',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'Fila',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'Falta Produzir',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'dias',
                    render: function(data, type, row) {
                        return `<span class="diasClicado" data-fase="${row.codFase}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
                    }
                },
                {
                    data: 'Meta Dia',
                    render: function(data) {
                        return parseInt(data).toLocaleString();
                    }
                },
                {
                    data: 'Realizado',
                    render: function(data, type, row) {
                        const realizado = parseInt(data);
                        const meta = parseInt(row['Meta Dia']);
                        let icon = '';

                        if (realizado < meta) {
                            icon = ' <i class="fas fa-arrow-down" style="color: red; float: right;"></i>';
                        } else if (realizado > meta) {
                            icon = ' <i class="fas fa-arrow-up" style="color: green; float: right;"></i>';
                        }

                        return realizado.toLocaleString() + icon;
                    }
                },
                {
                data: null, // Coluna para a porcentagem
                render: function(data, type, row) {
                    const realizado = parseInt(row['Realizado']);
                    const meta = parseInt(row['Meta Dia']);
                    if (meta === 0) {
                        return '0%';
                    }
                    const percent = (realizado / meta * 100).toFixed(2);
                    return `${percent}%`;
                }
            }
            ],
            language: {
                paginate: {
                    first: 'Primeira',
                    previous: '<',
                    next: '>',
                    last: 'Última',
                },
            },
        });

        $('#search').on('keyup', function() {
            tabela.search(this.value).draw();
        });

        $('#table').on('click', '.diasClicado', function() {
            const codFase = $(this).data('fase');
            console.log(codFase)
            Consulta_Cronograma_Fase($('#codigoPlano').val(), codFase);
        });
    }


    async function ConsultaLote() {
        if ($('#codigoPlano').val() === '') {
            Mensagem('Campo Vazio', 'warning')
        } else {
            try {
                const response = await $.ajax({
                    type: 'GET',
                    url: 'requests.php',
                    dataType: 'json',
                    data: {
                        acao: 'Consultar_Lotes',
                        plano: $('#codigoPlano').val()
                    }
                });

                const $select = $('#SelectLote');
                $select.empty();
                $select.append('<option value="">Selecione o Lote:</option>');

                response.forEach(item => {
                    $select.append(`<option value="${item.lote}">${item.lote} - ${item.nomelote}</option>`);
                });
                // Ativar o Select2 para o elemento
                $('#SelectLote').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    height: '30px',

                });
                $('.select2-selection__rendered').addClass('form-control')

                $('#DescPlano').val(response[0]['nomelote']);
                $('#selects').removeClass('d-none');
            } catch (error) {
                console.error('Erro ao consultar chamados:', error);
            } finally {
                $('#loadingModal').modal('hide');
            }
        }
    }



    function SelecaoLote() {

        ConsultarMetas(false);
        $('#table').removeClass('d-none');
        $('#campo-search').removeClass('d-none');

    }
</script>
