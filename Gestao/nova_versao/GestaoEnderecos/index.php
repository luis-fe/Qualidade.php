<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>

<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">

<div class="titulo-tela d-flex justify-content-between align-items-center mb-3">
    
    <div>
        <span class="span-icone"><i class="bi bi-bullseye"></i></span> 
        Gestao de Enderecamento - <strong>Almoxarifado Aviamentos</strong>
    </div>

    <div class="d-flex align-items-center gap-2">
        
 

        <button type="button" class="btn btn-primary btn-sm text-nowrap" onclick="abrirModalInserirEndereco();">
            <i class="bi bi-person-plus me-1"></i> Inserir novo Endereço
        </button>

    </div>

</div>



<div class="col-12 div-metas" style="background-color: lightgray; border-radius: 8px; padding: 10px;">
    <div class="div-tabela" style="max-width: 100%; overflow: auto; max-height: 800px; border-radius: 8px;">
        <table class="table table-bordered table-striped" id="table-metas" style="width: 100%;">
        <thead style="position: sticky; top: 0; background-color: #003366; z-index: 10;">
                <tr>
                    <th>Endereco</th>
                    <th>Rua</th>
                    <th>Quadra</th>
                    <th>Posicao</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalInserirEndereco" tabindex="-1" aria-labelledby="modalInserirEnderecoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header text-white" style="background-color: #003366;">
                <h5 class="modal-title" id="modalInserirEnderecoLabel">
                    <i class="bi bi-geo-alt-fill me-2"></i> Inserir Endereço
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                
                <div class="d-flex justify-content-center mb-4">
                    <div class="btn-group" role="group" aria-label="Tipo de Inserção">
                        <input type="radio" class="btn-check" name="tipoInsercao" id="radioIndividual" value="individual" autocomplete="off" checked>
                        <label class="btn btn-outline-primary px-4" for="radioIndividual">Inserir Individual</label>

                        <input type="radio" class="btn-check" name="tipoInsercao" id="radioMassa" value="massa" autocomplete="off">
                        <label class="btn btn-outline-primary px-4" for="radioMassa">Inserir em Massa</label>
                    </div>
                </div>

                <div id="section-individual">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Dados do Endereço</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="indRua" class="form-label fw-bold">Rua</label>
                            <input type="text" class="form-control" id="indRua" placeholder="Ex: A">
                        </div>
                        <div class="col-md-4">
                            <label for="indQuadra" class="form-label fw-bold">Quadra</label>
                            <input type="text" class="form-control" id="indQuadra" placeholder="Ex: 01">
                        </div>
                        <div class="col-md-4">
                            <label for="indPosicao" class="form-label fw-bold">Posição</label>
                            <input type="text" class="form-control" id="indPosicao" placeholder="Ex: 10">
                        </div>
                    </div>
                </div>

                <div id="section-massa" class="d-none">
                    <h6 class="text-muted border-bottom pb-2 mb-3">Intervalo de Endereços</h6>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="masRuaIni" class="form-label fw-bold text-success">Rua Inicial</label>
                            <input type="text" class="form-control border-success" id="masRuaIni" placeholder="Ex: A">
                        </div>
                        <div class="col-md-6">
                            <label for="masRuaFim" class="form-label fw-bold text-danger">Rua Final</label>
                            <input type="text" class="form-control border-danger" id="masRuaFim" placeholder="Ex: D">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="masQuadraIni" class="form-label fw-bold text-success">Quadra Inicial</label>
                            <input type="text" class="form-control border-success" id="masQuadraIni" placeholder="Ex: 1">
                        </div>
                        <div class="col-md-6">
                            <label for="masQuadraFim" class="form-label fw-bold text-danger">Quadra Final</label>
                            <input type="text" class="form-control border-danger" id="masQuadraFim" placeholder="Ex: 5">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="masPosicaoIni" class="form-label fw-bold text-success">Posição Inicial</label>
                            <input type="text" class="form-control border-success" id="masPosicaoIni" placeholder="Ex: 1">
                        </div>
                        <div class="col-md-6">
                            <label for="masPosicaoFim" class="form-label fw-bold text-danger">Posição Final</label>
                            <input type="text" class="form-control border-danger" id="masPosicaoFim" placeholder="Ex: 20">
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success px-4" onclick="salvarEnderecos()">
                    <i class="bi bi-save me-1"></i> Salvar
                </button>
            </div>
            
        </div>
    </div>
</div>


<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script.js"></script>