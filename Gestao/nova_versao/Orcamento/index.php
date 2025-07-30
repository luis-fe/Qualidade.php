<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>
<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style5.css">
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<style>
    .card-setor,
    .card-header {
        min-width: 100%;
        background-color: #10045a !important;
        color: white;
        font-size: 23px;
        font-weight: 700;
        text-align: center;
    }

    .card-header {
        font-size: 19px !important;
    }
</style>

<div class="titulo-tela d-flex justify-content-between align-items-center">
    <div>
        <span class="span-icone"><i class="bi bi-stack"></i></span>
        Orçado x Realizado
    </div>
    <button class="btn btn-outline-secondary btn-sm d-none" onclick="$('#btn-voltar').addClass('d-none'); $('#container-info').removeClass('d-none'); $('#div-detalhamento').addClass('d-none'); $('.accordion').removeClass('d-none');" id="btn-voltar">
        <i class="fa-solid fa-arrow-left"></i> Voltar
    </button>
</div>


<div class="container-fluid mt-2" id="form-container">
    <div class="accordion col-12 p-3" style="color: black;">
        <h3>Filtros</h3>
        <div class="col-12 row position-relative justify-content-center">
            <div class="col-md-3 mb-3">
                <label for="data-inicial" class="form-label">Data Inicial</label>
                <input type="date" class="form-control text-center" id="data-inicial" name="data-inicial">
            </div>
            <div class="col-md-3 mb-3">
                <label for="data-final" class="form-label">Data Final</label>
                <input type="date" class="form-control text-center" id="data-final" name="data-final">
            </div>
            <div class="col-md-3 mb-3">
                <label for="select-empresas" class="form-label">Empresa</label>
                <select id="select-empresas" class="form-select">
                    <option value="">Selecione</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="select-area" class="form-label">Área</label>
                <select id="select-area" class="form-select">
                    <option value="">Selecione</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="select-grupo-gastos" class="form-label">Grupo de Gastos</label>
                <select id="select-grupo-gastos" class="form-select">
                    <option value="">Selecione</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="select-centro-custos" class="form-label">Centro de Custos</label>
                <select id="select-centro-custos" class="form-select">
                    <option value="">Selecione</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-geral w-100" onclick="Cosulta_Resumos()">Consultar</button>
            </div>
        </div>
    </div>
    <div class="container my-4" id="container-info">
        <div class="row justify-content-center align-items-center text-center g-4 mb-5">
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm rounded-4">
                    <div class="card-header bg-primary text-white fw-bold">ORÇADO</div>
                    <div class="card-body">
                        <div id="orcado-geral" class="fs-4 fw-semibold text-dark text-nowrap"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-1 col-sm-12 d-flex justify-content-center align-items-center">
                <div class="fs-3 fw-bold">X</div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm rounded-4">
                    <div class="card-header bg-primary text-white fw-bold">REALIZADO</div>
                    <div class="card-body">
                        <div id="realizado-geral" class="fs-4 fw-semibold text-dark text-nowrap"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="card shadow-sm rounded-4">
                    <div class="card-header bg-primary text-white fw-bold">DIFERENÇA</div>
                    <div class="card-body">
                        <div id="card-diferenca" class="fs-4 fw-semibold text-dark text-nowrap"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="card shadow-sm rounded-4">
                    <div class="card-header bg-primary text-white fw-bold">%</div>
                    <div class="card-body">
                        <div id="card-percentual" class="fs-4 fw-semibold"></div>
                    </div>
                </div>
            </div>

        </div>

        <div class="d-flex flex-wrap justify-content-start" id="container-cards">

        </div>
    </div>
    <div class="my-4 d-none" id="div-detalhamento">
        <div class="row justify-content-center align-items-center text-center g-4 mb-4">
            <div class="col-md-3 col-sm-12">
                <div class="card shadow-sm rounded-4">
                    <div class="card-setor text-white rounded-top-4 py-2 fw-bold fs-6" id="titulo-detalha-orcado">
                        ORÇADO
                    </div>
                    <div class="p-3 fs-4 fw-semibold text-dark" id="detalha-orcado"></div>
                </div>
            </div>
            <div class="col-md-1 col-12 d-flex justify-content-center align-items-center">
                <div class="fs-3 fw-bold">×</div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="card shadow-sm rounded-4">
                    <div class="card-setor text-white rounded-top-4 py-2 fw-bold fs-6" id="titulo-detalha-realizado">
                    </div>
                    <div class="p-3 fs-4 fw-semibold text-dark" id="detalha-realizado"></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12">
                <div class="card shadow-sm rounded-4">
                    <div class="card-setor text-white rounded-top-4 py-2 fw-bold fs-6">
                        DIFERENÇA
                    </div>
                    <div class="p-3 fs-4 fw-semibold" id="detalha-diferenca"></div>
                </div>
            </div>
        </div>
        <div class="d-flex" style="height: 500px; gap: 12px;">
            <div class="flex-fill col-md-6 mb-5" style="background: #f0f0f0; border-radius: 8px; overflow: hidden;">
                <div class="shadow-box" style="height: 100%; overflow: auto;">
                    <table class="table table-bordered table-striped mb-0" id="table-contas" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Item</th>
                                <th>Orçado</th>
                                <th>Realizado</th>
                                <th>% Realizado</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex-fill col-md-6 mb-5" style="background: #f0f0f0; border-radius: 8px; overflow: hidden;">
                <div class="shadow-box" style="height: 100%; overflow: auto;">
                    <table class="table table-bordered table-striped mb-0" id="table-contas-detalhadas" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Valor</th>
                                <th>Documento</th>
                                <th>Fornecedor</th>
                                <th>Codigo</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script.js"></script>
