$(document).ready(async () => {
    $('#NomeRotina').text('Início');
    $('#loadingModal').modal('show');
    await Promise.all([
        ConsultarDadosEstoque(),
        ConsultarDadosPedidos(),
        ConsultarDadosEndereco()
    ]);
    await Grafico1();
    await Grafico2();
    await Grafico3();
    await Grafico4();
    $('#loadingModal').modal('hide');
});

let PecasEstoque = '';
let PecasRepostas = '';
let TotalPecasPedidos = "";
let TotalPecasPedidosRepostas = "";
let TotalPedidosRetorna = "";
let TotalPedidosFechados = "";
let TotalDeEnderecos = "";
let TotalEnderecosDisponives = "";

const ConsultarDadosEstoque = () => {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Dados_Estoque'
            },
            success: (data) => {
                PecasEstoque = parseInt(data[0]['1.1-Total de Peças Nat. 5'].replace(/\./g, '').replace(/\ pçs/, '')).toLocaleString('pt-BR');
                PecasRepostas = parseInt(data[0]['1.3-Peçs Repostas'].replace(/\./g, '').replace(/\ pçs/, '')).toLocaleString('pt-BR');
                TotalPecasPedidos = parseInt(data[0]['2.1- Total de Skus nos Pedidos em aberto '].replace(/\./g, '').replace(/\ pçs/, '')).toLocaleString('pt-BR');
                TotalPecasPedidosRepostas = parseInt(data[0]['2.3-Qtd de Enderecos OK Reposto nos Pedido'].replace(/\./g, '').replace(/\ pçs/, '')).toLocaleString('pt-BR');
                resolve();
            },
            error: (xhr, status, error) => {
                reject(error);
            }
        });
    });
}

const ConsultarDadosPedidos = () => {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Dados_Pedidos'
            },
            success: (data) => {
                TotalPedidosRetorna = parseInt(data[0]["1. Total de Pedidos no Retorna"].replace(/\./g, '').replace(/\ pçs/, '')).toLocaleString('pt-BR');
                TotalPedidosFechados = parseInt(data[0]["2. Total de Pedidos fecham 100%"].replace(/\./g, '').replace(/\ pçs/, '')).toLocaleString('pt-BR');
                resolve();
            },
            error: (xhr, status, error) => {
                reject(error);
            }
        });
    });
}

const ConsultarDadosEndereco = () => {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Dados_Enderecos',
                natureza: '5',
            },
            success: (data) => {
                TotalDeEnderecos = parseInt(data[0]['1- Total de Enderecos Natureza '].replace(/\./g, '').replace(/\ pçs/, '')).toLocaleString('pt-BR');
                TotalEnderecosDisponives = parseInt(data[0]['2- Total de Enderecos Disponiveis'].replace(/\./g, '').replace(/\ pçs/, '')).toLocaleString('pt-BR');
                resolve();
            },
            error: (xhr, status, error) => {
                reject(error);
            }
        });
    });
}

async function Grafico1() {
    let PecasNaoRepostas = (PecasEstoque - PecasRepostas).toLocaleString('pt-BR');
    PecasNaoRepostas = PecasNaoRepostas.replace(/\,/g, '.')
    if (!isNaN(PecasEstoque) && !isNaN(PecasRepostas)) {
        const PercentualReposto = (PecasRepostas / PecasEstoque) * 100;
        const PercentualNaoReposto = 100 - PercentualReposto;
        console.log(PercentualReposto);
        console.log(PercentualNaoReposto)
        const options = {
            chart: {
                type: 'donut',
                height: '50%'
            },
            title: {
                text: 'Taxa de Peças Repostas',
                align: 'center',
                style: {
                    fontSize: '20px',
                    fontWeight: 'bold',
                    color: '#333'
                }
            },
            series: [PercentualReposto, PercentualNaoReposto],
            labels: [`Total De Peças no Estoque: <b>${PecasEstoque}</b>`, `Total de Peças Repostas: <b>${PecasRepostas}</b>`, `Diferença: ${PecasNaoRepostas}`],
            colors: ['#112d7e', '#008FFB', 'red'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: '200px'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            name: {
                                show: true
                            },
                            value: {
                                show: true,
                                formatter: function (val) {
                                    return val.toFixed(2) + "%"; // Mostra o valor com duas casas decimais
                                }
                            },
                            total: {
                                show: true,
                                formatter: function () {
                                    return PercentualReposto.toFixed(2) + '%'; // Exibe o percentual de endereços ocupados no centro do donut
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: true,
                position: 'bottom',
                fontSize: '15px'
            }
        };

        const chart1 = new ApexCharts(document.querySelector("#chart1"), options);
        chart1.render();
    } else {
        throw new Error('Valores inválidos para criar o gráfico');
    }
}

async function Grafico2() {
    let totalEnderecosOcupados = parseInt(TotalDeEnderecos.replace(/\./g, '')) - parseInt(TotalEnderecosDisponives.replace(/\./g, ''));
    let totalEnderecosOcupados2 = (parseInt(TotalDeEnderecos.replace(/\./g, '')) - parseInt(TotalEnderecosDisponives.replace(/\./g, ''))).toLocaleString('pt-BR');;
    if (!isNaN(totalEnderecosOcupados)) {
        const PercentualEnderecosOcupados = (totalEnderecosOcupados / parseInt(TotalDeEnderecos.replace(/\./g, ''))) * 100;
        const PercentualEnderecosDisponiveis = 100 - PercentualEnderecosOcupados;

        const options = {
            chart: {
                type: 'donut',
                height: '50%'
            },
            title: {
                text: 'Taxa de Ocupação dos Endereços',
                align: 'center',
                style: {
                    fontSize: '20px',
                    fontWeight: 'bold',
                    color: '#333'
                }
            },
            series: [PercentualEnderecosOcupados, PercentualEnderecosDisponiveis],
            labels: [`Enderecos Totais: <b>${TotalDeEnderecos}</b>`, `Enderecos Ocupados: <b>${totalEnderecosOcupados2}</b>`, `Diferença: ${TotalEnderecosDisponives}`],
            colors: ['#112d7e', '#008FFB', 'red'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: '200px'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            name: {
                                show: true
                            },
                            value: {
                                show: true,
                                formatter: function (val) {
                                    return val.toFixed(2) + "%"; // Mostra o valor com duas casas decimais
                                }
                            },
                            total: {
                                show: true,
                                formatter: function () {
                                    return PercentualEnderecosOcupados.toFixed(2) + '%'; // Exibe o percentual de endereços ocupados no centro do donut
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: true,
                position: 'bottom',
                fontSize: '15px'
            }
        };

        const chart2 = new ApexCharts(document.querySelector("#chart2"), options);
        chart2.render();

    } else {
        throw new Error('Valores inválidos para criar o gráfico');
    }
}

async function Grafico3() {
    // Parse os valores como números inteiros removendo pontos e espaços
    TotalPedidosRetorna = parseInt(TotalPedidosRetorna.replace(/\./g, '').replace(/\ pçs/, ''));
    TotalPedidosFechados = parseInt(TotalPedidosFechados.replace(/\./g, '').replace(/\ pçs/, ''));

    // Calcule o total de pedidos que não fecham
    let TotalPedidosNaoFecham = (TotalPedidosRetorna - TotalPedidosFechados).toLocaleString('pt-BR');

    // Verifique se os valores são números válidos
    if (!isNaN(TotalPedidosRetorna) && !isNaN(TotalPedidosFechados)) {
        // Calcule os percentuais
        const PercentualPedidosFecham = (TotalPedidosFechados / TotalPedidosRetorna) * 100;
        const PercentualPedidosNaoFecham = 100 - PercentualPedidosFecham;

        // Defina as opções do gráfico
        const options = {
            chart: {
                type: 'donut',
                height: '50%'
            },
            title: {
                text: 'Taxa de Pedidos 100%',
                align: 'center',
                style: {
                    fontSize: '20px',
                    fontWeight: 'bold',
                    color: '#333'
                }
            },
            series: [PercentualPedidosFecham, PercentualPedidosNaoFecham],
            labels: [
                `Pedidos no Retorna: <b>${TotalPedidosRetorna.toLocaleString('pt-BR')}</b>`, 
                `Pedidos 100% Repostos: <b>${TotalPedidosFechados.toLocaleString('pt-BR')}</b>`, 
                `Diferença: ${TotalPedidosNaoFecham}`
            ],
            colors: ['#112d7e', '#008FFB', 'red'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: '200px'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            name: {
                                show: true
                            },
                            value: {
                                show: true,
                                formatter: function (val) {
                                    return val.toFixed(2) + "%"; // Mostra o valor com duas casas decimais
                                }
                            },
                            total: {
                                show: true,
                                formatter: function () {
                                    return PercentualPedidosFecham.toFixed(2) + '%'; // Exibe o percentual de endereços ocupados no centro do donut
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: true,
                position: 'bottom',
                fontSize: '15px'
            }
        };

        // Renderize o gráfico
        const chart3 = new ApexCharts(document.querySelector("#chart3"), options);
        chart3.render();

    } else {
        throw new Error('Valores inválidos para criar o gráfico');
    }
}


async function Grafico4() {
     TotalPecasPedidos = parseInt(TotalPecasPedidos.replace(/\./g, '').replace(/\ pçs/, ''));
     TotalPecasPedidosRepostas = parseInt(TotalPecasPedidosRepostas.replace(/\./g, '').replace(/\ pçs/, ''));

    let TotalPecasNaoRepostas = TotalPecasPedidos - TotalPecasPedidosRepostas;

    // Verifique se os valores são números válidos
    if (!isNaN(TotalPecasPedidos) && !isNaN(TotalPecasPedidosRepostas)) {
        // Calcule os percentuais
        const PercentualReposto = (TotalPecasPedidosRepostas / TotalPecasPedidos) * 100;
        const PercentualNaoReposto = 100 - PercentualReposto;

        // Defina as opções do gráfico
        const options = {
            chart: {
                type: 'donut',
                height: '50%'
            },
            title: {
                text: 'Taxa de Peças Disponiveis para Separação',
                align: 'center',
                style: {
                    fontSize: '20px',
                    fontWeight: 'bold',
                    color: '#333'
                }
            },
            series: [PercentualReposto, PercentualNaoReposto],
            labels: [
                `Total de Peças nos Pedidos: <b>${TotalPecasPedidos.toLocaleString('pt-BR')}</b>`, 
                `Total de Peças Repostas: <b>${TotalPecasPedidosRepostas.toLocaleString('pt-BR')}</b>`, 
                `Diferença: ${TotalPecasNaoRepostas.toLocaleString('pt-BR')}`
            ],
            colors: ['#112d7e', '#008FFB', 'red'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: '200px'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            name: {
                                show: true
                            },
                            value: {
                                show: true,
                                formatter: function (val) {
                                    return val.toFixed(2) + "%"; // Mostra o valor com duas casas decimais
                                }
                            },
                            total: {
                                show: true,
                                formatter: function () {
                                    return PercentualReposto.toFixed(2) + '%'; // Exibe o percentual de endereços ocupados no centro do donut
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: true,
                position: 'bottom',
                fontSize: '15px'
            }
        };

        // Renderize o gráfico
        const chart4 = new ApexCharts(document.querySelector("#chart4"), options);
        chart4.render();

    } else {
        throw new Error('Valores inválidos para criar o gráfico');
    }
}

