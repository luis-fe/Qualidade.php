<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">

<style>

    /* Aplica a largura de 80% somente ao modal com id 'modal-detalhamentoSku' */
#modal-detalhamentoSku .modal-dialog {
    max-width: 80% !important; /* Define a largura do modal para 80% */
}

.tooltip .tooltip-inner {
    background-color: #000 !important; /* fundo preto */
    color: #fff !important;            /* texto branco */
    font-size: 18px !important;        /* tamanho da fonte */
    padding: 6px 8px !important;                 /* espaçamento opcional */
    border-radius: 4px !important;                /* borda levemente arredondada */
    max-width: 500px !important;       /* aumenta a largura máxima da caixa */
    white-space: normal !important;    /* permite quebra de linha */
}

.tooltip.bs-tooltip-top .tooltip-arrow::before,
.tooltip.bs-tooltip-bottom .tooltip-arrow::before,
.tooltip.bs-tooltip-start .tooltip-arrow::before,
.tooltip.bs-tooltip-end .tooltip-arrow::before {
    border-color: #000 !important; /* cor da setinha do tooltip */
}

#table-detalhamentoSku tbody tr.linha-destacada td {
    background-color: rgb(224, 33, 33) !important;
    color: white !important;
}

#btn-sim, #btn-nao {
  transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}


</style>

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
                    <th>Falta<br>Prog</br><input type="search" class="search-input search-input-analise"></th>
                    <th>Sugestao <br>Pela MP</br><input type="search" class="search-input search-input-analise"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Dados da tabela -->
            </tbody>
                                     <tfoot>
                                <tr>
                                    <th>Total:</th>
                                    <th colspan="2"></th>
                                    <th id="Falta Prog"></th>
                                    <th id="Sugestão"></th>
                                    <th></th>
                                </tr>
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

    
<div class="modal fade modal-custom" id="modal-simulacao" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Simulações</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form id="form-simulacao">
                <div class="modal-body col-12" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                    <div class="select mb-4 text-start d-none" id="campo-simulacao">
                        <label for="select-simulacao" class="form-label">Simulação</label>
                        <select id="select-simulacao" class="form-select">
                        </select>
                    </div>
                    <div class="mb-4 col-12 d-none" id="inputs-container-marcas">
                        <h6 class="fw-bold">MARCA</h6>
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <label class="fw-bold">M.POLLO</label>
                                <input type="text" id="MPOLLO" class="inputs-percentuais input-marca col-12" placeholder="%" />
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="fw-bold">PACO</label>
                                <input type="text" id="PACO" class="inputs-percentuais input-marca col-12" placeholder="%" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 col-12">
                        <h6 class="fw-bold">CLASSIFICAÇÕES</h6>
                        <div id="inputs-container" class="row">
                        </div>
                    </div>
                    <div class="mt-5 col-12">
                        <h6 class="fw-bold">CATEGORIAS</h6>
                        <div id="inputs-container-categorias" class="row">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-salvar">
                        <span><i class="bi bi-floppy"></i></span>
                        Salvar e Simular
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade modal-custom" id="modal-cad_simulacao" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Cadastro De Simulações</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <form id="form-cad_simulacao">
                <div class="modal-body col-12" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                    <div class="mb-4 col-12" id="campo-desc-simulacao">
                        <label for="descricao-simulacao" class="fw-bold">Descrição da Simulação</label>
                        <input type="text" id="descricao-simulacao" class="form-control" placeholder="Insira a descrição" required />
                    </div>
                    <div class="mb-4 col-12" id="inputs-container-Cadmarcas">
                        <h6 class="fw-bold">MARCA</h6>
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <label class="fw-bold">M.POLLO</label>
                                <input type="text" id="MPOLLO" class="inputs-percentuais input-marca2 col-12" placeholder="%" value="100%" />
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="fw-bold">PACO</label>
                                <input type="text" id="PACO" class="inputs-percentuais input-marca2 col-12" placeholder="%" value="100%"/>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 col-12">
                        <h6 class="fw-bold">CLASSIFICAÇÕES</h6>
                        <div id="inputs-Cadcontainer" class="row">
                        </div>
                    </div>
                    <div class="mt-5 col-12">
                        <h6 class="fw-bold">CATEGORIAS</h6>
                        <button id="btn-zerar-categorias" type="button" class="btn btn-primary mb-3">Zerar Percentuais</button>
                        <div id="inputs-Cadcontainer-Cadcategorias" class="row">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-salvar">
                        <span><i class="bi bi-floppy"></i></span>
                        Cadastar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="modal-question" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center">
        <p class="fs-4">Deseja Recalcular a Análise de Materiais?</p>
      </div>
      <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" id="btn-sim">Sim</button>
            <button type="button" class="btn btn-secondary" id="btn-nao">Não</button>
      </div>
    </div>
  </div>
</div>



<div class="modal fade modal-custom" id="modal-detalhamentoSku" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titulo-detalhamentoSku" style="color: black;">Detalhamento Matéria Prima: </h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="d-flex  mt-2">
                <span style="width: 25px; height: 25px; background-color: red; display: inline-block; margin-right: 16px;"></span>
                <span style="color: black;">Legenda: itens que restringe a Sugestao</span>
            </div>
            <div class="modal-body" style="align-items: start; text-align: left">
                <div class="div-tabela" style="max-width: 100%; overflow: auto;">
                    <table class="table table-bordered" id="table-detalhamentoSku" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Cód.<br>Red.</br><input type="search" class="search-input search-input-detalhamentoSku"></th>
                                <th>Cód<br>Componente</br><input type="search" class="search-input search-input-detalhamentoSku"></th>
                                <th>Descricao<br><input type="search" class="search-input search-input-detalhamentoSku"></th>
                                <th>Estoque<br>MP.</br><input type="search" class="search-input search-input-detalhamentoSku"></th>
                                <th>Comprometido<br>Requisicao</br><input type="search" class="search-input search-input-detalhamentoSku"></th>
                                <th>
                                <span 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    title="Estoque Líquido = Estoque - Requisição">
                                    Estoque<br>Líquido</br>
                                </span>
                                <input type="search" class="search-input search-input-detalhamentoSku">
                                </th>
                                <th>
                                    <span 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    title="É a Necessidade Total dessa Matéria Prima em 'TODOS' os Skus Necessarios(negativo)">
                                    Necessidade<br>Total</br>
                                    </span>

                                <input type="search" class="search-input search-input-detalhamentoSku"></th>
                                <th>
                                     <span 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    title="É o total de Matéria Prima distribuida para esse SKU, utilizada para saber o rendimento de PCs">
                                    Estoque MP.<br>Distr.</br>
                                    </span>
                                    <input type="search" class="search-input search-input-detalhamentoSku"></th>
                                <th>Falta<br>Prog.</br><input type="search" class="search-input search-input-detalhamentoSku"></th>
                                <th>Sugestão<br>PC</br><input type="search" class="search-input search-input-detalhamentoSku"></th>
                                <th>obs.:<br></br><input type="search" class="search-input search-input-detalhamentoSku"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados da tabela -->
                        </tbody>
                    </table>
                </div>
                <div class="custom-pagination-container pagination-detalhamentoSku d-md-flex col-12 text-center text-md-start">
                    <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                        <label for="text">Itens por página</label>
                        <input id="itens-detalhamentoSku" class="input-itens" type="text" value="15" min="1">
                    </div>
                    <div id="pagination-detalhamentoSku" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script1.js"></script>
