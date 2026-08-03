/* ============================================================
   Paleta da tela — Azul Escuro | Azul Claro | Branco
   ============================================================ */
const CORES = {
    azulEscuro: '#10045a',
    azulEscuro2: '#2a1aa0',
    azulClaro: '#008ffb',
    azulClaro2: '#00d4ff',
    azulClaro3: '#6fc5ff',
    azulApagado: '#c3d9f2',
    azulSuave: '#e8f1fd',
    branco: '#ffffff',
    borda: '#dfe7f3',
    texto: '#1c2434',
    textoSuave: '#5f6f8a',
    grade: '#e6edf8'
};

// Degradê padrão das barras: azul claro -> azul escuro
const FILL_BARRA = {
    type: 'gradient',
    gradient: {
        shade: 'dark',
        type: 'vertical',
        shadeIntensity: .25,
        gradientToColors: [CORES.azulEscuro],
        inverseColors: false,
        opacityFrom: 1,
        opacityTo: .95,
        stops: [0, 100]
    }
};

const FILL_BARRA_HORIZONTAL = {
    ...FILL_BARRA,
    gradient: { ...FILL_BARRA.gradient, type: 'horizontal' }
};

// Base de tema aplicada a todos os gráficos
const TEMA_BASE = {
    fontFamily: 'Segoe UI, system-ui, -apple-system, Arial, sans-serif',
    foreColor: CORES.textoSuave
};

// Marcação de estado vazio padronizada
const semDados = (mensagem = 'Nenhum dado a ser exibido') =>
    `<div class="sem-dados"><i class="bi bi-bar-chart-line"></i>${mensagem}</div>`;


/* ============================================================
   Cruzamento de filtros (cross-filter)

   Os painéis de Motivo, Origem, Faccionista e Fornecedor são
   agregações do mesmo conjunto devolvido por 'detalha_defeitos'.
   Com isso o cruzamento é feito no navegador, sem nova requisição.

   Ficam de fora, por não existirem no grão do detalhe:
     - Base Tecido  -> a API do detalhe não devolve 'nomeItem'
     - Total de Peças Baixadas (denominador do donut) -> é do período
   ============================================================ */
const DIMENSOES = {
    motivo:      { campo: 'nome',                    rotulo: 'Motivo',      alvo: '#graficoBarras' },
    origem:      { campo: 'nomeOrigem',              rotulo: 'Origem',      alvo: '#graficoOrigemAgrupado' },
    faccionista: { campo: 'nomeFaccicionista',       rotulo: 'Faccionista', alvo: '#graficoTerceirizados' },
    fornecedor:  { campo: 'fornencedorPreferencial', rotulo: 'Fornecedor',  alvo: '#graficoFornecedores' }
};

const SEM_VALOR = '(não informado)';

// Meta do índice de 2ª qualidade, em %. É o limite de preenchimento do medidor.
const META_2QUALIDADE = 1.5;

const ESTADO = {
    detalhe: [],   // linhas cruas de detalha_defeitos
    base: {},      // agregados vindos da API — retrato sem nenhum filtro
    totais: { pecas: 0, segundaQualidade: 0 },
    filtros: {},   // dimensao -> Set de valores selecionados
    graficos: {}   // seletor -> instância ApexCharts em tela
};

// Mesma regra de conversão usada no rodapé da tabela, para os totais baterem
const numero = (valor) => {
    if (typeof valor === 'number') return valor;
    if (valor === null || valor === undefined) return 0;
    const n = parseInt(String(valor).replace(/[^0-9-]/g, ''), 10);
    return isNaN(n) ? 0 : n;
};

const escaparHtml = (texto) => String(texto).replace(/[&<>"']/g, (c) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
}[c]));

const valorDim = (linha, dim) => {
    const bruto = linha[DIMENSOES[dim].campo];
    const texto = (bruto === null || bruto === undefined) ? '' : String(bruto).trim();
    return texto === '' ? SEM_VALOR : texto;
};

const temFiltro = () => Object.keys(ESTADO.filtros).length > 0;

/**
 * Linhas do detalhe após aplicar os filtros ativos.
 * `exceto` deixa a própria dimensão de fora do filtro para que o painel
 * continue exibindo os itens irmãos — senão sobraria só a barra clicada.
 */
function linhasFiltradas(exceto) {
    const dims = Object.keys(ESTADO.filtros);
    if (dims.length === 0) return ESTADO.detalhe;

    return ESTADO.detalhe.filter((linha) =>
        dims.every((dim) => dim === exceto || ESTADO.filtros[dim].has(valorDim(linha, dim)))
    );
}

// Agrupa as linhas por dimensão somando a quantidade, do maior para o menor
function agregar(linhas, dim) {
    const mapa = new Map();

    linhas.forEach((linha) => {
        const chave = valorDim(linha, dim);
        mapa.set(chave, (mapa.get(chave) || 0) + numero(linha.qtd));
    });

    return [...mapa.entries()]
        .map(([rotulo, qtd]) => ({ rotulo, qtd }))
        .sort((a, b) => b.qtd - a.qtd);
}

function alternarFiltro(dim, valor) {
    const selecao = ESTADO.filtros[dim];

    if (selecao && selecao.has(valor)) {
        selecao.delete(valor);
        if (selecao.size === 0) delete ESTADO.filtros[dim];
    } else if (selecao) {
        selecao.add(valor);
    } else {
        ESTADO.filtros[dim] = new Set([valor]);
    }

    aplicarFiltros();
}

function limparFiltros() {
    ESTADO.filtros = {};
    aplicarFiltros();
}

// Redesenha todos os painéis conforme os filtros ativos
function aplicarFiltros() {
    const ativo = temFiltro();
    document.body.classList.toggle('com-filtro', ativo);
    renderizarChips();

    renderizarGraficoBarras(ativo ? agregar(linhasFiltradas('motivo'), 'motivo') : ESTADO.base.motivo);
    renderizarGraficoOrigemAgrupado(ativo ? agregar(linhasFiltradas('origem'), 'origem') : ESTADO.base.origem);
    renderizarGraficoTerceirizados(ativo ? agregar(linhasFiltradas('faccionista'), 'faccionista') : ESTADO.base.faccionista);
    renderizarGraficoFornecedor(ativo ? agregar(linhasFiltradas('fornecedor'), 'fornecedor') : ESTADO.base.fornecedor);

    // Marca o painel que originou cada filtro, para ficar claro de onde
    // veio o recorte que os demais estão exibindo
    Object.keys(DIMENSOES).forEach((dim) => {
        $(DIMENSOES[dim].alvo)
            .closest('.grafico')
            .toggleClass('grafico--selecionado', Boolean(ESTADO.filtros[dim]));
    });

    const linhas = linhasFiltradas(null);
    const total2Qualidade = ativo
        ? linhas.reduce((soma, linha) => soma + numero(linha.qtd), 0)
        : ESTADO.totais.segundaQualidade;

    $('#totalPecas').text(ESTADO.totais.pecas.toLocaleString('pt-BR'));
    $('#totalPecas2Qualidade').text(total2Qualidade.toLocaleString('pt-BR'));

    renderizarGrafico(total2Qualidade, ESTADO.totais.pecas);
    Tabela_detalha_defeitos(linhas);
}

function renderizarChips() {
    const caixa = $('#chipsFiltros');
    if (!caixa.length) return;

    const chips = [];
    Object.keys(ESTADO.filtros).forEach((dim) => {
        ESTADO.filtros[dim].forEach((valor) => {
            chips.push(
                `<span class="chip-filtro" data-dim="${dim}" data-valor="${escaparHtml(valor)}" title="Remover este filtro">
                    <strong>${DIMENSOES[dim].rotulo}:</strong> ${escaparHtml(valor)}
                    <i class="bi bi-x-lg"></i>
                 </span>`
            );
        });
    });

    if (chips.length) {
        chips.push('<button type="button" class="chip-limpar" id="limparFiltros"><i class="bi bi-eraser"></i> Limpar tudo</button>');
    }

    caixa.html(chips.join(''));
}


/* ============================================================
   Ciclo de vida dos gráficos
   ============================================================ */
function montarGrafico(seletor, dados, opcoes) {
    const elemento = document.querySelector(seletor);
    if (!elemento) return;

    // Sempre destrói a instância anterior: os painéis são remontados
    // a cada clique no cruzamento de filtros
    if (ESTADO.graficos[seletor]) {
        try {
            ESTADO.graficos[seletor].destroy();
        } catch (erro) {
            console.warn(`Falha ao destruir o gráfico ${seletor}:`, erro);
        }
        delete ESTADO.graficos[seletor];
    }

    elemento.innerHTML = '';

    if (!dados || dados.length === 0) {
        $(elemento).html(semDados());
        return;
    }

    const grafico = new ApexCharts(elemento, opcoes);
    ESTADO.graficos[seletor] = grafico;
    grafico.render();
}

// Série no formato {x, y}: dispensa xaxis.categories e mantém o mesmo
// formato com e sem realce
const serieDe = (dados) => [{
    name: 'Quantidade',
    data: dados.map((item) => ({ x: item.rotulo, y: item.qtd }))
}];

const cliqueNaBarra = (dim, dados) => ({
    dataPointSelection: (evento, contexto, config) => {
        const item = dados[config.dataPointIndex];
        if (!item) return;

        // Sai do handler antes de redesenhar. aplicarFiltros() destrói e
        // remonta os gráficos — inclusive este, que ainda está no meio do
        // processamento do próprio clique dentro do ApexCharts.
        setTimeout(() => alternarFiltro(dim, item.rotulo), 0);
    },
    // Clique no rótulo do eixo seleciona a mesma categoria
    xAxisLabelClick: (evento, contexto, config) => {
        const item = dados[config.labelIndex];
        if (!item) return;

        setTimeout(() => alternarFiltro(dim, item.rotulo), 0);
    }
});

/**
 * Aplica o realce da seleção: o que está selecionado fica azul escuro,
 * o restante em azul apagado. Os rótulos ganham fundo branco para
 * continuarem legíveis sobre os dois tons.
 */
function realce(dados, dim, opcoes) {
    const selecao = ESTADO.filtros[dim];
    if (!selecao) return opcoes;

    return {
        ...opcoes,
        colors: dados.map((item) => (selecao.has(item.rotulo) ? CORES.azulEscuro : CORES.azulApagado)),
        fill: { type: 'solid', opacity: 1 },
        legend: { show: false },
        plotOptions: {
            ...opcoes.plotOptions,
            bar: { ...opcoes.plotOptions.bar, distributed: true }
        },
        dataLabels: {
            ...opcoes.dataLabels,
            // Atenção: no ApexCharts, background.foreColor é o PREENCHIMENTO
            // da caixa; a cor do texto vem de style.colors. Os dois iguais
            // deixam o número invisível.
            style: { ...opcoes.dataLabels.style, colors: [CORES.azulEscuro] },
            background: {
                enabled: true,
                foreColor: CORES.branco,
                borderColor: CORES.azulClaro3,
                borderWidth: 1,
                borderRadius: 4,
                padding: 3,
                opacity: 1
            }
        }
    };
}

// Desliga o realce próprio do ApexCharts para não brigar com o nosso
const ESTADOS_APEX = {
    active: { filter: { type: 'none' } },
    hover: { filter: { type: 'lighten', value: .08 } }
};


// Formata no fuso local. toISOString() converte para UTC e, no horário
// de Brasília, devolveria o dia anterior.
const paraCampoData = (data) =>
    `${data.getFullYear()}-${String(data.getMonth() + 1).padStart(2, '0')}-${String(data.getDate()).padStart(2, '0')}`;

/**
 * O atributo max fecha o seletor de calendário em hoje, mas em vários
 * navegadores ainda dá para digitar uma data futura. Aqui ela é trazida
 * de volta antes de consultar. Comparação direta funciona no formato ISO.
 */
function limitarDatasAoDiaDeHoje() {
    const hoje = paraCampoData(new Date());

    ['#dataInicio', '#dataFim'].forEach((campo) => {
        if ($(campo).val() > hoje) $(campo).val(hoje);
    });
}

$(document).ready(async () => {
    // Período padrão: do primeiro dia do mês corrente até hoje
    const hoje = new Date();
    const inicioDoMes = new Date(hoje.getFullYear(), hoje.getMonth(), 1);
    $('#dataInicio').val(paraCampoData(inicioDoMes));
    $('#dataFim').val(paraCampoData(hoje));

    // O calendário não abre datas futuras. Definido aqui, e não no HTML,
    // para o limite acompanhar a virada do dia sem reeditar a página.
    $('#dataInicio, #dataFim').attr('max', paraCampoData(hoje));

    // Delegado: sobrevive à recriação dos chips a cada filtro
    $(document).on('click', '.chip-filtro', function () {
        alternarFiltro($(this).attr('data-dim'), $(this).attr('data-valor'));
    });
    $(document).on('click', '#limparFiltros', limparFiltros);

    atualizar();
});

async function atualizar(){
    limitarDatasAoDiaDeHoje();

    // Exibe no campo, mas mantém um valor oculto para manipulação correta
    let campoBusca = document.getElementById("campoBusca").value
        .toUpperCase()
        .replace(/ /g, "%20");

    // Nova consulta = novo conjunto de dados: os cruzamentos anteriores caem
    ESTADO.filtros = {};

    await Cosultar_Qualidade();
    await Consultar_Motivos(campoBusca);
    await Consultar_defeito_baseTecido(campoBusca);
    await Cosultar_Origem_faccionista(campoBusca);
    await Cosultar_Origem_fornecedor(campoBusca);
    await Cosultar_Origem(campoBusca);
    await detalha_defeitos(campoBusca);

    aplicarFiltros();

      // 👇 força o navegador a redesenhar os gráficos
  setTimeout(() => {
    window.dispatchEvent(new Event('resize'));
  }, 300);
};

const Cosultar_Qualidade = async () => {
    $('#loadingModal').modal('show');
    const dataInicial = $('#dataInicio').val();
    const dataFinal = $('#dataFim').val();

    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Cosultar_Qualidade',
                dataInicial: dataInicial,
                dataFinal: dataFinal
            }
        });

        ESTADO.totais.pecas = numero(data[0]['2- Total Peças Baixadas periodo']);
        ESTADO.totais.segundaQualidade = numero(data[0]['1- Peças com Motivo de 2Qual.']);
    } catch (error) {
        console.error('Erro ao consultar qualidade:', error);
        ESTADO.totais = { pecas: 0, segundaQualidade: 0 };
        $('#graficoDonut').html(semDados('Erro ao carregar os dados de qualidade'));
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consultar_Motivos = async (campoBusca) => {
    $('#loadingModal').modal('show');
    try {
        const dataInicial = $('#dataInicio').val();
        const dataFinal = $('#dataFim').val();

        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Motivos',
                dataInicial: dataInicial,
                dataFinal: dataFinal,
                campoBusca: campoBusca
            }
        });

        ESTADO.base.motivo = normalizar(data, 'motivo2Qualidade');
    } catch (error) {
        console.error('Erro ao consultar motivos:', error);
        ESTADO.base.motivo = [];
    } finally {
        $('#loadingModal').modal('hide');
    }
};


const Consultar_defeito_baseTecido = async (campoBusca) => {
    $('#loadingModal').modal('show');
    try {
        const dataInicial = $('#dataInicio').val();
        const dataFinal = $('#dataFim').val();

        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Cosultar_Fornecedor_base',
                dataInicial: dataInicial,
                dataFinal: dataFinal,
                campoBusca: campoBusca
            }
        });

        // Fora do cruzamento: o detalhe não traz 'nomeItem', então este painel
        // continua refletindo apenas o período e a busca avançada.
        renderizarGraficoBarras_baseTecido(normalizar(data, 'nomeItem'));
    } catch (error) {
        console.error('Erro ao consultar base tecido:', error);
        renderizarGraficoBarras_baseTecido([]);
    } finally {
        $('#loadingModal').modal('hide');
    }
};



const Cosultar_Origem_faccionista = async (campoBusca) => {
    $('#loadingModal').modal('show');
    try {
        const dataInicial = $('#dataInicio').val();
        const dataFinal = $('#dataFim').val();

        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Cosultar_Origem',
                dataInicial: dataInicial,
                dataFinal: dataFinal,
                campoBusca: campoBusca
            }
        });

        ESTADO.base.faccionista = normalizar(data, 'nomeFaccicionista');
    } catch (error) {
        console.error('Erro ao consultar faccionistas:', error);
        ESTADO.base.faccionista = [];
    } finally {
        $('#loadingModal').modal('hide');
    }
};


const Cosultar_Origem_fornecedor = async (campoBusca) => {
    $('#loadingModal').modal('show');
    try {
        const dataInicial = $('#dataInicio').val();
        const dataFinal = $('#dataFim').val();

        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Cosultar_Fornecedor',
                dataInicial: dataInicial,
                dataFinal: dataFinal,
                campoBusca: campoBusca
            }
        });

        ESTADO.base.fornecedor = normalizar(data, 'fornencedorPreferencial');
    } catch (error) {
        console.error('Erro ao consultar fornecedores:', error);
        ESTADO.base.fornecedor = [];
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Cosultar_Origem = async (campoBusca) => {
    $('#loadingModal').modal('show');
    try {
        const dataInicial = $('#dataInicio').val();
        const dataFinal = $('#dataFim').val();

        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'defeitos_porOrigem',
                dataInicial: dataInicial,
                dataFinal: dataFinal,
                campoBusca: campoBusca
            }
        });

        ESTADO.base.origem = normalizar(data, 'nomeOrigem');
    } catch (error) {
        console.error('Erro ao consultar origens:', error);
        ESTADO.base.origem = [];
    } finally {
        $('#loadingModal').modal('hide');
    }
};


const detalha_defeitos = async (campoBusca) => {
    $('#loadingModal').modal('show');
    try {
        const dataInicial = $('#dataInicio').val();
        const dataFinal = $('#dataFim').val();

        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'detalha_defeitos',
                dataInicial: dataInicial,
                dataFinal: dataFinal,
                campoBusca: campoBusca
            }
        });

        ESTADO.detalhe = Array.isArray(data) ? data : [];
    } catch (error) {
        console.error('Erro ao consultar o detalhamento:', error);
        ESTADO.detalhe = [];
    } finally {
        $('#loadingModal').modal('hide');
    }
};

// Converte a resposta agregada da API para o formato {rotulo, qtd} dos gráficos
function normalizar(lista, campo) {
    if (!Array.isArray(lista)) return [];

    return lista
        .map((item) => {
            const bruto = item[campo];
            const texto = (bruto === null || bruto === undefined) ? '' : String(bruto).trim();
            return { rotulo: texto === '' ? SEM_VALOR : texto, qtd: numero(item.qtd) };
        })
        .sort((a, b) => b.qtd - a.qtd);
}

// Função para formatar a data de yyyy-mm-dd para dd/mm/yyyy
const formatDateToDDMMYYYY = (date) => {
    const [ano, mes, dia] = date.split('-');
    return `${dia}/${mes}/${ano}`;
};

const percentual = (valor) => valor.toFixed(2).replace('.', ',') + '%';

/**
 * Medidor do índice de 2ª qualidade.
 * O preenchimento tem como limite a META: 1,5% equivale ao arco cheio,
 * então 0,75% preenche metade. Acima da meta o arco satura em 100% e o
 * medidor troca para azul escuro.
 */
const renderizarGrafico = (pecasComMotivo, totalPecasBaixadas) => {
    const totalPecas = numero(totalPecasBaixadas);
    const pecas2Qualidade = numero(pecasComMotivo);

    // Evita divisão por zero
    const indice = totalPecas > 0 ? (pecas2Qualidade / totalPecas) * 100 : 0;
    const acimaDaMeta = indice > META_2QUALIDADE;
    const preenchimento = Math.min((indice / META_2QUALIDADE) * 100, 100);
    const desvio = indice - META_2QUALIDADE;

    $('#indiceRealizado').text(percentual(indice));
    $('#indiceMeta').text(percentual(META_2QUALIDADE));
    $('#indiceDesvio')
        .text((desvio >= 0 ? '+' : '−') + Math.abs(desvio).toFixed(2).replace('.', ',') + ' p.p.')
        .toggleClass('leitura-valor--acima', acimaDaMeta);

    if (pecas2Qualidade === 0 && totalPecas === 0) {
        montarGrafico('#graficoDonut', [], {}); // sem dados: cai no estado vazio
        return;
    }

    const corBase = acimaDaMeta ? CORES.azulEscuro : CORES.azulClaro;
    const corTopo = acimaDaMeta ? CORES.azulEscuro2 : CORES.azulClaro2;

    var optionsMedidor = {
        chart: {
            ...TEMA_BASE,
            type: 'radialBar',
            height: 240,
            offsetY: -8,
            sparkline: { enabled: false }
        },
        series: [preenchimento],
        labels: [`Meta ${percentual(META_2QUALIDADE)}`],
        colors: [corBase],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'horizontal',
                shadeIntensity: .2,
                gradientToColors: [corTopo],
                stops: [0, 100]
            }
        },
        stroke: { lineCap: 'round' },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                hollow: { size: '62%' },
                track: {
                    background: CORES.azulSuave,
                    strokeWidth: '100%',
                    margin: 6
                },
                dataLabels: {
                    name: {
                        offsetY: 26,
                        color: CORES.textoSuave,
                        fontSize: '11px',
                        fontWeight: 600
                    },
                    value: {
                        offsetY: -14,
                        color: CORES.azulEscuro,
                        fontSize: '28px',
                        fontWeight: 700,
                        // Mostra o índice real, não o quanto o arco foi preenchido
                        formatter: () => percentual(indice)
                    }
                }
            }
        }
    };

    montarGrafico('#graficoDonut', optionsMedidor.series, optionsMedidor);
};

async function renderizarGraficoBarras(dados) {
    dados = dados || [];
    const chartWidth = Math.max(350, dados.length * 40);

    const chartOptions = realce(dados, 'motivo', {
        chart: {
            ...TEMA_BASE,
            type: 'bar',
            height: 350,
            width: `${chartWidth}px`, // Mantém a largura dinâmica
            toolbar: { show: false },
            dropShadow: { enabled: false },
            events: cliqueNaBarra('motivo', dados)
        },
        states: ESTADOS_APEX,
        series: serieDe(dados),
        colors: [CORES.azulClaro],
        fill: FILL_BARRA,
        xaxis: {
            type: 'category',
            axisBorder: { color: CORES.grade },
            axisTicks: { color: CORES.grade },
            labels: {
                rotate: -90, // Rotaciona totalmente para evitar sobreposição
                trim: false, // Garante que o texto não seja cortado
                style: {
                    fontSize: '10px',
                    colors: CORES.textoSuave
                }
            }
        },
        yaxis: {
            labels: { style: { fontSize: '10px', colors: CORES.textoSuave } }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                borderRadiusApplication: 'end',
                barHeight: '75%', // Ajusta a altura das barras
                columnWidth: '60%'
            }
        },
        grid: {
            borderColor: CORES.grade,
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
            padding: {
                bottom: 60 // Dá mais espaço para a legenda não ser cortada
            }
        },
        tooltip: { y: { formatter: (val) => Number(val).toLocaleString('pt-BR') } },
        // 🌟 CONFIGURAÇÃO PARA ALTERAR A FONTE DO RÓTULO DE DADOS 🌟
        dataLabels: {
            enabled: true, // É importante que esteja 'true'
            formatter: (val) => Number(val).toLocaleString('pt-BR'),
            style: {
                fontSize: '10px', // Altere para o tamanho desejado
                fontWeight: '600', // Altere para o peso desejado (ex: 'bold')
                colors: [CORES.branco]
            },
            dropShadow: { enabled: false }
        }
    });

    montarGrafico('#graficoBarras', dados, chartOptions);
}

async function renderizarGraficoBarras_baseTecido(dados) {
    dados = dados || [];
    const chartWidth = Math.max(350, dados.length * 35);

    const chartOptions = {
        chart: {
            ...TEMA_BASE,
            type: 'bar',
            height: 350,
            width: `${chartWidth}px`,  // Mantém a largura dinâmica
            toolbar: { show: false },
            dropShadow: { enabled: false }
        },
        series: serieDe(dados),
        colors: [CORES.azulClaro],
        fill: FILL_BARRA,
        xaxis: {
            type: 'category',
            axisBorder: { color: CORES.grade },
            axisTicks: { color: CORES.grade },
            labels: {
                rotate: -90,  // Rotaciona totalmente para evitar sobreposição
                trim: false,  // Garante que o texto não seja cortado
                style: {
                    fontSize: '11px',
                    colors: CORES.textoSuave
                }
            }
        },
        yaxis: {
            labels: { style: { fontSize: '10px', colors: CORES.textoSuave } }
        },
        plotOptions: {
            bar: {
                borderRadius: 3,
                borderRadiusApplication: 'end',
                barHeight: '80%', // Ajusta a altura das barras
                columnWidth: '60%'
            }
        },
        dataLabels: {
            enabled: true,
            formatter: (val) => Number(val).toLocaleString('pt-BR'),
            style: {
                fontSize: '10px',
                fontWeight: '600',
                colors: [CORES.branco]
            },
            dropShadow: { enabled: false }
        },
        tooltip: { y: { formatter: (val) => Number(val).toLocaleString('pt-BR') } },
        grid: {
            borderColor: CORES.grade,
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
            padding: {
                bottom: 50 // Dá mais espaço para a legenda não ser cortada
            }
        }
    };

    montarGrafico('#graficoBaseTecido', dados, chartOptions);
}

async function renderizarGraficoTerceirizados(dados) {
    dados = dados || [];
    const chartHeight = Math.max(250, dados.length * 25);

    const chartOptions = realce(dados, 'faccionista', {
        chart: {
            ...TEMA_BASE,
            type: 'bar',
            height: `${chartHeight}px`,
            width: '100%',  // Mantém a largura dinâmica
            toolbar: { show: false },
            dropShadow: { enabled: false },
            events: cliqueNaBarra('faccionista', dados)
        },
        states: ESTADOS_APEX,
        series: serieDe(dados),
        colors: [CORES.azulClaro],
        fill: FILL_BARRA_HORIZONTAL,
        xaxis: {
            type: 'category',
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                show: false,
                rotate: -90,  // Rotaciona totalmente para evitar sobreposição
                trim: false,  // Garante que o texto não seja cortado
                style: {
                    fontSize: '10px',
                    colors: CORES.textoSuave
                }
            }
        },
        yaxis: {
            labels: { style: { fontSize: '10px', colors: CORES.textoSuave } }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                borderRadiusApplication: 'end',
                barHeight: '95%',
                horizontal: true,
            }
        },
        grid: {
                    borderColor: CORES.grade,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: false } },
                    padding: { bottom: 0 }
                },
        tooltip: { y: { formatter: (val) => Number(val).toLocaleString('pt-BR') } },
                 // 🌟 CONFIGURAÇÃO PARA ALTERAR A FONTE DO RÓTULO DE DADOS 🌟
        dataLabels: {
            enabled: true, // É importante que esteja 'true'
            formatter: (val) => Number(val).toLocaleString('pt-BR'),
            style: {
                fontSize: '11px', // Altere para o tamanho desejado
                fontWeight: '600', // Altere para o peso desejado (ex: 'bold')
                colors: [CORES.branco]
            },
            dropShadow: { enabled: false }
        }
    });

    montarGrafico('#graficoTerceirizados', dados, chartOptions);
}

async function renderizarGraficoFornecedor(dados) {
    dados = dados || [];
    const chartHeight = Math.max(250, dados.length * 25);

    const chartOptions = realce(dados, 'fornecedor', {
        chart: {
            ...TEMA_BASE,
            type: 'bar',
            height: `${chartHeight}px`,
            width: '100%',  // Mantém a largura dinâmica
            toolbar: { show: false },
            dropShadow: { enabled: false },
            events: cliqueNaBarra('fornecedor', dados)
        },
        states: ESTADOS_APEX,
        series: serieDe(dados),
        colors: [CORES.azulEscuro2],
        fill: {
            ...FILL_BARRA_HORIZONTAL,
            gradient: { ...FILL_BARRA_HORIZONTAL.gradient, gradientToColors: [CORES.azulClaro] }
        },
        xaxis: {
            type: 'category',
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                show: false,
                rotate: -90,  // Rotaciona totalmente para evitar sobreposição
                trim: false,  // Garante que o texto não seja cortado
                style: {
                    fontSize: '10px',
                    colors: CORES.textoSuave
                }
            }
        },
        yaxis: {
            labels: { style: { fontSize: '10px', colors: CORES.textoSuave } }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                borderRadiusApplication: 'end',
                barHeight: '95%',
                horizontal: true,
            }
        },
        grid: {
                    borderColor: CORES.grade,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: false } },
                    padding: { bottom: 0 }
                },
        tooltip: { y: { formatter: (val) => Number(val).toLocaleString('pt-BR') } },
                 // 🌟 CONFIGURAÇÃO PARA ALTERAR A FONTE DO RÓTULO DE DADOS 🌟
        dataLabels: {
            enabled: true, // É importante que esteja 'true'
            formatter: (val) => Number(val).toLocaleString('pt-BR'),
            style: {
                fontSize: '11px', // Altere para o tamanho desejado
                fontWeight: '600', // Altere para o peso desejado (ex: 'bold')
                colors: [CORES.branco]
            },
            dropShadow: { enabled: false }
        }
    });

    montarGrafico('#graficoFornecedores', dados, chartOptions);
}


async function renderizarGraficoOrigemAgrupado(dados) {
    dados = dados || [];
    const chartHeight = 180; // altura fixa mais apropriada para barras verticais

    const chartOptions = realce(dados, 'origem', {
        chart: {
            ...TEMA_BASE,
            type: 'bar',
            height: `${chartHeight}px`,
            width: '100%',
            toolbar: { show: false },
            dropShadow: { enabled: false },
            events: cliqueNaBarra('origem', dados)
        },
        states: ESTADOS_APEX,
        series: serieDe(dados),
        colors: [CORES.azulClaro],
        fill: FILL_BARRA,
       xaxis: {
    type: 'category',
    labels: {
        show: true,
        rotate: -45,
        rotateAlways: true,
        trim: false,
        hideOverlappingLabels: false,
        style: {
            fontSize: '10px',
            fontWeight: 'normal',
            colors: CORES.textoSuave,
            textAlign: 'center' // 👈 Centraliza o texto!
        }
    },
    tickPlacement: 'on',
    axisTicks: { show: false },
    axisBorder: { show: false }
},
        yaxis: {
            title: {
        text: undefined // 👈 remove o título do eixo Y
    },
            labels: {
                show: false,
                style: { fontSize: '10px', colors: CORES.textoSuave }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                borderRadiusApplication: 'end',
                horizontal: false, // 👈 Agora as barras ficam verticais
                columnWidth: '50%' // 👈 Ajusta a espessura das barras
            }
        },
        grid: {
            borderColor: CORES.grade,
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } },
            padding: { bottom: 0 }
        },
        tooltip: { y: { formatter: (val) => Number(val).toLocaleString('pt-BR') } },
        // 👇 Aqui vem a mágica
        dataLabels: {
            enabled: true,
             formatter: function (val) {
        // 👇 transforma 1200 em "1.200"
        return val.toLocaleString('pt-BR');},
            style: {
                colors: [CORES.branco], // texto branco
                fontSize: '11px',
                fontWeight: 'bold'
            },
            dropShadow: { enabled: false },
            background: {
                enabled: true,
                foreColor: CORES.azulEscuro, // 👈 preenchimento da caixa, não o texto
                borderRadius: 4,
                padding: 3,
                opacity: 1,
                borderWidth: 0,
                borderColor: CORES.azulEscuro
            }
        }
    });

    montarGrafico('#graficoOrigemAgrupado', dados, chartOptions);
}




let searchTimeout;

function Tabela_detalha_defeitos(lista) {
    if ($.fn.DataTable.isDataTable('#tabela_detalhamento')) {
        $('#tabela_detalhamento').DataTable().destroy();
        }

    // 1. 🎯 Capturar a instância da tabela na variável 'tabela'
    const tabela = $('#tabela_detalhamento').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 12,
        data: lista || [],
        dom: 'Bfrtip',
        buttons: {
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
                    title: 'Analise Defeitos por OP/Motivo',
                    className: 'btn-tabelas',
                    exportOptions: {
                        columns: ':visible',
                    }
                },
                // ... outros botões
            ]
        },

        autoWidth: true,
        scrollX: true,

        columns: [
            { data: 'numeroOP', width: '5%' },
            { data: 'codEngenharia', width: '5%' },
            { data: 'descProd', width: '10%' },
            { data: 'data_receb', width: '5%' },
            { data: 'nomeOrigem', width: '10%' },
            { data: 'nome', width: '25%' },
            { data: 'nomeFaccicionista', width: '15%' },
            { data: 'fornencedorPreferencial', width: '15%' },
            { data: 'qtd', width: '10%' }
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },

        // 2. 🚀 Mover a lógica de pesquisa para initComplete (executado apenas uma vez)
     initComplete: function () {
            var tabelaApi = this.api();

            function atualizarTotal() {
                var coluna_qtd_indice = 8;

                var intVal = function (i) {
                    if (typeof i === 'string') return i.replace(/[^0-9]/g, '') * 1;
                    return typeof i === 'number' ? i : 0;
                };

                var totalVisivel = tabelaApi
                    .column(coluna_qtd_indice, { search: 'applied' })
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $('#total-quantidade')
                    .html(totalVisivel.toLocaleString('pt-BR'))
                    .css('font-weight', 'bold');
            }

            // Atualiza o total a cada filtro.
            // O .off() evita empilhar handlers: a tabela é remontada a cada
            // clique no cruzamento de filtros.
            $('.search-input-defeitos').off('input.defeitos').on('input.defeitos', function () {
                const input = $(this);
                clearTimeout(searchTimeout);

                searchTimeout = setTimeout(() => {
                    tabelaApi
                        .column(input.closest('th').index())
                        .search(input.val())
                        .draw();

                    atualizarTotal(); // 👈 recalcula o total após o filtro
                }, 500);
            });

            // A tabela é remontada a cada cruzamento: reaplica o que já estava
            // digitado nos campos de busca do cabeçalho
            let reaplicar = false;
            $('.search-input-defeitos').each(function () {
                const valor = $(this).val();
                if (valor) {
                    tabelaApi.column($(this).closest('th').index()).search(valor);
                    reaplicar = true;
                }
            });
            if (reaplicar) tabelaApi.draw();

            // Chama 1x ao iniciar
            atualizarTotal();
        },


       footerCallback: function (row, data, start, end, display) {
    var api = this.api();
    var coluna_qtd_indice = 8;

    var intVal = function (i) {
        if (typeof i === 'string') {
            return i.replace(/[^0-9]/g, '') * 1;
        }
        return typeof i === 'number' ? i : 0;
    };

    var totalVisivel = api
        .column(coluna_qtd_indice, { search: 'applied' })
        .data()
        .reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);

    $('#total-quantidade').html(
        totalVisivel.toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
    ).css('font-weight', 'bold');
},

    });
}
