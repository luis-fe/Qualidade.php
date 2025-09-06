<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<style>


</style>


<div class="d-flex justify-content-between align-items-center titulo-tela" id="titulo">
    <div>
        <span class="span-icone">
            <i class="bi bi-clipboard-data-fill"></i>
        </span> 
        Tendência de Vendas
    </div>
</div>



<div class="mt-3 row justify-content-center" id="selecao-plano" style="max-width: 100%; overflow:auto">
    <form id="form-vendas" class="row" onsubmit="Consulta_Tendencias(); return false;">
        <div class="col-12 col-md-6">
            <div class="select text-start">
                <label for="select-plano" class="form-label">Selecionar plano</label>
                <select id="select-plano" class="form-select" required>
                </select>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="select text-start">
                <label for="select-pedidos-bloqueados" class="form-label">Considera pedidos bloqueados?</label>
                <select id="select-pedidos-bloqueados" class="form-select" required>
                    <option></option>
                    <option value="sim">Sim</option>
                    <option value="nao">Não</option>
                </select>
            </div>
        </div>

        <div class="col-12 col-md-2 d-flex align-items-end justify-content-center">
            <button type="submit" class="btn btn-geral w-100" style="margin-top: 32px;">Consultar</button>
        </div>
    </form>
</div>




<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script6.js"></script>
