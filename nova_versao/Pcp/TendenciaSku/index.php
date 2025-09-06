<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<style>


</style>


<div class="d-flex justify-content-between align-items-center titulo-tela" id="titulo">
    <div>
        <span class="span-icone">
            <i class="bi bi-clipboard-data-fill"></i>
        </span> 
        Tendência de Vendas
    </div>
</div>



<div class="mt-3 row justify-content-center" id="selecao-plano" style="max-width: 100%; overflow:auto">
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
                    <th>Marca<br><input type="search" class="search-input search-input-tendencia" style="min-width: 80px;"></th>
                    <th>Referência<br><input type="search" class="search-input search-input-tendencia" style="min-width: 90px;"></th>
                    <th>Tam.<br><input type="search" class="search-input search-input-tendencia" style="min-width: 50px;"></th>
                    <th>Cor<br><input type="search" class="search-input search-input-tendencia" style="min-width: 70px;"></th>
                    <th>Descrição<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Reduzido<br><input type="search" class="search-input search-input-tendencia" style="min-width: 90px;"></th>
                    <th>Categoria<br><input type="search" class="search-input search-input-tendencia" style="min-width: 110px;"></th>
                    <th>Abc<br><input type="search" class="search-input search-input-tendencia" style="min-width: 60px;"></th>
                    <th>Abc/Categ.<br><input type="search" class="search-input search-input-tendencia" style="min-width: 60px;"></th>
                    <th>Qtd.de<br>Pedidos</th>
                    <th>Valor<br>Vendido</th>
                    <th>Previsão<br>de Vendas</th>
                    <th>Qtd.<br>Pedida</th>
                    <th>Qtd.<br>Faturada</th>
                    <th>Saldo<br>Col Anterior</th>
                    <th>Qtd. em<br>Estoque</th>
                    <th>Qtd. em<br>Processo</th>
                    <th>Falta<br>Programar</th>
                    <th>Disponível</th>
                    <th>Disponível<br>Pronta Entrega</th>
                    <th>Prev.<br>Sobra</th>
                    <th>Status Afv<br><input type="search" class="search-input search-input-tendencia" style="min-width: 120px;"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui vão os dados da tabela -->
            </tbody>
            <tfoot>
                <tr>
                    <th>Total:</th>
                    <th colspan="9"></th>
                    <th id="totalValorVendido"></th>
                    <th id="totalPrevicaoVendas"></th>
                    <th id="totalQtdePedida"></th>
                    <th id="totalQtdeFaturada"></th>
                    <th id="totalColAnterior"></th>
                    <th id="totalEstoqueAtual"></th>
                    <th id="totalEmProcesso"></th>
                    <th id="totalFaltaProg"></th>
                    <th id="totalDisponivel"></th>
                    <th id="prevSobra"></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="custom-pagination-container pagination-detalhamento d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="itens-tendencia">Itens por página</label>
            <input id="itens-tendencia" class="input-itens" type="text" value="12" min="1">
        </div>
        <div id="pagination-tendencia" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
            <!-- Paginação será inserida aqui -->
        </div>
    </div>
</div>



<div class="col-12 mt-3 div-simulacao" style="background-color: lightgray; border-radius: 8px; border: 1px solid black; padding: 16px;">
    <p class="fs-4 fw-bold text-dark">SIMULAÇÃO</p>
    <div class="modal-body col-12" style="align-items: start; text-align: left; overflow-y: auto;">
                    <div class="select mb-4 text-start d-none" id="campo-simulacao">
                        <label for="select-simulacao" class="form-label">Simulação</label>
                        <select id="select-simulacao" class="form-select"></select>
                    </div>

</div>




<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script6.js"></script>
