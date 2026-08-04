<?php
include_once('requests.php');
include_once("../../../templates/LoadingGestao.php");
include_once('../../../templates/headerGestao.php');
?>

<!-- jQuery, Bootstrap, DataTables (+ Buttons/jszip) e ApexCharts vêm de
     headerGestao.php e footerGestao.php, como nas demais telas -->
<link rel="stylesheet" href="style.css">

<!-- ============================================================
     Frame 1 — Indicadores e filtro de período
     ============================================================ -->
<div class="col-12">
  <section class="frame frame-indicadores">

    <div class="kpi kpi--claro" title="Peças baixadas no período. Não acompanha o cruzamento de filtros.">
      <span class="kpi-rotulo">Realizado pçs</span>
      <span class="kpi-valor kpi-valor--destaque" id="totalPecas">—</span>
    </div>

    <div class="kpi kpi--escuro" title="Peças com motivo de 2ª qualidade. Acompanha o cruzamento de filtros.">
      <span class="kpi-rotulo">2ª Qualidade</span>
      <span class="kpi-valor" id="totalPecas2Qualidade">—</span>
    </div>

    <div class="frame-divisor"></div>

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

    <div class="busca-avancada">
      <label for="campoBusca">Busca Avançada</label>
      <div class="busca-campo">
        <input type="text"
               class="form-control"
               id="campoBusca"
               placeholder="Filtrar por termo...">
        <i class="bi bi-search icone-busca"
           title="Aplicar busca"
           onclick="atualizar()"></i>
      </div>
    </div>

    <button type="button" class="btn btn-geral" onclick="atualizar();">
      <i class="fas fa-sync-alt"></i> Atualizar
    </button>

  </section>
</div>

<div class="col-12">
  <div id="chipsFiltros" class="chips-filtros"></div>
</div>

<!-- ============================================================
     Painéis de análise
     ============================================================ -->
<div class="col-12">

  <div class="grafico-container mt-3">

    <!-- Esquerda (40%): índice de 2ª Qualidade, medidor limitado pela meta -->
    <section class="grafico grafico--40 frame-indice">
      <div class="painel-titulo">
        <i class="bi bi-speedometer2"></i>
        <h2>2ª Qualidade</h2>
        <span class="selo selo-atencao" title="O total de peças baixadas é do período inteiro e não acompanha o cruzamento">
          base: total do período
        </span>
      </div>

      <div class="frame-indice-corpo">

        <div class="medidor">
          <div id="graficoDonut"></div>
        </div>

        <div class="leituras">
          <div class="leitura">
            <span class="leitura-rotulo">Realizado</span>
            <span class="leitura-valor" id="indiceRealizado">—</span>
          </div>
          <div class="leitura">
            <span class="leitura-rotulo">Meta</span>
            <div class="leitura-linha">
              <span class="leitura-valor leitura-valor--meta" id="indiceMeta">1,50%</span>
              <button type="button"
                      class="btn-meta"
                      id="btnAbrirMetas"
                      title="Informar as metas mensais"
                      data-bs-toggle="modal"
                      data-bs-target="#modalMetas">
                <i class="bi bi-card-checklist"></i>
              </button>
            </div>
          </div>
          <div class="leitura">
            <span class="leitura-rotulo">Desvio</span>
            <span class="leitura-valor" id="indiceDesvio">—</span>
          </div>
        </div>

      </div>
    </section>

    <!-- Direita (60%): Defeitos por Origem.
         grafico--preenche faz o corpo esticar até o fim do frame, para o
         gráfico aproveitar toda a altura da linha. -->
    <div class="grafico grafico--60 grafico--preenche">
      <div class="painel-titulo">
        <i class="bi bi-diagram-3-fill"></i>
        <h2>Defeitos por Origem</h2>
      </div>
      <div class="card-body p-2">
        <div id="graficoOrigemAgrupado"></div>
      </div>
    </div>

  </div>

  <div class="grafico-container mt-3">

    <div class="grafico grafico--40">
      <div class="painel-titulo">
        <i class="bi bi-people-fill"></i>
        <h2>Defeitos por Terceirizados</h2>
      </div>
      <div class="card-body p-2">
        <div id="graficoTerceirizados" style="width: 100%; height: 300px;"></div>
      </div>
    </div>

    <div class="grafico grafico--60">
      <div class="painel-titulo">
        <i class="bi bi-bar-chart-fill"></i>
        <h2>Defeitos por Motivo</h2>
      </div>
      <div class="card-body p-2 rolagem-suave" style="overflow-x: auto;">
        <div id="graficoBarras" style="width: 100%; height: 300px;"></div>
      </div>
    </div>

  </div>

  <div class="grafico-container mt-3">

    <div class="grafico grafico--40">
      <div class="painel-titulo">
        <i class="bi bi-truck"></i>
        <h2>Defeitos por Fornecedor</h2>
      </div>
      <div class="card-body p-2">
        <div id="graficoFornecedores" style="width: 100%; height: 300px;"></div>
      </div>
    </div>

    <div class="grafico grafico--60">
      <div class="painel-titulo">
        <i class="bi bi-layers-fill"></i>
        <h2>Defeitos por Base Tecido</h2>
        <span class="selo" title="A base do tecido não existe no detalhamento, por isso este painel não entra no cruzamento">
          não afetado pelo filtro
        </span>
      </div>
      <div class="card-body p-2 rolagem-suave" style="overflow-x: auto;">
        <div id="graficoBaseTecido" style="width: 100%; height: 300px;"></div>
      </div>
    </div>

  </div>

    <div class="tabela-container mt-3 mb-3">
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

</div>

<!-- ============================================================
     Modal — metas mensais de 2ª Qualidade
     Os meses são desenhados pelo script a partir do retorno da API,
     então a lista de meses vem sempre do backend.
     ============================================================ -->
<div class="modal fade" id="modalMetas" tabindex="-1" aria-labelledby="modalMetasTitulo" aria-hidden="true">
  <!-- sem modal-dialog-scrollable: o <form> envolve corpo e rodapé e quebraria
       o cálculo de altura do Bootstrap, cortando os campos em telas baixas -->
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content modal-metas">

      <div class="modal-header">
        <h5 class="modal-title" id="modalMetasTitulo">
          <i class="bi bi-card-checklist"></i> Metas de 2ª Qualidade
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <form id="formMetas" novalidate>

        <div class="modal-body">

          <div class="metas-topo">
            <div class="campo-filtro">
              <label for="metaAno">Ano</label>
              <select id="metaAno" class="form-select"></select>
            </div>
            <p class="metas-ajuda">
              Informe a meta de cada mês em percentual. Ex.: <strong>1,50</strong> para 1,50%.
              A <strong>média acumulada</strong> é calculada de janeiro até o mês e não é gravada.
            </p>
          </div>

          <div class="metas-grade" id="metasGrade"></div>

          <p class="metas-aviso" id="metasAviso" role="alert"></p>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-cancelar" data-bs-dismiss="modal">
            <i class="bi bi-x-lg"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-geral" id="btnSalvarMetas">
            <i class="bi bi-check2"></i> Salvar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<?php
include_once('../../../templates/footerGestao.php');
?>

<script src="script.js"></script>
