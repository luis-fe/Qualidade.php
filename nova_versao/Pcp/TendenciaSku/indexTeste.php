<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

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


    .periodo-vendas {
        font-size: 16px !important ;
        color: #555; /* opcional para ajustar contraste */
    }

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


<!-- Modal para exibir imagem -->
<div class="modal fade modal-custom" id="modal-imagemMP" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-top modal-xl modal-grande">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="customModalLabel" style="color: black;">Imagem:</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      
      <div class="modal-body" id="modal-body-imagem" style="align-items: start; text-align: left;">
                <div class="modal-footer justify-content-between">
                    <button id="btn-anterior" class="btn btn-secondary">⬅️ Anterior</button>
                    <span id="contador-imagens" class="text-muted"></span>
                    <button id="btn-proximo" class="btn btn-secondary">Próximo ➡️</button>
        </div>
        <!-- A imagem será injetada aqui via JavaScript -->
        <div id="imagem-container" class="text-center">
          <!-- <img src="..." class="img-fluid"> será inserido aqui -->
        </div>
      </div>
      
    </div>
  </div>
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


<div class="modal fade modal-custom" id="modal-simulacao" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Simulações</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            
                <form id="form-simulacao" onsubmit="return simulacao($('#select-simulacao').val(), '');">

                <div class="modal-body col-12" style="align-items: start; text-align: left; overflow-y: auto;">
                    <div class="select mb-4 text-start d-none" id="campo-simulacao">
                        <label for="select-simulacao" class="form-label">Simulação</label>
                        <select id="select-simulacao" class="form-select"></select>
                    </div>

                    <div class="mb-4 col-12 d-none" id="inputs-container-marcas">
                        <h6 class="fw-bold text-white bg-dark">MARCA</h6>
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
                        <h6 class="fw-bold text-white bg-dark">CLASSIFICAÇÕES</h6>
                        <div id="inputs-container" class="row"></div>
                    </div>

                    <div class="mt-5 col-12">
                        <h6 class="fw-bold text-white bg-dark">CATEGORIAS</h6>
                        <div id="inputs-container-categorias" class="row"></div>
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
                            <span id="TituloSelecaoEngenharias">Todas Selecionadas</span>
                        <button type="button" 
                                class="btn btn-excluir d-flex flex-column align-items-center justify-content-center" 
                                style="width: 120px" 
                                id="btn-limpar-lotes" 
                                onclick="Deletar_SimulacaoProduto()">
                                <span class="d-inline-flex align-items-center" style="gap: 4px; font-size: 11px;">
                                    <i class="bi bi-x-circle"></i>
                                     Limpar
                                </span>             
                        </button>

                        
                        
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-excluir" onclick="Deletar_Simulacao()">
                        <span><i class="bi bi-trash3-fill"></i></span> Excluir Simulação
                    </button>
                    <button type="button" class="btn btn-salvar" onclick="simulacao($('#select-simulacao').val(),'cadastro'); return false;">
                        <span><i class="bi bi-floppy"></i></span>
                        Salvar e Simular
                    </button>
                </div>



            </div>
            </form>

        </div>
    </div>
</div>



<div class="modal fade modal-custom" id="modal-selecaoEngenharias" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Seleção de Engenharias</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style=" min-width: 100%; max-height: 600px; overflow: auto">
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
    </div>
</div>


<div class="modal fade modal-custom" id="modal-nova-simulacao" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;">Nova Simulação</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button> 
            </div>

            <form id="form-nova-simulacao" >
                
                <div class="modal-body col-12" style="align-items: start; text-align: left; max-height: 400px; overflow-y: auto;">
                    
                    <div class="mb-4 col-12" id="campo-desc-simulacao">
                        <label for="descricao-simulacao" class="fw-bold">Descrição da Simulação</label>
                        <input type="text" id="descricao-simulacao" class="form-control" placeholder="Insira a descrição" required />
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
                            <span id="TituloSelecaoEngenharias">Todas Selecionadas</span>
                        </div>
                    </div>


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
            </form>



        </div>
    </div>
</div>



<div class="col-12 mt-3 div-detalhamento-skus d-none" style="background-color: lightgray; border-radius: 8px; border: 1px solid black; padding: 16px;">
    <p class="fs-4 fw-bold text-dark">Analise da Simulacao de Previsao</p>
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
    <div class="custom-pagination-container pagination-detalhamento-skus d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-detalhamento-skus" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-detalhamento-skus" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
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

    <button type="button" class="btn btn-success d-none" id="ConfPedidosSaldo">
    <i class="bi bi-file-earmark-excel-fill me-2"></i>
    Conf. Pedidos Saldo
    </button>


<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script6.js"></script>
