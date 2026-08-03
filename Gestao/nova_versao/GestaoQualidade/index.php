<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="style.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="col-12" style="margin-top: 2px;">
  <div class="barra-filtros d-flex flex-wrap gap-3 align-items-end">

    <div class="campo-filtro">
      <label for="dataInicio">Data Inicial</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
        <input type="date" id="dataInicio" class="form-control">
      </div>
    </div>

    <div class="campo-filtro">
      <label for="dataFim">Data Final</label>
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
        <input type="date" id="dataFim" class="form-control">
      </div>
    </div>

    <div class="busca-avancada position-relative" style="max-width: 240px; flex: 1 1 200px;">
      <label for="campoBusca">Busca Avançada</label>
      <input type="text"
             class="form-control"
             id="campoBusca"
             placeholder="Filtrar por termo...">
      <i class="bi bi-search icone-busca position-absolute"
         title="Aplicar busca"
         onclick="atualizar()"></i>
    </div>

    <button type="button" class="btn btn-geral" onclick="atualizar();">
      <i class="fas fa-sync-alt"></i> Atualizar
    </button>

    <div class="d-flex flex-wrap gap-3 ms-auto">
      <div class="kpi kpi--claro">
        <span class="kpi-rotulo">Total de Peças</span>
        <span class="kpi-valor kpi-valor--destaque" id="totalPecas">—</span>
      </div>

      <div class="kpi kpi--escuro">
        <span class="kpi-rotulo">Total 2ª Qualidade</span>
        <span class="kpi-valor" id="totalPecas2Qualidade">—</span>
      </div>
    </div>

  </div>
</div>

<div class="col-12 mt-3">
  <div class="grafico-container rolagem-suave"
       style="max-height: 260px; overflow-y: auto;">

    <div class="grafico mb-0">
      <div class="painel-titulo">
        <i class="bi bi-pie-chart-fill"></i>
        <h2>% 2ª Qualidade</h2>
      </div>
      <div class="card-body p-0 d-flex justify-content-center align-items-center"
           style="overflow: hidden;">
        <div id="graficoDonut"></div>
      </div>
    </div>

    <div class="grafico">
      <div class="painel-titulo">
        <i class="bi bi-diagram-3-fill"></i>
        <h2>Defeitos por Origem</h2>
      </div>
      <div class="card-body p-2 d-flex" style="width: 100%;">
        <div id="graficoOrigemAgrupado" style="width: 100%;"></div>
      </div>
    </div>

  </div>

  <div class="grafico-container mt-3">

    <div class="grafico">
      <div class="painel-titulo">
        <i class="bi bi-people-fill"></i>
        <h2>Defeitos por Terceirizados</h2>
      </div>
      <div class="card-body p-2">
        <div id="graficoTerceirizados" style="width: 100%; height: 300px;"></div>
      </div>
    </div>

    <div class="grafico">
      <div class="painel-titulo">
        <i class="bi bi-truck"></i>
        <h2>Defeitos por Fornecedor</h2>
      </div>
      <div class="card-body p-2">
        <div id="graficoFornecedores" style="width: 100%; height: 300px;"></div>
      </div>
    </div>

  </div>

  <div class="grafico-container mt-3">

    <div class="grafico">
      <div class="painel-titulo">
        <i class="bi bi-bar-chart-fill"></i>
        <h2>Defeitos por Motivo</h2>
      </div>
      <div class="card-body p-2 rolagem-suave" style="overflow-x: auto;">
        <div id="graficoBarras" style="width: 100%; height: 300px;"></div>
      </div>
    </div>

  </div>

    <div class="tabela-container mt-3">
        <div class="painel-titulo">
            <i class="bi bi-table"></i>
            <h2>Análise Detalhada por OP / Motivo</h2>
        </div>
        <div class="p-0">
            <table id="tabela_detalhamento" class="table table-hover table-bordered mb-0 tabela-fonte-pequena">
                <thead>
                    <tr>
                        <th>Ordem<br>Prod.</th>
                        <th>Cod<br>Engenharia</th>
                        <th>Descrição<br>Produto</th>
                        <th>Data<br>Diagnóstico</th>
                        <th>Origem</th>
                        <th>Motivo<br><input type="search" class="search-input search-input-defeitos" style="min-width: 2px;"></th>
                        <th>Faccionista<br><input type="search" class="search-input search-input-defeitos" style="min-width: 2px;"></th>  
                        <th>Fornecedor<br><input type="search" class="search-input search-input-defeitos" style="min-width: 2px;"></th>  
                        <th>Qtd:</th> 
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8" style="text-align: right;">Total:</th>
                        <th id="total-quantidade"></th> 
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="grafico-container mt-3 mb-3">
        <div class="grafico">
            <div class="painel-titulo">
                <i class="bi bi-layers-fill"></i>
                <h2>Defeitos por Base Tecido</h2>
            </div>
            <div class="card-body p-2 rolagem-suave" style="overflow-x: auto;">
                <div id="graficoBaseTecido" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>

</div>

<?php
include_once('../../templates/footer.php');
?>

<script src="script.js"></script>