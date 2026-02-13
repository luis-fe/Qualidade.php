<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
    .menus {
        display: flex;
        justify-content: start;
        padding: 0px 10px;
        margin-top: 15px;
    }

    .dataTables_wrapper {
        display: block;
    }

    .custom-pagination-container {
        justify-content: space-between;
        align-items: center;
        background-color: lightgray;
        padding: 5px;
        border-radius: 8px;
    }

    .button-filtros {
        border: 1px solid black !important;
        background-color: white !important;
        margin-bottom: 10px !important;
        margin-left: 0px !important;
        margin-right: -7px !important;
        width: 70px;
        padding: 2px 0px;
    }

    .button-filtros:hover {
        background-color: lightgray !important;
    }

    tfoot tr th {
        background-color: #003366 !important;
        color: white !important;
        font-weight: bold !important;
        text-align: center !important;
    }

    #CabecalhoModal th {
        background-color: #003366 !important;
        color: white !important;
        font-weight: bold !important;
        text-align: center !important;
    }

    #formato_descricao {
        max-width: 150px !important;
        width: 150px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    /* Efeitos de Hover na tabela principal */
    #table-falta-produzir-categorias tbody tr:hover > td {
        background-color: rgb(199, 225, 252) !important;
        cursor: pointer;
    }

    #table-metas tbody tr:hover > td {
        background-color: rgb(199, 225, 252) !important;
        cursor: pointer;
    }

    /* Estilos dos Modais existentes */
    #modal-cargaOP_fase .modal-dialog,
    #modal-realizadoDia .modal-dialog {
        width: 90vw !important;
        max-width: 90vw !important;
        margin: auto !important;
    }

    #modal-cargaOP_fase .modal-content,
    #modal-realizadoDia .modal-content {
        width: 100% !important;
    }

    #modal-realizadoDia thead th,
    #modal-realizadoDia tfoot th,
    #modal-cargaOP_fase thead th,
    #modal-cargaOP_fase tfoot th {
        position: sticky !important;
        z-index: 10 !important;
        padding: 0.5rem !important;
        height: 60px !important;
        vertical-align: middle !important;
    }

    #modal-realizadoDia thead th,
    #modal-cargaOP_fase thead th {
        top: 0 !important;
    }

    #modal-realizadoDia tfoot th,
    #modal-cargaOP_fase tfoot th {
        bottom: 0 !important;
    }

    #modal-cargaOP_fase thead th input {
        margin: 0 !important;
        padding: 0.25rem 0.5rem !important;
        box-sizing: border-box !important;
        width: 100% !important;
        border: 1px solid #ced4da !important;
    }

    #table-cargaOP_fase {
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }

    #modal-cargaOP_fase tbody tr:first-child td {
        margin-top: 0 !important;
        padding-top: 0.5rem !important;
    }

    #modal-cargaOP_fase thead th {
        border-bottom: 2px solid #dee2e6 !important;
    }

    /* --------------------------------------------------------- */
    /* NOVOS ESTILOS PARA O RECURSO DE REQUISIÇÕES (COLLAPSE)    */
    /* --------------------------------------------------------- */

    /* Garante alinhamento vertical quando o botão expande a linha */
    #table-metas td {
        vertical-align: middle !important;
    }

    /* Estilização da área interna (Card) que abre */
    #table-metas .collapse .card-body {
        background-color: #fff;
        border: 1px solid #ced4da;
        box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
        padding: 0; /* Remove padding para a tabela interna encostar nas bordas */
        margin-top: 5px;
    }

    /* Ajuste da tabela interna de requisições */
    #table-metas .collapse table th {
        background-color: #6c757d !important; /* Cinza escuro para diferenciar */
        color: white !important;
        font-size: 0.8rem;
        padding: 5px;
    }

    #table-metas .collapse table td {
        font-size: 0.8rem;
        padding: 5px;
        background-color: white !important; /* Garante fundo branco */
        color: #333;
        cursor: default; /* Remove o cursor de "mãozinha" na sub-tabela */
    }

    /* Sobrescreve o hover azul da tabela pai para não afetar a tabela filha */
    #table-metas tbody tr:hover .collapse table tr:hover td {
        background-color: #f1f1f1 !important; /* Um cinza bem leve só para diferenciar */
    }

    /* Cursor de mãozinha para indicar clique */
.sortable {
    cursor: pointer;
    user-select: none; /* Evita selecionar o texto ao clicar rápido */
    position: relative;
}

/* Efeito hover */
.sortable:hover {
    background-color: #d8dde2 !important; /* Um azul um pouco mais claro que o cabeçalho */
}

/* Setinhas indicativas (usando caracteres simples ou ícones do Bootstrap se preferir) */
.sort-asc::after {
    content: " ▲";
    font-size: 0.8em;
    float: right;
}

.sort-desc::after {
    content: " ▼";
    font-size: 0.8em;
    float: right;
}

/* Remove a borda superior da linha escondida para parecer conectada à de cima */
.hiddenRow {
    padding: 0 !important;
}

/* Garante que o conteúdo interno ocupe tudo e anime corretamente */
.accordion-body {
    padding: 15px;
    background-color: #f8f9fa; /* Fundo levemente cinza */
    box-shadow: inset 0 0 10px rgba(0,0,0,0.05);
}

/* Fundo cinza claro para a área expandida (atrás da tabela de 70%) */
.fundo-expandido {
    background-color: #f0f2f5 !important;
    padding: 10px 0; /* Espaçamento vertical */
}

/* Sombra suave para a tabela de 70% se destacar */
.tabela-centralizada-shadow {
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border-radius: 4px;
    overflow: hidden;
}

</style>

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-bullseye"></i></span> Almoxarifado de Aviamentos - <strong>FILA DE REQUISICOES</strong>
</div>
<div class="col-12 div-metas" style="background-color: lightgray; border-radius: 8px; padding: 10px;">

    <div class="row mb-2">
        <div class="col-md-3">
            <div class="card text-white" style="background-color: #003366; border-radius: 8px;">
                <div class="card-body p-2 text-center">
                    <h6 class="card-title m-0" style="font-size: 0.9rem; opacity: 0.8;">Total de Peças</h6>
                    <h3 class="card-text font-weight-bold m-0" id="lblTotalPecas">0</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="div-tabela" style="max-width: 100%; overflow: auto; max-height: 800px; border-radius: 8px;">

<div class="col-12 div-metas" style="background-color: lightgray; border-radius: 8px; padding: 10px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto; max-height: 100%; border-radius: 8px;">
        <table class="table table-bordered table-striped" id="table-metas" style="width: 100%;">
            <thead style="position: sticky; top: 0; background-color: #003366; z-index: 10;">
                <tr>
                    <th class="sortable" data-prop="numeroOP">
                        Numero OP<br>
                        <input type="search" class="search-input search-input-metas" style="min-width: 20px;">
                    </th>                  
                    <th class="sortable" data-prop="codProduto">
                        Codigo Produto<br>
                        <input type="search" class="search-input search-input-metas" style="min-width: 10px;">
                    </th>
                    <th class="sortable" data-prop="descricao">
                        Descricao<br>
                        <input type="search" class="search-input search-input-metas" style="min-width: 100px;">
                    </th>
                    <th class="sortable" data-prop="QtdPecas_x">
                        Qtde Peças
                    </th>
                    <th class="sortable" data-prop="prioridade">
                        Prioridade<br>
                        <input type="search" class="search-input search-input-metas" style="min-width: 100px;">

                    </th>
                    <th class="sortable" data-prop="dataBaixa_x">
                        Data Entrada<br>
                    </th>
                    <th>Requisicoes
                    </th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>
</div>

<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script2.js"></script>