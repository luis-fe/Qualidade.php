/* ============================================================
   Paleta da tela — Azul Escuro | Azul Claro | Branco
   ============================================================ */
const CORES = {
    azulEscuro: '#10045a',
    azulEscuro2: '#2a1aa0',
    azulClaro: '#008ffb',
    azulClaro2: '#00d4ff',
    azulClaro3: '#6fc5ff',
    azulSuave: '#e8f1fd',
    branco: '#ffffff',
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

$(document).ready(async () => {
    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0]; // Obtém a data de hoje no formato 'aaaa-mm-dd'
    await $('#dataInicio, #dataFim').val(formattedDate);
    atualizar();

});

async function atualizar(){
    // Exibe no campo, mas mantém um valor oculto para manipulação correta
    let campoBusca = document.getElementById("campoBusca").value
        .toUpperCase()
        .replace(/ /g, "%20");
    console.log(`teste input avançado: ${campoBusca}`)
    // Certifique-se de que o gráfico só será renderizado após o DOM estar completamente carregado
    await Cosultar_Qualidade();
    await Consultar_Motivos(campoBusca);
    await Consultar_defeito_baseTecido(campoBusca);
    await Cosultar_Origem_faccionista(campoBusca);
    await Cosultar_Origem_fornecedor(campoBusca);
    await Cosultar_Origem(campoBusca);
    await detalha_defeitos(campoBusca);
      // 👇 força o navegador a redesenhar os gráficos
  setTimeout(() => {
    window.dispatchEvent(new Event('resize'));
  }, 300);
};

const Cosultar_Qualidade = async () => {
    $('#loadingModal').modal('show');
    const dataInicial = $('#dataInicio').val();
    const dataFinal = $('#dataFim').val();
    console.log(`${dataInicial} e ${dataFinal}`);

    try {
        // Pega os valores das datas no formato yyyy-mm-dd e formata para dd/mm/yyyy

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

        if (data[0]["1- Peças com Motivo de 2Qual."] === 0) {
            $('#graficoDonut').html(semDados());
        } else {
            $('#graficoDonut').html('');
            renderizarGrafico(data[0]["1- Peças com Motivo de 2Qual."], data[0]["2- Total Peças Baixadas periodo"]);
        }

            $('#totalPecas').text(
                Number(data[0]['2- Total Peças Baixadas periodo']).toLocaleString('pt-BR')
            );        
        $('#totalPecas2Qualidade').text(
            Number(data[0]['1- Peças com Motivo de 2Qual.']).toLocaleString('pt-BR'));
    } catch (error) {
        console.error('Erro ao consultar qualidade:', error);
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

        // Verifica se os dados estão vazios
        if (data.length === 0) {
            $('#graficoBarras').html(semDados());
        } else {
            $('#graficoBarras').html('');
            renderizarGraficoBarras(data);
        }

    } catch (error) {
        console.error('Erro ao consultar motivos:', error);
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

        // Verifica se os dados estão vazios
        if (data.length === 0) {
            $('#graficoBaseTecido').html(semDados());
        } else {
            $('#graficoBaseTecido').html('');
            renderizarGraficoBarras_baseTecido(data);
        }

    } catch (error) {
        console.error('Erro ao consultar motivos:', error);
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

        // Verifica se os dados estão vazios
        if (data === null) {
            $('#graficoTerceirizados').html(semDados());
        } else {
            $('#graficoTerceirizados').html('');
            renderizarGraficoTerceirizados(data);
        }

    } catch (error) {
        console.error('Erro ao consultar motivos:', error);
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

        // Verifica se os dados estão vazios
        if (data === null) {
            $('#graficoFornecedores').html(semDados());
        } else {
            $('#graficoFornecedores').html('');
            renderizarGraficoFornecedor(data);
        }

    } catch (error) {
        console.error('Erro ao consultar motivos:', error);
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

        // Verifica se os dados estão vazios
        if (data === null) {
            $('#graficoOrigemAgrupado').html(semDados());
        } else {
            $('#graficoOrigemAgrupado').html('');
            renderizarGraficoOrigemAgrupado(data);
        }

    } catch (error) {
        console.error('Erro ao consultar motivos:', error);
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


        Tabela_detalha_defeitos(data);


    } catch (error) {
        console.error('Erro ao consultar motivos:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

// Função para formatar a data de yyyy-mm-dd para dd/mm/yyyy
const formatDateToDDMMYYYY = (date) => {
    const [ano, mes, dia] = date.split('-');
    return `${dia}/${mes}/${ano}`;
};

// Função para renderizar o gráfico de donuts ApexCharts
const renderizarGrafico = (pecasComMotivo, totalPecasBaixadas) => {
    const chartElementDonut = document.querySelector("#graficoDonut");
    if (!chartElementDonut) {
        console.error('Elemento #graficoDonut não encontrado.');
        return;
    }

    const totalPecas = parseFloat(totalPecasBaixadas) || 0;
    const pecas2Qualidade = parseFloat(pecasComMotivo) || 0;

    // Evita divisão por zero
    const porcentagem2Qualidade = totalPecas > 0 ? (pecas2Qualidade / totalPecas) * 100 : 0;
    const porcentagemDiferenca = 100 - porcentagem2Qualidade;

    var optionsDonut = {
        chart: {
            ...TEMA_BASE,
            type: 'donut',
            height: '90%'
        },
        series: [porcentagem2Qualidade, porcentagemDiferenca],
        labels: ["Peças com Motivo 2Qual.", "Peças Sem Defeito"],
        // Azul escuro destaca o índice de 2ª qualidade; azul claro é o restante
        colors: [CORES.azulEscuro, CORES.azulClaro3],
        stroke: {
            width: 2,
            colors: [CORES.branco]
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(2) + "%"; // Exibe o percentual com 2 casas decimais
            },
            style: {
                fontSize: '10px',
                fontWeight: 600,
                colors: [CORES.branco, CORES.azulEscuro]
            },
            dropShadow: { enabled: false }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%', // Ajusta o tamanho do buraco do donut
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Índice 2ª',
                            fontSize: '12px',
                            fontWeight: 600,
                            color: CORES.textoSuave,
                            formatter: function () {
                                return porcentagem2Qualidade.toFixed(2) + '%';
                            }
                        },
                        value: {
                            fontSize: '18px',
                            fontWeight: 700,
                            color: CORES.azulEscuro
                        }
                    }
                }
            }
        },
        tooltip: {
            y: { formatter: (val) => val.toFixed(2) + '%' }
        },
        // >>> CONFIGURAÇÃO PARA REMOVER A LEGENDA <<<
        legend: {
            show: false // Propriedade que desabilita a exibição da legenda
        }
    };

    var chartDonut = new ApexCharts(chartElementDonut, optionsDonut);
    chartDonut.render();
  //  chartDonut.resize();

};

async function renderizarGraficoBarras(data) {
    const chartWidth = Math.max(350, data.length * 40);

    const chartOptions = {
        chart: {
            ...TEMA_BASE,
            type: 'bar',
            height: 350,
            width: `${chartWidth}px`, // Mantém a largura dinâmica
            toolbar: { show: false },
            dropShadow: { enabled: false }
        },
        series: [{
            name: 'Quantidade',
            data: data.map(item => item.qtd)
        }],
        colors: [CORES.azulClaro],
        fill: FILL_BARRA,
        xaxis: {
            categories: data.map(item => item.motivo2Qualidade),
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
        // 🌟 CONFIGURAÇÃO PARA ALTERAR A FONTE DO RÓTULO DE DADOS 🌟
        dataLabels: {
            enabled: true, // É importante que esteja 'true'
            style: {
                fontSize: '10px', // Altere para o tamanho desejado
                fontWeight: '600', // Altere para o peso desejado (ex: 'bold')
                colors: [CORES.branco]
            },
            dropShadow: { enabled: false },
            background: { enabled: false }
        }
    };

    const chart = new ApexCharts(document.querySelector("#graficoBarras"), chartOptions);
    chart.render();
}

async function renderizarGraficoBarras_baseTecido(data) {
    const chartWidth = Math.max(350, data.length * 35);

    const chartOptions = {
        chart: {
            ...TEMA_BASE,
            type: 'bar',
            height: 350,
            width: `${chartWidth}px`,  // Mantém a largura dinâmica
            toolbar: { show: false },
            dropShadow: { enabled: false }
        },
        series: [{
            name: 'Quantidade',
            data: data.map(item => item.qtd)
        }],
        colors: [CORES.azulClaro],
        fill: FILL_BARRA,
        xaxis: {
            categories: data.map(item => item.nomeItem),
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
            style: {
                fontSize: '10px',
                fontWeight: '600',
                colors: [CORES.branco]
            },
            dropShadow: { enabled: false }
        },
        grid: {
            borderColor: CORES.grade,
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
            padding: {
                bottom: 50 // Dá mais espaço para a legenda não ser cortada
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#graficoBaseTecido"), chartOptions);
    chart.render();
    //chart.resize();

}

async function renderizarGraficoTerceirizados(data) {
    const chartHeight = Math.max(250, data.length * 25);

    const chartOptions = {
        chart: {
            ...TEMA_BASE,
            type: 'bar',
            height: `${chartHeight}px`,
            width: '100%',  // Mantém a largura dinâmica
            toolbar: { show: false },
            dropShadow: { enabled: false }
        },
        series: [{
            name: 'Quantidade',
            data: data.map(item => item.qtd)
        }],
        colors: [CORES.azulClaro],
        fill: FILL_BARRA_HORIZONTAL,
        xaxis: {
            categories: data.map(item => item.nomeFaccicionista),
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
    };

    const chart = new ApexCharts(document.querySelector("#graficoTerceirizados"), chartOptions);
    chart.render();
   // chart.resize();

}

async function renderizarGraficoFornecedor(data) {
    const chartHeight = Math.max(250, data.length * 25);

    const chartOptions = {
        chart: {
            ...TEMA_BASE,
            type: 'bar',
            height: `${chartHeight}px`,
            width: '100%',  // Mantém a largura dinâmica
            toolbar: { show: false },
            dropShadow: { enabled: false }
        },
        series: [{
            name: 'Quantidade',
            data: data.map(item => item.qtd)
        }],
        colors: [CORES.azulEscuro2],
        fill: {
            ...FILL_BARRA_HORIZONTAL,
            gradient: { ...FILL_BARRA_HORIZONTAL.gradient, gradientToColors: [CORES.azulClaro] }
        },
        xaxis: {
            categories: data.map(item => item.fornencedorPreferencial),
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
    };

    const chart = new ApexCharts(document.querySelector("#graficoFornecedores"), chartOptions);
    chart.render();
   // chart.resize();

}


async function renderizarGraficoOrigemAgrupado(data) {
    const chartHeight = 180; // altura fixa mais apropriada para barras verticais

    const chartOptions = {
        chart: {
            ...TEMA_BASE,
            type: 'bar',
            height: `${chartHeight}px`,
            width: '100%',
            toolbar: { show: false },
            dropShadow: { enabled: false }
        },
        series: [{
            name: 'Quantidade',
            data: data.map(item => item.qtd)
        }],
        colors: [CORES.azulClaro],
        fill: FILL_BARRA,
       xaxis: {
    categories: data.map(item => item.nomeOrigem),
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
                foreColor: CORES.branco, // cor do texto dentro do fundo
                borderRadius: 4,
                padding: 3,
                opacity: 1,
                borderWidth: 0,
                borderColor: CORES.azulEscuro,
                color: CORES.azulEscuro // 👈 fundo em azul escuro
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#graficoOrigemAgrupado"), chartOptions);
    chart.render();
   //     chart.resize();
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
        data: lista,
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

            // Atualiza o total a cada filtro
            $('.search-input-defeitos').on('input', function () {
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

