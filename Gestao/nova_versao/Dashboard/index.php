<?php
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');

// Define a data atual no formato Y-m-d (ex: 2026-03-10)
$dataHoje = date('Y-m-d');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">

<div class="container-fluid">
    <div class="titulo-tela d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="span-icone"><i class="bi bi-bullseye"></i></span> 
            <strong>PRODUTIVIDADE AVIAMENTOS</strong>
        </div>
    </div>

<div class="card mb-3 p-2 shadow-sm">
        <div class="row align-items-center justify-content-start">
            
            <div class="col-auto d-flex align-items-center gap-2">
                <label for="dataInicio" class="mb-0 fw-bold text-nowrap">Data Início:</label>
                <input type="date" id="dataInicio" class="form-control form-control-sm" value="<?php echo $dataHoje; ?>">
            </div>
            
            <div class="col-auto d-flex align-items-center gap-2 mt-2 mt-sm-0">
                <label for="dataFim" class="mb-0 fw-bold text-nowrap">Data Fim:</label>
                <input type="date" id="dataFim" class="form-control form-control-sm" value="<?php echo $dataHoje; ?>">
            </div>
            
            <div class="col-auto mt-2 mt-sm-0">
                <button type="button" id="btnFiltrar" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>

        </div>
    </div>

    <h5 class="mt-4 mb-2" style="color: #003366;"><strong>RANKING REPOSIÇÃO</strong></h5>
    <div class="div-tabela mb-4" style="max-width: 100%; overflow: auto; max-height: 40vh; border-radius: 8px;">
        <table class="table table-bordered table-striped" id="table-reposicao" style="width: 100%;">
            <thead style="position: sticky; top: 0; background-color: #003366; color: white; z-index: 10;">
                <tr>
                    <th style="width: 80px; text-align: center;">Posição</th>
                    <th>Repositor</th>
                    <th style="text-align: center;">qtd. Kit <br>Reposto</th>
                    <th style="text-align: center;">qtd <br>Endereços</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
    </div>

    <h5 class="mt-4 mb-2" style="color: #003366;"><strong>RANKING CONFERÊNCIA</strong></h5>
    <div class="div-tabela mb-4" style="max-width: 100%; overflow: auto; max-height: 40vh; border-radius: 8px;">
        <table class="table table-bordered table-striped" id="table-conferencia" style="width: 100%;">
            <thead style="position: sticky; top: 0; background-color: #003366; color: white; z-index: 10;">
                <tr>
                    <th style="width: 80px; text-align: center;">Posição</th>
                    <th>Conferente</th>
                    <th style="text-align: center;">Ops <br>Conferidas</th>
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
<script src="script.js"></script>