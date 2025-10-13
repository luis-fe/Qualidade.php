<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include_once("../../../templates/Loading.php")
?>
<link rel="stylesheet" href="style.css">


<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <div class="row">
            <div class="col-12 col-md-2">
                <label for="Natureza" class="form-label">Natureza</label>
                <select class="form-select" id="SelectNatureza" required>
                    <option value="5">5</option>
                    <option value="7">7</option>
                    <option value="">Ambas</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label for="dataInicio" class="form-label">Início</label>
                <input type="date" id="dataInicio" class="form-control">
            </div>
            <div class="col-12 col-md-4">
                <label for="dataFim" class="form-label">Fim</label>
                <input type="date" id="dataFim" class="form-control">
            </div>
            <div class="col-12 col-md-2 text-center mt-3 mt-md-4">
                <button type="button" id="ButtonFiltrar" class="btn btn-primary">Filtrar</button>
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
            <div class="col-12 col-md-6">
                <div id="search-container">
                    <input type="text" id="searchEmbalagens" class="form-control" placeholder="Pesquisar...">
                </div>
            </div>
        </div>
        <div class="table-responsive mt-3">
            <table class="table table-bordered" id="TableEmbalagens">
                <thead>
                    <tr>
                        <th scope="col">Rua</th>
                        <th scope="col">Quantidade de Endereços</th>
                        <th scope="col">Status</th>
                        <th scope="col">% Realizado</th>
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
                <label for="text" class="form-label">Endereços Totais:</label>
                <label for="text" class="form-control btn-primary" id="EnderecosTotais"></label>
            </div>
            <div class="col-12 col-md-2">
                <label for="text" class="form-label">Endereços Inventariados:</label>
                <label for="text" class="form-control btn-primary" id="EnderecosInventariados"></label>
            </div>
            <div class="col-12 col-md-2">
                <label for="text" class="form-label">Peças Totais:</label>
                <label for="text" class="form-control btn-primary" id="PecasTotais"></label>
            </div>
            <div class="col-12 col-md-2">
                <label for="text" class="form-label">Peças Inventariados:</label>
                <label for="text" class="form-control btn-primary" id="PecasInventariadas"></label>
            </div>
        </div>
    </div>
</div>


<?php include_once("../../../templates/footer.php"); ?>
<script src="script.js"></script>
