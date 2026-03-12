<?php
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');

// Define a data atual no formato Y-m-d (ex: 2026-03-10)
$dataHoje = date('Y-m-d');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">

<style>
    @media (min-width: 768px) {
        .divisor-vertical {
            border-right: 2px solid #dee2e6; /* Linha cinza clara */
            padding-right: 20px;
        }
        .col-conferencia {
            padding-left: 20px;
        }
    }
</style>

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

    <div class="row">
        
        <div class="col-md-6 divisor-vertical">
            <h5 class="mt-2 mb-2" style="color: #003366;"><strong>RANKING REPOSIÇÃO</strong></h5>
            <div class="div-tabela mb-4" style="max-width: 100%; overflow: auto; max-height: 60vh; border-radius: 8px; border: 1px solid #dee2e6;">
                <table class="table table-bordered table-striped mb-0" id="table-reposicao" style="width: 100%;">
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
        </div>

        <div class="col-md-6 col-conferencia">
            <h5 class="mt-2 mb-2" style="color: #003366;"><strong>RANKING CONFERÊNCIA</strong></h5>
            <div class="div-tabela mb-4" style="max-width: 100%; overflow: auto; max-height: 60vh; border-radius: 8px; border: 1px solid #dee2e6;">
                <table class="table table-bordered table-striped mb-0" id="table-conferencia" style="width: 100%;">
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

    </div> </div>

<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script.js"></script>