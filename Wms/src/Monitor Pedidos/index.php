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

    #BtnOps,
    #BtnPedidos,
    #BtnFiltrar {
        border: 1px solid lightGray
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
                                <label for="data-emissao-final">Data de Emissão Inicial</label>
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
                        <button class="btn" id="BtnOps">Monitor de Op's</button>
                    </div>
                </div>
                <div class="table-responsive d-none">
                    <table class="table table-bordered" id="TablePedidos">
                        <thead>
                            <tr>
                                <th scope="col">Pedido</th>
                                <th scope="col">Marca</th>
                                <th scope="col">Tipo de Nota</th>
                                <th scope="col">Cód. Cliente</th>
                                <th scope="col">Data de Emissão</th>
                                <th scope="col">Previsão Inicial</th>
                                <th scope="col">Último Faturamento</th>
                                <th scope="col">Previsão Próximo Embarque</th>
                                <th scope="col">Entregas Solicitadas</th>
                                <th scope="col">Entregas Faturadas</th>
                                <th scope="col">Entregas Restantes</th>
                                <th scope="col">Qtd. Peças Faturadas</th>
                                <th scope="col">Saldo R$</th>
                                <th scope="col">R$ Atendido/COR</th>
                                <th scope="col">R$ Atendido Distríbuido</th>
                                <th scope="col">Qtd. Peças Saldo</th>
                                <th scope="col">Qtd. Peças Atendidas/COR</th>
                                <th scope="col">Qtd. Peças Distribuídas/COR</th>
                                <th scope="col">Sugestão Pedido</th>
                                <th scope="col">% Distribuído</th>
                                <th scope="col">Pedidos Agrupados</th>
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
                        <button class="btn" id="BtnFiltrar" onclick="ConsultaOps()">Filtrar Op's</button>
                    </div>
                    <div class="col-12 col-md-2 mb-3 text-center justify-content-center">
                        <button class="btn" id="BtnPedidos">Monitor de Pedidos</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="TableOps">
                        <thead>
                            <tr>
                                <th scope="col">Numero Op</th>
                                <th scope="col">Engenharia</th>
                                <th scope="col">Descrição</th>
                                <th scope="col">Cód. Fase Atual</th>
                                <th scope="col">Nome da Fase</th>
                                <th scope="col">Quantidade em Pedidos</th>
                                <th scope="col">Necessidade em Peças</th>
                                <th scope="col">Prioridade</th>
                                <th scope="col">Previsão de Término</th>
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

<?php include_once("../../../templates/footer.php"); ?>
<script src="script.js"></script>
