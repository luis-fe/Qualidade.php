<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
?>

<link rel="stylesheet" href="style.css">
<style>
    .fixed-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: white;
        /* Altere a cor de fundo conforme necessário */
    }
</style>
<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <div class="row">
            <div class="col-12 col-md-1">
                <label for="Natureza" class="form-label">Natureza</label>
                <select class="form-select col-12" id="SelectNatureza" required>
                    <option value="5">5</option>
                    <option value="7">7</option>
                    <option value="">Ambas</option>
                </select>
            </div>
            <div class="col-12 col-md-2 justify-content-end align-items-end">
                <label for="Engenharia" class="form-label">Engenharia</label>
                <div id="search-container" class="input-group col-12 col-md-12">
                    <span class="input-group-text" id="search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="search" id="searchEngenharia" class="form-control" placeholder="Engenharia" aria-label="Pesquisar" aria-describedby="search-icon">
                </div>
            </div>
            <div class="col-12 col-md-2 justify-content-end align-items-end">
                <label for="Rua" class="form-label">Rua</label>
                <div id="search-container" class="input-group col-12 col-md-12">
                    <span class="input-group-text" id="search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="search" id="searchRua" class="form-control" placeholder="Rua" aria-label="Pesquisar" aria-describedby="search-icon">
                </div>
            </div>
            <div class="col-12 col-md-2 justify-content-end align-items-end">
                <label for="Modulo" class="form-label">Módulo</label>
                <div id="search-container" class="input-group col-12 col-md-12">
                    <span class="input-group-text" id="search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="search" id="searchModulo" class="form-control" placeholder="Módulo" aria-label="Pesquisar" aria-describedby="search-icon">
                </div>
            </div>
            <div class="col-12 col-md-2 justify-content-end align-items-end">
                <label for="Posicao" class="form-label">Posição</label>
                <div id="search-container" class="input-group col-12 col-md-12">
                    <span class="input-group-text" id="search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="search" id="searchPosicao" class="form-control" placeholder="Posição" aria-label="Pesquisar" aria-describedby="search-icon">
                </div>
            </div>
            <div class="col-12 col-md-2 justify-content-end align-items-end">
                <label for="CodReduzido" class="form-label">Cód. Reduzido</label>
                <div id="search-container" class="input-group col-12 col-md-12">
                    <span class="input-group-text" id="search-icon">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="search" id="searchCodReduzido" class="form-control" placeholder="CodReduzido" aria-label="Pesquisar" aria-describedby="search-icon">
                </div>
            </div>
            <div class="col-12 col-md-1 text-center mt-3 mt-md-1 d-flex justify-content-end align-items-end">
                <button type="button" id="ButtonAtualizar" onclick="ConsultaEstoques()" class="btn btn-primary col-12">Atualizar</button>
            </div>
        </div>
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
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered" id="TableEstoques">
                <thead class="fixed-header">
                    <tr>
                                <th scope="col">Rua</th>
                                <th scope="col">Módulo</th>
                                <th scope="col">Posição</th>
                                <th scope="col">Endereço</th>
                                <th scope="col">Engenharia</th>
                                <th scope="col">Tamanho</th>
                                <th scope="col">Cor</th>
                                <th scope="col">Reduzido</th>
                                <th scope="col">Descrição</th>
                                <th scope="col">Saldo</th>
                            </tr>
                </thead>
                <tbody>
                    <!-- Tabela preenchida via JavaScript -->
                </tbody>
            </table>
        </div>
        <div class="container3" style="margin-top: 1rem; min-width: 100%">
            <div class="col-6 col-md-7 align-items-center text-align-center justify-content-center" id="Paginacao">
            </div>
        </div>
    </div>
</div>


<?php include_once("../../../templates/footer.php"); ?>

<script src="script.js"></script>
