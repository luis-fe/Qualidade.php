<?php
include_once('requests.php');
include_once("../../templates/Loading.php");
include_once('../../templates/headerGarantia.php');
?>

<!-- ==================== ESTILOS ==================== -->
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" 
  href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" 
  href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<!-- ==================== SCRIPTS BASE ==================== -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
  label {
    color: black !important;
  }

  .grafico-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: space-between;
  }

  .grafico {
    flex: 1 1 45%;
    min-width: 280px;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

  h2 {
    font-size: 18px;
    font-weight: bold;
    margin: auto;
    text-align: center;
  }
</style>

<!-- ==================== TÍTULO ==================== -->
<div class="titulo-tela">
  <span class="span-icone"><i class="bi bi-speedometer"></i></span> Dashboards
</div>

<!-- ==================== FILTROS ==================== -->
<div class="col-12 mt-2">
  <div class="d-flex flex-wrap gap-3 align-items-end p-2">

    <!-- Data Início -->
    <div class="position-relative">
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
        <input type="date" id="dataInicio" class="form-control">
      </div>
    </div>

    <!-- Data Fim -->
    <div class="position-relative">
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
        <input type="date" id="dataFim" class="form-control">
      </div>
    </div>

    <!-- Botão Atualizar -->
    <button id="btnAtualizar" class="btn btn-geral" style="margin-bottom: 0;">
      <i class="fas fa-sync-alt"></i> Atualizar
    </button>

    <!-- Cards de Totais -->
    <div class="card text-center" style="min-width: 150px;">
      <div class="card-body p-2">
        <h6 class="card-title mb-1">Total de Peças</h6>
        <h5 class="card-text fw-bold text-primary" id="totalPecas"></h5>
      </div>
    </div>

    <div class="card text-center" style="min-width: 150px;">
      <div class="card-body p-2">
        <h6 class="card-title mb-1">Total 2ª Qualidade</h6>
        <h5 class="card-text fw-bold text-danger" id="totalPecas2Qualidade"></h5>
      </div>
    </div>

  </div>
</div>

<!-- ==================== GRÁFICOS ==================== -->
<div class="col-12 mt-2">
  <div class="col-12 mt-0 p-0 grafico-container" 
        style="max-height: 300px; overflow-y: auto;">

    <div class="grafico card mb-0" style="width: 100%;">
        <div class="card-header pt-0">
            <h2 class="h6 mb-0">% 2ª Qualidade</h2>
        </div>
        <div class="card-body p-0 d-flex justify-content-center align-items-center" 
        style="max-height: 100px; overflow: hidden;"> 
            <div id="graficoDonut" class="w-100 h-100">
            </div>
        </div>
    </div>

    <div class="grafico card" style="width: 100%;">
            <div class="card-header p-0">
                <h2 class="h6 mb-0">Defeitos por motivos</h2>
            </div>
            <div class="card-body p-2 d-flex justify-content-center align-items-center" 
                style="max-height: 150px; overflow: hidden;"> 
                <div id="graficoBarras" class="w-100 h-100"></div>
            </div>
    </div>

  </div>

  <div class="row mt-1 p-3 grafico-container">
    <!-- Gráfico Terceirizados -->
    <div class="col-md-6 grafico">
      <h2>Defeitos por terceirizados</h2>
      <div id="graficoTerceirizados" style="width: 100%; height: 300px;"></div>
    </div>

    <!-- Tabela -->
    <div class="col-md-6">
      <h2>Análise por Ordem</h2>
      <table id="tabela_detalhamento2" class="table table-hover mt-2">
        <thead style="font-size: 14px;">
          <tr>
            <th>Ordem<br>Prod.</th>
            <th>Cod<br>Engenharia</th>
            <th>Descrição<br>Produto</th>
            <th>Data<br>Diagnóstico</th>
            <th>Origem</th>
            <th>Motivo</th>
            <th>Qtd.</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

    <div class="row mt-1 p-3 grafico-container">
            <!-- Tabela -->
        <div class="col-md-6">
        <h2>Análise por Ordem</h2>
        <table id="tabela_detalhamento" class="table table-hover mt-1">
            <thead style="font-size: 14px;">
            <tr>
                <th>Ordem<br>Prod.</th>
                <th>Cod<br>Engenharia</th>
                <th>Descrição<br>Produto</th>
                <th>Data<br>Diagnóstico</th>
                <th>Origem</th>
                <th>Motivo</th>
                <th>Qtd.</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
        </div>
    </div>
</div>

<?php
include_once('../../templates/footer.php');
?>

<!-- ==================== SEU SCRIPT PRINCIPAL ==================== -->
<script src="script.js"></script>
