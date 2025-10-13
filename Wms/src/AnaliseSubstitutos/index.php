<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
?>
<link rel="stylesheet" href="style.css">
<style>
    .dropdown-menu {
        max-height: 200px;
        overflow-y: auto;
    }
    
</style>

<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="itensPorPagina">Mostrar</label>
                <select class="form-select d-inline w-auto" id="itensPorPagina">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <label for="text">elementos</label>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-5 mb-3">
                <div id="search-container">
                    <input type="text" id="searchFila" class="form-control" placeholder="Pesquisar...">
                </div>
            </div>
            <div class="col-12 col-md-3 mb-3">
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle w-100" type="button" id="filtroDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Categorias
                    </button>
                    <div class="dropdown-menu p-3 w-100" aria-labelledby="filtroDropdown">
                        <label><input type="checkbox" id="selectAll"> Selecionar Todos</label><br>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <button class="btn btn-primary w-100" id="Selecionar">Selecionar Op's</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="TableTags">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAllCheckbox"></th>
                        <th scope="col">Número OP</th>
                        <th scope="col">Código Produto</th>
                        <th scope="col">Cor Produto</th>
                        <th scope="col">Código Principal</th>
                        <th scope="col">Descrição Principal</th>
                        <th scope="col">Código Substituto</th>
                        <th scope="col">Descrição Substituto</th>
                        <th scope="col">Categorias</th>
                        <th scope="col">Aplicação</th>
                        <th scope="col">OP Especial?</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div class="row mt-3">
            <div class="col-12 d-flex justify-content-center" id="Paginacao">
                <!-- Paginação será inserida aqui -->
            </div>
        </div>
    </div>
</div>

<?php include_once("../../../templates/footer.php"); ?>

<script src="script.js"></script>