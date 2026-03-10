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

    <div class="card mb-3 p-3 shadow-sm">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label for="dataInicio" class="form-label">Data Início</label>
                <input type="date" id="dataInicio" class="form-control" value="<?php echo $dataHoje; ?>">
            </div>
            <div class="col-md-3">
                <label for="dataFim" class="form-label">Data Fim</label>
                <input type="date" id="dataFim" class="form-control" value="<?php echo $dataHoje; ?>">
            </div>
            <div class="col-md-2">
                <button type="button" id="btnFiltrar" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
        </div>
    </div>

    <div class="div-tabela" style="max-width: 100%; overflow: auto; max-height: 70vh; border-radius: 8px;">
        <table class="table table-bordered table-striped" id="table-metas" style="width: 100%;">
            <thead style="position: sticky; top: 0; background-color: #003366; color: white; z-index: 10;">
                <tr>
                    <th>Repositor</th>
                    <th>qtd. Kit <br>Reposto</th>
                    <th>qtd <br>Endereços</th>
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