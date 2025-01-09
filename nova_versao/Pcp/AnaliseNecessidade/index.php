<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-bag-check"></i></span> Análise de Materiais
</div>

<div class="mt-3 row justify-content-center" id="selecao-plano">
    <form id="form-vendas" class="row" onsubmit="Analise_Materiais(); return false;">
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

<div class="col-12 div-analise d-none" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered table-striped" id="table-analise" style="width: 100%;">
            <thead>
                <tr>
                    <th>Código<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Descrição<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Código red.<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Necessidade<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Estoque Atual<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Compra<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Requisições<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Fornecedor Principal<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Medida<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Lote Mínimo<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Lote Múltiplo<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Lead Time<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Fator de Conversão<br><input type="search" class="search-input search-input-analise"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-analise d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-analise" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-analise" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
        </div>
    </div>
</div>


<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script1.js"></script>
