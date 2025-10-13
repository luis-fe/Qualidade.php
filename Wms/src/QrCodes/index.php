<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
?>
<link rel="stylesheet" href="style.css">
<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <div class="card col-12 col-md-6 justify-content-center align-items-center mx-auto">
            <h2 class="mb-4 text-center">Insira a Quantidade de Caixas</h2>
            <input type="number" id="InputNumeroCaixas" class="form-control mb-4" placeholder="Ex: 100">
            <button class="btn btn-primary" id="ButtonImprimir">Imprimir</button>
        </div>
    </div>
</div>

<?php include_once("../../../templates/footer.php"); ?>
<script src="script.js"></script>
