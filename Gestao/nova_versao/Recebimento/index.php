<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
    @media print {
        /* Esconde elementos de interface */
        .no-print, header, footer, .btn, .titulo-tela, #loadingModal, .span-icone { display: none !important; }
        
        body { margin: 0 !important; padding: 0 !important; background-color: white !important; }
        body * { visibility: hidden; }
        #container-cards, #container-cards * { visibility: visible; }

        @page {
            /* Aumentei aqui para 15cm para comportar o card maior */
            size: 15.0cm 4.0cm !important; 
            margin: 0 !important;
        }

        #container-cards {
            /* Definindo a largura do container para bater com a página */
            width: 15.0cm !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
        }

        .card-etiqueta {
            /* Aumentei a largura para 15cm e a altura para 4cm para dar mais respiro */
            width: 15.0cm !important; 
            height: 4.0cm !important;
            page-break-after: always !important;
            page-break-inside: avoid !important;
            break-after: page !important;
            display: block !important;
            border: none !important;
            box-shadow: none !important;
        }

        .card-body-custom {
            display: flex !important;
            flex-direction: row !important;
            width: 100% !important;
            height: 100% !important;
            /* Ajuste o padding conforme necessário para centralizar o conteúdo no novo tamanho */
            padding: 0.1cm 0.5cm !important; 
            box-sizing: border-box !important;
        }
    }
</style>

<div class="titulo-tela d-flex justify-content-between align-items-center mb-3">
    <div>
        <span class="span-icone"><i class="bi bi-bullseye"></i></span> 
        Recebimento - <strong>Almoxarifado Aviamentos</strong>
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
            <input type="text" id="filtroCodigo" class="form-control form-control-sm border-primary" placeholder="🔍 Filtrar Codigo...">
        </div>
        <div class="col-md-3">
            <input type="text" id="filtroDescricao" class="form-control form-control-sm border-primary" placeholder="🔍 Filtrar Descricao...">
        </div>
        <div class="col-md-3">
            <input type="text" id="filtroFornecedor" class="form-control form-control-sm border-primary" placeholder="🔍 Filtrar Fornecedor...">
        </div>
        <div class="col-md-3">
            <input type="text" id="filtroCategoria" class="form-control form-control-sm border-primary" placeholder="🔍 Filtrar Categoria...">
        </div>
    </div>

    <div class="div-tabela" style="max-width: 100%; overflow: auto; max-height: 90%; border-radius: 8px;">
        <table class="table table-bordered table-striped" id="table-metas" style="width: 100%;">
            <thead style="position: sticky; top: 0; background-color: #003366; color: white; z-index: 10;">
                <tr>
                    <th>Codigo</br>Reduzido</th>
                    <th>Codigo Material</th>
                    <th>Descricao Material</th>
                    <th>Fornecedor</th>
                    <th>Unidade</br>Medida</th>
                    <th>Saldo</br>a Repor</th>
                    <th>Categoria</th>
                    <th>Montar</br>Kit</th>
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