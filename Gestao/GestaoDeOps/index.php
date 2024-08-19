<?php
include_once("../../templates/header1.php");
include_once("../../templates/loading1.php");
?>
<link rel="stylesheet" href="style.css">
<style>
    @media (max-width: 1100px) {

        #cards,
        #cardsPrioridades {
            display: block;
            flex-direction: column;
            margin-bottom: 10px;
            align-items: center !important;
        }

        #card,
        .legenda,
        #Botoes {
            min-width: 80% !important;
            margin-bottom: 5px;
        }

        #Botoes{
            text-align: center;
            justify-content: center;
            align-items: center !important;
        }


    }
</style>

<label for="" style="font-size: 30px; font-weight: 700; color: black;">Fila de Fases</label>
<div class="container" style="background-color: rgb(179, 179, 179); border-radius: 5px; height: calc(100vh - 160px); max-height: calc(100vh - 160px); overflow: auto; width: 100%; min-width: 100%; padding-top: 25px; padding-left: 30px;">
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
   <div class="row col-12 mb-2 d-flex justify-content-center align-items-center" id="cardsPrioridades">
        <!-- Container para o filtro e botões -->
        <div class="d-flex flex-wrap align-items-center col-12 mb-2 col-md-2" id="Botoes">
            <i class="fa-solid fa-filter mr-3" id="BotaoFiltros" title="Filtros" style="font-size: 30px; cursor: pointer;" onclick="$('#modalFiltros').modal('show');"></i>
            <i class="fa-solid fa-download mr-3" id="BotaoExcel" title="Exportar Excel" style="font-size: 30px; cursor: pointer;" onclick="ExportarExcel()"></i>
            <i class="fa-solid fa-ban mr-3" title="Remover Filtro de Prioridade" style="font-size: 30px; cursor: pointer;" onclick="Prioridade1 = ''; Prioridade2 = ''; Status = ''; FiltrarDadosPrioridade()"></i>
        </div>

        <!-- Container para os cards -->
        <div class="d-flex flex-wrap col-12 col-md-10 justify-content-center" id="cardsPrioridades">
            <div class="card legenda blink d-none col-1 Claudino align-items-center justify-content-center" id="legenda" onclick="Prioridade1 = 'CLAUDINO'; FiltrarDadosPrioridade()">
                <label for="" style="cursor:pointer">CLAUDINO</label>
            </div>
            <div class="card legenda blink d-none col-1 Avista align-items-center justify-content-center" id="legenda" onclick="Prioridade1 = 'A VISTA ANTECIPADO'; FiltrarDadosPrioridade()">
                <label for="" style="cursor:pointer">A VISTA</label>
            </div>
            <div class="card legenda blink d-none col-1 FatAtrasado align-items-center justify-content-center" id="legenda" onclick="Prioridade1 = 'FAT ATRASADO'; FiltrarDadosPrioridade()">
                <label for="" style="cursor:pointer">FAT ATRASADO</label>
            </div>
            <div class="card legenda col-1 d-flex align-items-center justify-content-center" id="legenda" style="background-color: red;" onclick="Prioridade1 = 'P/FAT.'; FiltrarDadosPrioridade()">
                <label for="" style="cursor:pointer">P/ FAT.</label>
            </div>
            <div class="card legenda col-1 d-flex align-items-center justify-content-center" id="legenda" style="background-color: red;" onclick="Prioridade1 = 'URGENTE'; FiltrarDadosPrioridade()">
                <label for="" style="cursor:pointer">URGENTE</label>
            </div>
            <div class="card legenda col-1 d-flex align-items-center justify-content-center" id="legenda" onclick="Prioridade1 = 'QM1'; Prioridade2 = 'QP1'; FiltrarDadosPrioridade()">
                <label for="" style="cursor:pointer">QM1/QP1</label>
            </div>
            <div class="card legenda col-1 d-flex align-items-center justify-content-center" id="legenda" onclick="Prioridade1 = 'QM2'; Prioridade2 = 'QP2'; FiltrarDadosPrioridade()">
                <label for="" style="cursor:pointer">QM2/QP2</label>
            </div>
            <div class="card legenda col-1 d-flex align-items-center justify-content-center" id="legenda" onclick="Prioridade1 = 'QM3'; Prioridade2 = 'QP3'; FiltrarDadosPrioridade()">
                <label for="" style="cursor:pointer">QM3/QP3</label>
            </div>
            <div class="card legenda col-1 d-flex align-items-center justify-content-center" id="legenda" onclick="Prioridade1 = 'QM4'; Prioridade2 = 'QP4'; FiltrarDadosPrioridade()">
                <label for="" style="cursor:pointer">QM4/QP4</label>
            </div>
            <div class="card legenda col-1 d-flex align-items-center justify-content-center" id="legenda" onclick="Prioridade1 = 'QM5'; Prioridade2 = 'QP5'; FiltrarDadosPrioridade()">
                <label for="" style="cursor:pointer">QM5/QP5</label>
            </div>
            <div class="card legenda col-1 d-flex align-items-center justify-content-center" id="legenda" onclick="Prioridade1 = 'QM6'; Prioridade2 = 'QP6'; FiltrarDadosPrioridade()">
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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



<?php include_once("../../templates/footer1.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="script1.js"></script>
