<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
@media print {
    /* Esconde elementos que não devem ir pro papel */
    .no-print { display: none !important; }

    body * { visibility: hidden; }
    #container-cards, #container-cards * { visibility: visible; }
    
    #container-cards {
        position: absolute;
        left: 0;
        top: 0;
        width: 10.1cm; 
        margin: 0 !important;
        padding: 0 !important;
    }

    .card-etiqueta {
        width: 10.1cm;
        height: 2.6cm;
        page-break-after: always;
        border: none !important;
        box-shadow: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* ZERA A MARGEM DO NAVEGADOR */
    @page {
        size: 10.6cm 2.6cm;
        margin: 0 !important; 
    }
    
    body {
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Garante que imagens sempre sejam impressas e no tamanho certo */
    img {
        visibility: visible !important;
        display: block !important;
        opacity: 1 !important;
    }

    /* Força o tamanho do QR Code no papel */
    .img-qrcode {
        width: 100px !important;
        height: 100px !important;
    }
}
</style>

<div class="titulo-tela d-flex justify-content-between align-items-center mb-3">
    <div>
        <span class="span-icone"><i class="bi bi-bullseye"></i></span> 
        <strong>CONFERENCIA DE AVIAMENTOS</strong>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-warning btn-sm text-nowrap" onclick="imprimirSelecionados();">
            <i class="bi bi-printer me-1"></i> Imprimir Etiquetas
        </button>

        <button type="button" class="btn btn-primary btn-sm text-nowrap" onclick="abrirModalInserirEndereco();">
            <i class="bi bi-person-plus me-1"></i> Configurar Categorias
        </button>
    </div>
</div>

<div id="container-cards" class="d-none d-flex flex-wrap gap-2 mt-3 p-2"></div>


<div class="col-12 div-metas" style="background-color: lightgray; border-radius: 8px; padding: 10px;">
    
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="text" id="filtroNumeroOP" class="form-control form-control-sm border-primary" placeholder="🔍 Filtrar Numero OP...">
        </div>
        <div class="col-md-3">
            <input type="text" id="filtroCodigoProduto" class="form-control form-control-sm border-primary" placeholder="🔍 Filtrar Codigo Produto...">
        </div>
        <div class="col-md-3">
            <input type="text" id="filtroDescricao" class="form-control form-control-sm border-primary" placeholder="🔍 Filtrar Descricao...">
        </div>
        <div class="col-md-3">
            <input type="text" id="filtroSeparador" class="form-control form-control-sm border-primary" placeholder="🔍 Filtrar Separador...">
        </div>
    </div>

    <div class="div-tabela" style="max-width: 100%; overflow: auto; max-height: 90%; border-radius: 8px;">
        <table class="table table-bordered table-striped" id="table-metas" style="width: 100%;">
            <thead style="position: sticky; top: 0; background-color: #003366; color: white; z-index: 10;">
                <tr>
                    <th>Numero</br>OP</th>
                    <th>Codigo</br>Produto</th>
                    <th>Descricao</th>
                    <th>Prioridade:</th>
                    <th>Fase Atual</th>
                    <th>Separado Por:</th>
                    <th>Conferir</br></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalItensOP" tabindex="-1" aria-labelledby="modalItensOPLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #003366;">
                <h5 class="modal-title" id="modalItensOPLabel">
                    <i class="bi bi-list-check me-2"></i> Conferência de Itens - OP: <span id="spanNumeroOP" class="fw-bold text-warning"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body bg-light d-flex flex-column p-3">
                
                <div class="row mb-4 justify-content-center mt-2 align-items-center">
                    
                    <div class="col-md-6">
                        <div class="input-group input-group-lg shadow-sm" style="border: 2px solid #003366; border-radius: 0.5rem; overflow: hidden;">
                            <span class="input-group-text bg-white" style="border: none; border-right: 1px solid #ccc;">
                                <i class="bi bi-upc-scan fs-4 text-primary"></i>
                            </span>
                            
                            <input type="text" id="inputQrCode" class="form-control" placeholder="Bipe o QR Code (OP||Material)" autocomplete="off" style="border: none; font-size: 1.2rem;">
                            
                            <button class="btn text-white fw-bold px-4" type="button" id="btnBuscarQrCode" style="background-color: #003366; border: none;">
                                Conferir
                            </button>
                        </div>
                        <div class="text-center mt-2 small text-muted">
                            <i class="bi bi-info-circle me-1"></i> Aguardando leitura do bipador...
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card text-white text-center shadow-sm" style="background-color: #17a2b8; border: none; border-radius: 0.5rem;">
                            <div class="card-body p-2">
                                <span class="d-block small text-uppercase fw-bold mb-1" style="letter-spacing: 1px;">Progresso da OP</span>
                                <strong class="fs-3">
                                    <span id="contadorBipados">0</span> / <span id="contadorTotal">0</span>
                                </strong>
                                <button type="button" class="btn btn-sm btn-outline-light mt-2 w-100 fw-bold" onclick="limparConferencia()">
                                    <i class="bi bi-arrow-counterclockwise"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="table-responsive flex-grow-1" style="height: 0; overflow-y: auto;">
                    <table class="table table-bordered table-striped" id="table-itens-conferencia" style="width: 100%; margin-bottom: 0;">
                        <thead style="position: sticky; top: 0; background-color: #003366; color: white; z-index: 10;">
                            <tr>
                                <th>Número OP</th>
                                <th>Código Material</th>
                                <th>Nome Material</th>
                                <th>Localização</th> 
                                <th>Separado por</th>
                                <th>Qtde</th>
                                <th>Saldo Estoque</th>
                            </tr>
                        </thead>
                        <tbody>
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

<div class="modal fade" id="modalSucesso" tabindex="-1" aria-labelledby="modalSucessoLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalSucessoLabel">
                    <i class="bi bi-check-circle-fill me-2"></i>Conferência Concluída
                </h5>
            </div>
            
            <div class="modal-body text-center p-4">
                <i class="bi bi-award text-success mb-3" style="font-size: 4rem;"></i>
                <h4 class="mb-3">Conferência finalizada com sucesso!</h4>
                <p class="text-muted">Todos os itens desta OP foram bipados corretamente. Deseja efetivar a conferência no sistema?</p>
            </div>
            
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success px-4" onclick="efetivarConferencia()">
                    <i class="bi bi-check-lg me-1"></i> Efetivar
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalConfirmarExclusao" tabindex="-1" aria-labelledby="modalConfirmarExclusaoLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalConfirmarExclusaoLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Atenção
                </h5>
            </div>
            
            <div class="modal-body text-center p-4">
                <h5 class="mb-3">Item já bipado!</h5>
                <p>O material <strong id="spanMaterialExcluir" class="text-danger"></strong> já foi conferido.</p>
                <p class="text-muted mb-0">Deseja excluir a conferência deste item e voltar a contagem?</p>
            </div>
            
            <div class="modal-footer justify-content-center">
                <button type="button" id="btnNaoExcluir" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Não
                </button>
                <button type="button" class="btn btn-danger px-4" onclick="excluirBipagem()">
                    <i class="bi bi-trash me-1"></i> Sim, excluir
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalErroBipagem" tabindex="-1" aria-labelledby="modalErroBipagemLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalErroBipagemLabel">
                    <i class="bi bi-x-octagon-fill me-2"></i>Erro de Leitura
                </h5>
            </div>
            
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-circle text-danger mb-3" style="font-size: 4rem;"></i>
                <h5 class="mb-3" id="textoErroBipagem"></h5>
            </div>
            
            <div class="modal-footer justify-content-center">
                <button type="button" id="btnFecharErroBipagem" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-return-left me-1"></i> Voltar
                </button>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script.js"></script>