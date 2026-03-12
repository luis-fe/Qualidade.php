<?php
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
        <button type="button" class="btn btn-primary btn-sm text-nowrap" onclick="$('#modalConfigurarItens').modal('show');">
            <i class="bi bi-gear me-1"></i> Configurar Itens a Desconsiderar
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

<div class="modal fade" id="modalLoginMatricula" tabindex="-1" aria-labelledby="modalLoginMatriculaLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: 2px solid #003366;">
            <div class="modal-header text-white" style="background-color: #003366;">
                <h5 class="modal-title" id="modalLoginMatriculaLabel">
                    <i class="bi bi-person-badge me-2"></i> Identificação Necessária
                </h5>
            </div>
            
            <div class="modal-body text-center p-4">
                <i class="bi bi-shield-lock text-secondary mb-3" style="font-size: 3rem;"></i>
                <p class="text-muted mb-4">Informe sua matrícula para liberar os recursos do sistema.</p>
                
                <div class="form-group mb-4 px-3 text-start">
                    <label for="inputMatriculaLogin" class="fw-bold mb-1 text-primary">Matrícula:</label>
                    <input type="text" id="inputMatriculaLogin" class="form-control form-control-lg text-center fw-bold shadow-sm" placeholder="Ex: 12345" autocomplete="off">
                </div>
                
                <div class="text-center mb-2" style="min-height: 30px;">
                    <span id="labelNomeOperador" class="fs-5 text-muted fst-italic">Aguardando digitação...</span>
                </div>
            </div>
            
            <div class="modal-footer justify-content-center bg-light">
                <button type="button" id="btnAcessarSistema" class="btn btn-success btn-lg px-5 shadow-sm" disabled>
                    <i class="bi bi-box-arrow-in-right me-2"></i> Acessar Sistema
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalItensOP" tabindex="-1" aria-labelledby="modalItensOPLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #003366;">
                <h5 class="modal-title" id="modalItensOPLabel">
                    <i class="bi bi-list-check me-2"></i> Conferência de Itens - OP: <span id="spanNumeroOP" class="fw-bold text-warning"></span>
                </h5>

                <div class="ms-auto me-4 text-end d-none d-sm-block" style="font-size: 0.9rem;">
                    <i class="bi bi-person-check-fill text-info me-1"></i>
                    <span id="modalOpNomeUsuario" class="fw-bold text-light"></span> 
                    <span id="modalOpMatriculaUsuario" class="text-white-50 ms-1 small"></span>
                </div>
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
                                
                                <div class="d-flex gap-1 mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-light flex-fill fw-bold" onclick="limparConferencia()">
                                        <i class="bi bi-arrow-counterclockwise"></i> Limpar
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning flex-fill fw-bold text-dark" onclick="finalizarComPendencia()">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Pendência
                                    </button>
                                </div>
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

<div class="modal fade" id="modalConfirmarPendencia" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-question-circle-fill me-2"></i> Confirmar Finalização
                </h5>
            </div>
            
            <div class="modal-body text-center p-4">
                <i class="bi bi-exclamation-octagon text-warning mb-3" style="font-size: 3.5rem;"></i>
                <h5 class="fw-bold">Deseja finalizar com pendência?</h5>
                <p class="text-muted">Esta ação encerrará a conferência da OP mesmo com itens faltando.</p>
            </div>
            
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Não
                </button>
                <button type="button" class="btn btn-warning px-4 fw-bold text-dark" onclick="efetivarFinalizacaoPendencia()">
                    <i class="bi bi-check-lg me-1"></i> Sim, Finalizar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPendenciaSucesso" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Conferência com Pendência
                </h5>
            </div>
            
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i class="bi bi-clipboard-x text-warning" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold">Finalizado com Pendência!</h4>
                <p class="text-muted">A OP foi encerrada e o som de confirmação foi emitido.</p>
            </div>
            
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-dark px-5 fw-bold" data-bs-dismiss="modal">
                    <i class="bi bi-check2-all me-1"></i> OK, Entendido
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
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Sair
                </button>
                <button type="button" class="btn btn-success px-3 fw-bold" onclick="efetivarConferencia()">
                    <i class="bi bi-check-lg me-1"></i> Efetivar Total
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfigurarItens" tabindex="-1" aria-labelledby="modalConfigurarItensLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #003366;">
                <h5 class="modal-title" id="modalConfigurarItensLabel">
                    <i class="bi bi-node-minus-fill me-2"></i> Configurar Itens a Serem Desconsiderados
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Lista de materiais ignorados na conferência</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" onclick="adicionarLinhaDesconsiderar()">
                        <i class="bi bi-plus-lg me-1"></i> Adicionar Item
                    </button>
                </div>

                <div class="table-responsive shadow-sm" style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 6px;">
                    <table class="table table-bordered table-striped mb-0 text-nowrap" id="table-itens-desconsiderados">
                        <thead style="position: sticky; top: 0; background-color: #e9ecef; z-index: 1;">
                            <tr>
                                <th class="text-center">Código do Material</th>
                                <th>Nome do Material</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
            
            <div class="modal-footer justify-content-between bg-light">
                <button type="button" class="btn btn-secondary px-4 shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success px-4 shadow-sm" onclick="salvarItensDesconsiderados()">
                    <i class="bi bi-save me-1"></i> Salvar Configuração
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