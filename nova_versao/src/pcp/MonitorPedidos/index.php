<?php
include_once("requests.php");
include_once("../../../templates/Loading.php");
include_once("../../../templates/headerPcp.php");
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="style2.css">
<style>
    .accordion {
        width: 80%;
        margin: 0px auto;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .accordion h3 {
        background-color: lightgray;
        color: black;
        margin: 0;
        padding: 15px;
        font-size: 16px;
        border-bottom: 1px solid white;
        border-radius: 5px 5px 0 0;
        border: 1px solid lightgray;
    }

    .accordion .ui-accordion-content {
        background-color: white;
        border-radius: 0 0 5px 5px;
        padding: 15px;
        font-size: 16px;
        border: 1px solid #ddd;
    }

    .accordion h3.ui-state-active {
        background-color: lightgray;
    }

    .inner-accordion h3.ui-state-active {
        background-color: lightgray;
        color: black;
    }

    .accordion h3:hover {
        cursor: pointer;
        background-color: lightgray;
    }

    .input-group {
        margin-bottom: 15px;
    }

    .dropdown {
        margin-top: 15px;
    }

    .inner-accordion {
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .accordion {
            width: 95%;
        }

        .form-group,
        .dropdown {
            width: 100%;
            margin-left: 0;
        }
    }

    .input-group {
        position: relative;
    }

    .input-group .form-control {
        border: 1px solid #007bff;
        border-radius: 5px 0 0 5px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .input-group .btn {
        border-radius: 0 5px 5px 0;
        background-color: #007bff;
        color: white;
        border: 1px solid #007bff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .input-group .btn:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    table.dataTable {
        border-collapse: collapse;
        width: 100%;
        max-width: 100%;
        overflow: auto;
        font-size: 14px;
        padding: 5px;
        font-weight: 600;
    }

    table.dataTable thead {
        background-color: #002955;
        color: white;
        font-size: 15px;
    }

    table.dataTable thead th {
        padding: 5px;
        text-align: center;
        border-bottom: 1px solid lightgray;
        border-right: 1px solid gray;
    }

    .table.dataTable th,
    .table.dataTable td {
        white-space: nowrap;
        vertical-align: middle;
        border: 1px solid #dee2e6;
    }


    table.dataTable td {
        padding: 7px;
    }

    table.dataTable tbody tr:hover {
        background-color: lightblue;
        /* Cor de fundo ao passar o mouse */
    }

    /* Estilo para as células */
    table.dataTable tbody td {
        border-bottom: 1px solid lightgray;
        border-right: 1px solid lightgray;
    }

    .dataTables_wrapper .dataTables_paginate {
        margin-top: 20px;
        text-align: right;
        background-color: red;
        /* Alinha os botões à direita */
    }

    #itemCountContainer .paginate_button,
    #itemCountContainer-ops .paginate_button,
    #itemCountContainer-sem-ops .paginate_button,
    #itemCountContainer-lista-pedidos .paginate_button {
        padding: 5px 8px;
        margin: 0 0px;
        background: #007bff;
        color: #002955;
        background-color: white;
        border-top: 1px solid gray;
        border-bottom: 1px solid gray;
        border-right: 0.5px solid gray;
        border-left: 0.5px solid gray;
        transition: background-color 0.3s;
        cursor: pointer;
        text-decoration: none;
    }

    #itemCountContainer .paginate_button.previous,
    #itemCountContainer-ops .paginate_button.previous,
    #itemCountContainer-sem-ops .paginate_button.previous,
    #itemCountContainer-lista-pedidos .paginate_button.previous {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
        border-left: 1px solid gray;
    }

    #itemCountContainer .paginate_button.next,
    #itemCountContainer-ops .paginate_button.next,
    #itemCountContainer-sem-ops .paginate_button.next,
    #itemCountContainer-lista-pedidos .paginate_button.next {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
        border-right: 1px solid gray;
    }

    #itemCountContainer .paginate_button:hover,
    #itemCountContainer-ops .paginate_button:hover,
    #itemCountContainer-sem-ops .paginate_button:hover,
    #itemCountContainer-lista-pedidos .paginate_button:hover  {
        background: #002955;
        color: white;
        /* Cor do botão ao passar o mouse */
    }

    #itemCountContainer .paginate_button.current,
    #itemCountContainer-ops .paginate_button.current,
    #itemCountContainer-sem-ops .paginate_button.current,
    #itemCountContainer-lista-pedidos .paginate_button.current {
        background: #002955;
        color: white;
        /* Cor do botão ativo */
    }


    /* Estilo para a linha de pesquisa */
    .search-row input {
        width: 100%;
        /* Largura do campo de pesquisa */
        box-sizing: border-box;
        /* Inclui padding e border no cálculo da largura */
        padding: 5px;
        /* Padding do campo de pesquisa */
        margin-top: 5px;
        /* Espaço acima do campo de pesquisa */
    }

    .pagination-container-pedidos {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        font-size: 13px;
    }

    #itemCount {
        width: 60px;
        display: inline-block;
        border-radius: 15px;
        border: 1px solid gray;
        font-size: 13px;
    }

    .search {
        margin-top: -10px;
        border-radius: 15px;
        padding: 1px 10px;
    }

    .dataTables_filter {
        display: none;
    }

    .div-tabela input:focus {
        outline: none;
    }

    .ButtonExcel {
        background: linear-gradient(135deg, #4caf50, #81c784) !important;
        border: none;
        border-radius: 15px;
        color: white;
        padding: 5px 15px;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s, transform 0.3s;
        cursor: pointer;
        float: left;
        margin-bottom: 10px;
        margin-right: 10px;
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

    .btn-botoes {
        border: 1px solid gray;
        border-radius: 15px;
        padding: 6px 15px;
        float: left;
    }

    .btn-botoes:hover {
        background-color: lightgray;
        border: 1px solid gray;
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
</style>
<div class="titulo" style="padding: 10px; text-align: left; border-bottom: 1px solid black; color: black; font-size: 15px; font-weight: 600;"><i class="icon ph-bold ph-monitor"></i> Monitor de Pedidos</div>
<div class="accordion" style="min-width: 100%; text-align: left;">
    <h3>Filtros de Cálculo</h3>
    <div>
        <div class="row g-3 justify-content-center" style="padding: auto; margin: auto; font-size: 14px;">
            <div class="" style="width: 200px;">
                <label for="dataInicio" class="form-label" style="font-size: 16px; font-weight: 500; color: black;">Data de Início</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" class="form-control" id="data-inicio-pedido">
                </div>
            </div>
            <div class="" style="width: 200px;">
                <label for="dataFim" class="form-label" style="font-size: 16px; font-weight: 500; color: black;">Data de Fim</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" class="form-control" id="data-fim-pedido">
                </div>
            </div>
            <div class="" style="width: 200px; border: 1px solid lightgray; border-radius: 8px; font-size: 15px; text-align: center;">
                <label for="tipo-data" style="font-size: 16px; font-weight: 500; color: black;">Tipo de Data</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="TipoDeData1" name="TipoData" value="DataEmissao">
                    <label class="form-check-label" for="TipoDeData1">Data Emissão</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="TipoDeData2" name="TipoData" value="DataPrevOri">
                    <label class="form-check-label" for="TipoDeData2">Data Previsão Original</label>
                </div>
            </div>
            <div class="" style="width: 200px; border: 1px solid lightgray; border-radius: 8px; font-size: 15px; text-align: center; margin-left: 10px;">
                <label for="tipo-priorizacao" style="font-size: 16px; font-weight: 500; color: black;">Priorizar</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="Priorizacao1" name="TipoPriorizacao" value="DataPrevisao">
                    <label class="form-check-label" for="Priorizacao1">Data Previsão</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="Priorizacao2" name="TipoPriorizacao" value="Faturamento">
                    <label class="form-check-label" for="Priorizacao2">Faturamento</label>
                </div>
            </div>
            <div class="" style="width: 200px;">
                <div class="dropdown">
                    <button class="btn btn-opcoes dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Tipos de Nota
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="#">Opção 1</a></li>
                        <li><a class="dropdown-item" href="#">Opção 2</a></li>
                        <li><a class="dropdown-item" href="#">Opção 3</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">Opção 4</a></li>
                    </ul>
                </div>
            </div>
            <div class="" style="width: 200px;">
                <label for="dataInicio2" class="form-label" style="font-size: 16px; font-weight: 500; color: black;">Emissão Inicial</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" class="form-control" id="data-emissao-inicial">
                </div>
            </div>
            <div class="" style="width: 200px;">
                <label for="dataFim2" class="form-label" style="font-size: 16px; font-weight: 500; color: black;">Emissão Final</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <input type="date" class="form-control" id="data-emissao-final">
                </div>
            </div>
            <button class="btn btn-opcoes" style="background-color: #002955 !important;" id="btn-filtrar">Filtrar</button>
        </div>
        <div class="inner-accordion">
            <h3>Filtros Avançados</h3>
            <div>
                <p>Em desenvolvimento</p>
            </div>
        </div>
    </div>
</div>
<div class="corpo d-none">
    <div class="menu-tela" style="border-bottom: 1px solid lightgray; width: 100%; margin-top: 10px; color: black; text-align: left; padding-left: 10px;">
        <button class="btn btn-menus" id="btn-pedidos" onclick="$('#btn-pedidos').addClass('btn-menu-clicado'); $('#btn-ops').removeClass('btn-menu-clicado'); $('#btn-sem-ops').removeClass('btn-menu-clicado'); $('.corpo-pedidos').removeClass('d-none'); $('.corpo-ops').addClass('d-none'); $('.corpo-sem-ops').addClass('d-none')"><i class="icon ph-bold ph-folders" style="margin-right: 5px;"></i>Pedidos</button>
        <button class="btn btn-menus" id="btn-ops" onclick="$('#btn-pedidos').removeClass('btn-menu-clicado'); $('#btn-ops').addClass('btn-menu-clicado'); $('#btn-sem-ops').removeClass('btn-menu-clicado'); $('.corpo-pedidos').addClass('d-none'); $('.corpo-ops').removeClass('d-none'); $('.corpo-sem-ops').addClass('d-none')"><i class="icon ph-bold ph-file" style="margin-right: 5px;"></i>Ops</button>
        <button class="btn btn-menus" id="btn-sem-ops" onclick="$('#btn-pedidos').removeClass('btn-menu-clicado'); $('#btn-ops').removeClass('btn-menu-clicado'); $('#btn-sem-ops').addClass('btn-menu-clicado');$('.corpo-pedidos').addClass('d-none'); $('.corpo-ops').addClass('d-none'); $('.corpo-sem-ops').removeClass('d-none')"><i class="icon ph-bold ph-file-minus" style="margin-right: 5px;"></i>Sem Ops</button>
    </div>
    <div class="corpo-pedidos d-flex justify-content-md-start justify-content-center" style="padding: 5px 10px; flex-wrap: wrap;">
        <div class="col-12">
            <div class="div-tabela" style="max-width: 100%; overflow: auto">
                <table class="table table-bordered" id="TablePedidos">
                    <thead>
                        <tr>
                            <th scope="col">Pedido<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Marca<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Tipo de Nota<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Cód. Cliente<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Data de Emissão<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Previsão Inicial<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Último Faturamento<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Previsão Próximo Embarque<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Entregas Solicitadas<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Entregas Faturadas<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Entregas Restantes<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Qtd. Peças Faturadas<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Saldo R$<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">R$ Atendido/COR<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">R$ Atendido Distríbuido<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Qtd. Peças Saldo<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Qtd. Peças Atendidas/COR<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Qtd. Peças Distribuídas/COR<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Sugestão Pedido<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">% Distribuído<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                            <th scope="col">Pedidos Agrupados<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aqui vão os dados da tabela -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-container-pedidos">
        </div>
    </div>
    <div class="corpo-ops d-flex d-none justify-content-md-start justify-content-center" style="padding: 5px 10px; flex-wrap: wrap;">
        <div class="col-12">
            <div class="div-tabela" style="max-width: 100%; overflow: auto">
                <table class="table table-bordered" id="TableOps">
                    <thead>
                        <tr>
                            <th scope="col">Numero Op<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-ops search">
                            </th>
                            <th scope="col">Engenharia<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-ops search">
                            </th>
                            <th scope="col">Descrição<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-ops search">
                            </th>
                            <th scope="col">Cód. Fase Atual<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-ops search">
                            </th>
                            <th scope="col">Nome da Fase<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-ops search">
                            </th>
                            <th scope="col">Quantidade em Pedidos<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-ops search">
                            </th>
                            <th scope="col">Necessidade em Peças<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-ops search">
                            </th>
                            <th scope="col">Prioridade<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-ops search">
                            </th>
                            <th scope="col">Previsão de Término<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-ops search">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aqui vão os dados da tabela -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-container-ops">
        </div>
    </div>
    <div class="corpo-sem-ops d-flex d-none justify-content-md-start justify-content-center" style="padding: 5px 10px; flex-wrap: wrap;">
        <div class="col-12">
            <div class="div-tabela" style="max-width: 100%; overflow: auto">
                <table class="table table-bordered" id="Table-sem-ops">
                    <thead>
                        <tr>
                            <th scope="col">Engenharia<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-sem-ops search">
                            </th>
                            <th scope="col">Tamanho<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-sem-ops search">
                            </th>
                            <th scope="col">Código Cor<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-sem-ops search">
                            </th>
                            <th scope="col">Descrição<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-sem-ops search">
                            </th>
                            <th scope="col">Quantidade em Pedidos<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-sem-ops search">
                            </th>
                    </thead>
                    <tbody>
                        <!-- Aqui vão os dados da tabela -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-container-sem-ops">
        </div>
    </div>
</div>

<div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="max-height: 80vh; overflow: auto">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 75vh; overflow: auto">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table-">
                        <thead id="fixed-header">
                            <tr>
                                <th>Data Reposição</th>
                                <th>Cod Barras Tag</th>
                                <th>Cod Reduzido</th>
                                <th>EPC</th>
                                <th>Nome</th>
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

<div class="modal fade modal-custom" id="modal-filtros-ops" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customModalLabel" style="color: black;">Filtros </h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                <div class="div-tabela table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="table-lista-pedidos">
                        <thead style="position: sticky; top: -5px; z-index: 2;">
                            <tr>
                                <th scope="col">Ações</th>
                                <th scope="col">Código Pedido<br>
                                    <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-lista-pedidos search" style="width: 100%">
                                </th>
                                <th scope="col">Data Emissão<br>
                                    <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-lista-pedidos search" style="width: 100%">
                                </th>
                                <th scope="col">Código Cliente<br>
                                    <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-lista-pedidos search" style="width: 100px;">
                                </th>
                                <th scope="col">Nome Cliente<br>
                                    <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-lista-pedidos search" style="width: 100px;">
                                </th>
                                <th scope="col">Código Tipo de Nota<br>
                                    <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-lista-pedidos search" style="width: 100%">
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados da tabela -->
                        </tbody>
                    </table>
                </div>
                <div class="pagination-container-lista-pedidos">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn" id="btn-selecionar-pedidos" style="background-color: #002955; color: white">Selecionar</button>
            </div>
        </div>
    </div>
</div>

<?php
include_once("../../../templates/footer.php");
?>

<script>
    let TipoDataSelecionado = "";
    let PriorizacaoSelecionado = "";
    $(function() {
        $(".accordion").accordion({
            collapsible: true,
            active: false,
            heightStyle: "content"
        });

        $(".inner-accordion").accordion({
            collapsible: true,
            active: false,
            heightStyle: "content"
        });
    });

    $(document).ready(async () => {
        const dataAtual = new Date();
        const dataFormatada = getdataFormatada(dataAtual);
        $('#data-inicio-pedido').val(dataFormatada);
        $('#data-fim-pedido').val(dataFormatada);
        $('#data-emissao-inicial').val(dataFormatada);
        $('#data-emissao-final').val(dataFormatada);
        $('#data-inicio-ops').val(dataFormatada);
        $('#data-fim-ops').val(dataFormatada);

        $('input[name="TipoPriorizacao"]').change(function() {
            PriorizacaoSelecionado = $('input[name="TipoPriorizacao"]:checked').val();
            console.log(PriorizacaoSelecionado);
        });

        $('input[name="TipoData"]').change(function() {
            TipoDataSelecionado = $('input[name="TipoData"]:checked').val();
            console.log(TipoDataSelecionado);
        });

        $('.dropdown-toggle').on('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            $(this).next('.dropdown-menu').toggle();
        });

        $('#btn-pedidos').addClass('btn-menu-clicado')
    });

    $('#btn-filtrar').click(async () => {
        await ConsultaPedidos();
        await ConsultaOps(true, 'data-inicio-pedido', 'data-fim-pedido');
        Consulta_Sem_Op(true, 'data-inicio-pedido', 'data-fim-pedido');
        console.log('teste')
    })

    function getdataFormatada(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    const ConsultaPedidos = async () => {
        console.log('teste')
        try {
            $('#loadingModal').modal('show');
            const iniVenda = $('#data-inicio-pedido').val();
            const finalVenda = $('#data-fim-pedido').val();
            const emissaoinicial = $('#data-emissao-inicial').val();
            const emissaofinal = $('#data-emissao-final').val();
            const tipoNota = '1,2,3,4';
            const tipoData = TipoDataSelecionado;
            const parametroClassificacao = PriorizacaoSelecionado;


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

            // $("#Filtros").removeClass('d-none');
            // $("#ItensPagina").removeClass('d-none');
            // $(".table-responsive").removeClass('d-none');
            $("#accordion").accordion({
                active: false
            });

            const DadosFormatados = formatarDados(response[0]['6 -Detalhamento']);
            console.log(DadosFormatados)
            DadosPedidos = DadosFormatados;
            criarTabelaPedidos(DadosPedidos);
            $('.corpo').removeClass('d-none')

        } catch (error) {
            console.log('Erro:', error);
        } finally {}
    }

    const Consultar_Lista_Pedidos = async () => {
        try {
            $('#loadingModal').modal('show');
            const iniVenda = $('#data-inicio-pedido').val();
            const finalVenda = $('#data-fim-pedido').val();
            const tipoData = PriorizacaoSelecionado;
            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Lista_Pedidos',
                    iniVenda: iniVenda,
                    finalVenda: finalVenda
                }
            });

            console.log(response);
            criarTabelaListaPedidos(response);
        } catch (error) {
            console.log('Erro:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }


    async function Filtro_Monitor_Ops(Pedidos) {
        $('#loadingModal').modal('show');
        const dados = {
            "dataInico": $('#data-inicio-pedido').val(),
            "dataFim": $('#data-fim-pedido').val(),
            "arrayPedidos": Pedidos
        }

        var requestData = {
            acao: "Filtro_Monitor_Ops",
            dados: dados
        };
        try {
            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response)
            criarTabelaOps(response['resposta'][0]['6 -Detalhamento']);
            $('#modal-filtros-ops').modal('hide');
            PedidosSelecionados = []
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error); // Exibir erro se ocorrer
            $('#loadingModal').modal('hide');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }


    const ConsultaOps = async (alteraData, datainicio, datafim) => {
        try {
            $('#loadingModal').modal('show');
            const dataInicio = $(`#${datainicio}`).val();
            const dataFim = $(`#${datafim}`).val();
            console.log(dataInicio)

            if (alteraData === true) {
                const dataInicioPedido = $('#data-inicio-pedido').val();
                $('#data-inicio-ops').val(dataInicioPedido);

                const dataFimPedido = $('#data-fim-pedido').val();
                $('#data-fim-ops').val(dataFimPedido);
            }


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



    async function Consulta_Sem_Op(alteraData, datainicio, datafim) {
        $('#loadingModal').modal('show');
        const dados = {
            "dataInico": $(`#${datainicio}`).val(),
            "dataFim": $(`#${datafim}`).val()
        }

        if (alteraData === true) {
            const dataInicioPedido = $('#data-inicio-pedido').val();
            $('#data-inicio-sem-ops').val(dataInicioPedido);

            const dataFimPedido = $('#data-fim-pedido').val();
            $('#data-fim-sem-ops').val(dataFimPedido);
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
            console.log(response);
            CriarTabelaSemOps(response['resposta'])
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error); // Exibir erro se ocorrer
            $('#loadingModal').modal('hide');
        } finally {
            $('#loadingModal').modal('hide');
        }
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

    function formatarMoeda(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    function criarTabelaPedidos(listaPedidos) {

        // Adiciona a coluna "Diferenca_Entregas"
        listaPedidos.forEach(item => {
            item['Diferenca_Entregas'] = item['09-Entregas Solic'] - item['10-Entregas Fat'];
        });

        // Verifica se a DataTable já foi inicializada e a destrói para evitar erros
        if ($.fn.DataTable.isDataTable('#TablePedidos')) {
            $('#TablePedidos').DataTable().destroy();
        }

        // Cria a DataTable para "TablePedidos"
        const tabelaPedidos = $('#TablePedidos').DataTable({
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
                text: '<i class="fas fa-file-excel"></i> Excel',
                title: 'Fila de Reposição',
                className: 'ButtonExcel'
            }, ],
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
                    render: function(data) {
                        return data + '%'; // Adiciona o símbolo de porcentagem
                    }
                },
                {
                    data: 'Agrupamento'
                }
            ],
            pagingType: 'simple',
            language: {
                paginate: {
                    first: 'Primeira',
                    previous: '<i class="icon ph-bold ph-skip-back"></i>',
                    next: '<i class="icon ph-bold ph-skip-forward"></i>',
                    last: 'Última'
                },
                emptyTable: "Nenhum dado disponível na tabela",
                zeroRecords: "Nenhum registro encontrado"
            },
            drawCallback: function() {
                const info = this.api().page.info();
                const currentPageInput = `
            <input type="text" id="pageInput" class="form-control" value="${info.page + 1}" min="1" max="${info.pages}" 
            style="width: 50px; text-align: center; margin-left: 3px; margin-right: 3px">
        `;
                const message = `Página ${currentPageInput} de ${info.pages}`;

                const paginateContainer = $('.pagination-container-pedidos');

                if (!$('#itemCountContainer').length) {
                    paginateContainer.before(`
                <div id="itemCountContainer" class="d-flex flex-column flex-md-row justify-content-between align-items-md-end align-items-start w-100 mb-2" style="min-height: 50px;">
                    <div class="d-flex flex-row align-items-center mb-2 mb-md-0">
                        <input type="number" id="itemCount" class="form-control" min="1" max="99" value="${info.length}" style="width: 60px;">
                        <span class="ms-1" style="color: black; width: 200px">Registro(s) por página</span>
                    </div>
                    <div class="d-flex flex-row align-items-center justify-content-end w-100 w-md-auto mt-2 mt-md-0">
                        <span class="pagination-info me-3 d-flex align-items-center justify-content-center" style="color: black;">${message}</span>
                    </div>
                </div>
            `);

                    // Move os botões de paginação para o novo layout sem duplicar
                    $('.dataTables_wrapper .dataTables_paginate').appendTo('#itemCountContainer .d-flex.align-items-center.justify-content-end');
                } else {
                    // Apenas atualiza a mensagem
                    $('.pagination-info').html(message);
                }

                // Listener para mudar o número de registros por página
                $('#itemCount').off('change').on('change', function() {
                    const count = parseInt($(this).val(), 10);
                    if (count > 0) {
                        tabelaPedidos.page.len(count).draw();
                    }
                });

                // Listener para digitar e mudar de página ao pressionar Enter
                $('#pageInput').off('keydown').on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        const newPage = parseInt($(this).val(), 10) - 1; // Páginas no DataTable são baseadas em zero
                        if (newPage >= 0 && newPage < info.pages) {
                            tabelaPedidos.page(newPage).draw('page');
                        } else {
                            alert(`Por favor, insira um número de página entre 1 e ${info.pages}.`);
                        }
                    }
                });
            }
        });

        // Filtros de busca
        $('.search-input').on('keyup change', function() {
            const columnIndex = $(this).closest('th').index();
            const searchTerm = $(this).val();
            tabelaPedidos.column(columnIndex).search(searchTerm).draw();
        });

        // Evitar propagação do clique no input de busca
        $('.search-input').on('click', function(e) {
            e.stopPropagation();
        });

        // Prevenir o envio do formulário ao pressionar Enter dentro do input de busca
        $('.search-input').on('keydown', function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                e.stopPropagation();
            }
        });
        $('#TablePedidos').on('click', '.codPedidoClicado', function() {
            const codPedido = $(this).attr('data-codigoPedido');
            console.log('codPedido:', codPedido); // Ajuste aqui para melhorar a depuração
            DetalharPedido(codPedido);
        });
    };


    function criarTabelaListaPedidos(listaPedidos) {

        // Verifica se a DataTable já foi inicializada e a destrói para evitar erros
        if ($.fn.DataTable.isDataTable('#table-lista-pedidos')) {
            $('#table-lista-pedidos').DataTable().destroy();
            $(`#itemCountContainer-lista-pedidos`).remove();
        }

        // Cria a DataTable para "table-lista-pedidos"
        const tabelaListaPedidos = $('#table-lista-pedidos').DataTable({
            responsive: false,
            paging: true,
            info: false,
            searching: true,
            colReorder: true,
            data: listaPedidos,
            lengthChange: false,
            pageLength: 10,
            fixedHeader: true,
            columns: [{
                    data: null,
                    render: function(row) {
                        return `<div class="acoes d-flex justify-content-center align-items-center" style="height: 100%;">
                            <input type="checkbox" class="row-checkbox" value="${row.codLote}">
                        </div>`;
                    }
                },
                {
                    data: 'codPedido'
                },
                {
                    data: 'dataEmissao'
                },
                {
                    data: 'codCliente'
                },
                {
                    data: 'nome_cli'
                },
                {
                    data: 'codTipoNota'
                },
            ],
            pagingType: 'simple',
            language: {
                paginate: {
                    first: 'Primeira',
                    previous: '<i class="icon ph-bold ph-skip-back"></i>',
                    next: '<i class="icon ph-bold ph-skip-forward"></i>',
                    last: 'Última'
                },
                emptyTable: "Nenhum dado disponível na tabela",
                zeroRecords: "Nenhum registro encontrado"
            },
            drawCallback: function() {
                const info = this.api().page.info();
                const currentPageInput = `
            <input type="text" id="pageInput-lista-pedidos" class="form-control" value="${info.page + 1}" min="1" max="${info.pages}" 
            style="width: 50px; text-align: center; margin-left: 3px; margin-right: 3px">
        `;
                const message = `Página ${currentPageInput} de ${info.pages}`;

                const paginateContainer = $('.pagination-container-lista-pedidos');

                if (!$('#itemCountContainer-lista-pedidos').length) {
                    paginateContainer.before(`
                <div id="itemCountContainer-lista-pedidos" class="d-flex flex-column flex-md-row justify-content-between align-items-md-end align-items-start w-100 mb-2" style="min-height: 50px;">
                    <div class="d-flex flex-row align-items-center mb-2 mb-md-0">
                        <input type="number" id="itemCount-lista-pedidos" class="form-control" min="1" max="99" value="${info.length}" style="width: 60px;">
                        <span class="ms-1" style="color: black; width: 200px">Registro(s) por página</span>
                    </div>
                    <div class="d-flex flex-row align-items-center justify-content-end w-100 w-md-auto mt-2 mt-md-0">
                        <span class="pagination-info-lista-pedidos me-3 d-flex align-items-center justify-content-center" style="color: black;">${message}</span>
                    </div>
                </div>
            `);

                    // Move os botões de paginação para o novo layout sem duplicar
                    $('.dataTables_wrapper .dataTables_paginate').appendTo('#itemCountContainer-lista-pedidos .d-flex.align-items-center.justify-content-end');
                } else {
                    // Apenas atualiza a mensagem
                    $('.pagination-info-lista-pedidos').html(message);
                }

                // Listener para mudar o número de registros por página
                $('#itemCount-lista-pedidos').off('change').on('change', function() {
                    const count = parseInt($(this).val(), 10);
                    if (count > 0) {
                        tabelaListaPedidos.page.len(count).draw();
                    }
                });

                // Listener para digitar e mudar de página ao pressionar Enter
                $('#pageInput-lista-pedidos').off('keydown').on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        const newPage = parseInt($(this).val(), 10) - 1; // Páginas no DataTable são baseadas em zero
                        if (newPage >= 0 && newPage < info.pages) {
                            tabelaListaPedidos.page(newPage).draw('page');
                        } else {
                            alert(`Por favor, insira um número de página entre 1 e ${info.pages}.`);
                        }
                    }
                });
            }
        });

        // Filtros de busca
        $('.search-input-lista-pedidos').on('keyup change', function() {
            const columnIndex = $(this).closest('th').index();
            const searchTerm = $(this).val();
            tabelaListaPedidos.column(columnIndex).search(searchTerm).draw();
        });

        // Evitar propagação do clique no input de busca
        $('.search-input-lista-pedidos').on('click', function(e) {
            e.stopPropagation();
        });

        // Prevenir o envio do formulário ao pressionar Enter dentro do input de busca
        $('.search-input-lista-pedidos').on('keydown', function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        const table = $('#table-lista-pedidos').DataTable();
        let PedidosSelecionados = []

        async function VerificaPedidosSelecionados() {
            PedidosSelecionados.length = 0; // Limpa o array antes de verificar

            // Itera sobre cada linha da tabela
            table.rows().every(function() {
                const checkbox = $(this.node()).find('.row-checkbox'); // Seletor correto
                if (checkbox.is(':checked')) {
                    const row = this.data();
                    const CodigoPedido = row['codPedido']; // Certifique-se de que a chave está correta

                    // Adiciona o código do lote ao array se não estiver presente
                    if (!PedidosSelecionados.includes(CodigoPedido)) {
                        PedidosSelecionados.push(CodigoPedido);
                    }
                }
            });

            // Verifica se nenhum lote foi selecionado
            if (PedidosSelecionados.length === 0) {
                Mensagem_Salva('Nenhum pedido selecionado!', 'warning')
            }
        };

        $('#btn-selecionar-pedidos').click(async () => {
            await VerificaPedidosSelecionados(); // Aguarda a verificação
            if (PedidosSelecionados.length === 0) {

            } else {
                try {
                    Filtro_Monitor_Ops(PedidosSelecionados)
                } catch (error) {
                    console.error('Erro na solicitação AJAX:', error); // Exibe erro se ocorrer
                    $('#loadingModal').modal('hide');
                    Mensagem('Erro', 'error');
                }
            }
        });
    }


    function criarTabelaOps(listaOps) {
        if ($.fn.DataTable.isDataTable('#TableOps')) {
            $('#TableOps').DataTable().destroy();
            $(`#itemCountContainer-ops`).remove();
        }
        // Criar DataTable para TableOps
        const tabelaOps = $('#TableOps').DataTable({
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
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    title: 'Fila de Reposição',
                    className: 'ButtonExcel'
                },
                {
                    text: '<i class="icon ph-bold ph-funnel" style="margin-right: 5px;"></i> Filtros',
                    title: 'Adicionar Faccionista',
                    className: 'btn-botoes',
                    action: async function(e, dt, node, config) {
                        Consultar_Lista_Pedidos();
                        $('#modal-filtros-ops').modal('show')
                    },
                }
            ],
            columns: [{
                    data: 'numeroop',
                    render: function(data, type) {
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
                }
            ],
            pagingType: 'simple',
            language: {
                paginate: {
                    first: 'Primeira',
                    previous: '<i class="icon ph-bold ph-skip-back"></i>',
                    next: '<i class="icon ph-bold ph-skip-forward"></i>',
                    last: 'Última'
                },
                emptyTable: "Nenhum dado disponível na tabela",
                zeroRecords: "Nenhum registro encontrado"
            },
            drawCallback: function() {
                const info = this.api().page.info();
                const currentPageInput = `
            <input type="text" id="pageInput-ops" class="form-control" value="${info.page + 1}" min="1" max="${info.pages}" 
            style="width: 50px; text-align: center; margin-left: 3px; margin-right: 3px">
        `;
                const message = `Página ${currentPageInput} de ${info.pages}`;

                const paginateContainer = $('.pagination-container-ops');

                if (!$('#itemCountContainer-ops').length) {
                    paginateContainer.before(`
                <div id="itemCountContainer-ops" class="d-flex flex-column flex-md-row justify-content-between align-items-md-end align-items-start w-100 mb-2" style="min-height: 50px;">
                    <div class="d-flex flex-row align-items-center mb-2 mb-md-0">
                        <input type="number" id="itemCount-ops" class="form-control" min="1" max="99" value="${info.length}" style="width: 60px;">
                        <span class="ms-1" style="color: black; width: 200px">Registro(s) por página</span>
                    </div>
                    <div class="d-flex flex-row align-items-center justify-content-end w-100 w-md-auto mt-2 mt-md-0">
                        <span class="pagination-info-ops me-3 d-flex align-items-center justify-content-center" style="color: black;">${message}</span>
                    </div>
                </div>
            `);

                    // Move os botões de paginação para o novo layout sem duplicar
                    $('.dataTables_wrapper .dataTables_paginate').appendTo('#itemCountContainer-ops .d-flex.align-items-center.justify-content-end');
                } else {
                    // Apenas atualiza a mensagem
                    $('.pagination-info-ops').html(message);
                }

                // Listener para mudar o número de registros por página
                $('#itemCount-ops').off('change').on('change', function() {
                    const count = parseInt($(this).val(), 10);
                    if (count > 0) {
                        tabelaOps.page.len(count).draw();
                    }
                });

                // Listener para digitar e mudar de página ao pressionar Enter
                $('#pageInput-ops').off('keydown').on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        const newPage = parseInt($(this).val(), 10) - 1; // Páginas no DataTable são baseadas em zero
                        if (newPage >= 0 && newPage < info.pages) {
                            tabelaOps.page(newPage).draw('page');
                        } else {
                            alert(`Por favor, insira um número de página entre 1 e ${info.pages}.`);
                        }
                    }
                });
            }
        });

        // Filtros de busca
        $('.search-input-ops').on('keyup change', function() {
            const columnIndex = $(this).closest('th').index();
            const searchTerm = $(this).val();
            tabelaOps.column(columnIndex).search(searchTerm).draw();
        });

        // Evitar propagação do clique no input de busca
        $('.search-input-ops').on('click', function(e) {
            e.stopPropagation();
        });

        // Prevenir o envio do formulário ao pressionar Enter dentro do input de busca
        $('.search-input-ops').on('keydown', function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }

    function CriarTabelaSemOps(listaOps) {
        if ($.fn.DataTable.isDataTable('#Table-sem-ops')) {
            $('#Table-sem-ops').DataTable().destroy();
        }
        // Criar DataTable para TableOps
        const tabelaSemOps = $('#Table-sem-ops').DataTable({
            responsive: false,
            paging: true,
            info: false,
            searching: true,
            colReorder: true,
            data: listaOps,
            lengthChange: false,
            fixedHeader: true,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Excel',
                title: 'Fila de Reposição',
                className: 'ButtonExcel'
            }, ],
            columns: [{
                    data: 'codEngenharia'
                },
                {
                    data: 'tamanho'
                },
                {
                    data: 'codCor'
                },
                {
                    data: 'nomeSKU'
                },
                {
                    data: 'QtdSaldo'
                },
            ],
            pagingType: 'simple',
            language: {
                paginate: {
                    first: 'Primeira',
                    previous: '<i class="icon ph-bold ph-skip-back"></i>',
                    next: '<i class="icon ph-bold ph-skip-forward"></i>',
                    last: 'Última'
                },
                emptyTable: "Nenhum dado disponível na tabela",
                zeroRecords: "Nenhum registro encontrado"
            },
            drawCallback: function() {
                const info = this.api().page.info();
                const currentPageInput = `
            <input type="text" id="pageInput-sem-ops" class="form-control" value="${info.page + 1}" min="1" max="${info.pages}" 
            style="width: 50px; text-align: center; margin-left: 3px; margin-right: 3px">
        `;
                const message = `Página ${currentPageInput} de ${info.pages}`;

                const paginateContainer = $('.pagination-container-sem-ops');

                if (!$('#itemCountContainer-sem-ops').length) {
                    paginateContainer.before(`
                <div id="itemCountContainer-sem-ops" class="d-flex flex-column flex-md-row justify-content-between align-items-md-end align-items-start w-100 mb-2" style="min-height: 50px;">
                    <div class="d-flex flex-row align-items-center mb-2 mb-md-0">
                        <input type="number" id="itemCount-sem-ops" class="form-control" min="1" max="99" value="${info.length}" style="width: 60px;">
                        <span class="ms-1" style="color: black; width: 200px">Registro(s) por página</span>
                    </div>
                    <div class="d-flex flex-row align-items-center justify-content-end w-100 w-md-auto mt-2 mt-md-0">
                        <span class="pagination-info-sem-ops me-3 d-flex align-items-center justify-content-center" style="color: black;">${message}</span>
                    </div>
                </div>
            `);

                    // Move os botões de paginação para o novo layout sem duplicar
                    $('.dataTables_wrapper .dataTables_paginate').appendTo('#itemCountContainer-sem-ops .d-flex.align-items-center.justify-content-end');
                } else {
                    // Apenas atualiza a mensagem
                    $('.pagination-info-sem-ops').html(message);
                }

                // Listener para mudar o número de registros por página
                $('#itemCount-sem-ops').off('change').on('change', function() {
                    const count = parseInt($(this).val(), 10);
                    if (count > 0) {
                        tabelaSemOps.page.len(count).draw();
                    }
                });

                // Listener para digitar e mudar de página ao pressionar Enter
                $('#pageInput-sem-ops').off('keydown').on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        const newPage = parseInt($(this).val(), 10) - 1; // Páginas no DataTable são baseadas em zero
                        if (newPage >= 0 && newPage < info.pages) {
                            tabelaSemOps.page(newPage).draw('page');
                        } else {
                            alert(`Por favor, insira um número de página entre 1 e ${info.pages}.`);
                        }
                    }
                });
            }
        });

        // Filtros de busca
        $('.search-input-sem-ops').on('keyup change', function() {
            const columnIndex = $(this).closest('th').index();
            const searchTerm = $(this).val();
            tabelaSemOps.column(columnIndex).search(searchTerm).draw();
        });

        // Evitar propagação do clique no input de busca
        $('.search-input-sem-ops').on('click', function(e) {
            e.stopPropagation();
        });

        // Prevenir o envio do formulário ao pressionar Enter dentro do input de busca
        $('.search-input-sem-ops').on('keydown', function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }
</script>
