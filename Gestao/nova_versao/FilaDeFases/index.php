<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>
<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
    #detalha-info {
        position: absolute;
        display: none;
        z-index: 9999;
        background: white;
        border: 1px solid #ccc;
        padding: 10px;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        min-width: 300px;
    }
</style>

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-stack"></i></span> Fila de Fases
</div>

<div class="mt-3 mb-4 d-flex align-items-center gap-3">
    <div class="custom-dropdown d-flex flex-column">
        <button class="select-colecoes col-12" id="dropdownToggle" style="min-width: 250px; max-width: 250px;">
            Coleções
        </button>
        <div class="menu-colecoes" id="menu-colecoes" style="min-width: 250px; max-width: 250px;">
        </div>
    </div>

    <div>
        <button class="btn btn-geral mt-3" onclick="Consulta_Fila()">Consultar</button>
    </div>
</div>

<div class="col-12" id="Graficos">
    <!-- Gráficos serão inseridos aqui -->
</div>

<div id="detalha-info" class="detalha-fila" style="max-height: 400px; overflow: auto;">
    <div class="col-12" id="Graficos2">
        <!-- Gráficos serão inseridos aqui -->
    </div>
</div>

<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script.js"></script>
