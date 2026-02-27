<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">

<style>
    /* Ajuste de espessura visual */
    .pagina-container {
        padding: 5px 10px; 
        margin-top: 5px;   
    }

    .titulo-tela {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 1.05rem; 
        font-weight: bold;
        margin-bottom: 0px; 
        color: #10045a;
    }

    .span-icone {
        background-color: #10045a;
        color: white;
        padding: 2px 5px; 
        border-radius: 6px;
    }

    /* Estilização dos Cards */
    .card-filtro {
        background-color: #fff;
        border-radius: 4px;
        padding: 10px; 
        box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
        margin-bottom: 10px; 
        margin-top: 10px;
    }
    
    /* Correção de Z-index */
    .select2-container {
        z-index: 1050 !important;
    }
    .modal {
        z-index: 2150 !important;
    }
    .modal-backdrop {
        z-index: 2100 !important;
    }

    /* Estilo das Tabelas */
    .div-tabela {
        max-width: 100%;
        overflow: auto;
        max-height: 75vh;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }

    /* Cabeçalho Fixo para Tabelas Principais e de Modais */
    #table-metas thead th,
    .modal table thead th {
        position: sticky;
        top: 0;
        background-color: #003366 !important;
        color: white !important;
        z-index: 10;
        vertical-align: middle;
        text-align: center;
    }

    /* Hover nas linhas */
    #table-metas tbody tr:hover td,
    .modal table tbody tr:hover td {
        background-color: rgb(199, 225, 252) !important;
        cursor: pointer;
    }

    .search-input {
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 2px 5px;
        font-weight: normal;
        color: black;
        width: 100%;
    }
</style>

<div class="container-fluid pagina-container">
    
    <div class="titulo-tela">
        <span class="span-icone"><i class="bi bi-bullseye"></i></span> 
        PLANO DE METAS
    </div>

    <div class="card-filtro">
        <div class="row g-3" id="selecao-plano">
            <div class="col-12 col-md-6">
                <label for="select-plano" class="form-label fw-bold">Selecionar Plano</label>
                <select id="select-plano" class="form-select select2-js" required></select>
            </div>
            <div class="col-12 col-md-6 d-none" id="div-selecionar-lote">
                <label for="select-lote" class="form-label fw-bold">Selecionar Lote</label>
                <select id="select-lote" class="form-select select2-js" required></select>
            </div>
        </div>
    </div>

    <div class="div-metas d-none">
        <div class="div-tabela shadow-sm bg-white">
            <table class="table table-bordered table-striped m-0" id="table-metas" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Seq.</th>
                        <th>Cód. Fase</th>
                        <th>Nome Fase<br><input type="search" class="search-input search-input-metas"></th>
                        <th>Previsão Peças</th>
                        <th>Falta Programar</th>
                        <th>Carga</th>
                        <th>Fila</th>
                        <th>Falta Produzir</th>
                        <th>Qtd. Dias</th>
                        <th>Meta Anterior</th>
                        <th>Meta Dia</th>
                        <th>Realizado</th>
                        <th>Efic. %</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-filtros" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark">Filtros de Pesquisa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-filtros" onsubmit="Consulta_Metas(true); return false;">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Data de Início:</label>
                        <input type="date" class="form-control" id="data-inicial" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Data de Fim:</label>
                        <input type="date" class="form-control" id="data-final" required>
                    </div>
                    <div class="fw-bold mb-2">Tipos de Op's</div>
                    <div id="TiposOps" class="p-2 border rounded bg-light"></div>
                    
                    <div class="modal-footer px-0 pb-0 mt-3 border-0">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-filter"></i> Filtrar Resultados
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-realizado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header text-dark">
                <h5 class="modal-title" id="titulo-realizado"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="max-height: 80vh; overflow: auto">
                <table class="table table-bordered table-striped m-0" id="table-realizado" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Dia</th>
                            <th>Realizado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="table-light sticky-bottom fw-bold text-center">
                        <tr>
                            <td colspan="2">Total</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-realizadoDia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header text-dark">
                <h5 class="modal-title" id="titulo-realizadoDia">Detalhes do Realizado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="max-height: 80vh; overflow: auto">
                <table class="table table-bordered table-striped m-0" id="table-realizadoDia" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Coleção</th>
                            <th>Nº OP</th>
                            <th>Engenharia</th>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Realizado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="table-light sticky-bottom fw-bold text-center">
                        <tr>
                            <td colspan="5">Total</td>
                            <td id="total-realizado-dia">0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cargaOP_fase" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title" id="titulo-cargaOP_fase">Detalhamento de Carga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="max-height: 80vh; overflow: auto">
                <table class="table table-bordered table-striped m-0" id="table-cargaOP_fase" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Coleção<br><input type="search" class="search-input"></th>
                            <th>Nº OP</th>
                            <th>Categoria<br><input type="search" class="search-input"></th>
                            <th>Cód. Prod</th>
                            <th>Descrição</th>
                            <th>Prio</th>
                            <th>Carga</th>
                            <th>Entrada</th>
                            <th>Dias</th>
                            <th>Ger. OP</th>
                            <th>L.T. Acum</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="table-light sticky-bottom fw-bold text-center">
                        <tr>
                            <td colspan="6">Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-previsao-categorias" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title">Previsão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="max-height: 80vh; overflow: auto">
                <table class="table table-bordered table-striped m-0" id="table-previsao-categorias" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Previsão</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-resumo-fila" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title" id="titulo-fila">Resumo da Fila</h5>
                <div class="ms-3 d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btn-fase">Por Fase</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-categoria">Por Categoria</button>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="max-height: 80vh; overflow: auto">
                <table class="table table-bordered table-striped m-0" id="table-resumo-fila" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Fase Atual</th>
                            <th>Fila</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="table-light sticky-bottom fw-bold text-center">
                        <tr><td>Total</td><td></td></tr>
                    </tfoot>
                </table>

                <table class="table table-bordered table-striped m-0" id="table-resumo-filacategoria" style="width: 100%; display: none;">
                    <thead>
                        <tr>
                            <th>Fase Atual</th>
                            <th>Categoria<br><input type="search" class="search-input"></th>
                            <th>Fila</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="table-light sticky-bottom fw-bold text-center">
                        <tr><td colspan="2">Total</td><td></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-falta-produzir-categorias" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-dark">
                <h5 class="modal-title" id="titulo-falta-produzir">Falta Produzir</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="max-height: 80vh; overflow: auto">
                <table class="table table-bordered table-striped m-0" id="table-falta-produzir-categorias" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Carga</th>
                            <th>Fila</th>
                            <th>Falta Programar</th>
                            <th>Falta Produzir</th>
                            <th>Dias</th>
                            <th>Meta Diária</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot class="table-light sticky-bottom fw-bold text-center">
                        <tr>
                            <td>Total</td><td></td><td></td><td></td><td></td><td></td><td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cronograma" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark">Cronograma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-cronograma">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Data de Início:</label>
                        <input type="date" class="form-control" id="data-inicial-cronograma" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Data de Fim:</label>
                        <input type="date" class="form-control" id="data-final-cronograma" disabled>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../../../templates/footerGestao.php');
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="script2.js"></script>

<script>
$(document).ready(function() {
    // 1. INSTRUÇÃO CRÍTICA PARA MODAIS NÃO FICAREM OCULTOS
    // Isso move todos os modais para a raiz do documento, evitando sobreposição de containers
    $('.modal').appendTo("body");

    // 2. Inicializa Select2
    $('.select2-js').select2({
        width: '100%',
        placeholder: "Selecione uma opção"
    });

    // 3. Select2 dentro de Modais
    $('.modal').on('shown.bs.modal', function() {
        $(this).find('.select2-js').select2({
            dropdownParent: $(this)
        });
    });
});
</script>