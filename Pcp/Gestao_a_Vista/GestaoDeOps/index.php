<?php
include_once("requests.php");
include_once("../../../templates/headsGestao.php");
include("../../../templates/LoadingGestao.php");
?>
<link rel="stylesheet" href="style.css">


<div class="container-fluid" id="form-container">
    <div class="Corpo auto-height">
        <div class="row text-align-center justify-content-center mb-1">
            <div class="col-sm-6 col-md-2">
                <div class="card">
                    <div class="card-body" id="cardInfos1" style="background-color: #2498db;">
                        <h5 class="card-title">Qtd. Op's Abertas:</h5>
                        <p class="card-text" id="text1"></p>
                    </div>
                </div>
            </div>

            <!-- Card: Qtd. Peças -->
            <div class="col-sm-6 col-md-2">
                <div class="card">
                    <div class="card-body" id="cardInfos2" style="background-color: #2498db;">
                        <h5 class="card-title">Qtd. Peças:</h5>
                        <p class="card-text" id="text2"></p>
                    </div>
                </div>
            </div>

            <!-- Card: Qtd. Op's no Prazo -->
            <div class="col-sm-6 col-md-2">
                <div class="card" onclick="Status = '0-Normal'; FiltrarDadosPrioridade()">
                    <div class="card-body" id="cardInfos3" style="background-color: rgb(40, 167, 69)">
                        <h5 class="card-title">Qtd. Op's no Prazo:</h5>
                        <p class="card-text" id="text3"></p>
                    </div>
                </div>
            </div>

            <!-- Card: Qtd. Op's em Atenção -->
            <div class="col-sm-6 col-md-2">
                <div class="card" onclick="Status = '1-Atencao'; FiltrarDadosPrioridade()">
                    <div class="card-body" id="cardInfos4" style="background-color: rgb(255, 193, 7)">
                        <h5 class="card-title">Qtd. Op's em Atenção:</h5>
                        <p class="card-text" id="text4"></p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-2" onclick="Status = '2-Atrasado'; FiltrarDadosPrioridade()">
                <div class="card">
                    <div class="card-body" id="cardInfos5" style="background-color: rgb(220, 53, 69)">
                        <h5 class="card-title">Qtd. Op's Atrasadas:</h5>
                        <p class="card-text" id="text5"></p>
                    </div>
                </div>
            </div>

        </div>
        <div class="row col-12 col-md-12 d-flex align-items-end mb-2">
            <i class="fa-solid fa-filter col-1" id="BotaoFiltros" title="Filtros" style="font-size: 30px; cursor: pointer" onclick="$('#modalFiltros').modal('show');"></i>
            <div class="col-1 d-flex">
                <i class="fa-solid fa-download col-6" id="BotaoExcel" title="Exportar Excel" style="font-size: 30px; cursor: pointer" onclick="ExportarExcel()"></i>
                <i class="fa-solid fa-ban col-6" title="Remover Filtro de Prioridade" style="font-size: 30px; cursor: pointer" onclick="Prioridade1 = ''; Prioridade2 = ''; Status = ''; FiltrarDadosPrioridade()"></i>
            </div>

            <div class="row legenda col-10">
                <div class="card legenda blink col-1 Claudino d-none" id="legenda" onclick="Prioridade1 = 'CLAUDINO'; FiltrarDadosPrioridade()">
                    <label for="" style="cursor:pointer">CLAUDINO</label>
                </div>
                <div class="card legenda blink col-1 Avista d-none" id="legenda" onclick="Prioridade1 = 'A VISTA ANTECIPADO'; FiltrarDadosPrioridade()">
                    <label for="" style="cursor:pointer">A VISTA</label>
                </div>
                <div class="card legenda blink col-1 FatAtrasado d-none" id="legenda" onclick="Prioridade1 = 'FAT ATRASADO'; FiltrarDadosPrioridade()">
                    <label for="" style="cursor:pointer">FAT ATRASADO</label>
                </div>
                <div class="card legenda col-1" id="legenda" style="background-color: red" onclick="Prioridade1 = 'P/FAT.'; FiltrarDadosPrioridade()">
                    <label for="" style="cursor:pointer">P/ FAT.</label>
                </div>
                <div class="card legenda col-1" id="legenda" style="background-color: red" onclick="Prioridade1 = 'URGENTE'; FiltrarDadosPrioridade()">
                    <label for="" style="cursor:pointer">URGENTE</label>
                </div>
                <div class="card legenda col-1" id="legenda" onclick="Prioridade1 = 'QM1'; Prioridade2 = 'QP1'; FiltrarDadosPrioridade()">
                    <label for="" style="cursor:pointer">QM1/QP1</label>
                </div>
                <div class="card legenda col-1" id="legenda" onclick="Prioridade1 = 'QM2'; Prioridade2 = 'QP2'; FiltrarDadosPrioridade()">
                    <label for="" style="cursor:pointer">QM2/QP2</label>
                </div>
                <div class="card legenda col-1" id="legenda" onclick="Prioridade1 = 'QM3'; Prioridade2 = 'QP3'; FiltrarDadosPrioridade()">
                    <label for="" style="cursor:pointer">QM3/QP3</label>
                </div>
                <div class="card legenda col-1" id="legenda" onclick="Prioridade1 = 'QM4'; Prioridade2 = 'QP4'; FiltrarDadosPrioridade()">
                    <label for="" style="cursor:pointer">QM4/QP4</label>
                </div>
                <div class="card legenda col-1" id="legenda" onclick="Prioridade1 = 'QM5'; Prioridade2 = 'QP5'; FiltrarDadosPrioridade()">
                    <label for="" style="cursor:pointer">QM5/QP5</label>
                </div>
                <div class="card legenda col-1" id="legenda" onclick="Prioridade1 = 'QM6'; Prioridade2 = 'QP6'; FiltrarDadosPrioridade()">
                    <label for="" style="cursor:pointer">QM6/QP6</label>
                </div>
            </div>
        </div>
       <div class="row col-12" style="height: 68vh; max-height: 68vh; background-color: lightgray; border: 1px solid black; border-radius: 10px; width: 100%; min-width: 100%">
            <h2>Lista de Op's</h2>
            <div class="row" id="Corpo" style="height: 61vh; max-height: 61vh; min-height: 61vh; overflow: auto">
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalFiltros" tabindex="-1" role="dialog" aria-labelledby="modalFiltrosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered-top" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFiltrosLabel">Filtros de Relatório</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulário com filtros -->
                <form id="formFiltros" style="text-align: left;">
                    <div class="form-group">
                        <label for="InputContem">Contém:</label>
                        <input type="text" class="form-control" id="InputContem">
                    </div>
                    <div class="form-group" style="align-items: center; text-align: center; justify-content: center;">
                        <label>Ordenação:</label>
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-primary active">
                                <input type="radio" name="opcoesOrdenacao" id="btnUrgente" data-valor="prioridade" autocomplete="off" checked> Prioridade
                            </label>
                            <label class="btn btn-outline-primary">
                                <input type="radio" name="opcoesOrdenacao" id="btnLeadTime" data-valor="tempo" autocomplete="off"> Lead Time
                            </label>
                        </div>
                    </div>
                    <div class="form-group" style="font-size: 20px; font-weight: 500">Coleções</div>
                    <div class="form-group" id="colecoes">
                    </div>
                    <!-- Adicione mais campos de filtro conforme necessário -->
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary" onclick="aplicarFiltros()">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>

<div class="ModalPendencia" id="ModalPendencia">
    <span class="close" id="FecharPendencia">&times;</span>
    <div style="margin-bottom: 20px; margin-right: 30px; justify-content: center; align-items: center; text-align: center;">
        <label for="text" id="TituloModal" style="font-size: 25px;">Pendências</label>
    </div>
    <div style="display: flex; flex-direction: column;" class="DivPendencias" id="DivPendencias">
    </div>
</div>

<div class="modal fade" id="ModalJustificativa" tabindex="-1" role="dialog" aria-labelledby="ModalJustificativaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="text-align: left;">
            <div class="modal-header">
                <h5 class="modal-title" id="NumeroOP">Justificativa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#ModalJustificativa').modal('hide')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="InputJustificativa" class="col-form-label" style="font-size: 25px;">Justificativa:</label>
                    <textarea class="form-control" name="InputJustificativa" id="InputJustificativa" rows="4" style="font-size: 20px;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="SalvarObs" onclick="SalvarJustificativa()">Salvar <i class="bi bi-check-circle-fill"></i></button>
            </div>
        </div>
    </div>
</div>



<?php include_once("../../../templates/footer copy.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="script.js"></script>
