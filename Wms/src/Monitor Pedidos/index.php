<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
?>

<link rel="stylesheet" href="style.css">
<style>
    #form-container {
        min-width: 100%;
        width: 100%;
        height: calc(100vh - 50px);
        padding: 20px;
        overflow-y: auto;
        background-color: gray;
    }

    .Corpo {
        width: 100%;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        border-radius: 10px;
        overflow: auto;
        background-color: var(--branco);
        padding: 20px;
        max-height: calc(100% - 50px);
    }

    #Infos {
        display: flex;
        height: 5vh;
        margin-top: 40px;
        justify-content: left;
        align-items: center;
        text-align: right;
    }

    #itensPorPagina,
    #itensPorPaginaOps {
        max-width: 100px;
        margin-left: 5px;
        margin-right: 5px;
        margin-top: -10px;
    }

    .table-container {
        margin-top: 20px;
        position: relative;
    }

    .table-responsive {
        min-height: 59vh;
        max-height: 59vh;
        overflow: auto;
    }

    .table {
        padding: auto;
        margin: auto;
        width: 100%;
        min-width: 100%;
        max-width: 100%;
        min-height: 100%;
        max-height: 100%;
        overflow: auto;
    }

    .table th,
    .table td {
        white-space: nowrap;
    }

    .table tbody tr:hover {
        background-color: var(--corFundoTabela);
    }

    .table th {
        background-color: var(--corBase);
        color: var(--branco);
        text-align: center;
    }

    #Paginacao,
    #PaginacaoOps {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 10px;
        min-width: 100%;
        height: auto;
        overflow-x: auto;
        padding: 10px 0;
        flex-wrap: wrap;
        /* Adiciona wrap para melhor responsividade */
    }

    #Paginacao .paginate_button,
    #PaginacaoOps .paginate_button {
        margin: 3px;
        padding: 3px 6px;
        color: var(--corBase);
        border: 1px solid var(--corBase);
        border-radius: 4px;
        cursor: pointer;
        background-color: var(--branco);
    }

    #Paginacao .paginate_button:hover,
    #PaginacaoOps .paginate_button:hover {
        background-color: var(--corBase);
        color: var(--branco);
    }

    #Paginacao .paginate_button.current,
    #PaginacaoOps .paginate_button.current {
        background-color: var(--corBase);
        color: var(--branco);
    }

    .acoes {
        display: flex;
        justify-content: space-around;
        align-items: center;
        height: 100%;
    }

    .acoes i {
        cursor: pointer;
        font-size: 20px;
        margin: 0 0;
    }

    .dataTables_wrapper .dataTables_filter {
        display: none;
    }

    .ButtonExcel i {
        color: green;
        font-size: 25px;
    }

    .ButtonVisibilidade {
        border: none !important;
    }


    .dropdown-menu {
        max-height: 200px;
        overflow-y: auto;
    }

    #checkboxContainerPedidos label,
    #checkboxContainerMarca label {
        display: block;
        margin: 0;
        padding: 0;
    }

    .dropdown-toggle {
        border: 1px solid lightGray
    }

    #accordion {
        padding: 0;
    }

    #accordion2 {
        padding: 0;
        width: 100%;
        min-width: 100%;
    }



    @media (max-width: 768px) {
        td.descricao {
            white-space: normal;
            word-break: break-word;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .table {
            padding: auto;
            margin: auto;
            width: 100%;
            min-width: 100%;
            max-width: 100%;
            min-height: 100%;
            max-height: 100%;
            overflow: auto;
        }

        #form-container,
        .Corpo {
            padding: 10px;
        }

        #Paginacao,
        #PaginacaoOps {
            flex-direction: column;
            /* Direção da coluna para melhor ajuste em telas pequenas */
        }

        #Paginacao .paginate_button,
        #PaginacaoOps .paginate_button {
            margin: 5px 0;
        }
    }

      .btn-custom {
            background-color: var(--corBase);
            color: white;
            border: none;
            transition: background-color 0.3s, color 0.3s;
            font-size: 0.8rem;
        }

        .btn-custom:hover {
            background-color: rgb(37, 112, 251);
            color: var(--Preto);
        }
</style>

<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <div class="row" id="campoPedidos">
            <div class="col-12">
                <div id="accordion" style="margin-bottom: 15px">
                    <h3>Filtros de Cálculo</h3>
                    <div class="row">
                        <div class="row justify-content-center">
                        <div class="form-group col-6 col-md-4 col-lg-3 text-start d-flex align-items-end">
                            <div class="w-100">
                                <label for="data-inicio">Data Início</label>
                                <input type="date" class="form-control" id="data-inicio-pedido">
                            </div>
                        </div>
                        <div class="form-group col-6 col-md-4 col-lg-3 text-start d-flex align-items-end">
                            <div class="w-100">
                                <label for="data-fim">Data Fim</label>
                                <input type="date" class="form-control" id="data-fim-pedido">
                            </div>
                        </div>
                        <div class="form-group col-6 col-md-4 col-lg-3 text-center d-flex align-items-end">
                            <div class="w-100">
                                <label for="tipo-data">Tipo de Data</label>
                                <div>
                                    <input type="radio" id="TipoDeData1" name="TipoData" value="DataEmissao">
                                    <label for="TipoDeData1">Data Emissão</label>
                                </div>
                                <div>
                                    <input type="radio" id="TipoDeData2" name="TipoData" value="DataPrevOri">
                                    <label for="TipoDeData2">Data Previsão Original</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-6 col-md-4 col-lg-3 text-center d-flex align-items-end">
                            <div class="w-100">
                                <label for="tipo-priorizacao">Priorizar</label>
                                <div>
                                    <input type="radio" id="Priorizacao1" name="TipoPriorizacao" value="DataPrevisao">
                                    <label for="Priorizacao1">Data Previsão</label>
                                </div>
                                <div>
                                    <input type="radio" id="Priorizacao2" name="TipoPriorizacao" value="Faturamento">
                                    <label for="Priorizacao2">Faturamento</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-6 col-md-4 col-lg-2 text-center d-flex align-items-end">
                            <div class="w-100">
                                <div class="dropdown">
                                    <button class="btn btn-custom dropdown-toggle w-100" style="border: 1px solid lightgray" type="button" id="filtroTipoDeNota" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Tipos de Nota
                                    </button>
                                    <div class="dropdown-menu p-3 w-100" aria-labelledby="filtroDropdownTipoDeNotas">
                                        <input type="text" id="searchInputNotas" class="form-control mb-2" placeholder="Pesquisar..." style="font-size: 0.9rem;">
                                        <label><input type="checkbox" id="selectAllNotas"> Selecionar Todos</label><br>
                                        <div id="checkboxContainerNotas">
                                            <!-- Checkboxes para Pedidos serão adicionados aqui -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-6 col-md-4 col-lg-3 text-start d-flex align-items-end">
                            <div class="w-100">
                                <label for="data-emissao-inicial">Data de Emissão Inicial</label>
                                <input type="date" class="form-control" id="data-emissao-inicial">
                            </div>
                        </div>
                            <div class="form-group col-6 col-md-4 col-lg-3 text-start d-flex align-items-end">
                            <div class="w-100">
                                <label for="data-emissao-final">Data de Emissão Final</label>
                                <input type="date" class="form-control" id="data-emissao-final">
                            </div>
                        </div>
                    </div>
                        <div id="accordion2" style="margin-bottom: 10px">
                            <h3>Filtros Especiais</h3>
                            <div class="row">
                                <div class="form-group col-sm-6 col-md-2 text-center">
                                    <label for="cod-cliente">Cód. Cliente</label>
                                    <input type="text" class="form-control" id="Cliente">
                                </div>
                                <div class="form-group col-sm-6 col-md-2 text-center">
                                    <label for="cod-representante">Cód. Representante</label>
                                    <input type="text" class="form-control" id="Representante">
                                </div>
                                <div class="form-group col-sm-6 col-md-2 text-center">
                                    <label for="cod-pedido">Cód. Pedido</label>
                                    <input type="text" class="form-control" id="Pedido">
                                </div>
                                <div class="form-group col-sm-6 col-md-2 text-center">
                                    <label for="conceito-cliente">Conceito Cliente</label>
                                    <input type="text" class="form-control" id="Conceito">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-12 text-center">
                            <button type="button" class="btn btn-primary" onclick="consultarDados();">Consultar</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3 d-none" id="ItensPagina">
                        <label for="itensPorPagina">Mostrar</label>
                        <select class="form-select d-inline w-auto" id="itensPorPagina">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <label for="text">elementos</label>
                    </div>
                </div>
                <div class="row d-none" id="Filtros">
                    <div class="col-12 col-md-3 mb-3">
                        <div id="search-container">
                            <input type="text" id="searchMonitorPedidos" class="form-control" placeholder="Pesquisar...">
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="dropdown">
                            <button class="btn  dropdown-toggle w-100" type="button" id="filtroDropdownPedidos" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Pedidos
                            </button>
                            <div class="dropdown-menu p-3 w-100" aria-labelledby="filtroDropdownPedidos">
                                <input type="text" id="searchInputPedidos" class="form-control mb-2" placeholder="Pesquisar...">
                                <label><input type="checkbox" id="selectAllPedidos"> Selecionar Todos</label><br>
                                <div id="checkboxContainerPedidos">
                                    <!-- Checkboxes para Pedidos serão adicionados aqui -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle w-100" type="button" id="filtroDropdownMarca" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Marca
                            </button>
                            <div class="dropdown-menu p-3 w-100" aria-labelledby="filtroDropdownMarca">
                                <input type="text" id="searchInputMarca" class="form-control mb-2" placeholder="Pesquisar...">
                                <label><input type="checkbox" id="selectAllMarca"> Selecionar Todos</label><br>
                                <div id="checkboxContainerMarca">
                                    <!-- Checkboxes para Marcas serão adicionados aqui -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <button class="btn btn-custom" id="BtnOps">Monitor de Op's</button>
                    </div>
                </div>
                <div class="table-responsive d-none">
                    <table class="table table-bordered" id="TablePedidos">
                        <thead>
                            <tr>
                                <th scope="col"><p>Pedido</p></th>
                                <th scope="col"><p>Marca</p></th>
                                <th scope="col">Tipo<p>Nota</p></th>
                                <th scope="col">Cód.<p>Cliente</p></th>
                                <th scope="col">Data<p>Emissão</p></th>
                                <th scope="col">Previsão<p>Inicial</p></th>
                                <th scope="col">Último<p>Faturamento</p></th>
                                <th scope="col">Previsão<p>Próx/Embarque</p></th>
                                <th scope="col">Entregas<p>Solicitadas</p></th>
                                <th scope="col">Entregas <p>Fat.</p></th>
                                <th scope="col">Entregas <p>Restante</p></th>
                                <th scope="col">Qtd.Peças<p>Faturadas</p></th>
                                <th scope="col">Saldo<p>R$</p></th>
                                <th scope="col">R$<p>Atendido/COR</p></th>
                                <th scope="col">R$<p>Atendido Distríbuido</p></th>
                                <th scope="col">Qtd.<p>Peças Saldo</p></th>
                                <th scope="col">Qtd.Peças<p>Atendidas/COR</p></th>
                                <th scope="col">Qtd.Peças <p>Distribuídas/COR</p></th>
                                <th scope="col">Sugestão<p>Pedido</p></th>
                                <th scope="col">%<p>Distribuído</p></th>
                                <th scope="col">Pedidos<p>Agrupados</p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aqui vão os dados da tabela -->
                        </tbody>
                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-center" id="Paginacao">
                        <!-- Paginação será inserida aqui -->
                    </div>
                </div>
            </div>
        </div>
        <div class="row d-none" id="CampoOps">
            <div class="col-12">
                <div class="col-md-6 mb-3" id="ItensPagina">
                    <label for="itensPorPagina">Mostrar</label>
                    <select class="form-select d-inline w-auto" id="itensPorPaginaOp">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <label for="text">elementos</label>
                </div>

                <div class="row text-center align-items-end" id="Filtros">
                    <div class="col-12 col-md-2 mb-3 text-center justify-content-center">
                        <div id="search-container">
                            <input type="text" id="searchMonitorOp" class="form-control" placeholder="Pesquisar...">
                        </div>
                    </div>
                    <div class="col-12 col-md-2 mb-3 text-center justify-content-center">
                        <label for="data-inicio">Data Inicio</label>
                        <input type="date" class="form-control" id="data-inicio-ops">
                    </div>
                    <div class="col-12 col-md-2 mb-3 text-center justify-content-center">
                        <label for="data-fim">Data Fim</label>
                        <input type="date" class="form-control" id="data-fim-ops">
                    </div>
                    <div class="col-12 col-md-2 mb-3 text-center justify-content-center">
                        <button class="btn btn-custom" id="BtnFiltrar" onclick="ConsultaOps()">Filtrar Op's</button>
                    </div>
                    <div class="col-12 col-md-2 mb-3 text-center justify-content-center">
                        <button class="btn btn-custom" id="BtnPedidos">Monitor de Pedidos</button>
                    </div>
                    <div class="col-12 col-md-2 mb-3 text-center justify-content-center">
                    <button class="btn btn-custom" id="BtnProdutos" onclick="Consulta_Sem_Op()">Produtos sem Op</button>
                </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="TableOps">
                        <thead>
                            <tr>
                                <th scope="col">Numero<p>Op<p></th>
                                <th scope="col"><p>Engenharia</p></th>
                                <th scope="col"><p>Descrição</p></th>
                                <th scope ="col"><p>qtdOP</p></th>
                                <th scope="col">Fase <p>Atual</p></th>
                                <th scope="col">Nome <p>Fase</p></th>
                                <th scope="col">Quantidade <p>em Pedidos</p></th>
                                <th scope="col">Necessidade <p>em Peças</p></th>
                                <th scope="col"><p>Prioridade</p></th>
                                <th scope="col">Previsão<p>de Término</p></th>
                                <th scope="col">descricao<p>Lote</p></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aqui vão os dados da tabela -->
                        </tbody>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-center" id="PaginacaoOps">
                        <!-- Paginação será inserida aqui -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable">
                        <thead id="fixed-header">
                            <tr>
                                <th>Engenharia</th>
                                <th>Código Reduzido</th>
                                <th>Tipo de Op</th>
                                <th>Tamanho</th>
                                <th>Cor</th>
                                <th>Quantidade de Peças</th>
                                <th>Necessidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be appended here -->
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

<div class="modal fade" id="dataModalPedidos" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabelPedidos"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTablePedidos">
                        <thead id="fixed-header-pedidos">
                            <tr>
                                <th>Engenharia</th>
                                <th>Cor</th>
                                <th>Código Reduzido</th>
                                <th>Descrição</th>
                                <th>Saldo Pedido</th>
                                <th>Em Estoque</th>
                                <th>Situação</th>
                                <th>Numero Op</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be appended here -->
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

<div class="modal fade" id="modal-ops-faltando" tabindex="-1" aria-labelledby="modal-ops-faltando" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="max-height: 80vh; overflow: auto">
            <div class="modal-header">
                <h5 class="modal-title" id="titulo-modal"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 75vh; overflow: auto">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable-sem-ops">
                        <thead id="fixed-header">
                            <tr>
                               <th>Engenharia</th>
                                <th>Descrição</th>
                                <th>Tamanho</th>
                                <th>Cód. Cor</th>
                                <th>Qtd. em Pedidos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be appended here -->
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

<?php include_once("../../../templates/footer.php"); ?>
<script>
    let PriorizacaoSelecionado = "";
    let TipoDataSelecionado = "";
    let DadosPedidos = "";
    let DadosOps = "";

    $(document).ready(function() {
        $('#itensPorPagina').change(function() {
            const itensPorPagina = $(this).val();
            $('#TablePedidos').DataTable().page.len(itensPorPagina).draw();
        });

        $('#itensPorPaginaOp').change(function() {
            const itensPorPaginaOp = $(this).val();
            $('#TableOps').DataTable().page.len(itensPorPaginaOp).draw();
        });
        $("#accordion").accordion({
            collapsible: true
        });

        $("#accordion2").accordion({
            collapsible: true
        });

        $("#accordion2").accordion({
            active: false
        });

        $('#NomeRotina').text('Monitor de Pedidos');

        $('input[name="TipoPriorizacao"]').change(function() {
            PriorizacaoSelecionado = $('input[name="TipoPriorizacao"]:checked').val();
            console.log(PriorizacaoSelecionado);
        });

        $('input[name="TipoData"]').change(function() {
            TipoDataSelecionado = $('input[name="TipoData"]:checked').val();
            console.log(TipoDataSelecionado);
        });

        const dataAtual = new Date();
        const dataFormatada = getdataFormatada(dataAtual);
        $('#data-inicio-pedido').val(dataFormatada);
        $('#data-fim-pedido').val(dataFormatada);
        $('#data-emissao-inicial').val(dataFormatada);
        $('#data-emissao-final').val(dataFormatada);
        $('#data-inicio-ops').val(dataFormatada);
        $('#data-fim-ops').val(dataFormatada);

        $('#searchInputPedidos').on('keyup', function() {
            var searchText = $(this).val().toLowerCase();
            var $container = $('#checkboxContainerPedidos');
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

        $('#searchInputMarca').on('keyup', function() {
            var searchText = $(this).val().toLowerCase();
            var $container = $('#checkboxContainerMarca');
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

        $('.dropdown-toggle').on('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            $(this).next('.dropdown-menu').toggle();
        });

        let today = new Date().toISOString().split('T')[0];
        $('#dataInicio').val(today);
        $('#dataFim').val(today);

    });

    function getdataFormatada(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatarMoeda(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    function formatarDados(data) {
        return data.map(item => {
            return {
                '01-MARCA': item['01-MARCA'],
                '02-Pedido': item['02-Pedido'],
                '03-tipoNota': item['03-tipoNota'],
                '04-PrevOriginal': item['04-Prev.Original'],
                '05-PrevAtualiz': item['05-Prev.Atualiz'],
                '06-codCliente': item['06-codCliente'],
                '08-vlrSaldo': formatarMoeda(item['08-vlrSaldo']),
                '09-Entregas Solic': item['09-Entregas Solic'],
                '10-Entregas Fat': item['10-Entregas Fat'],
                '11-ultimo fat': item['11-ultimo fat'],
                '12-qtdPecas Fat': item['12-qtdPecas Fat'],
                '13-Qtd Atende': item['13-Qtd Atende'],
                '14- Qtd Saldo': item['14- Qtd Saldo'],
                '15-Qtd Atende p/Cor': item['15-Qtd Atende p/Cor'],
                '18-Sugestao(Pedido)': item['18-Sugestao(Pedido)'],
                '21-Qnt Cor(Distrib)': item['21-Qnt Cor(Distrib.)'],
                '22-Valor Atende por Cor(Distrib)': formatarMoeda(item['22-Valor Atende por Cor(Distrib.)']),
                '23-% qtd cor': item['23-% qtd cor'],
                '16-Valor Atende por Cor': formatarMoeda(item['16-Valor Atende por Cor']),
                'Saldo +Sugerido': item['Saldo +Sugerido'],
                'dataEmissao': item['dataEmissao'],
                'Agrupamento': item['Agrupamento'],
            };
        });
    }

    const ConsultaPedidos = async () => {
        try {
            $('#loadingModal').modal('show');
            const iniVenda = $('#data-inicio-pedido').val();
            const finalVenda = $('#data-fim-pedido').val();
            const emissaoinicial = $('#data-emissao-inicial').val();
            const emissaofinal = $('#data-emissao-final').val();
            const tipoNota = '1,2,3,4';
            const parametroClassificacao = TipoDataSelecionado;
            const tipoData = PriorizacaoSelecionado;

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Pedidos',
                    iniVenda: iniVenda,
                    finalVenda: finalVenda,
                    tipoNota: tipoNota,
                    parametroClassificacao: parametroClassificacao,
                    tipoData: tipoData,
                    emissaoinicial: emissaoinicial,
                    emissaofinal: emissaofinal
                }
            });

            console.log(response);
            $('#checkboxContainerPedidos').empty();
            $('#checkboxContainerMarca').empty();
            response[0]['6 -Detalhamento'].forEach(item => {
                $('#checkboxContainerPedidos').append(`<label><input type="checkbox" class="filtro" value="${item["02-Pedido"]}"> ${item["02-Pedido"]}</label>`);
            });
            $('#checkboxContainerMarca').append(`<label><input type="checkbox" class="filtro" value="PACO"> PACO</label>`);
            $('#checkboxContainerMarca').append(`<label><input type="checkbox" class="filtro" value="M.POLLO"> M.POLLO</label>`);

            $("#Filtros").removeClass('d-none');
            $("#ItensPagina").removeClass('d-none');
            $(".table-responsive").removeClass('d-none');
            $("#accordion").accordion({
                active: false
            });

            const DadosFormatados = formatarDados(response[0]['6 -Detalhamento']);
            DadosPedidos = DadosFormatados;
            criarTabelaPedidos(DadosPedidos);
        } catch (error) {
            console.log('Erro:', error);
        } finally {}
    }



    const ConsultaOpsInicio = async () => {
        try {
            const dataInicioPedido = $('#data-inicio-pedido').val(); // Valor da input #data-inicio-pedido

            // Setando o valor da input de tipo data com o mesmo valor da #data-inicio-pedido
            $('#data-inicio-ops').val(dataInicioPedido);

            const dataFimPedido = $('#data-fim-pedido').val(); // Valor da input #data-fim-pedido

            // Setando o valor da input de tipo data com o mesmo valor da #data-fim-pedido
            $('#data-fim-ops').val(dataFimPedido);

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Ops',
                    dataInicio: dataInicioPedido,
                    dataFim: dataFimPedido,
                }
            });

            DadosOps = response[0]['6 -Detalhamento'];
            criarTabelaOps(DadosOps);
        } catch (error) {
            console.error('Erro:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    const ConsultaOps = async () => {
        try {
            $('#loadingModal').modal('show');
            const dataInicio = $('#data-inicio-ops').val();

            const dataFim = $('#data-fim-ops').val();

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Ops',
                    dataInicio: dataInicio,
                    dataFim: dataFim,
                }
            });

            DadosOps = response[0]['6 -Detalhamento'];
            criarTabelaOps(DadosOps);
            $('#loadingModal').modal('hide');
        } catch (error) {
            console.error('Erro:', error);
            $('#loadingModal').modal('hide');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    const DetalharOp = async (op) => {
        console.log(op)
        try {
            $('#loadingModal').modal('show');

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Detalhar_Op',
                    numeroOp: `${op}`,
                }
            });
            console.log(response)
            await CriarTabelaModal(response);

            $('#dataModal').modal('show');
            $('#fixed-header').css({
                'position': 'sticky',
                'top': '0',
                'z-index': '1000'
            });
            $('#dataModalLabel').text(`Número Op: ${op}`)
        } catch (error) {
            console.error('Erro:', error);
            $('#loadingModal').modal('hide');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    const DetalharPedido = async (Pedido) => {
        console.log(Pedido);
        try {
            $('#loadingModal').modal('show');

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Detalhar_Pedido',
                    numeroPedido: Pedido,
                }
            });
            console.log(response);
            await CriarTabelaModalPedido(response);

            $('#dataModalPedidos').modal('show');
            $('#fixed-header-pedidos').css({
                'position': 'sticky',
                'top': '0',
                'z-index': '1000'
            });
            $('#dataModalLabelPedidos').html(`Número Pedido: ${Pedido}<br>Qtd Embarques: ${response[0]['02-Embarque']}<br>Cliente: ${response[0]['03-nome_cli']}`);
        } catch (error) {
            console.error('Erro:', error);
            $('#loadingModal').modal('hide');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }


    async function consultarDados() {

        await ConsultaPedidos();
        await ConsultaOpsInicio();

    }
    $('#BtnOps').on('click', function() {
        $('#CampoOps').removeClass('d-none');
        $('#campoPedidos').addClass('d-none');
        $('#NomeRotina').text("Monitor de Op's");
    });

    $('#BtnPedidos').on('click', function() {
        $('#CampoOps').addClass('d-none');
        $('#campoPedidos').removeClass('d-none');
    });


    function criarTabelaPedidos(listaPedidos) {
        $('#Paginacao .dataTables_paginate').remove();
        $('#PaginacaoOps .dataTables_paginate').remove();

        listaPedidos.forEach(item => {
            item['Diferenca_Entregas'] = item['09-Entregas Solic'] - item['10-Entregas Fat'];
        });

        if ($.fn.DataTable.isDataTable('#TablePedidos')) {
            $('#TablePedidos').DataTable().destroy();
        }

        const tabela = $('#TablePedidos').DataTable({
            excel: true,
            responsive: false,
            paging: true,
            info: false,
            searching: true,
            colReorder: true,
            data: listaPedidos,
            lengthChange: false,
            pageLength: 10,
            fixedHeader: true,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    title: 'Fila de Reposição',
                    className: 'ButtonExcel'
                },
                {
                    extend: 'colvis',
                    text: 'Visibilidade das Colunas',
                    className: 'ButtonVisibilidade'
                }
            ],
            columns: [{
                    data: '02-Pedido',
                    render: function(data, type, row) {
                        return `<span class="codPedidoClicado" data-codigoPedido="${data}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
                    }
                },
                {
                    data: '01-MARCA'
                },
                {
                    data: '03-tipoNota'
                },
                {
                    data: '06-codCliente'
                },
                {
                    data: 'dataEmissao'
                },
                {
                    data: '04-PrevOriginal'
                },
                {
                    data: '11-ultimo fat'
                },
                {
                    data: '05-PrevAtualiz'
                },
                {
                    data: '09-Entregas Solic'
                },
                {
                    data: '10-Entregas Fat'
                },
                {
                    data: 'Diferenca_Entregas'
                },
                {
                    data: '12-qtdPecas Fat'
                },
                {
                    data: '08-vlrSaldo'
                },
                {
                    data: '16-Valor Atende por Cor'
                },
                {
                    data: '22-Valor Atende por Cor(Distrib)'
                },
                {
                    data: 'Saldo +Sugerido'
                },
                {
                    data: '15-Qtd Atende p/Cor'
                },
                {
                    data: '21-Qnt Cor(Distrib)'
                },
                {
                    data: '18-Sugestao(Pedido)'
                },
                {
                    data: '23-% qtd cor',
                    render: function(data, type, row) {
                        // Adiciona o símbolo de porcentagem apenas durante a exibição
                        if (type === 'display') {
                            return data + '%'; // Adiciona o símbolo de porcentagem ao valor
                        }
                        return data; // Retorna o valor original para outras operações (por exemplo, ordenação)
                    }
                },
                {
                    data: 'Agrupamento'
                },
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

        //--Criando a paginação da tabela
        $('.dataTables_paginate').appendTo('#Paginacao');

        $('#Paginacao .paginate_button.previous').on('click', function() {
            tabela.page('previous').draw('page');
        });

        $('#Paginacao .paginate_button.next').on('click', function() {
            tabela.page('next').draw('page');
        });

        const paginaInicial = 1;
        tabela.page(paginaInicial - 1).draw('page');

        $('#Paginacao .paginate_button').on('click', function() {
            $('#Paginacao .paginate_button').removeClass('current');
            $(this).addClass('current');
        });

        $('#selectAllMarca').on('change', function() {
            let isChecked = $(this).is(':checked');
            $('#checkboxContainerMarca .filtro:visible').prop('checked', isChecked);
            atualizarFiltroMarca();
        });

        // Evento de mudança no checkbox "Selecionar Todos" de Pedidos
        $('#selectAllPedidos').on('change', function() {
            let isChecked = $(this).is(':checked');
            $('#checkboxContainerPedidos .filtro:visible').prop('checked', isChecked);
            atualizarFiltroPedido();
        });

        $('#checkboxContainerMarca').on('change', '.filtro', function() {
            let todosMarcados = $('#checkboxContainerMarca .filtro:visible').length === $('#checkboxContainerMarca .filtro:visible:checked').length;
            $('#selectAllMarca').prop('checked', todosMarcados);
            atualizarFiltroMarca();
        });

        $('#checkboxContainerPedidos').on('change', '.filtro', function() {
            let todosMarcados = $('#checkboxContainerPedidos .filtro:visible').length === $('#checkboxContainerPedidos .filtro:visible:checked').length;
            $('#selectAllPedidos').prop('checked', todosMarcados);
            atualizarFiltroPedido();
        });

        function atualizarFiltroMarca() {
            var filtros = [];
            $('#checkboxContainerMarca .filtro:checked').each(function() {
                filtros.push($(this).val());
            });
            tabela.column(1).search(filtros.join('|'), true, false).draw();
        }

        function atualizarFiltroPedido() {
            var filtros = [];
            $('#checkboxContainerPedidos .filtro:checked').each(function() {
                filtros.push($(this).val());
            });
            tabela.column(0).search(filtros.join('|'), true, false).draw();
        }
        //-- Pesquisa qualquer palavra na tabela
        $('#searchMonitorPedidos').on('keyup', function() {
            tabela.search(this.value).draw();
        });

        $('#TablePedidos').on('click', '.codPedidoClicado', function() {
            const codPedido = $(this).attr('data-codigoPedido');
            console.log('codPedido:', codPedido); // Ajuste aqui para melhorar a depuração
            DetalharPedido(codPedido);
        });
    }

    function criarTabelaOps(listaOps) {
        console.log(listaOps);

        // Remover elementos de paginação se existirem
        if ($('#Paginacao .dataTables_paginate').length) {
            $('#Paginacao .dataTables_paginate').remove();
        }
        if ($('#PaginacaoOps .dataTables_paginate').length) {
            $('#PaginacaoOps .dataTables_paginate').remove();
        }

        // Destruir DataTable se já estiver inicializada
        if ($.fn.DataTable.isDataTable('#TableOps')) {
            $('#TableOps').DataTable().destroy();
        }

        // Criar DataTable
        const tabela = $('#TableOps').DataTable({
            excel: true,
            responsive: false,
            paging: true,
            info: false,
            searching: true,
            colReorder: true,
            data: listaOps,
            lengthChange: false,
            pageLength: 10,
            fixedHeader: true,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i>',
                    title: 'Fila de Reposição',
                    className: 'ButtonExcel'
                },
                {
                    extend: 'colvis',
                    text: 'Visibilidade das Colunas',
                    className: 'ButtonVisibilidade'
                }
            ],
            columns: [{
                    data: 'numeroop',
                    render: function(data, type, row) {
                        return `<span class="codOpClicado" data-codigoOp="${data}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
                    }
                },
                {
                    data: 'codItemPai'
                },
                {
                    data: 'descricao'
                },
                {
                    data: 'qtdOP'
                },
                {
                    data: 'codFaseAtual'
                },
                {
                    data: 'nome'
                },
                {
                    data: 'Ocorrencia Pedidos'
                },
                {
                    data: 'AtendePçs'
                },
                {
                    data: 'prioridade'
                },
                {
                    data: 'dataPrevisaoTermino'
                },
                {
                    data: 'descricaoLote'
                },
            ],
            language: {
                paginate: {
                    first: 'Primeira',
                    previous: '<',
                    next: '>',
                    last: 'Última'
                }
            }
        });

        // Mover elementos de paginação
        $('.dataTables_paginate').appendTo('#PaginacaoOps');

        // Eventos de paginação
        $('#PaginacaoOps .paginate_button.previous').on('click', function() {
            tabela.page('previous').draw('page');
        });

        $('#PaginacaoOps .paginate_button.next').on('click', function() {
            tabela.page('next').draw('page');
        });

        // Selecionar página inicial
        const paginaInicial = 1;
        tabela.page(paginaInicial - 1).draw('page');

        // Atualizar estado dos botões de paginação
        $('#PaginacaoOps .paginate_button').on('click', function() {
            $('#PaginacaoOps .paginate_button').removeClass('current');
            $(this).addClass('current');
        });

        // Pesquisa na tabela
        $('#searchMonitorOp').on('keyup', function() {
            tabela.search(this.value).draw();
        });

        // Evento de clique na linha da tabela
        $('#TableOps').on('click', '.codOpClicado', function() {
            const codOp = $(this).attr('data-codigoOp');
            console.log('codOp:', codOp); // Ajuste aqui para melhorar a depuração
            DetalharOp(codOp);
        });
    }


    async function CriarTabelaModal(dados) {
        // Destruir DataTable se já estiver inicializada
        if ($.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable().destroy();
        }

        // Criar DataTable
        const tabela = $('#dataTable').DataTable({
            excel: true,
            responsive: false,
            paging: false,
            info: false,
            searching: false,
            colReorder: true,
            data: dados,
            lengthChange: false,
            pageLength: 10,
            fixedHeader: true,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa-solid fa-file-excel"></i>',
                title: 'Fila de Reposição',
                className: 'ButtonExcel'
            }],
            columns: [{
                    data: 'itemPai'
                },
                {
                    data: '03- Cód Reduzido'
                },
                {
                    data: '01-tipoNota'
                },
                {
                    data: '05-tam'
                },
                {
                    data: '04-cor'
                },
                {
                    data: '06-pcsOP'
                },
                {
                    data: '07-Necessidade'
                },
            ],
        });

    }


    async function CriarTabelaModalPedido(dados) {
    if ($.fn.DataTable.isDataTable('#dataTablePedidos')) {
        $('#dataTablePedidos').DataTable().destroy();
    }

    $('#dataTablePedidos').DataTable({
        excel: true,
        responsive: false,
        paging: false,
        info: false,
        searching: false,
        colReorder: true,
        data: dados,
        lengthChange: false,
        pageLength: 10,
        fixedHeader: true,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="fa-solid fa-file-excel"></i>',
            title: 'Fila de Reposição',
            className: 'ButtonExcel'
        }],
        columns: [
            { data: '04-codProduto' },
            { data: '06-codCor' },
            { data: '05-codReduzido' },
            { data: '07-nomeSKU' },
            { data: '08-QtdSaldoPedido' },
            { data: '09-QtdAtendeEstoque' },
            { data: '10-situacao', visible: false},
            { data: '11-numeroop' }
        ],
        createdRow: function(row, data, dataIndex) {
            if (data['10-situacao'] === 'Atendeu') {
                $(row).css('background-color', 'rgb(42, 214, 74)');
            } else if (data['10-situacao'] === 'Nao Atendeu') {
                $(row).css('background-color', 'rgb(228, 87, 87)');
            }
        }
    });
}

        async function TabelaFaltaOps(data) {
        // Destrua a DataTable existente, se já estiver inicializada
        if ($.fn.DataTable.isDataTable('#dataTable-sem-ops')) {
            $('#dataTable-sem-ops').DataTable().clear().destroy();
        }

        // Limpe o corpo da tabela
        const tbody = $('#dataTable-sem-ops tbody');
        tbody.empty();

        // Adicione as novas linhas
        data.forEach(item => {
            const row = `<tr>
           <td>${item['codEngenharia']}</td>
            <td>${item['tamanho']}</td>
            <td>${item['codCor']}</td>
            <td>${item['nomeSKU']}</td>
            <td>${item['QtdSaldo']}</td>
        </tr>`;
            tbody.append(row);
        });

        // Inicialize a DataTable
        $('#dataTable-sem-ops').DataTable({
            excel: true,
            paging: false,
            searching: false,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa-solid fa-file-excel"></i>',
                title: 'Produtos sem Op',
                className: 'ButtonExcel'
            }, ],
            order: [
                [0, 'asc']
            ],
        });
    }


    async function Consulta_Sem_Op() {
        $('#loadingModal').modal('show');
        const dados = {
            "dataInico": $('#data-inicio-pedido').val(),
            "dataFim": $('#data-fim-pedido').val()
        }

        var requestData = {
            acao: "Consulta_Sem_Op",
            dados: dados
        };

        try {
            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response['resposta'])
            await TabelaFaltaOps(response['resposta']);
            $('#modal-ops-faltando').modal('show');
            $('#fixed-header').css({
                'position': 'sticky',
                'top': '0',
                'z-index': '1000'
            });
            $('#titulo-modal').text(`Produtos sem Op's`)
            $('#loadingModal').modal('hide');

        } catch (error) {
            console.error('Erro na solicitação AJAX:', error); // Exibir erro se ocorrer
            $('#loadingModal').modal('hide');
        }
    }




    //------------------------------------------CRIAÇAO DOS FILTROS ESPECIAIS---------------------------------------//
    let Clientes = [];

    $('#Cliente').on('keypress', function(event) {
        if (event.which === 13) {

            event.preventDefault();

            let cliente = $(this).val().trim();

            if (cliente !== '') {
                if (Clientes.includes(cliente)) {
                    alert('O código do cliente já foi adicionado.');
                } else {
                    Clientes.push(cliente);
                    $(this).val('');
                    console.log('Clientes:', Clientes);
                }
            }
        }
    });
</script>
