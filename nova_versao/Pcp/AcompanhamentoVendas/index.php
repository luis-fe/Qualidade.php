<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<link rel="stylesheet" href="style.css">
<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-bar-chart-line"></i></span> Acomp. Vendas
</div>

<div class="col-12 mt-2 d-flex" style="border-bottom: 1px solid lightgray; max-width: 100%; overflow-x: auto">
    <button class="btn btn-menu" id="btn-vendas" onclick="alterna_button_selecionado(this); $('.div-vendas-categoria').addClass('d-none'); $('.div-vendas').removeClass('d-none'); $('.div-vendas-sku').addClass('d-none');">
        <i class="fa-solid fa-map"></i>
        <span>Vendido</span>
    </button>
    <button class="btn btn-menu disabled" id="btn-vendas-categoria" onclick="alterna_button_selecionado(this); $('.div-vendas-categoria').removeClass('d-none'); $('.div-vendas').addClass('d-none'); $('.div-vendas-sku').addClass('d-none');">
        <i class="fa-solid fa-clone"></i>
        <span>Vendidos Categoria</span>
    </button>
    <!-- <button class="btn btn-menu disabled" id="btn-vendas-categoria" onclick="alterna_button_selecionado(this); $('.div-vendas-categoria').addClass('d-none'); $('.div-vendas').addClass('d-none'); $('.div-vendas-sku').removeClass('d-none');">
        <i class="fa-solid fa-clone"></i>
        <span>Vendidos Sku</span>
    </button> -->
</div>

<div class="mt-3 row justify-content-center" id="selecao-plano">
    <form id="form-vendas" class="row" onsubmit="Vendas_por_Plano(); return false;">
        <div class="col-12 col-md-6">
            <div class="select text-start">
                <label for="select-plano" class="form-label">Selecionar plano</label>
                <select id="select-plano" class="form-select" required>
                </select>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="select text-start">
                <label for="select-pedidos-bloqueados" class="form-label">Considera pedidos bloqueados?</label>
                <select id="select-pedidos-bloqueados" class="form-select" required>
                    <option></option>
                    <option value="sim">Sim</option>
                    <option value="nao">Não</option>
                </select>
            </div>
        </div>

        <div class="col-12 col-md-2 d-flex align-items-end justify-content-center">
            <button type="submit" class="btn btn-geral w-100" style="margin-top: 32px;">Consultar</button>
        </div>
    </form>
</div>

<div class="col-12 div-vendas mt-3" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered" id="table-vendas" style="width: 100%;">
            <thead>
                <tr>
                    <th>Marca<br><input type="search" class="search-input"></th>
                    <th>Meta R$<br><input type="search" class="search-input"></th>
                    <th>R$ Vendido<br><input type="search" class="search-input"></th>
                    <th>Meta Peças<br><input type="search" class="search-input"></th>
                    <th>Peças Vendidas<br><input type="search" class="search-input"></th>
                    <th>Peças Faturadas<br><input type="search" class="search-input"></th>
                    <th>Falta Programar<br><input type="search" class="search-input"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th id="metaFinanceira"></th>
                    <th id="valorVendido"></th>
                    <th id="metaPecas"></th>
                    <th id="qtdePedida"></th>
                    <th id="qtdeFaturada"></th>
                    <th id="faltaProgVendido"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="custom-pagination-container pagination-venda d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-vendas" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-vendas" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

        </div>
    </div>
</div>


<div class="col-12 div-vendas-categoria d-none mt-3" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela-vendas-categoria" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered" id="table-vendas-categoria" style="width: 100%;">
            <thead>
                <tr>
                    <th>Categoria<br><input type="search" class="search-input"></th>
                    <th>Marca<br><input type="search" class="search-input"></th>
                    <th>Meta R$<br><input type="search" class="search-input"></th>
                    <th>R$ Vendido<br><input type="search" class="search-input"></th>
                    <th>Meta peças<br><input type="search" class="search-input"></th>
                    <th>Peças Vendidas<br><input type="search" class="search-input"></th>
                    <th>Peças Faturadas<br><input type="search" class="search-input"></th>

                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th></th>
                    <th id="metaFinanceira"></th>
                    <th id="valorVendido"></th>
                    <th id="metaPecas"></th>
                    <th id="quantidadeVendida"></th>
                    <th id="quantidadeFaturada"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="custom-pagination-container pagination-vendas-categoria d-md-flex col-12 text-center text-md-start">
        <div id="custom-info-vendas-categoria" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-vendas-categoria" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-vendas-categoria" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
        </div>
    </div>
</div>

<div class="col-12 div-vendas-sku d-none mt-3" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela-vendas-categoria" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered" id="table-vendas-categoria" style="width: 100%;">
            <thead>
                <tr>
                    <th>Categoria<br><input type="search" class="search-input"></th>
                    <th>Marca<br><input type="search" class="search-input"></th>
                    <th>Meta R$<br><input type="search" class="search-input"></th>
                    <th>R$ Vendido<br><input type="search" class="search-input"></th>
                    <th>Meta peças<br><input type="search" class="search-input"></th>
                    <th>Peças Vendidas<br><input type="search" class="search-input"></th>
                    <th>Peças Faturadas<br><input type="search" class="search-input"></th>

                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-vendas-categoria d-md-flex col-12 text-center text-md-start">
        <div id="custom-info-vendas-categoria" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-vendas-categoria" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-vendas-categoria" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
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
<script src="script1.js"></script>
