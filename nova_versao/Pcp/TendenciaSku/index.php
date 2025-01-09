<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<link rel="stylesheet" href="style.css">
<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-clipboard-data-fill"></i></span> Tendência de Vendas
</div>


<div class="mt-3 row justify-content-center" id="selecao-plano">
    <form id="form-vendas" class="row" onsubmit="Consulta_Tendencias(); return false;">
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

<div class="col-12 div-tendencia mt-3 d-none" style="background-color: lightgray; border-radius: 8px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered" id="table-tendencia" style="width: 100%;">
            <thead>
                <tr>
                    <th>Marca<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Referência<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Tamanho<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Cor<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Descrição<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Reduzido<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Categoria<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Qtd. de Pedidos<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Valor Vendido<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Previsão de Vendas<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Qtd. Pedida<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Qtd. Faturada<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Qtd. em Estoque<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Qtd. em Processo<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Falta Programar<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Disponível<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Prev. Sobra<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Status Afv<br><input type="search" class="search-input search-input-tendencia"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th colspan="5"></th>
                    <th id="totalValorVendido"></th>
                    <th id="totalPrevicaoVendas"></th>
                    <th id="totalQtdePedida"></th>
                    <th id="totalQtdeFaturada"></th>
                    <th id="totalEstoqueAtual"></th>
                    <th id="totalEmProcesso"></th>
                    <th id="totalFaltaProg"></th>
                    <th id="totalDisponivel"></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="custom-pagination-container pagination-venda d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-tendencia" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-tendencia" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-simulacao" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Editar Meta</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                <div id="inputs-container" style="justify-content: center; align-items: center; text-align: center"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-salvar" onclick="Simular_Programacao()">
                    <span><i class="bi bi-floppy"></i></span>
                    Simular
                </button>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script.js"></script>
