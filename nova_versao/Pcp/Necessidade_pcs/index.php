<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-bag-check"></i></span> Necessidade x Pçs a Programar
</div>

<div class="mt-3 row justify-content-center" id="selecao-plano">
    <form id="form-vendas" class="row" onsubmit="AnaliseProgramacaoPelaMP(); return false;">
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

<div class="col-12 mt-4 mb-4 div-analise d-none" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered" id="table-analise" style="width: 100%;">
            <thead>
                <tr>
                    <th>Categoria<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Marca<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Engenharia<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Código red.<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Descricao<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Cor<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Tam<br><input type="search" class="search-input search-input-analise"></th>
                    <th>faltaProg<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Sugestao <br>Pela MP</br><input type="search" class="search-input search-input-analise"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Dados da tabela -->
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

    <!-- Modal -->
    <div class="modal fade" id="categoriaModal" tabindex="-1" aria-labelledby="categoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="categoriaModalLabel">Escolha uma Categoria MP</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
            <div id="categoriaCheckboxes" class="d-flex flex-column text-start ps-2">
            <!-- Checkboxes serão inseridos aqui via JavaScript -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-success" onclick="confirmarCategoria()">Confirmar</button>
        </div>
        </div>
    </div>
    </div>



<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script1.js"></script>
