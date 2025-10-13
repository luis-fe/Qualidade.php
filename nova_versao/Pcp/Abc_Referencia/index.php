<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<link rel="stylesheet" href="style.css">

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-graph-up-arrow"></i></span> Abc por referência
</div>


<div class="mt-3 row justify-content-center" id="selecao-plano">
    <form id="form-vendas" class="row" onsubmit="Abc_Referencia(); return false;">
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

<div class="col-12 div-abc mt-3 d-none" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered" id="table-abc" style="width: 100%;">
            <thead>
                <tr>
                    <th>Marca<br><input type="search" class="search-input search-input-abc"></th>
                    <th>Referência<br><input type="search" class="search-input search-input-abc"></th>
                    <th>Descrição<br><input type="search" class="search-input search-input-abc"></th>
                    <th>Classificação Abc<br><input type="search" class="search-input search-input-abc"></th>
                    <th>Categoria<br><input type="search" class="search-input search-input-abc"></th>
                    <th>Class. Abc Categoria<br><input type="search" class="search-input search-input-abc"></th>
                    <th>Qt. Pedida<br><input type="search" class="search-input search-input-abc"></th>
                    <th>Qtd. Faturada<br><input type="search" class="search-input search-input-abc"></th>
                    <th>Valor Vendido<br><input type="search" class="search-input search-input-abc"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th colspan="5"></th>
                    <th id="qtdePedida"></th>
                    <th id="qtdeFaturada"></th>
                    <th id="valorVendido"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="custom-pagination-container pagination-venda d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-abc" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-abc" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

        </div>
    </div>
</div>
<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script.js"></script>
