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







<div 
    id="simulacao-container" 
    class="mt-3 div-simulacao mx-auto d-none p-3 border border-dark rounded" 
    style="width: 60%; max-height: 80vh; overflow-y: auto; background-color: lightgray; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050;">
    
    <form>
        <!-- Botão fechar -->
        <button 
            type="button" 
            onclick="fecharSimulacao()" 
            class="btn-close position-absolute top-0 end-0 m-2" 
            aria-label="Fechar">
        </button> 

        <p class="fs-4 fw-bold text-dark">SIMULAÇÃO</p> 

        <div class="modal-body text-start overflow-auto"> 
            <div class="mb-4" id="campo-simulacao"> 
                <label for="select-simulacao" class="form-label">Simulação</label> 
                <select id="select-simulacao" class="form-select"></select> 
            </div> 
        </div> 

        <div class="mb-4 col-12" id="inputs-container-marcas"> 
            <h6 class="fw-bold text-white bg-dark p-1">MARCA</h6> 
            <div class="row"> 
                <div class="col-12 col-md-3"> 
                    <label class="fw-bold">M.POLLO</label> 
                    <input type="text" id="MPOLLO" class="form-control inputs-percentuais input-marca" placeholder="%" /> 
                </div> 
                <div class="col-12 col-md-3"> 
                    <label class="fw-bold">PACO</label> 
                    <input type="text" id="PACO" class="form-control inputs-percentuais input-marca" placeholder="%" /> 
                </div> 
            </div> 
        </div> 

        <div class="mt-5 col-12"> 
            <h6 class="fw-bold text-white bg-dark p-1">CLASSIFICAÇÕES</h6> 
            <div id="inputs-container" class="row"></div> 
        </div>

        <div class="mt-5 col-12"> 
            <h6 class="fw-bold text-white bg-dark p-1">CATEGORIAS</h6> 
            <div id="inputs-container-categorias" class="row"></div> 
        </div> 

        <div class="px-3 pb-4"> 
            <h6 class="fw-bold text-white bg-dark p-1">PRODUTOS</h6> 
            <div id="inputs-container-PRODUTOS" class="d-flex align-items-center gap-2"> 
                <button type="button" 
                        class="btn btn-primary" 
                        id="btn-adicionar-lotes" 
                        onclick="Consulta_Engenharias()"> 
                    <i class="bi bi-plus"></i> 
                    <span style="font-size: 12px;">Escolher</span> 
                </button> 

                <span id="TituloSelecaoEngenharias">Todas Selecionadas</span> 

                <button type="button" 
                        class="btn btn-danger d-flex flex-column align-items-center justify-content-center" 
                        id="btn-limpar-lotes" 
                        onclick="Deletar_SimulacaoProduto()"> 
                    <span class="d-inline-flex align-items-center" style="gap: 4px; font-size: 11px;"> 
                        <i class="bi bi-x-circle"></i> Limpar 
                    </span> 
                </button> 
            </div> 
        </div> 

        <div class="modal-footer"> 
            <button type="button" 
                    class="btn btn-danger" 
                    onclick="Deletar_Simulacao()"> 
                <i class="bi bi-trash3-fill"></i> Excluir Simulação 
            </button> 

            <button type="button" 
                    class="btn btn-success" 
                    onclick="simulacao($('#select-simulacao').val(),''); return false;"> 
                <i class="bi bi-floppy"></i> Salvar e Simular 
            </button> 
        </div> 
    </form> 
</div>


<div 
    id="nova-simulacao-container" 
    class="mt-3 div-nova-simulacao mx-auto d-none p-3 border border-dark rounded" 
    style="width: 60%; height: 90%; overflow-y: auto; background-color: lightgray; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050;">



    <form id="form-nova-simulacao" >

        <!-- Botão fechar -->
        <button 
            type="button" 
            onclick="fecharNovaSimulacao()" 
            class="btn-close position-absolute top-0 end-0 m-2" 
            aria-label="Fechar">
        </button> 

                <p class="fs-4 fw-bold text-dark">Nova Simulação</p>


                
                <div class="modal-body col-12" style="align-items: start; text-align: left; overflow-y: auto;">
                    
                    <div class="mb-4 col-12" id="campo-desc-simulacao">
                        <label for="descricao-simulacao" class="fw-bold">Descrição da Simulação</label>
                        <input type="text" id="descricao-simulacao" class="form-control" placeholder="Insira a descrição" required />
                    </div>

                    <div class="mb-4 col-12" id="inputs-container-configuracoess">
                        <h6 class="fw-bold text-white bg-dark">
                        <i class="bi bi-gear-fill me-2"></i> Configurações
                        </h6>

                        <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="igualarDisponivel" checked>
                        <label class="form-check-label" for="igualarDisponivel">
                            Deseja igualar o <strong>"disponível"</strong> ao <strong>"falta programar"</strong> para os casos de <strong>falta programar menor  disponível</strong>?
                        </label>
                        </div>
                    </div>

                    <div class="mb-4 col-12 d-none" id="inputs-container-novas-marcas">
                        <h6 class="fw-bold text-white bg-dark">MARCA</h6>  
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <label class="fw-bold">M.POLLO</label>
                                <input type="text" id="MPOLLO" class="inputs-percentuais input-marca-nova col-12" placeholder="%100" />
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="fw-bold">PACO</label>
                                <input type="text" id="PACO" class="inputs-percentuais input-marca-nova col-12" placeholder="%100" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 col-12">
                        <h6 class="fw-bold text-white bg-dark">CLASSIFICAÇÕES</h6>
                        <div id="inputs-container-nova" class="row"></div>
                    </div>

                    <div class="mt-5 col-12">   
                        <h6 class="fw-bold text-white bg-dark">CATEGORIAS</h6>
                        <div id="inputs-container-categorias-nova" class="row" placeholder="%100"></div>
                    </div>

                    <div class="px-3 pb-4">
                        <h6 class="fw-bold text-white bg-dark">PRODUTOS</h6>
                        <div id="inputs-container-PRODUTOS" class="d-flex align-items-center gap-2">
                            <button type="button" 
                                    class="btn btn-salvar" 
                                    style="width: 120px" 
                                    id="btn-adicionar-lotes" 
                                    onclick="Consulta_Engenharias()">
                                <span><i class="bi bi-plus"></i></span>
                                <span style="font-size: 12px;">Escolher</span>
                                
                            </button>
                            <span id="TituloSelecaoEngenharias2">Todas Selecionadas</span>
                        </div>
                    </div>

    </form>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-salvar" 
                                onclick="$('.input-categoria-2').val('0,00%')">
                            <span><i class="bi bi-x-octagon"></i></span>
                            Zerar Categorias
                        </button>
                        <button type="button" class="btn btn-salvar" onclick="simulacao($('#descricao-simulacao').val(),'cadastro'); return false;">
                            <span><i class="bi bi-floppy"></i></span>
                            Salvar e Simular
                        </button>
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



<div class="modal fade modal-custom" id="modal-detalhamento-OrdemProd" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Detalhamento Ordem Producao</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="align-items: start; text-align: left">
                <div class="div-tabela" style="max-width: 100%; overflow: auto;">
                    <table class="table table-bordered" id="table-detalhamento-OrdemProd" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Ordem Producao<br><input type="search" class="search-input search-input-detalhamento-OrdemProd"></th>
                                <th>cod. FaseAtual<br><input type="search" class="search-input search-input-detalhamento-OrdemProd"></th>
                                <th>nome FaseAtual<br><input type="search" class="search-input search-input-detalhamento-OrdemProd"></th>
                                <th>Quantidade<br><input type="search" class="search-input search-input-detalhamento-OrdemProd"></th>
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
                        <input id="itens-detalhamento-OrdemProd" class="input-itens" type="text" value="15" min="1">
                    </div>
                    <div id="pagination-detalhamento-OrdemProd" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-detalhamento-pedidosSaldo" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Detalhamento Pedidos Saldo Colecao Anterior</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="align-items: start; text-align: left">
                <div class="div-tabela" style="max-width: 100%; overflow: auto;">
                    <table class="table table-bordered" id="table-detalhamento-pedidosSaldo" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>codReduzido<br><input type="search" class="search-input search-input-detalhamento-pedidosSaldo"></th>
                                <th>codPedido<br><input type="search" class="search-input search-input-detalhamento-pedidosSaldo"></th>
                                <th>codTipoNota<br><input type="search" class="search-input search-input-detalhamento-pedidosSaldo"></th>
                                <th>dataEmissao<br><input type="search" class="search-input search-input-detalhamento-pedidosSaldo"></th>
                                <th>dataPrevFat<br><input type="search" class="search-input search-input-detalhamento-pedidosSaldo"></th>
                                <th>SaldoColAnt<br><input type="search" class="search-input search-input-detalhamento-pedidosSaldo"></th>
                                <th>qtdeFaturadaSaldo<br><input type="search" class="search-input search-input-detalhamento-pedidosSaldo"></th>
                                <th>qtdePedidaSaldo<br><input type="search" class="search-input search-input-detalhamento-pedidosSaldo"></th>

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
                        <input id="itens-detalhamento-pedidosSaldo" class="input-itens" type="text" value="15" min="1">
                    </div>
                    <div id="pagination-detalhamento-pedidosSaldo" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div 
    id="modal-selecaoEngenharias" 
    class="mt-3 div-selecaoEngenharias mx-auto d-none p-3 border border-dark rounded" 
    style="width: 60%; max-height: 90vh; overflow-y: auto; background-color: lightgray; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050;">
    
                <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Seleção de Engenharias</h5>
                    <button 
            type="button" 
            onclick="fecharselecaoEngenharia()" 
            class="btn-close position-absolute top-0 end-0 m-2" 
            aria-label="Fechar">
        </button> 
            </div>
            <div class="modal-body" style=" min-width: 100%; max-height: 100%; overflow: auto">
                <div class="div-tabela-lotes-csw" style="max-width: 100%; overflow: auto;">
                    <table class="table table-bordered table-striped" id="table-lotes-csw" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Marca<br><input type="search" class="search-input search-input-lotes-csw" style="min-width: 70px;"></th>
                                <th>Código Produto<br><input type="search" class="search-input search-input-lotes-csw" style="min-width: 150px;"></th>
                                <th>Descrição<br><input type="search" class="search-input search-input-lotes-csw" style="min-width: 150px;"></th>
                                <th>Percentual<br></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aqui vão os dados da tabela -->
                        </tbody>
                    </table>
                </div>
                <div class="custom-pagination-container pagination-lotes-csw d-md-flex col-12 text-center text-md-start">
                    <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                        <label for="text">Itens por página</label>
                        <input id="itens-lotes-csw" class="input-itens" type="text" value="10" min="1">
                    </div>
                    <div id="pagination-lotes-csw" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-salvar" style="width: 100px" id="btn-salvarProdutosSimulacao">
                    <span><i class="bi bi-floppy"></i></span>
                    Salvar
                </button>
            </div>

</div>




<div class="modal fade modal-custom" id="modal-detalhamento-simulacaoSku"tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Analise da Simulacao de Previsao</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
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


    </div>


</div>



<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script6.js"></script>
