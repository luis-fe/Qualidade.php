<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include_once("../../../templates/Loading.php")
?>
<link rel="stylesheet" href="style.css">


<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <div class="row mt-3">
            <div class="col-12 col-md-6 d-flex align-items-center mb-3">
                <label for="itensPorPagina" class="me-2">Mostrar</label>
                <select class="form-select" id="itensPorPagina" style="width: auto;">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span class="ms-2">elementos</span>
            </div>
            <div class="col-12 col-md-6">
                <div id="search-container">
                    <input type="text" id="searchTags" class="form-control" placeholder="Pesquisar...">
                </div>
            </div>
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered" id="TableTagsConf">
                <thead>
                    <tr>
                        <th scope="col">Código do Pedido</th>
                        <th scope="col">Engenharia</th>
                        <th scope="col">Código Reduzido</th>
                        <th scope="col">Tag</th>
                        <th scope="col">Epc</th>
                        <th scope="col">Data da Separação</th>
                        <th scope="col">Separador</th>
                        <th scope="col">Endereço de Origem</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Tabela preenchida via JavaScript -->
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3" id="Paginacao">
            <!-- Paginação será preenchida pelo DataTables -->
        </div>
        <div class="row text-center" style="margin-top: 25px; width: 100%; align-items: center; justify-content: center">
            <div class="col-12 col-md-2">
                <label for="text" class="form-label">Total Peças:</label>
                <label for="text" class="form-control btn-primary" id="TotalPcs"></label>
            </div>
            <div class="col-12 col-md-2">
                <label for="text" class="form-label">Total Pedidos:</label>
                <label for="text" class="form-control btn-primary" id="TotalPedidos"></label>
            </div>
        </div>
    </div>
</div>


<?php include_once("../../../templates/footer.php"); ?>
<script src="script.js"></script>
