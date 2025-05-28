<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
    .form-label {
        font-weight: bold;
        color: #555;
    }

    .inputs-percentuais {
        background-color: #f5f5f5;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px 15px;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .inputs-percentuais:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        background-color: #fff;
    }
</style>
<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-clipboard-data-fill"></i></span> Tendência de Vendas
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
                    <th>Marca<br><input type="search" class="search-input search-input-tendencia" style="min-width: 150px;"></th>
                    <th>Referência<br><input type="search" class="search-input search-input-tendencia" style="min-width: 150px;"></th>
                    <th>Tamanho<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Cor<br><input type="search" class="search-input search-input-tendencia" style="min-width: 100px;"></th>
                    <th>Descrição<br><input type="search" class="search-input search-input-tendencia"></th>
                    <th>Reduzido<br><input type="search" class="search-input search-input-tendencia" style="min-width: 150px;"></th>
                    <th>Categoria<br><input type="search" class="search-input search-input-tendencia" style="min-width: 150px;"></th>
                    <th>Abc<br><input type="search" class="search-input search-input-tendencia" style="min-width: 100px;"></th>
                    <th>Abc Categoria<br><input type="search" class="search-input search-input-tendencia" style="min-width: 100px;"></th>
                    <th>Qtd. de Pedidos<br></th>
                    <th>Valor Vendido<br></th>
                    <th>Previsão de Vendas<br></th>
                    <th>Qtd. Pedida<br></th>
                    <th>Qtd. Faturada<br></th>
                    <th>Saldo Col Anterior<br></th>
                    <th>Qtd. em Estoque<br></th>
                    <th>Qtd. em Processo<br></th>
                    <th>Falta Programar<br></th>
                    <th>Disponível<br></th>
                    <th>Prev. Sobra<br></th>
                    <th>Status Afv<br><input type="search" class="search-input search-input-tendencia" style="min-width: 150px;"></th>
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
    <div class="custom-pagination-container pagination-venda d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-tendencia" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-tendencia" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">

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
            <form id="form-simulacao" onsubmit="simulacao($('#select-simulacao').val(), ''); return false;">
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
                    <button type="button" class="btn btn-excluir" onclick="Deletar_Simulacao()">
                        <span><i class="bi bi-floppy"></i></span>
                        Excluir Simulação
                    </button>
                    <button type="submit" class="btn btn-salvar">
                        <span><i class="bi bi-floppy"></i></span>
                        Salvar e Simular
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-nova-simulacao" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Nova Simulação</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-nova-simulacao" onsubmit="simulacao($('#descricao-simulacao').val(),'cadastro'); return false;">
                <div class="modal-body col-12" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                    <div class="mb-4 col-12" id="campo-desc-simulacao">
                        <label for="descricao-simulacao" class="fw-bold">Descrição da Simulação</label>
                        <input type="text" id="descricao-simulacao" class="form-control" placeholder="Insira a descrição" required />
                    </div>
                    <div class="mb-4 col-12 d-none" id="inputs-container-novas-marcas">
                        <h6 class="fw-bold">MARCA</h6>
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <label class="fw-bold">M.POLLO</label>
                                <input type="text" id="MPOLLO" class="inputs-percentuais input-marca-nova col-12" value="100%" placeholder="%" />
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="fw-bold">PACO</label>
                                <input type="text" id="PACO" class="inputs-percentuais input-marca-nova col-12" value="100%" placeholder="%" />
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 col-12">
                        <h6 class="fw-bold">CLASSIFICAÇÕES</h6>
                        <div id="inputs-container-nova" class="row">
                        </div>
                    </div>
                    <div class="mt-5 col-12">
                        <h6 class="fw-bold">CATEGORIAS</h6>
                        <div id="inputs-container-categorias-nova" class="row">
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

<div class="modal fade modal-custom" id="modal-detalhamento-skus" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Nova Simulação</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="align-items: start; text-align: left">
                <div class="div-tabela" style="max-width: 100%; overflow: auto;">
                    <table class="table table-bordered" id="table-detalhamento-skus" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Nome<br>Simulação</br><input type="search" class="search-input search-input-detalhamento-skus"></th>
                                <th>Reduzido<br><input type="search" class="search-input search-input-detalhamento-skus"></th>
                                <th>Previsão Vendas<br>Original</br><input type="search" class="search-input search-input-detalhamento-skus"></th>
                                <th>%<br>Marca</br><input type="search" class="search-input search-input-detalhamento-skus"></th>
                                <th>%<br>ABC</br><input type="search" class="search-input search-input-detalhamento-skus"></th>
                                <th>%<br>Categoria</br><input type="search" class="search-input search-input-detalhamento-skus"></th>
                                <th>%<br>Considerado</br><input type="search" class="search-input search-input-detalhamento-skus"></th>
                                <th>Nova<br>Previsão</br><input type="search" class="search-input search-input-detalhamento-skus"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados da tabela -->
                        </tbody>
                    </table>
                </div>
                <div class="custom-pagination-container pagination-detalhamento d-md-flex col-12 text-center text-md-start">
                    <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                        <label for="text">Itens por página</label>
                        <input id="itens-detalhamento-skus" class="input-itens" type="text" value="15" min="1">
                    </div>
                    <div id="pagination-detalhamento-skus" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-detalhamento-pedidos" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Detalhamento Pedidos</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="align-items: start; text-align: left">
                <div class="div-tabela" style="max-width: 100%; overflow: auto;">
                    <table class="table table-bordered" id="table-detalhamento-pedidos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>codPedido<br><input type="search" class="search-input search-input-detalhamento-pedidos"></th>
                                <th>codTipoNota<br><input type="search" class="search-input search-input-detalhamento-pedidos"></th>
                                <th>dataEmissao<br><input type="search" class="search-input search-input-detalhamento-pedidos"></th>
                                <th>dataPrevFat<br><input type="search" class="search-input search-input-detalhamento-pedidos"></th>
                                <th>marca<br><input type="search" class="search-input search-input-detalhamento-pedidos"></th>
                                <th>qtdeFaturada<br><input type="search" class="search-input search-input-detalhamento-pedidos"></th>
                                <th>qtdePedida<br><input type="search" class="search-input search-input-detalhamento-pedidos"></th>
                                <th>valorVendido<br><input type="search" class="search-input search-input-detalhamento-pedidos"></th>

                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados da tabela -->
                        </tbody>
                    </table>
                </div>
                <div class="custom-pagination-container pagination-detalhamento d-md-flex col-12 text-center text-md-start">
                    <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                        <label for="text">Itens por página</label>
                        <input id="itens-detalhamento-pedidos" class="input-itens" type="text" value="15" min="1">
                    </div>
                    <div id="pagination-detalhamento-pedidos" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script6.js"></script>
