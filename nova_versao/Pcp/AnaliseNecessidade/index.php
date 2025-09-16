<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerPcp.php');
?>
<!-- Adicione o CSS do Select2 -->
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

    .modal-grande {
    max-width: 95% !important; /* ou qualquer valor, como 1200px */
    width: 95%;
}
</style>

<div class="titulo-tela" id="titulo">
    <span class="span-icone"><i class="bi bi-bag-check"></i></span> Análise de Materiais
</div>

<div class="mt-3 row justify-content-center" id="selecao-plano">
    <form id="form-vendas" class="row" onsubmit="Selecionar_Calculo(); return false;">
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
                    <th>Código<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Descrição<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Código red.<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Necessidade<br>Calculada</br></th>
                    <th>Necessidade<br>Ajustada</br></th>
                    <th>Estoque<br>Atual</br></th>
                    <th>Compra<br></br></th>
                    <th>Requisições<br></br></th>
                    <th>Fornecedor Principal<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Medida<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Lote<br>Mínimo</br></th>
                    <th>Lote<br>Múltiplo</br></th>
                    <th>Lead<br>Time</br></th>
                    <th>Fator<br>Conversão</br></th>
                    <th>Item Substituto<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Descrição<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Saldo<br></br></th>
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

<div class="row mt-3">
    <div class="col-12 col-md-6 div-naturezas d-none" style="background-color: lightgray; border-radius: 8px; border: 1px solid black; padding: 16px;">
        <p class="fs-4 fw-bold text-dark">Naturezas</p>
        <div class="div-tabela" style="max-width: 100%; overflow: auto;">
            <table class="table table-bordered" id="table-naturezas" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Código<br><input type="search" class="search-input search-input-naturezas"></th>
                        <th>Descrição<br><input type="search" class="search-input search-input-naturezas"></th>
                        <th>Natureza<br><input type="search" class="search-input search-input-naturezas"></th>
                        <th>Estoque<br><input type="search" class="search-input search-input-naturezas"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dados da tabela -->
                </tbody>
            </table>
        </div>
        <div class="custom-pagination-container pagination-naturezas d-md-flex col-12 text-center text-md-start">
            <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                <label for="text">Itens por página</label>
                <input id="itens-naturezas" class="input-itens" type="text" value="10" min="1">
            </div>
            <div id="pagination-naturezas" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 div-comprometido d-none" style="background-color: lightgray; border-radius: 8px; border: 1px solid black; padding: 16px;">
        <p class="fs-4 fw-bold text-dark">Comprometido com Requisições</p>
        <div class="div-tabela" style="max-width: 100%; overflow: auto;">
            <table class="table table-bordered" id="table-comprometido" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Código<br><input type="search" class="search-input search-input-comprometido"></th>
                        <th>Descrição<br><input type="search" class="search-input search-input-comprometido"></th>
                        <th>Op<br><input type="search" class="search-input search-input-comprometido"></th>
                        <th>Em requisição<br><input type="search" class="search-input search-input-comprometido"></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dados da tabela -->
                </tbody>
            </table>
        </div>
        <div class="custom-pagination-container pagination-comprometido d-md-flex col-12 text-center text-md-start">
            <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                <label for="text">Itens por página</label>
                <input id="itens-comprometido" class="input-itens" type="text" value="10" min="1">
            </div>
            <div id="pagination-comprometido" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
            </div>
        </div>
    </div>
</div>

<div class="col-12 mt-3 div-comprometido-compras d-none" style="background-color: lightgray; border-radius: 8px; border: 1px solid black; padding: 16px;">
    <p class="fs-4 fw-bold text-dark">Solicitações e Pedidos de Compra</p>
    <div class="div-tabela" style="max-width: 100%; overflow: auto;">
        <table class="table table-bordered" id="table-comprometido-compras" style="width: 100%;">
            <thead>
                <tr>
                    <th>Código<br><input type="search" class="search-input search-input-comprometido-compras"></th>
                    <th>Descrição<br><input type="search" class="search-input search-input-comprometido-compras"></th>
                    <th>Número<br><input type="search" class="search-input search-input-comprometido-compras"></th>
                    <th>Tipo<br><input type="search" class="search-input search-input-comprometido-compras"></th>
                    <th>Quantidade<br><input type="search" class="search-input search-input-comprometido-compras"></th>
                    <th>Previsão<br><input type="search" class="search-input search-input-comprometido-compras"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Dados da tabela -->
            </tbody>
        </table>
    </div>
    <div class="custom-pagination-container pagination-comprometido-compras d-md-flex col-12 text-center text-md-start">
        <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
            <label for="text">Itens por página</label>
            <input id="itens-comprometido-compras" class="input-itens" type="text" value="10" min="1">
        </div>
        <div id="pagination-comprometido-compras" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-detalhamento" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-xl modal-grande">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;" id="titulo-detalhamento";>Detalhamento</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="align-items: start; text-align: left">
                <div class="div-tabela" style="max-width: 100%; overflow: auto;">
                    <table class="table table-bordered" id="table-detalhamento" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Referência<br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Tam<br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Cor<br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Descrição<br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Reduzido<br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Qtd.<br>Pedidos</br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Previsão<br>Vendas</br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Qtd.<br>Pedida</br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Falta<br>Progr.</br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Class.<br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Class.<br>Categ.</br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Status<br>Afv</br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Unid.<br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Consumo<br><input type="search" class="search-input search-input-detalhamento"></th>
                                <th>Necessidade<br><input type="search" class="search-input search-input-detalhamento"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados da tabela -->
                        </tbody>
                          <tfoot>
                <tr>
                    <th>Total:</th>         <!-- coluna 0 -->
                    <th colspan="12"></th>  <!-- colunas 1 a 12 -->
                    <th id="Necessidade"></th> <!-- coluna 13 -->
                    <th></th>               <!-- coluna 14 -->
                </tr>
            </tfoot>
                    </table>
                </div>
                <div class="custom-pagination-container pagination-detalhamento d-md-flex col-12 text-center text-md-start">
                    <div id="custom-info" class="col-12 col-md-6 mb-2 mb-md-0">
                        <label for="text">Itens por página</label>
                        <input id="itens-detalhamento" class="input-itens" type="text" value="10" min="1">
                    </div>
                    <div id="pagination-detalhamento" class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

        <div class="mb-4 col-12" id="inputs-container-configuracoess">
            <h6 class="fw-bold text-white bg-dark">
            <i class="bi bi-gear-fill me-2"></i> Configurações
            </h6>

            <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="igualarDisponivel" checked>
            <label class="form-check-label" for="igualarDisponivel">
                Deseja igualar o <strong>"disponível"</strong> ao <strong>"falta programar"</strong> para os casos de <strong>falta programar menor que o disponível</strong>?
            </label>
            </div>
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

                        <div class="mb-4 col-12" id="inputs-container-configuracoess">
                            <h6 class="fw-bold text-white bg-dark">
                            <i class="bi bi-gear-fill me-2"></i> Configurações
                            </h6>

                            <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="igualarDisponivel2" checked>
                            <label class="form-check-label" for="igualarDisponivel2">
                                Deseja igualar o <strong>"disponível"</strong> ao <strong>"falta programar"</strong> para os casos de <strong>falta programar menor que o disponível</strong>?
                            </label>
                            </div>
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


<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script5.js"></script>
