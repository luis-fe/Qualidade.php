<?php
include_once("requests.php");
include_once("../../../templates/Loading.php");
include_once("../../../templates/headerPcp.php");
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="style2.css">
<style>
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
        /* Alinha os botões à direita */
    }

    .container-pagination .paginate_button {
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

    .container-pagination .paginate_button.previous {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
        border-left: 1px solid gray;
    }

    .container-pagination .paginate_button.next {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
        border-right: 1px solid gray;
    }

    .container-pagination .paginate_button:hover {
        background: #002955;
        color: white;
        /* Cor do botão ao passar o mouse */
    }

    .container-pagination .paginate_button.current {
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

    .pagination-container-planos {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        font-size: 13px;
    }

    #itemCount,
    #itemCount-notas,
    #itemCount-lotes,
    #itemCount-notas-csw,
    #itemCount-lotes-csw {
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
        border-radius: 8px;
        color: white;
        padding: 5px 15px;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s, transform 0.3s;
        cursor: pointer;
        float: left;
        margin-bottom: 10px;
    }

    .btn-menus.disabled {
        border: 1px solid black;
    }

    .btn-botoes {
        border: 1px solid gray;
        border-radius: 15px;
        padding: 6px 15px;
    }

    .btn-botoes:hover {
        background-color: lightgray;
        border: 1px solid gray;
    }

    .btn-save {
        border: 1px solid gray;
        border-radius: 15px;
        padding: 6px 15px;
        background-color: #0056b3;
        color: white;
    }

    .btn-save:hover {
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
<div class="titulo" style="padding: 10px; text-align: left; border-bottom: 1px solid black; color: black; font-size: 15px; font-weight: 600;"><i class="icon ph-bold ph-monitor"></i> Plano de Produção</div>

<div class="corpo">
    <div class="menu-tela" style="border-bottom: 1px solid lightgray; width: 100%; margin-top: 10px; color: black; text-align: left; padding-left: 10px;">
        <button class="btn btn-menus" id="btn-detalhes" onclick="$('#btn-detalhes').addClass('btn-menu-clicado'); $('#btn-colecoes').removeClass('btn-menu-clicado'); $('#btn-lotes').removeClass('btn-menu-clicado'); $('#btn-notas').removeClass('btn-menu-clicado'); $('.corpo-planos').removeClass('d-none'); $('.corpo-colecoes').addClass('d-none'); $('.corpo-lotes').addClass('d-none'); $('.corpo-notas').addClass('d-none'); if($('#codigo-plano').val() != ''){$('.btn-editar').removeClass('d-none')}"><i class="icon ph-bold ph-folders" style="margin-right: 5px;"></i>Detalhes</button>
        <button class="btn btn-menus disabled" id="btn-colecoes" onclick="$('.btn-salvar').addClass('d-none'); $('.btn-editar').addClass('d-none'); $('#btn-detalhes').removeClass('btn-menu-clicado'); $('#btn-colecoes').addClass('btn-menu-clicado'); $('#btn-lotes').removeClass('btn-menu-clicado'); $('#btn-notas').removeClass('btn-menu-clicado'); $('.corpo-planos').addClass('d-none'); $('.corpo-colecoes').removeClass('d-none'); $('.corpo-lotes').addClass('d-none'); $('.corpo-notas').addClass('d-none')"><i class="icon ph-bold ph-file-minus" style="margin-right: 5px;"></i>Coleções</button>
        <button class="btn btn-menus disabled" id="btn-lotes" onclick="$('.btn-salvar').addClass('d-none'); $('.btn-editar').addClass('d-none'); $('#btn-detalhes').removeClass('btn-menu-clicado'); $('#btn-colecoes').removeClass('btn-menu-clicado'); $('#btn-lotes').addClass('btn-menu-clicado'); $('#btn-notas').removeClass('btn-menu-clicado'); $('.corpo-planos').addClass('d-none'); $('.corpo-colecoes').addClass('d-none'); $('.corpo-lotes').removeClass('d-none'); $('.corpo-notas').addClass('d-none')"><i class="icon ph-bold ph-file-minus" style="margin-right: 5px;"></i>Lotes</button>
        <button class="btn btn-menus disabled" id="btn-notas" onclick="$('.btn-salvar').addClass('d-none'); $('.btn-editar').addClass('d-none'); $('#btn-detalhes').removeClass('btn-menu-clicado'); $('#btn-colecoes').removeClass('btn-menu-clicado'); $('#btn-lotes').removeClass('btn-menu-clicado'); $('#btn-notas').addClass('btn-menu-clicado'); $('.corpo-planos').addClass('d-none'); $('.corpo-colecoes').addClass('d-none'); $('.corpo-lotes').addClass('d-none') ; $('.corpo-notas').removeClass('d-none')"><i class="icon ph-bold ph-file-minus" style="margin-right: 5px;"></i>Tipos de Notas</button>
    </div>
    <div class="botoes d-flex col-12 border-bottom mt-3 mb-3 pb-2 justify-content-start">
        <div class="menu-botoes-principal btn-novo-plano" style="margin-right: 15px;">
            <button class="btn btn-botoes" id="btn-novo-plano" onclick="
                                        $('#itemCountContainer').addClass('d-none');
                                        $('.div-table-planos').addClass('d-none');
                                        $('.pagination-container-planos').addClass('d-none');
                                        $('.btn-novo-plano').addClass('d-none');
                                        $('.div-cadastro-plano').removeClass('d-none');
                                        $('.btn-salvar').removeClass('d-none');
                                        $('.btn-voltar').removeClass('d-none');">
                <i class="icon ph ph-pencil" style="margin-right: 5px;"></i>Novo
            </button>
        </div>
        <div class="menu-botoes-principal btn-voltar d-none" style="margin-right: 15px;">
            <button class="btn btn-botoes" id="btn-voltar" onclick="button_voltar()">
                <i class="icon ph-bold ph-arrow-u-up-left" style="margin-right: 5px;"></i>Voltar
            </button>
        </div>
        <div class="menu-botoes-principal btn-salvar d-none" style="margin-right: 15px;">
            <button class="btn btn-save" id="btn-salvar" onclick="Cadastrar_Plano();">
                <i class="icon ph-bold ph-floppy-disk" style="margin-right: 5px;"></i>Salvar
            </button>
        </div>
        <div class="menu-botoes-principal btn-editar d-none" style="margin-right: 15px;">
            <button class="btn btn-save" id="btn-editar" onclick="Alterar_Plano()">
                <i class="icon ph-bold ph-floppy-disk" style="margin-right: 5px;"></i>Salvar
            </button>
        </div>
        <div class="menu-botoes-principal corpo-colecoes d-none" style="margin-right: 15px;">
            <button class="btn btn-save" id="btn-adicionar-colecao">
                <i class="icon ph-bold ph-plus-square" style="margin-right: 5px;"></i>Adicionar
            </button>
        </div>
        <div class="menu-botoes-principal corpo-lotes d-none" style="margin-right: 15px;">
            <button class="btn btn-save" id="btn-adicionar-lote" onclick="Consultar_Lotes_Csw(); $('#modal-lotes').modal('show')">
                <i class="icon ph-bold ph-plus-square" style="margin-right: 5px;"></i>Adicionar
            </button>
        </div>
        <div class="menu-botoes-principal corpo-notas d-none" style="margin-right: 15px;">
            <button class="btn btn-save" id="btn-adicionar-nota" onclick="Consultar_Notas_Csw(); $('#modal-notas').modal('show')">
                <i class="icon ph-bold ph-plus-square" style="margin-right: 5px;"></i>Adicionar
            </button>
        </div>
    </div>
    <div class="corpo-planos d-flex justify-content-md-start justify-content-center flex-wrap" style="padding: 5px 10px;">
        <div class="col-12 div-table-planos" style="min-width: 100%;">
            <div class="div-tabela table-responsive" style="min-width: 100%;">
                <table class="table table-bordered table-striped table-hover" id="table-planos" style="min-width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">Ações</th>
                            <th scope="col">Código Plano<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-planos search" style="width: 100px;">
                            </th>
                            <th scope="col">Descrição<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-planos search" style="width: 100px;">
                            </th>
                            <th scope="col">Início das Vendas<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-planos search" style="width: 100px;">
                            </th>
                            <th scope="col">Final das Vendas<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-planos search" style="width: 100px;">
                            </th>
                            <th scope="col">Início do Faturamento<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-planos search" style="width: 100px;">
                            </th>
                            <th scope="col">Final do Faturamento<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-planos search" style="width: 100px;">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Tabela dados -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-container-planos">
        </div>

        <div class="col-12 div-cadastro-plano row d-none">
            <div class="d-flex col-12 justify-content-center" style="flex-wrap: wrap;">
                <div class="col-md-3  col-12 mb-3">
                    <label for="codigo-plano" class="form-label text-dark fw-bold">Código do Plano</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="codigo-plano" placeholder="Insira o Código" style="border: 1px solid gray; border-radius: 8px;">
                    </div>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <label for="descricao-plano" class="form-label text-dark fw-bold">Descrição do Plano</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="descricao-plano" placeholder="Insira a Descrição" style="border: 1px solid gray; border-radius: 8px;">
                    </div>
                </div>
            </div>

            <div class="d-flex w-100 col-12 justify-content-center" style="flex-wrap: wrap;">
                <!-- Data Inputs -->
                <div class="col-md-3 col-12 mb-3">
                    <label for="inicio-venda" class="form-label text-dark fw-bold">Inicio Vendas</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <input type="date" class="form-control" id="inicio-venda" style="border: 1px solid gray; border-radius: 8px; border-top-left-radius: 0px; border-bottom-left-radius: 0px;">
                    </div>
                </div>

                <div class="col-md-3 col-12 mb-3">
                    <label for="final-venda" class="form-label text-dark fw-bold">Final Vendas</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <input type="date" class="form-control" id="final-venda" style="border: 1px solid gray; border-radius: 8px; border-top-left-radius: 0px; border-bottom-left-radius: 0px;">
                    </div>
                </div>

                <div class="col-md-3 col-12 mb-3">
                    <label for="inicio-faturamento" class="form-label text-dark fw-bold">Inicio Fat.</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <input type="date" class="form-control" id="inicio-faturamento" style="border: 1px solid gray; border-radius: 8px; border-top-left-radius: 0px; border-bottom-left-radius: 0px;">
                    </div>
                </div>

                <div class="col-md-3 col-12 mb-3">
                    <label for="final-faturamento" class="form-label text-dark fw-bold">Final Fat.</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                        <input type="date" class="form-control" id="final-faturamento" style="border: 1px solid gray; border-radius: 8px; border-top-left-radius: 0px; border-bottom-left-radius: 0px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="corpo-colecoes d-flex justify-content-md-start justify-content-center flex-wrap d-none" style="padding: 5px 10px;">
        <div class="col-12 div-table-colecoes">
            <div class="div-tabela table-responsive">
                <table class="table table-bordered table-striped table-hover" id="table-colecoes">
                    <thead>
                        <tr>
                            <th scope="col">Ações</th>
                            <th scope="col">Código Plano<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search" style="width: 100px;">
                            </th>
                            <th scope="col">Descrição<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search" style="width: 150px;">
                            </th>
                            <th scope="col">Inicio das Vendas<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search" style="width: 150px;">
                            </th>
                            <th scope="col">Final das Vendas<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search" style="width: 150px;">
                            </th>
                            <th scope="col">Inicio do Faturamento<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search" style="width: 150px;">
                            </th>
                            <th scope="col">Final do Faturamento<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input search" style="width: 150px;">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Tabela dados -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-container-colecoes">
        </div>
    </div>
    <div class="corpo-lotes d-flex justify-content-md-start justify-content-center flex-wrap d-none" style="padding: 5px 10px;">
        <div class="col-12 div-table-lotes" style="min-width: 100%;">
            <div class="div-tabela table-responsive">
                <table class="table table-bordered table-striped table-hover" id="table-lotes" style="min-width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">Ações</th>
                            <th scope="col">Código Lote<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-lotes search" style="width: 100px;">
                            </th>
                            <th scope="col">Descrição<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-lotes search" style="width: 100%;">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Tabela dados -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-container-lotes">
        </div>
    </div>
    <div class="corpo-notas d-flex justify-content-md-start justify-content-center flex-wrap d-none" style="padding: 5px 10px;">
        <div class="col-12 div-table-notas" style="min-width: 100%;">
            <div class="div-tabela table-responsive">
                <table class="table table-bordered table-striped table-hover" id="table-notas" style="min-width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col">Ações</th>
                            <th scope="col">Código Tipo de Nota<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-notas search" style="width: 100%;">
                            </th>
                            <th scope="col">Descrição<br>
                                <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-notas search" style="width: 100%;">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Tabela dados -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pagination-container-notas">
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-notas" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customModalLabel" style="color: black;">Tipos de Notas (Csw)</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                <div class="div-tabela table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="table-notas-csw">
                        <thead style="position: sticky; top: -5px; z-index: 2;">
                            <tr>
                                <th scope="col">Ações</th>
                                <th scope="col">Código Lote<br>
                                    <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-notas-csw search" style="width: 100px;">
                                </th>
                                <th scope="col">Descrição<br>
                                    <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-notas-csw search" style="width: 100%">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados da tabela -->
                        </tbody>
                    </table>
                </div>
                <div class="pagination-container-notas-csw">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn" id="btn-selecionar-notas" style="background-color: #002955; color: white">Selecionar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-lotes" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customModalLabel" style="color: black;">Lotes (Csw)</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                <div class="div-tabela table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="table-lotes-csw">
                        <thead style="position: sticky; top: -5px; z-index: 2;">
                            <tr>
                                <th scope="col">Ações</th>
                                <th scope="col">Código Lote<br>
                                    <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-lotes-csw search" style="width: 100px;">
                                </th>
                                <th scope="col">Descrição<br>
                                    <div style="width: 100%; border-bottom: 1px solid gray; margin: 0; padding: 0; margin-bottom: -15px"></div><br><input type="search" class="search-input-lotes-csw search" style="width: 100%">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados da tabela -->
                        </tbody>
                    </table>
                </div>
                <div class="pagination-container-lotes-csw">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn" id="btn-selecionar-lotes" style="background-color: #002955; color: white">Selecionar</button>
            </div>
        </div>
    </div>
</div>


<?php
include_once("../../../templates/footer.php");
?>

<script>
    $(document).ready(async () => {
        ConsultaPlanos()
    });

    function button_voltar() {
        $('.btn-voltar').addClass('d-none');
        $('.btn-editar').addClass('d-none');
        $('.btn-novo-plano').removeClass('d-none');
        $('.div-table-planos').removeClass('d-none');
        $('.div-cadastro-plano').addClass('d-none');
        $('.pagination-container-planos').removeClass('d-none');
        $('#itemCountContainer-planos').removeClass('d-none');
        $('#codigo-plano').val('');
        $('#descricao-plano').val('');
        $('#inicio-venda').val('');
        $('#inicio-faturamento').val('');
        $('#final-faturamento').val('');
        $('#btn-colecoes').addClass('disabled');
        $('#btn-notas').addClass('disabled');
        $('#btn-lotes').addClass('disabled');
        $('.corpo-planos').removeClass('d-none');
        $('.corpo-lotes').addClass('d-none');
        $('.corpo-colecoes').addClass('d-none');
        $('.corpo-notas').addClass('d-none');

    };


    const ConsultaPlanos = async () => {
        console.log('teste')
        try {
            $('#loadingModal').modal('show');

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Planos',
                }
            });

            console.log(response);
            criarTabelaPlanos(response)

        } catch (error) {
            console.log('Erro:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    const Consultar_Notas_Vinculados = async () => {
        try {
            $('#loadingModal').modal('show');

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Notas_Vinculados',
                    plano: $('#codigo-plano').val()
                }
            });

            console.log(response);
            criarTabelaNotas(response)
        } catch (error) {
            console.log('Erro:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    const Consultar_Lotes_Vinculados = async () => {
        try {
            $('#loadingModal').modal('show');

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Lotes_Vinculados',
                    plano: $('#codigo-plano').val()
                }
            });

            console.log(response);
            criarTabelaLotes(response)
        } catch (error) {
            console.log('Erro:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    const Consultar_Notas_Csw = async () => {
        try {
            $('#loadingModal').modal('show');

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Notas_Csw',
                }
            });

            console.log(response);
            criarTabelaNotasCsw(response)
        } catch (error) {
            console.log('Erro:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    const Consultar_Lotes_Csw = async () => {
        try {
            $('#loadingModal').modal('show');

            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Lotes_Csw',
                }
            });

            console.log(response);
            criarTabelaLotesCsw(response)
        } catch (error) {
            console.log('Erro:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    async function Cadastrar_Plano() {
        $('#loadingModal').modal('show');
        try {
            if ($('#descricao-plano').val() == '') {
                Mensagem('Descrição Obrigatória!', 'warning')
            } else {
                const dados = {
                    "codigoPlano": $('#codigo-plano').val(),
                    "descricaoPlano": $('#descricao-plano').val(),
                    "iniVendas": $('#inicio-venda').val(),
                    "fimVendas": $('#final-venda').val(),
                    "iniFat": $('#inicio-faturamento').val(),
                    "fimFat": $('#final-fatturamento').val()
                }
                var requestData = {
                    acao: "Cadastrar_Plano",
                    dados: dados
                };
                const response = await $.ajax({
                    type: 'POST',
                    url: 'requests.php',
                    contentType: 'application/json',
                    data: JSON.stringify(requestData),
                });
                console.log(response);
                if (response['resposta'][0]['Status'] == true) {
                    Mensagem_Salva('Plano Cadastrado', 'success');
                    $('#btn-colecoes').removeClass('disabled');
                    $('#btn-notas').removeClass('disabled');
                    $('#btn-lotes').removeClass('disabled');
                    $('.btn-editar').removeClass('d-none');
                    $('.btn-salvar').removeClass('d-none')
                    await Consultar_Notas_Vinculados()
                } else {
                    Mensagem_Salva('Plano já existe', 'error')
                }
            }
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    async function Alterar_Plano() {
        $('#loadingModal').modal('show');
        try {
            if ($('#descricao-plano').val() == '') {
                Mensagem('Descrição Obrigatória!', 'warning')
            } else {
                const dados = {
                    "codigoPlano": $('#codigo-plano').val(),
                    "descricaoPlano": $('#descricao-plano').val(),
                    "iniVendas": $('#inicio-venda').val(),
                    "fimVendas": $('#final-venda').val(),
                    "iniFat": $('#inicio-faturamento').val(),
                    "fimFat": $('#final-fatturamento').val()
                }
                var requestData = {
                    acao: "Alterar_Plano",
                    dados: dados
                };
                const response = await $.ajax({
                    type: 'PUT',
                    url: 'requests.php',
                    contentType: 'application/json',
                    data: JSON.stringify(requestData),
                });
                console.log(response);
                if (response[0]['Status'] == true) {
                    Mensagem_Salva('Plano Editado', 'success');
                    await Consultar_Notas_Vinculados()
                } else {
                    Mensagem_Salva('Erro', 'error')
                }
            }
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }


    function criarTabelaGenerica(listaDados, tabelaId, itemCountContainerId, pageInputId, searchInputClass, columnConfig, paginationContainer) {
        // Verifica se a DataTable já foi inicializada e a destrói para evitar erros
        if ($.fn.DataTable.isDataTable(`#${tabelaId}`)) {
            $(`#${tabelaId}`).DataTable().destroy();
            $(`#${itemCountContainerId}`).remove();
        }

        // Cria a DataTable
        const tabela = $(`#${tabelaId}`).DataTable({
            responsive: false,
            paging: true,
            info: false,
            searching: true,
            colReorder: true,
            data: listaDados,
            lengthChange: false,
            pageLength: 10,
            fixedHeader: true,
            columns: columnConfig,
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
                <input type="text" id="${pageInputId}" class="form-control" value="${info.page + 1}" min="1" max="${info.pages}" 
                style="width: 50px; text-align: center; margin-left: 3px; margin-right: 3px">
            `;
                const message = `Página ${currentPageInput} de ${info.pages}`;

                const paginateContainer = $(`.${paginationContainer}`);

                if (!$(`#${itemCountContainerId}`).length) {
                    paginateContainer.before(`
                    <div id="${itemCountContainerId}" class="container-pagination d-flex flex-column flex-md-row justify-content-between align-items-md-end align-items-start w-100 mb-2" style="min-height: 50px;">
                        <div class="d-flex flex-row align-items-center mb-2 mb-md-0">
                            <input type="number" id="itemCount-${tabelaId}" class="form-control" min="1" max="99" value="${info.length}" style="width: 60px;">
                            <span class="ms-1" style="color: black; width: 200px">Registro(s) por página</span>
                        </div>
                        <div class="d-flex flex-row align-items-center justify-content-end w-100 w-md-auto mt-2 mt-md-0">
                            <span class="pagination-info-${tabelaId} me-3 d-flex align-items-center justify-content-center" style="color: black;">${message}</span>
                        </div>
                    </div>
                `);

                    $('.dataTables_wrapper .dataTables_paginate').appendTo(`#${itemCountContainerId} .d-flex.align-items-center.justify-content-end`);
                } else {
                    $(`.pagination-info-${tabelaId}`).html(message);
                }

                $(`#itemCount-${tabelaId}`).off('change').on('change', function() {
                    const count = parseInt($(this).val(), 10);
                    if (count > 0) {
                        tabela.page.len(count).draw();
                    }
                });

                $(`#${pageInputId}`).off('keydown').on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        const newPage = parseInt($(this).val(), 10) - 1;
                        if (newPage >= 0 && newPage < info.pages) {
                            tabela.page(newPage).draw('page');
                        } else {
                            alert(`Por favor, insira um número de página entre 1 e ${info.pages}.`);
                        }
                    }
                });
            }
        });

        $(`.${searchInputClass}`).on('keyup change', function() {
            const columnIndex = $(this).closest('th').index();
            const searchTerm = $(this).val();
            tabela.column(columnIndex).search(searchTerm).draw();
        });

        $(`.${searchInputClass}`).on('click', function(e) {
            e.stopPropagation();
        });

        $(`.${searchInputClass}`).on('keydown', function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    }

    // Chamadas da função para criar as tabelas específicas
    function criarTabelaLotes(listaLotes) {
        criarTabelaGenerica(listaLotes, 'table-lotes', 'itemCountContainer-lotes', 'pageInput-lotes', 'search-input-lotes', [{
                data: null,
                render: function(row) {
                    return `<button style="border: 1px solid rgb(235, 190, 6); border-radius: 7px; padding-left: 7px; padding-right: 7px; background-color: red; font-size: 20px">
                            <i class="ph-bold ph-trash" title="Excluir" id="btn-Excluir"></i>
                        </button>`;
                }
            },
            {
                data: 'lote'
            },
            {
                data: 'nomelote'
            },
        ], 'pagination-container-lotes');
    }

    let LotesSelecionados = [];

    function criarTabelaLotesCsw(listaLotesCsw) {
        criarTabelaGenerica(listaLotesCsw, 'table-lotes-csw', 'itemCountContainer-lotes-csw', 'pageInput-lotes-csw', 'search-input-lotes-csw', [{
                data: null,
                render: function(row) {
                    return `<div class="acoes d-flex justify-content-center align-items-center" style="height: 100%;">
                            <input type="checkbox" class="row-checkbox" value="${row.codLote}">
                        </div>`;
                }
            },
            {
                data: 'codLote'
            },
            {
                data: 'nomeLote'
            },
        ], 'pagination-container-lotes-csw');

        const table = $('#table-lotes-csw').DataTable();

        async function VerificaLotesSelecionados() {
            LotesSelecionados.length = 0; // Limpa o array antes de verificar

            // Itera sobre cada linha da tabela
            table.rows().every(function() {
                const checkbox = $(this.node()).find('.row-checkbox'); // Seletor correto
                if (checkbox.is(':checked')) {
                    const row = this.data();
                    const CodigoLote = row['codLote']; // Certifique-se de que a chave está correta

                    // Adiciona o código do lote ao array se não estiver presente
                    if (!LotesSelecionados.includes(CodigoLote)) {
                        LotesSelecionados.push(CodigoLote);
                    }
                }
            });

            // Verifica se nenhum lote foi selecionado
            if (LotesSelecionados.length === 0) {
                Mensagem_Salva('Nenhum lote selecionado!', 'warning')
            }
        };

        $('#btn-selecionar-lotes').click(async () => {
            await VerificaLotesSelecionados(); // Aguarda a verificação
            if (LotesSelecionados.length === 0) {} else {
                try {
                    VincularLotesPlano()
                } catch (error) {
                    console.error('Erro na solicitação AJAX:', error); // Exibe erro se ocorrer
                    $('#loadingModal').modal('hide');
                    Mensagem('Erro', 'error');
                }
            }
        });
    }

    async function VincularLotesPlano() {
        $('#loadingModal').modal('show');
        try {

            const dados = {
                "codigoPlano": $('#codigo-plano').val(),
                "arrayCodLoteCsw": LotesSelecionados,
            }

            var requestData = {
                acao: "Vincular_Lote",
                dados: dados
            };

            const response = await $.ajax({
                type: 'PUT',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response);
            if (response[0]['Status'] == true) {
                Consultar_Lotes_Vinculados();
                Mensagem_Salva('Lotes Adicionados', 'success');
                LotesSelecionados = [];
                $('#modal-lotes').modal('hide');
            } else {
                Mensagem_Salva('Erro', 'error')
            }
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    let NotasSelecionadas = []

    function criarTabelaNotasCsw(listaNotasCsw) {
        criarTabelaGenerica(listaNotasCsw, 'table-notas-csw', 'itemCountContainer-notas-csw', 'pageInput-notas-csw', 'search-input-notas-csw', [{
                data: null,
                render: function(row) {
                    return `
                <div class="acoes d-flex justify-content-center align-items-center" style="height: 100%;">
                    <input type="checkbox" class="row-checkbox" value="${row.codigo}">
                </div>
            `;
                }
            },
            {
                data: 'codigo'
            },
            {
                data: 'descricao'
            },
        ], 'pagination-container-notas-csw');

        const table = $('#table-notas-csw').DataTable();

        async function VerificaNotasSelecinadas() {
            NotasSelecionadas.length = 0; // Limpa o array antes de verificar

            // Itera sobre cada linha da tabela
            table.rows().every(function() {
                const checkbox = $(this.node()).find('.row-checkbox'); // Seletor correto
                if (checkbox.is(':checked')) {
                    const row = this.data();
                    const CodigoNota = row['codigo']; // Certifique-se de que a chave está correta

                    // Adiciona o código do lote ao array se não estiver presente
                    if (!NotasSelecionadas.includes(CodigoNota)) {
                        NotasSelecionadas.push(CodigoNota);
                    }
                }
            });

            // Verifica se nenhum lote foi selecionado
            if (NotasSelecionadas.length === 0) {
                Mensagem_Salva('Nenhum tipo de nota selecionado!', 'warning')
            }
        };

        $('#btn-selecionar-notas').click(async () => {
            await VerificaNotasSelecinadas(); // Aguarda a verificação
            if (NotasSelecionadas.length === 0) {} else {
                try {
                    VincularNotasPlano()
                    console.log(NotasSelecionadas);


                } catch (error) {
                    console.error('Erro na solicitação AJAX:', error); // Exibe erro se ocorrer
                    $('#loadingModal').modal('hide');
                    Mensagem('Erro', 'error');
                }
            }
        });
    }


    async function VincularNotasPlano() {
        $('#loadingModal').modal('show');
        try {

            const dados = {
                "codigoPlano": $('#codigo-plano').val(),
                "arrayTipoNotas": NotasSelecionadas,
            }

            var requestData = {
                acao: "Vincular_Notas",
                dados: dados
            };

            const response = await $.ajax({
                type: 'PUT',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response);
            if (response[0]['Status'] == true) {
                Mensagem_Salva('Tipos de Notas Adicionados', 'success');
                NotasSelecionadas = [];
                Consultar_Notas_Vinculados()
                $('#modal-notas').modal('hide');
            } else {
                Mensagem_Salva('Erro', 'error')
            }
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    function criarTabelaNotas(listaNotas) {
        criarTabelaGenerica(listaNotas, 'table-notas', 'itemCountContainer-notas', 'pageInput-notas', 'search-input-notas', [{
                data: null,
                render: function(row) {
                    return `
                <div class="acoes">
                    <button style="border: 1px solid rgb(235, 190, 6); border-radius: 7px; padding-left: 7px; padding-right: 7px; background-color: red; font-size: 20px">
                        <i class="ph-bold ph-trash" title="Excluir" id="btn-Excluir"
                            onclick="
                                
                            "
                        ></i>
                    </button> 
                </div>
            `;
                }
            },
            {
                data: 'tipo nota'
            },
            {
                data: 'Descricao'
            },
        ], 'pagination-container-notas');
    }

    function criarTabelaPlanos(listaPlanos) {
        criarTabelaGenerica(listaPlanos, 'table-planos', 'itemCountContainer-planos', 'pageInput-planos', 'search-input-planos', [{
                data: null,
                render: function(row) {
                    return `
                        <div class="acoes">
                            <button style="border: 1px solid rgb(235, 190, 6); border-radius: 7px; padding-left: 7px; padding-right: 7px; background-color: rgb(235, 190, 6); font-size: 20px">
                                <i class="ph-bold ph-pencil-simple" title="Editar" id="btnEditar"
                                    onclick="
                                        $('#codigo-plano').val('${row['01- Codigo Plano']}');
                                        $('#descricao-plano').val('${row['02- Descricao do Plano']}');
                                        $('#inicio-venda').val('${row['03- Inicio Venda']}');
                                        $('#final-venda').val('${row['04- Final Venda']}');
                                        $('#inicio-faturamento').val('${row['05- Inicio Faturamento']}');
                                        $('#final-faturamento').val('${row['06- Final Faturamento']}');
                                        $('#itemCountContainer-planos').addClass('d-none');
                                        $('.div-table-planos').addClass('d-none');
                                        $('.pagination-container-planos').addClass('d-none');
                                        $('.btn-novo-plano').addClass('d-none');
                                        $('.div-cadastro-plano').removeClass('d-none');
                                        $('.btn-editar').removeClass('d-none');
                                        $('.btn-voltar').removeClass('d-none');
                                        $('#btn-colecoes').removeClass('disabled');
                                        $('#btn-notas').removeClass('disabled');
                                        $('#btn-lotes').removeClass('disabled');
                                        async function atualizarTabelas (){
                                        await Consultar_Notas_Vinculados();
                                        Consultar_Lotes_Vinculados()
                                        }

                                        atualizarTabelas()
                                        
                                    "
                                ></i>
                            </button> 
                        </div>
                    `;
                }
            },
            {
                data: '01- Codigo Plano'
            },
            {
                data: '02- Descricao do Plano'
            },
            {
                data: '03- Inicio Venda'
            },
            {
                data: '04- Final Venda'
            },
            {
                data: '05- Inicio Faturamento'
            },
            {
                data: '06- Final Faturamento'
            },
        ], 'pagination-container-planos');
    }
</script>
