$(document).ready(async () => {
    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0]; // Obtém a data de hoje no formato 'aaaa-mm-dd'
    await $('#dataInicio, #dataFim').val(formattedDate);
    atualizar();

});

async function atualizar(){
    // Exibe no campo, mas mantém um valor oculto para manipulação correta
    let campoBusca = document.getElementById("campoBusca").value;

    console.log(`teste input avançado: ${campoBusca}`)
    // Certifique-se de que o gráfico só será renderizado após o DOM estar completamente carregado
    await Cosultar_Qualidade();
    await Consultar_Motivos();
    await Consultar_defeito_baseTecido();
    await Cosultar_Origem_faccionista();
    await Cosultar_Origem_fornecedor();
    await Cosultar_Origem();
    await detalha_defeitos();
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
            $('#graficoDonut').html('<p>Nenhum dado a ser exibido</p>');
        } else {
            $('#graficoDonut').html('');
            renderizarGrafico(data[0]["1- Peças com Motivo de 2Qual."], data[0]["2- Total Peças Baixadas periodo"]);
        }

        $('#totalPecas').text(data[0]['2- Total Peças Baixadas periodo'])
        $('#totalPecas2Qualidade').text(data[0]['1- Peças com Motivo de 2Qual.'])
    } catch (error) {
        console.error('Erro ao consultar qualidade:', error);
        $('#graficoDonut').html('<p>Erro ao carregar os dados de qualidade</p>');
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consultar_Motivos = async () => {
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
                dataFinal: dataFinal
            }
        });

        // Verifica se os dados estão vazios
        if (data.length === 0) {
            $('#graficoBarras').html('<p>Nenhum dado a ser exibido</p>');
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


const Consultar_defeito_baseTecido = async () => {
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
                dataFinal: dataFinal
            }
        });

        // Verifica se os dados estão vazios
        if (data.length === 0) {
            $('#graficoBaseTecido').html('<p>Nenhum dado a ser exibido</p>');
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



const Cosultar_Origem_faccionista = async () => {
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
                dataFinal: dataFinal
            }
        });

        // Verifica se os dados estão vazios
        if (data === null) {
            $('#graficoTerceirizados').html('<p>Nenhum dado a ser exibido</p>');
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


const Cosultar_Origem_fornecedor = async () => {
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
                dataFinal: dataFinal
            }
        });

        // Verifica se os dados estão vazios
        if (data === null) {
            $('#graficoFornecedores').html('<p>Nenhum dado a ser exibido</p>');
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

const Cosultar_Origem = async () => {
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
                dataFinal: dataFinal
            }
        });

        // Verifica se os dados estão vazios
        if (data === null) {
            $('#graficoOrigemAgrupado').html('<p>Nenhum dado a ser exibido</p>');
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


const detalha_defeitos = async () => {
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
                dataFinal: dataFinal
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
            type: 'donut',
            // Você provavelmente vai querer remover ou diminuir esse 'height: 350' para caber no seu container de 100px.
            // Para caber nos 80px/100px que você definiu no HTML, você pode remover o 'height' aqui, 
            // ou defini-lo como 'height: 80', desde que o div pai também esteja limitado.
            height: '100%' // Usar 100% ou um valor menor (ex: 80) para respeitar o container de 80px/100px
        },
        series: [porcentagem2Qualidade, porcentagemDiferenca],
        labels: ["Peças com Motivo 2Qual.", "Peças Sem Defeito"],
        colors: ['#FF4560', '#008FFB'],
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(2) + "%"; // Exibe o percentual com 2 casas decimais
            },
            style: {
                fontSize: '10px'
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%', // Ajusta o tamanho do buraco do donut
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'indice 2º.',
                            fontSize: '12px',
                            color: '#333',
                            formatter: function () {
                                return porcentagem2Qualidade.toFixed(2) + '%';
                            }
                        }
                    }
                }
            }
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
        xaxis: {
            categories: data.map(item => item.motivo2Qualidade),
            labels: {
                rotate: -90, // Rotaciona totalmente para evitar sobreposição
                trim: false, // Garante que o texto não seja cortado
                style: {
                    fontSize: '10px',
                    // whiteSpace: 'break-spaces' // Faz a legenda quebrar linha
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                barHeight: '75%', // Ajusta a altura das barras
                // Para rótulos de dados dentro da barra (opcional):
                // dataLabels: { position: 'top' } 
            }
        },
        grid: {
            padding: {
                bottom: 60 // Dá mais espaço para a legenda não ser cortada
            }
        },
        // 🌟 CONFIGURAÇÃO PARA ALTERAR A FONTE DO RÓTULO DE DADOS 🌟
        dataLabels: {
            enabled: true, // É importante que esteja 'true'
            style: {
                fontSize: '10px', // Altere para o tamanho desejado
                fontFamily: 'Arial, sans-serif', // Altere para a fonte desejada
                fontWeight: '500', // Altere para o peso desejado (ex: 'bold')
                // color: '#000000' // Opcional: para mudar a cor do texto
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#graficoBarras"), chartOptions);
    chart.render();
}

async function renderizarGraficoBarras_baseTecido(data) {
    const chartWidth = Math.max(350, data.length * 35);

    const chartOptions = {
        chart: {
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
        xaxis: {
            categories: data.map(item => item.nomeItem),
            labels: {
                rotate: -90,  // Rotaciona totalmente para evitar sobreposição
                trim: false,  // Garante que o texto não seja cortado
                style: {
                    fontSize: '12px',
                  //  whiteSpace: 'break-spaces' // Faz a legenda quebrar linha
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 3,
                barHeight: '80%', // Ajusta a altura das barras
            }
        },
        grid: {
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
        xaxis: {
            categories: data.map(item => item.nomeFaccicionista),
            labels: {
                rotate: -90,  // Rotaciona totalmente para evitar sobreposição
                trim: false,  // Garante que o texto não seja cortado
                style: {
                    fontSize: '10px',
                    //whiteSpace: 'break-spaces' // Faz a legenda quebrar linha
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                barHeight: '95%',
                horizontal: true,
            }
        },
        grid: {
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: false } },
                    padding: { bottom: 0 }
                },
                 // 🌟 CONFIGURAÇÃO PARA ALTERAR A FONTE DO RÓTULO DE DADOS 🌟
        dataLabels: {
            enabled: true, // É importante que esteja 'true'
            style: {
                fontSize: '11px', // Altere para o tamanho desejado
                fontFamily: 'Arial, sans-serif', // Altere para a fonte desejada
                fontWeight: '500', // Altere para o peso desejado (ex: 'bold')
                // color: '#000000' // Opcional: para mudar a cor do texto
            }
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
        xaxis: {
            categories: data.map(item => item.fornencedorPreferencial),
            labels: {
                rotate: -90,  // Rotaciona totalmente para evitar sobreposição
                trim: false,  // Garante que o texto não seja cortado
                style: {
                    fontSize: '10px',
                    //whiteSpace: 'break-spaces' // Faz a legenda quebrar linha
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                barHeight: '95%',
                horizontal: true,
            }
        },
        grid: {
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: false } },
                    padding: { bottom: 0 }
                },
                 // 🌟 CONFIGURAÇÃO PARA ALTERAR A FONTE DO RÓTULO DE DADOS 🌟
        dataLabels: {
            enabled: true, // É importante que esteja 'true'
            style: {
                fontSize: '11px', // Altere para o tamanho desejado
                fontFamily: 'Arial, sans-serif', // Altere para a fonte desejada
                fontWeight: '500', // Altere para o peso desejado (ex: 'bold')
                // color: '#000000' // Opcional: para mudar a cor do texto
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#graficoFornecedores"), chartOptions);
    chart.render();
   // chart.resize();

}


async function renderizarGraficoOrigemAgrupado(data) {
    const chartHeight = 200; // altura fixa mais apropriada para barras verticais

    const chartOptions = {
        chart: {
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
                style: { fontSize: '10px' }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false, // 👈 Agora as barras ficam verticais
                columnWidth: '50%' // 👈 Ajusta a espessura das barras
            }
        },
        grid: {
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } },
            padding: { bottom: 0 }
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

