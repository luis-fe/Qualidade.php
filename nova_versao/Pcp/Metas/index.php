<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-bullseye"></i></span> Metas
</div>

<div class="col-12 mt-2 d-flex" style="border-bottom: 1px solid lightgray; max-width: 100%; overflow-x: auto">
    <button class="btn btn-menu" id="btn-metas" onclick="alterna_button_selecionado(this); $('.div-meta-categoria').addClass('d-none'); $('.div-meta').removeClass('d-none'); $('#opcoes').removeClass('d-none'); $('#selecao-plano').removeClass('d-none'); $('#selecao-marca').addClass('d-none')">
        <i class="fa-solid fa-map"></i>
        <span>Metas</span>
    </button>
    <button class="btn btn-menu disabled" id="btn-meta-categoria" onclick="alterna_button_selecionado(this); $('.div-meta-categoria').removeClass('d-none'); $('.div-meta').addClass('d-none'); $('#opcoes').addClass('d-none'); $('#selecao-plano').addClass('d-none'); $('#selecao-marca').removeClass('d-none')">
        <i class="fa-solid fa-clone"></i>
        <span>Metas Categoria</span>
    </button>
</div>

<div class="col-12 mt-2 d-none" id="opcoes" style="flex-wrap: wrap">
    <button class="btn btn-geral" style="width: 100px" onclick="$('#modal-metas').modal('show'); $('#select-marca').val('').change(); $('#input-meta-peca').val(''); $('#input-meta-rs').val(''); $('.modal-title').text('Nova Meta');">
        <span><i class="fa-solid fa-pencil"></i></span>
        Novo
    </button>
</div>

<div class="col-12 mt-3" id="selecao-plano">
    <div class="col-12 row">
        <div class="col-12">
            <div class="select" style="margin-right: 15px; margin-bottom: 10px">
                <select id="select-plano" class="form-select">
                    <option value="">Selecione um plano</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="col-12 div-meta" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela d-none" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-metas" style="width: 100%;">
            <thead>
                <tr>
                    <th>Ação</th>
                    <th>Marca<br><input type="search" class="search-input"></th>
                    <th>Meta R$<br><input type="search" class="search-input"></th>
                    <th>Meta Peças<br><input type="search" class="search-input"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-metas d-none col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-metas" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-metas" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

        </div>
    </div>
</div>


<div class="col-12 mt-3 d-none" id="selecao-marca">
    <div class="col-12 row">
        <div class="col-12">
            <div class="select" style="margin-right: 15px; margin-bottom: 10px">
                <select id="select-marca-categoria" class="form-select">
                    <option value="">Selecione uma marca</option>
                </select>
            </div>
        </div>
    </div>
</div>
<div class="col-12 div-meta-categoria d-none" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela-categoria" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-metas-categoria" style="width: 100%;">
            <thead>
                <tr>
                    <th>Ação</th>
                    <th>Categoria<br><input type="search" class="search-input"></th>
                    <th>Meta R$<br><input type="search" class="search-input"></th>
                    <th>Meta Peças<br><input type="search" class="search-input"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-metas-categoria d-none col-12 text-center text-md-start">
        <div id="custom-info-categoria" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-metas-categoria" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-metas-categoria" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-metas" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Editar Meta</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-metas" onsubmit="Salvar_Metas(); return false;">
                <div class="modal-body" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="select-marca" class="form-label">Marca</label>
                            <select id="select-marca" class="form-select" required>
                                <option value="">Selecione uma marca</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="input-meta-peca" class="form-label">Meta Peças</label>
                            <input type="text" class="form-control" id="input-meta-peca" placeholder="Meta em Peças" required>
                        </div>
                        <div class="col-md-6">
                            <label for="input-meta-rs" class="form-label">Meta R$</label>
                            <input type="text" class="form-control" id="input-meta-rs" placeholder="Meta em R$" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-salvar">
                        <span><i class="bi bi-floppy"></i></span>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-metas-categoria" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Editar Meta</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-metas-categoria" onsubmit="Salvar_Metas_Categoria(); return false;">
                <div class="modal-body" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="input-categoria" class="form-label">Categoria</label>
                            <input type="text" class="form-control" id="input-categoria" placeholder="Categoria" required disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="input-meta-peca-categoria" class="form-label">Meta Peças</label>
                            <input type="text" class="form-control" id="input-meta-peca-categoria" placeholder="Meta em Peças" required>
                        </div>
                        <div class="col-md-6">
                            <label for="input-meta-rs-categoria" class="form-label">Meta R$</label>
                            <input type="text" class="form-control" id="input-meta-rs-categoria" placeholder="Meta em R$" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-salvar">
                        <span><i class="bi bi-floppy"></i></span>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script.js"></script>
