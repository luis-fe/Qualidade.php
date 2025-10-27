$(document).ready(async () => {
    const today = new Date();
    const formattedDate = today.toISOString().split('T')[0]; // Obtém a data de hoje no formato 'aaaa-mm-dd'

    // Exibe no campo, mas mantém um valor oculto para manipulação correta
    $('#dataInicio, #dataFim').val(formattedDate);

    // Certifique-se de que o gráfico só será renderizado após o DOM estar completamente carregado
    await Cosultar_Qualidade();
    await Consultar_Motivos();
    await Cosultar_Origem_faccionista();
    await Cosultar_Origem();
    await detalha_defeitos();
});

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
};

async function renderizarGraficoBarras(data) {
    const chartWidth = Math.max(500, data.length * 50);

    const chartOptions = {
        chart: {
            type: 'bar',
            height: 500,
            width: `${chartWidth}px`,  // Mantém a largura dinâmica
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
                rotate: -90,  // Rotaciona totalmente para evitar sobreposição
                trim: false,  // Garante que o texto não seja cortado
                style: {
                    fontSize: '8px',
                  //  whiteSpace: 'break-spaces' // Faz a legenda quebrar linha
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                barHeight: '50%', // Ajusta a altura das barras
            }
        },
        grid: {
            padding: {
                bottom: 50 // Dá mais espaço para a legenda não ser cortada
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#graficoBarras"), chartOptions);
    chart.render();
}

async function renderizarGraficoTerceirizados(data) {
    const chartHeight = Math.max(500, data.length * 50);

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
                barHeight: 40,
                horizontal: true,
            }
        },
        grid: {
            padding: {
                bottom: 100 // Dá mais espaço para a legenda não ser cortada
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#graficoTerceirizados"), chartOptions);
    chart.render();
}


async function renderizarGraficoOrigemAgrupado(data) {
    const chartHeight = Math.max(500, data.length * 50);

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
                show: false // 👈 Oculta os valores do eixo X
            },
            axisTicks: { show: false }, // 👈 Remove as marquinhas
            axisBorder: { show: false } // 👈 Remove a linha do eixo
        },
        yaxis: {
            labels: {
                show: true, // 👈 Mantém os rótulos visíveis
                style: {
                    fontSize: '10px'
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                barHeight: 40,
                horizontal: true
            }
        },
        grid: {
            xaxis: { lines: { show: false } }, // 👈 Remove as linhas de grade verticais
            padding: {
                bottom: 100
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#graficoOrigemAgrupado"), chartOptions);
    chart.render();
}



function Tabela_detalha_defeitos(lista) {
    if ($.fn.DataTable.isDataTable('#tabela_detalhamento')) {
        $('#tabela_detalhamento').DataTable().destroy();
    }

    $('#tabela_detalhamento').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 12,
        data: lista,
        dom: 'Bfrtip',
        columns: [
            { data: 'numeroOP' },
            { data: 'codEngenharia' },
            { data: 'descProd' },
            { data: 'data_receb' },
            { data: 'nomeOrigem' },
            { data: 'nome' },
            { data: 'qtd' }
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        }
    });
}

