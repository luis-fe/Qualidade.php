<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerGarantia.php');
?>
<link rel="stylesheet" href="style.css">
<style>
    label {
        color: black !important;
    }

    .grafico-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-between;

    }

    .grafico {
        flex: 1 1 45%;
        min-width: 280px;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-speedometer"></i></span> Dashboards
</div>

<!-- Formulário de Filtro -->
<div class="col-12 mt-2">
    <div class="d-flex flex-wrap gap-3 align-items-end p-2">
        <!-- Campo de Data Início -->
        <div class="position-relative">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="date" id="dataInicio" class="form-control">
            </div>
        </div>

        <!-- Campo de Data Fim -->
        <div class="position-relative">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <input type="date" id="dataFim" class="form-control">
            </div>
        </div>

        <!-- Botão Atualizar -->
        <button class="btn btn-geral" style="margin-bottom: 0;" onclick="async function atualizar (){await Cosultar_Qualidade(); await Consultar_Motivos(); await Cosultar_Origem()}; atualizar()">
            <i class="fas fa-sync-alt"></i> Atualizar
        </button>

        <!-- Card Total de Peças -->
        <div class="card text-center" style="min-width: 150px;">
            <div class="card-body p-2">
                <h6 class="card-title mb-1">Total de Peças</h6>
                <h5 class="card-text fw-bold text-primary" id="totalPecas"></h5>
            </div>
        </div>

        <!-- Card Total 2ª Qualidade -->
        <div class="card text-center" style="min-width: 150px;">
            <div class="card-body p-2">
                <h6 class="card-title mb-1">Total 2ª Qualidade</h6>
                <h5 class="card-text fw-bold text-danger" id="totalPecas2Qualidade"></h5>
            </div>
        </div>
    </div>
</div>


<div class="col-12 mt-1 p-3 grafico-container">
    <div class="grafico" style="text-align: center;">
        <h2 style="font-size: 18px; font-weight: bold; padding: auto; margin: auto">% 2ª Qualidade</h2>
        <div id="graficoDonut"></div>
    </div>


    <div class="grafico" style="text-align: center;">
        <h2 style="font-size: 18px; font-weight: bold; padding: auto; margin: auto">Defeitos por motivos</h2>
        <div id="graficoBarras"></div>
    </div>

</div>
<div class="col-12 mt-1 p-3 grafico-container">
    <div class="grafico" style="text-align: center;">
        <h2 style="font-size: 18px; font-weight: bold; padding: auto; margin: auto">Defeitos por terceirizados</h2>
        <div id="graficoTerceirizados" style="width: 100%;"></div>
    </div>

</div>



<?php
include_once('../../templates/footer.php');
?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="script.js"></script>
