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

    #table-detalhamentoSku tbody tr.linha-destacada td {
        background-color: rgb(224, 33, 33) !important;
        color: white !important;
    }

    .tooltip .tooltip-inner {
        background-color: #000 !important;
        color: #fff !important;
        font-size: 18px !important;
        padding: 6px 8px !important;
        border-radius: 4px !important;
        max-width: 500px !important;
        white-space: normal !important;
    }

    .tooltip.bs-tooltip-top .tooltip-arrow::before,
    .tooltip.bs-tooltip-bottom .tooltip-arrow::before,
    .tooltip.bs-tooltip-start .tooltip-arrow::before,
    .tooltip.bs-tooltip-end .tooltip-arrow::before {
        border-color: #000 !important;
    }

        .modal-grande {
    max-width: 95% !important; /* ou qualquer valor, como 1200px */
    width: 95%;
}

        .modal-grande2 {
    max-width: 50% !important; /* ou qualquer valor, como 1200px */
    width: 50%;
}

/* Corrige a sobreposição de múltiplos modais */
.modal-backdrop {
  z-index: 1040 !important;
}

#modal-detalhamentoSku {
  z-index: 1050 !important;
}

#modal-imagemMP {
  z-index: 1100 !important;
}

/* Garante que múltiplos backdrops não bugam a interface */
.modal-backdrop + .modal-backdrop {
  z-index: 1055 !important;
}

/* Estilização da imagem dentro do modal */
#imagem-container img {
  max-width: 90%;
  max-height: 600px;
  display: block;
  margin: auto;
  border-radius: 6px;
  box-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
}

  


</style>

<div class="titulo-tela" id="titulo">
    <span class="span-icone"><i class="bi bi-bag-check"></i></span> Necessidade x Pçs a Programar
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
                    <th>Catagoria<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Marca<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Engenharia<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Cód. Reduzido<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Descrição<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Cor<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Tam<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Falta Prog<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Sugestão pela MP<br><input type="search" class="search-input search-input-analise"></th>
                    <th>Disponível Vendido<br><input type="search" class="search-input search-input-analise"></th>
                </tr>
            </thead>
            <tbody>
                <!-- Dados da tabela -->
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" style="text-align: right;">Totais:</th>
                    <th></th> <!-- FaltaProg -->
                    <th></th> <!-- Sugestão PCs -->
                    <th></th> <!-- Disponível (sem total) -->
                </tr>
            </tfoot>




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


<div id="nova-simulacao-container" class="mt-3 div-nova-simulacao mx-auto d-none p-3 border border-dark rounded" 
    style="width: 60%; height: 90%; overflow-y: auto; background-color: lightgray; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050;">

    <form id="form-nova-simulacao">
        <button type="button" onclick="fecharNovaSimulacao()" class="btn-close position-absolute top-0 end-0 m-2" aria-label="Fechar"></button> 
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
                <div id="inputs-container-categorias-nova" class="row"></div>
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
                    <button type="button" class="btn btn-salvar" style="width: 120px" id="btn-adicionar-lotes" onclick="Consulta_Engenharias()">
                        <span><i class="bi bi-plus"></i></span>
                        <span style="font-size: 12px;">Escolher</span>
                    </button>
                    <span id="TituloSelecaoEngenharias2">Todas Selecionadas</span>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-salvar" onclick="$('.input-categoria-2').val('0,00%')">
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


<div 
    id="informacoes-container" 
    class="mt-3 div-informacoes mx-auto p-3 border border-dark rounded d-none" 
    style="width: 60%; max-height: 80vh; overflow-y: auto; background-color: lightgray; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1050;">
    
    <form>
        <!-- Botão fechar -->
        <button 
            type="button" 
            onclick="fecharInformacoes()" 
            class="btn-close position-absolute top-0 end-0 m-2" 
            aria-label="Fechar">
        </button> 

        <p class="fs-4 fw-bold text-dark">INFORMAÇÕES</p> 



        <div class="mb-4 col-12" id="informacaoAtualizacao"> 
            <h6 class="fw-bold text-white bg-dark p-1">Atualização da Tendência</h6> 
            <div class="row"> 
            <h6>Cálculado no dia*: </h6>      
            <p><small><em>*A tendência é calculada manualmente pelo usuário e é utilizada para projetar cenários desejados de tendência de vendas no PCP</em></small></p>

            </div> 
        </div> 

        <div class="mb-4 col-12" id="informacaoSincronia"> 
            <h6 class="fw-bold text-white bg-dark p-1">Sincronia de Dados com ERP</h6> 
            <div class="row"> 
            <h6>Informativo de Vendas*: </h6>
            <p><small><em>* Sincroniza com o ERP os dados de vendas e faturamento, necessarios as análises dessa plataforma.</em></small></p>
            <h6>Estrutura da Materia Prima por Produto**: </h6>
            <p><small><em>** Sincroniza com o ERP os dados de Estrutura de Produto x Componentes Cadastrados, necessarios as análises dessa plataforma.</em></small></p>
            </div> 
        </div> 

        
        <div class="mb-4 col-12" id="informacaoHistoricoAtualizacoes"> 
            <h6 class="fw-bold text-white bg-dark p-1">Historico de Simulacoes projetadas </h6> 
            <div class="row"> 
            <h6>Lista de Simulacoes </h6>
            </div> 
        </div> 

        



    </form> 
</div>



<div class="modal fade" id="modal-categoria" tabindex="-1" aria-labelledby="modal-categoria" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoriaModalLabel">Escolha as Categorias MP</h5>
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


<!-- Modal para exibir imagem -->
<div class="modal fade modal-custom" id="modal-imagemMP" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-top modal-xl modal-grande2">
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


<div class="modal fade modal-custom" id="modal-detalhamentoSku" tabindex="-1" aria-labelledby="customModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-grande">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: black;" id="titulo-detalhamento";>Detalhamento Matéria Prima: </h5>
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
                                <th>Cód<br>Editado</br><input type="search" class="search-input search-input-detalhamentoSku"></th>
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

                                    <input type="search" class="search-input search-input-detalhamentoSku">
                                </th>
                                <th>
                                    <span
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="É o total de Matéria Prima distribuida para esse SKU, utilizada para saber o rendimento de PCs">
                                        Estoque MP.<br>Distr.</br>
                                    </span>
                                    <input type="search" class="search-input search-input-detalhamentoSku">
                                </th>
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


<?php
include_once('../../templates/footerPcp.php');
?>
<script src="script6.js"></script>
