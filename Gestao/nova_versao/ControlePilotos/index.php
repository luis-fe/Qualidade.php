<?php
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>
<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
</style>

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-stack"></i></span> Controle de Pilotos
</div>

<!-- ==================== FILTROS ==================== -->
<div class="col-12" style="margin-top: 1px;">    
    <div class="d-flex flex-wrap gap-3 align-items-end p-0">
   <div class="d-grid gap-2">
  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalRecebimentoPiloto">
    Movimentar Pilotos
</button>
  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalRetirarPilotoEAT">
    Retirar Pilotos_EAT
</button>
  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalInventariar">
    Inventariar local
</button>
</div>
    <!-- Cards de Totais -->
   <div class="card text-center bg-white" style="min-width: 100px;">
        <div class="card-body p-1">
            <h6 class="card-title mb-1 ">Peças Em Estoque</h6>
            <h5 class="card-text fw-bold" id="totalPecas"></h5>
        </div>
    </div>

    <div class="card text-center" style="min-width: 100px;">
            <div class="card-body p-1">
                <h6 class="card-title mb-1">Na Unid2</h6>
                <h5 class="card-text fw-bold text-primary" id="totalPecasUnid2"></h5>
            </div>

    </div>

        <div class="card text-center" style="min-width: 100px;">
            <div class="card-body p-1">
                <h6 class="card-title mb-1">Em Terceiros</h6>
                <h5 class="card-text fw-bold text-primary" id="totalPecas"></h5>
            </div>

    </div>

    
        <div class="card text-center" style="min-width: 100px;">
            <div class="card-body p-1">
                <h6 class="card-title mb-1">Na Montagem</h6>
                <h5 class="card-text fw-bold text-primary" id="totalPecas"></h5>
            </div>

    </div>


    <div class="card text-center" style="min-width: 100px;">
            <div class="card-body p-1">
                <h6 class="card-title mb-1">No EAT</h6>
                <h5 class="card-text fw-bold text-primary" id="totalPecas"></h5>
            </div>

    </div>

        <div class="card text-center" style="min-width: 100px;">
            <div class="card-body p-1">
                <h6 class="card-title mb-1">Em Transito</h6>
                <h5 class="card-text fw-bold text-primary" id="totalPecas"></h5>
            </div>

    </div>

    <div class="col-12 col-lg-2 bg-light p-0 border-end ms-auto">            
        <h5 class="text-center">Você Sabia:</h5>
            <a href="URL_DESTINO_DO_ANUNCIO" target="_blank">
            <img src="Imagem1.png" class="img-fluid rounded shadow-sm" alt="Descrição do Anúncio" style="width: 490px; height: 100px;"></a>
        </div>


</div>


<div class="modal fade" id="modalRecebimentoPiloto" tabindex="-1" aria-labelledby="modalRecebimentoPilotoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" id="modalRecebimentoPilotoLabel"> MOVIMENTACAO DE PILOTOS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="p-3 mb-4 border rounded">
                <div class="mb-3">
    <label for="tipoOperacao" class="form-label">Selecione o Tipo de Operação:</label>

                <select class="form-select" id="tipoOperacao" name="tipoOperacao" aria-label="Seleção de Tipo de Operação">
                    <option selected disabled value="">Escolha uma opção...</option>
                    
                    <option value="transferencia">Transferencia</option>
                    <option value="recebimento">Recebimento</option>
                </select>
            </div>
                </div>
                    <div class="d-flex align-items-center gap-2 d-none" id= "divDocumento">
                        <label for="documento" class="form-label mb-0">Número Documento:</label>
                        
                        <label id="valordocumento" class="form-label mb-0"><strong>xxxx</strong></label>
                        
                        <span class='ml-2 me-auto'>Outros documentos</span> 
                        
                    </div>
                <div id="div-informacoes" class="p-3 mb-4 border rounded d-none">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="inputMatricula" class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="inputMatricula" placeholder="Digite a matrícula">
                        </div>
                        <div class="col-md-6">
                            <label for="inputTag" class="form-label">Tag</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="inputTag" placeholder="Digite a tag">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="div-tabela" class="p-3 border rounded d-none">
                    <h6>**Tags Inseridas**</h6>
                    <table class="table table-striped table-hover table-bordered"  id="tabelaTagsInseridas">
                        <thead>
                            <tr>
                                <th scope="col">Tag Inserida</th>
                                <th scope="col">Data/Hora Inclusão</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            
                            </tr>
                        </tbody>
                    </table>
                </div>


                <div id="div-tabela2" class="p-3 border rounded d-none">
                    <h6>**Tags Recebimento**</h6>
                    <table class="table table-striped table-hover table-bordered" id="tabelaTagsRecebidas">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th scope="col">Tag Inserida</th>
                                <th scope="col">Data/Hora Inclusão</th>
                                <th scope="col">Transferido por:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary">Finalizar Recebimento</button>
            </div>
            
        </div>
    </div>
</div>



<div class="modal fade" id="modalInventariar" tabindex="-1" aria-labelledby="modalInventariarLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" id="modalRecebimentoPilotoLabel"> INVENTARIAR LOCAL</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="p-3 mb-4 border rounded">
                <div class="mb-3">
    <label for="tipoOperacao" class="form-label">Selecione o Local:</label>

                <select class="form-select" id="tipoOperacao" name="tipoOperacao" aria-label="Seleção de Tipo de Operação">
                    <option selected disabled value="">Escolha uma opção...</option>
                    
                    <option value="local1">Sala EAT</option>
                    <option value="local2">Montagem</option>
                </select>
            </div>
                </div>
                <div id="div-informacoes" class="p-3 mb-4 border rounded d-none">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="inputMatricula" class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="inputMatricula" placeholder="Digite a matrícula">
                        </div>
                        <div class="col-md-6">
                            <label for="inputTag" class="form-label">Tag</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="inputTag" placeholder="Digite a tag">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="div-tabela" class="p-3 border rounded d-none">
                    <h6>**Tags Inseridas**</h6>
                    <table class="table table-striped table-hover table-bordered"  id="tabelaTagsInseridas">
                        <thead>
                            <tr>
                                <th scope="col">Tag Inserida</th>
                                <th scope="col">Data/Hora Inclusão</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            
                            </tr>
                        </tbody>
                    </table>
                </div>

                
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
            
        </div>
    </div>
</div>



<div class="modal fade" id="modalRetirarPilotoEAT" tabindex="-1" aria-labelledby="modalRetirarPilotoEAT" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" id="modalRecebimentoPilotoLabel"> Retirar Piloto no EAT</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div id="div-informacoes" class="p-3 mb-4 border rounded">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="inputMatricula" class="form-label">Matrícula</label>
                            <input type="text" class="form-control" id="inputMatricula" placeholder="Digite a matrícula">
                        </div>
                        <div class="col-md-6">
                            <label for="labelNome" class="form-label">Nome</label>
                            <div class="input-group">
                            <label for="labelNomedescricao" class="form-label"> ---------------- </label>
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label for="inputTag" class="form-label">Tag</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="inputTag" placeholder="Digite a tag">
                            </div>
                        </div>

                    </div>
                </div>
                
                <div id="div-tabela" class="p-3 border rounded ">
                    <h6>**Tags na SALA**</h6>
                    <table class="table table-striped table-hover table-bordered"  id="tabelaTagsInseridas">
                        <thead>
                            <tr>
                                <th scope="col">Tag Inserida</th>
                                <th scope="col">Data/Hora Inclusão</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            
                            </tr>
                        </tbody>
                    </table>
                </div>

                
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
            
        </div>
    </div>
</div>



<div class="row mt-1 p-3 tabela-container">
        <div class="col-12">
            <table id="tabela_detalhamento" class="table table-hover table-bordered mt-1 tabela-fonte-pequena">
                <thead>
                    <tr>
                        <th>Cod<br>Prod.</th>
                        <th>Descrição<br></th>
                        <th>COR<br></th>
                        <th>TAM.<br></th>
                        <th>TAG<br></th>
                        <th>Status<br></th>  
                        <th>OP Atual<br></th>  
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                   
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>


<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script1.js"></script>

<script>
    // Se o seu código de controle de visibilidade estiver aqui,
    // ele funcionará perfeitamente, pois o $ já estará disponível.
    
    // Envolvemos a função jQuery no escopo 'async'
    $(async function() { // <-- Adicionado 'async' aqui!
        console.log("jQuery Pronto!");
        
        const $selectTipoOperacao = $('#tipoOperacao');
        const $divInformacoes = $('#div-informacoes');
        const $divTabela = $('#div-tabela');
        const $valorDocumentoLabel = $('#valordocumento'); // <-- CORREÇÃO: Variável da Label
        
        let doc_ = ''; // Valor padrão inicial

        // Chamada assíncrona para obter o documento.
        try {
            // Agora o 'await' funciona
            doc_ = await Gerar_doc_transf('1'); 
            console.log("Documento carregado:", doc_);
        } catch (error) {
            console.error("Não foi possível carregar o documento inicial:", error);
            doc_ = 'ERRO'; // Define um valor de erro em caso de falha
        }
        
        // Listener de mudança no SELECT
        $selectTipoOperacao.on('change', function() {
            const valorSelecionado = $selectTipoOperacao.val();
            
            if (valorSelecionado === 'transferencia') {
                
                // Atualiza a label com o valor obtido
                $valorDocumentoLabel.html(`<strong>${doc_}</strong>`);                
                $divInformacoes.removeClass('d-none');
                $divTabela.removeClass('d-none');
            } else {
                // Oculta tudo se não for 'transferencia'
                // O valor é redefinido para 'xxxx' (ou o valor original do HTML)
                $valorDocumentoLabel.text('--'); 
                
                $divInformacoes.addClass('d-none');
                $divTabela.addClass('d-none');
                //get_pilotos_em_transito();
            }
        }).trigger('change'); 



    });

</script>
