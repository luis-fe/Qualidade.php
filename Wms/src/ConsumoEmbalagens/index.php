<?php
include_once("requests.php");
include_once("../../../templates/heads.php");
include("../../../templates/Loading.php");
?>
<link rel="stylesheet" href="style.css">


<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <button class="btn btn-outline-primary mb-3" id="CadastrarUsuario" onclick="$('#modalCadEmbalagem').modal('show')">
            Nova Embalagem
            <span><i class="fa-solid fa-circle-plus"></i></span>
        </button>
        <div class="row">
            <div class="col-12 col-md-5">
                <label for="dataInicio" class="form-label">Início</label>
                <input type="date" id="dataInicio" class="form-control">
            </div>
            <div class="col-12 col-md-5">
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
                        <th scope="col">Código Embalagem</th>
                        <th scope="col">Tamanho Embalagem</th>
                        <th scope="col">Qtd. Consumida</th>
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
    </div>
</div>

<div class="modal fade" id="modalCadEmbalagem" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TituloModal">Cadastrar Nova Embalagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="FormCadEmbalagem">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="InputCodigoEmbalagem" placeholder=" " required autocomplete="off">
                        <label for="InputCodigoEmbalagem">Código da Embalagem (código do csw)</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="InputDescricao" placeholder=" " required autocomplete="off">
                        <label for="InputDescricao">Descrição da Embalagem</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="InputTamanho" placeholder=" " required autocomplete="off">
                        <label for="InputTamanho">Tamanho da Embalagem</label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once("../../../templates/footer.php"); ?>
<script src="script.js"></script>