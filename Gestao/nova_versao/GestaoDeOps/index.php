<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>
<!-- Adicione o CSS do Select2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<link rel="stylesheet" href="style.css">
<style>
</style>

<div class="titulo-tela">
    <span class="span-icone"><i class="bi bi-stack"></i></span> Gestão de Op's
</div>

<div class="container-fluid mt-2" id="form-container">
    <div class="auto-height">
        <div class="row text-align-center justify-content-center mb-1">
            <div class="" style="min-width: 220px; max-width: 220px;">
                <div class="card">
                    <div class="card-body" id="cardInfos1" style="background-color: #2498db;">
                        <h5 class="card-title">Op's Abertas:</h5>
                        <p class="card-text" id="text1"></p>
                    </div>
                </div>
            </div>

            <!-- Card: Qtd. Peças -->
            <div class="" style="min-width: 220px; max-width: 220px;">
                <div class="card">
                    <div class="card-body" id="cardInfos2" style="background-color: #2498db;">
                        <h5 class="card-title">Qtd. Peças:</h5>
                        <p class="card-text" id="text2"></p>
                    </div>
                </div>
            </div>

            <!-- Card: Qtd. Op's no Prazo -->
            <div class="" style="min-width: 220px; max-width: 220px;">
                <div class="card" onclick="Status = '0-Normal'; FiltrarDadosPrioridade()">
                    <div class="card-body" id="cardInfos3" style="background-color: rgb(40, 167, 69)">
                        <h5 class="card-title">Op's no Prazo:</h5>
                        <p class="card-text" id="text3"></p>
                    </div>
                </div>
            </div>

            <!-- Card: Qtd. Op's em Atenção -->
            <div class="" style="min-width: 220px; max-width: 220px;">
                <div class="card" onclick="Status = '1-Atencao'; FiltrarDadosPrioridade()">
                    <div class="card-body" id="cardInfos4" style="background-color: rgb(255, 193, 7)">
                        <h5 class="card-title">Op's em Atenção:</h5>
                        <p class="card-text" id="text4"></p>
                    </div>
                </div>
            </div>

            <div class="" style="min-width: 220px; max-width: 220px;">
                <div class="card" onclick="Status = '2-Atrasado'; FiltrarDadosPrioridade()">
                    <div class="card-body" id="cardInfos5" style="background-color: rgb(220, 53, 69)">
                        <h5 class="card-title">Op's Atrasadas:</h5>
                        <p class="card-text" id="text5"></p>
                    </div>
                </div>
            </div>

        </div>
        <div class="row col-12 col-md-12 d-flex align-items-end mb-2">
            <div class="d-flex" style="justify-content: center">
                <div class="row legenda mt-2">
                    <div class="card legenda blink Claudino d-none" style="min-width: 120px; max-width: 120px;" id="legenda" onclick="Prioridade1 = 'CLAUDINO'; FiltrarDadosPrioridade()">
                        <label for="" style="cursor:pointer">CLAUDINO</label>
                    </div>
                    <div class="card legenda blink Avista d-none" id="legenda" style="min-width: 140px; max-width: 140px;" onclick="Prioridade1 = 'A VISTA ANTECIPADO'; FiltrarDadosPrioridade()">
                        <label for="" style="cursor:pointer">ANTECIPADO</label>
                    </div>
                    <div class="card legenda blink FatAtrasado d-none" style="min-width: 155px; max-width: 155px;" id="legenda" onclick="Prioridade1 = 'FAT ATRASADO'; FiltrarDadosPrioridade()">
                        <label for="" style="cursor:pointer">FAT ATRASADO</label>
                    </div>
                    <div class="card legenda" id="legenda" style="background-color: red; min-width: 85px; max-width: 85px;" onclick="Prioridade1 = 'P/FAT.'; FiltrarDadosPrioridade()">
                        <label for="" style="cursor:pointer">P/ FAT.</label>
                    </div>
                    <div class="card legenda" id="legenda" style="background-color: red; min-width: 110px; max-width: 110px;" onclick="Prioridade1 = 'URGENTE'; FiltrarDadosPrioridade()">
                        <label for="" style="cursor:pointer">URGENTE</label>
                    </div>
                    <div class="card legenda" id="legenda" style="min-width: 110px; max-width: 110px;" onclick="Prioridade1 = 'QM1'; Prioridade2 = 'QP1'; FiltrarDadosPrioridade()">
                        <label for="" style="cursor:pointer">QM1/QP1</label>
                    </div>
                    <div class="card legenda" id="legenda" style="min-width: 110px; max-width: 110px;" onclick="Prioridade1 = 'QM2'; Prioridade2 = 'QP2'; FiltrarDadosPrioridade()">
                        <label for="" style="cursor:pointer">QM2/QP2</label>
                    </div>
                    <div class="card legenda" id="legenda" style="min-width: 110px; max-width: 110px;" onclick="Prioridade1 = 'QM3'; Prioridade2 = 'QP3'; FiltrarDadosPrioridade()">
                        <label for="" style="cursor:pointer">QM3/QP3</label>
                    </div>
                    <div class="card legenda" id="legenda" style="min-width: 115px; max-width: 115px;" onclick="Prioridade1 = 'QM4'; Prioridade2 = 'QP4'; FiltrarDadosPrioridade()">
                        <label for="" style="cursor:pointer">QM4/QP4</label>
                    </div>
                    <div class="card legenda" id="legenda" style="min-width: 110px; max-width: 110px;" onclick="Prioridade1 = 'QM5'; Prioridade2 = 'QP5'; FiltrarDadosPrioridade()">
                        <label for="" style="cursor:pointer">QM5/QP5</label>
                    </div>
                    <div class="card legenda QM6/QP6" id="legenda" style="min-width: 110px; max-width: 110px;" onclick="Prioridade1 = 'QM6'; Prioridade2 = 'QP6'; FiltrarDadosPrioridade()">
                        <label for="" style="cursor:pointer">QM6/QP6</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row col-12 position-relative" style="height: 68vh; max-height: 68vh; border: 1px solid black; border-radius: 10px; width: 100%; min-width: 100%; justify-content: center; padding: 15px;">
            <div class="d-flex justify-content-between align-items-center w-100">
                <h2 class="m-0">Lista de Op's</h2>
                <div class="d-flex gap-3">
                    <i class="fa-solid fa-filter" id="BotaoFiltros" title="Filtros" style="font-size: 30px; cursor: pointer;" onclick="$('#modal-filtros').modal('show');"></i>
                    <i class="fa-solid fa-download" id="BotaoExcel" title="Exportar Excel" style="font-size: 30px; cursor: pointer;" onclick="ExportarExcel()"></i>
                    <i class="fa-solid fa-ban text-danger" title="Remover Filtro de Prioridade" style="font-size: 30px; cursor: pointer;" onclick="Prioridade1 = ''; Prioridade2 = ''; Status = ''; FiltrarDadosPrioridade()"></i>
                </div>
            </div>

            <div class="row mt-3" id="Corpo" style="height: 55vh; max-height: 55vh; min-height: 55vh; overflow-y: auto; padding: 10px;">
                <!-- Aqui entra a lista de OPs -->
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

<div class="modal fade modal-custom" id="modal-filtros" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="">Filtros</h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Formulário com filtros -->
                <form id="formFiltros" class="p-3 bg-white rounded">
                    <div class="form-group">
                        <label for="InputContem" class="font-weight-bold d-block mb-2" style="font-size: 1.4rem; text-align: left;">Contém:</label>
                        <input type="text" class="form-control border-primary" id="InputContem" placeholder="Digite aqui...">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold d-block mb-2" style="font-size: 1.4rem; text-align: left;">Ordenação:</label>
                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <label class="btn btn-light shadow-sm px-3 py-2" id="btnUrgente">
                                <input type="radio" name="opcoesOrdenacao" value="prioridade" class="d-none"> 🔥 Prioridade
                            </label>
                            <label class="btn btn-light shadow-sm px-3 py-2" id="btnLeadTime">
                                <input type="radio" name="opcoesOrdenacao" value="tempo" class="d-none"> ⏳ Lead Time
                            </label>
                        </div>
                    </div>

                    <div class="form-group border-bottom pb-2">
                        <label class="font-weight-bold d-block mb-2" style="font-size: 1.4rem; text-align: left;">Coleções</label>
                    </div>
                    <div class="form-group" id="colecoes">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary" onclick="aplicarFiltros()">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-custom" id="modal-justificativa" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="" id="NumeroOP"></h5>
                <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="InputJustificativa" class="col-form-label d-block" style="font-size: 25px; text-align: left;">Justificativa:</label>
                    <textarea class="form-control" name="InputJustificativa" id="InputJustificativa" rows="4" style="font-size: 20px;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-salvar" id="SalvarObs" onclick="SalvarJustificativa()"> <span><i class="bi bi-floppy"></i></span> Salvar</button>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../../../templates/footerGestao.php');
?>
<script src="script.js"></script>
<script>
    document.querySelectorAll('[name="opcoesOrdenacao"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.btn-light').forEach(label => label.classList.remove('active', 'btn-primary'));
            this.parentElement.classList.add('active', 'btn-primary');
            this.checked = true; // Garante que o rádio está marcado
        });
    });
</script>
