<?php
include_once("../../templates/header1.php");
include_once("../../templates/loading1.php");
?>
<link rel="stylesheet" href="style.css">
<style>

</style>

<label for="" style="font-size: 30px; font-weight: 700; color: black;">Fila de Fases</label>
<div class="container"style="background-color: rgb(179, 179, 179); border-radius: 5px; height: calc(100vh - 160px); max-height: calc(100vh - 160px); overflow: auto; width: 100%; min-width: 100%; padding-top: 25px; padding-left: 30px;">
    <div class="row col-12">
        <div class="col-12 col-md-4 text-center">
            <div class="dropdown mt-2 col-9 mx-auto">
                <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="filtroDropdownColecao" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Coleção
                </button>
                <div class="dropdown-menu" aria-labelledby="filtroDropdownColecao" style="width: 300px">
                    <div class="p-2">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="search-icon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="searchInputColecao" class="form-control" placeholder="Pesquisar..." aria-label="Pesquisar" aria-describedby="search-icon">
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="selectAllColecao">
                            <label class="form-check-label" for="selectAllColecao">
                                Selecionar Todos
                            </label>
                        </div>
                        <div id="checkboxContainerColecao"></div>
                    </div>
                </div>
            </div>
            <div class="dropdown mt-3 col-9 mx-auto">
                <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="filtroDropdownCategoria" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Categoria
                </button>
                <div class="dropdown-menu" aria-labelledby="filtroDropdownCategoria">
                    <input type="text" id="searchInputCategoria" class="form-control mb-2" placeholder="Pesquisar...">
                    <label><input type="checkbox" id="selectAllCategoria"> Selecionar Todos</label><br>
                    <div id="checkboxContainerCategoria"></div>
                </div>
            </div>
            <div class="dropdown mt-3 col-9 mx-auto">
                <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="filtroDropdownTipoOp" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    TipoOp
                </button>
                <div class="dropdown-menu" aria-labelledby="filtroDropdownTipoOp">
                    <input type="text" id="searchInputTipoOp" class="form-control mb-2" placeholder="Pesquisar...">
                    <label><input type="checkbox" id="selectAllTipoOp"> Selecionar Todos</label><br>
                    <div id="checkboxContainerTipoOp"></div>
                </div>
            </div>
            <button class="btn btn-secondary mt-3" type="button" onclick="Filtrar()">
                Filtrar
            </button>
        </div>
        <div class="col-12 col-md-8" id="Graficos">
            <!-- Gráficos serão inseridos aqui -->
        </div>
    </div>
</div>


<div id="detalha-info" class="detalha-fila">
    <div class="col-12" id="Graficos2">
        <!-- Gráficos serão inseridos aqui -->
    </div>
</div>

<?php include_once("../../templates/footer1.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="script.js"></script>
