<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
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
</style>

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-bullseye"></i></span> Metas
</div>

<div class="mt-3 mb-4 row justify-content-left" id="selecao-plano">
    <div class="col-12 col-md-6">
        <div class="select text-start">
            <label for="select-plano" class="form-label">Selecionar plano</label>
            <select id="select-plano" class="form-select" required>
            </select>
        </div>
    </div>
    <div class="col-12 col-md-6 d-none" id="div-selecionar-lote">
        <div class="select text-start">
            <label for="select-lote" class="form-label">Selecionar Lote</label>
            <select id="select-lote" class="form-select" required>
            </select>
        </div>
    </div>
</div>

<div class="col-12 div-metas d-none" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-metas" style="width: 100%;">
            <thead>
                <tr>
                    <th>Sequencia<br></th>
                    <th>Cód. Fase<br></th>
                    <th>Nome Fase<br><input type="search" class="search-input search-input-metas" style="min-width: 150px;"></th>
                    <th>Previsão de Peças<br></th>
                    <th>Falta Programar<br></th>
                    <th>Carga<br></th>
                    <th>Fila <br></th>
                    <th>Falta Produzir<br></th>
                    <th>Qtd. Dias<br></th>
                    <th>Meta Dia<br></th>
                    <th>Realizado<br></th>
                    <th>Efic. %<br></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-metas d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-metas" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-metas" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-filtros" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Filtro</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-filtros" onsubmit="Consulta_Metas(true); return false;">
                    <div class="mb-3">
                        <label for="data-inicial" class="form-label text-dark fw-bold">Data de Início:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" class="form-control border-secondary rounded-end" id="data-inicial" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="data-final" class="form-label text-dark fw-bold">Data de Fim:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" class="form-control border-secondary rounded-end" id="data-final" required>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-salvar">
                            <span><i class="bi bi-floppy"></i></span>
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-custom" id="modal-realizado" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titulo-realizado" style="color: black;"></h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow: auto">
                <table class="table table-bordered table-striped" id="table-realizado" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Data<br></th>
                            <th>Dia<br></th>
                            <th>Realizado<br></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aqui vão os dados da tabela -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-cronograma" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Filtro</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-cronograma">
                    <div class="mb-3">
                        <label for="data-inicial" class="form-label text-dark fw-bold">Data de Início:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" class="form-control border-secondary rounded-end" id="data-inicial-cronograma" disabled>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="data-final" class="form-label text-dark fw-bold">Data de Fim:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" class="form-control border-secondary rounded-end" id="data-final-cronograma" disabled>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script5.js"></script>
