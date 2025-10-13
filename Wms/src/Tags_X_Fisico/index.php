<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
?>
<link rel="stylesheet" href="style.css">


<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <div class="row col-12">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" id="Infos">
                <label for="itensPorPagina">Mostrar</label>
                <select class="form-select" id="itensPorPagina">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <label for="text">elementos</label>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-6" id="Infos" style="justify-content: right;">
                <div id="search-container" class="col-12 col-sm-12 ">
                    <input type="text" id="searchFila" class="form-control" placeholder="Pesquisar...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="TableTags">
                    <thead>
                        <tr>
                            <th scope="col">Código Reduzido</th>
                            <th scope="col">Tags em Conferência</th>
                            <th scope="col">Tag's no Wms</th>
                            <th scope="col">Tag's no Posição de Estoque</th>
                            <th scope="col">Diferença's</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="container3" style="margin-top: 1rem; min-width: 100%">
                <div class="col-6 col-md-7 align-items-center text-align-center justify-content-center" id="Paginacao">
                </div>
            </div>
        </div>
    </div>
</div>



<?php include_once("../../../templates/footer.php"); ?>
<script src="script.js"></script>
