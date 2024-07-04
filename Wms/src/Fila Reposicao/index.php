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
            <div class="col-12 col-md-2 text-center mt-3 mt-md-4">
                <button type="button" id="ButtonFiltrar" class="btn btn-primary">Filtrar</button>
            </div>
            <div class="col-12 col-md-2 text-center mt-3 mt-md-4">
                <button type="button" id="ButtonAtualizarTags" class="btn btn-primary" onclick="AtualizaFila(); $('#Atualizar').addClass('d-none'); $('#Atualizando').removeClass('d-none')"><span id="Atualizar"><i class="fa-solid fa-play mr-1"></i>Atualizar</span><span id="Atualizando" class="d-none"><i class=" fa-solid fa-spinner mr-1"></i>Atualizando</span></button>
                <label for="text" id="LabelAtualizacao"></label>
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
            <table class="table table-bordered" id="TableFila">
                <thead>
                    <tr>
                        <th scope="col">Caixas Abertas</th>
                        <th scope="col">Código Reduzido</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Numero Op</th>
                        <th scope="col">Pçs</th>
                        <th scope="col">Estoque Csw</th>
                        <th scope="col">Estoque Endereçado</th>
                        <th scope="col">Descrição Lote</th>
                        <th scope="col">Data Entrada Op</th>
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
                <label for="text" class="form-label">Peças em Fila:</label>
                <label for="text" class="form-control btn-primary" id="TotalPcsFila"></label>
            </div>
            <div class="col-12 col-md-2">
                <label for="text" class="form-label">Total Caixas em Fila:</label>
                <label for="text" class="form-control btn-primary" id="TotalCaixasFila"></label>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable">
                        <thead id="fixed-header">
                            <tr>
                                <th>Data Reposição</th>
                                <th>Cod Barras Tag</th>
                                <th>Cod Reduzido</th>
                                <th>EPC</th>
                                <th>Nome</th>

                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be appended here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dataModal2" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel2"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable2">
                        <thead id="fixed-header">
                            <tr>
                                <th>Data Entrada</th>
                                <th>Cod Barras Tag</th>
                                <th>Cod Reduzido</th>
                                <th>EPC</th>
                                <th>Numero Op</th>

                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be appended here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>


<?php include_once("../../../templates/footer.php"); ?>
<script src="script.js"></script>
