$(document).ready(() => {
    ConsultaColecao();


    $('#searchInputColecao').on('keyup', function() {
        var searchText = $(this).val().toLowerCase();
        var $container = $('#checkboxContainerColecao');
        $container.find('.filtro').closest('label').each(function() {
            var $label = $(this);
            var labelText = $label.text().toLowerCase();
            if (labelText.includes(searchText)) {
                $label.show().prependTo($container);
            } else {
                $label.hide();
            }
        });
    });

    $('#NomeRotina').text('Fila das Fases')

    function fecharDropdowns() {
        $('.dropdown-menu').hide();
    }

    // Evento de clique no documento para fechar dropdowns ao clicar fora
    $(document).click(function(event) {
        var $target = $(event.target);
        if (!$target.closest('.dropdown').length) {
            fecharDropdowns();
        }
    });

    // Evento de clique nos botões de dropdown
    $('.dropdown-toggle').click(function(event) {
        event.stopPropagation();
        var $this = $(this);
        var $dropdownMenu = $this.next('.dropdown-menu');

        // Fechar todos os outros dropdowns
        $('.dropdown-menu').not($dropdownMenu).hide();

        // Toggle do dropdown clicado
        $dropdownMenu.toggle();
    });

});

async function ConsultarFila(Colecoes) {
    $('#loadingModal').modal('show');
    try {
        const dados = {
            Colecao: Colecoes
        };

        var requestData = {
            acao: "Consultar_Fila_Fases",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response);
        criarGraficos(response);
        DetalhaFila();
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

const ConsultaColecao = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Colecao'
            }
        });
        console.log(response)
        const ColecoesFiltradas = response.filter(item => item.COLECAO !== '-');
        $('#checkboxContainerColecao').empty();
        ColecoesFiltradas.forEach(item => {
            $('#checkboxContainerColecao').append(`<label><input type="checkbox" class="filtro" value="${item["COLECAO"]}"> ${item["COLECAO"]}</label>`);
        });
    } catch (error) {
        console.error('Erro ao consultar chamados:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}



async function DetalhaFila() {
    try {
        let timeoutId;
        $('.chart-container').hover(function() {
            clearTimeout(timeoutId);
            const index = $(this).index('.chart-container');
            const detalhaInfo = $('#detalha-info');
            const containerTop = $(this).offset().top;
            const windowTop = $(window).scrollTop();

            // Verifica se a modal ultrapassaria o topo da página
            if (containerTop - detalhaInfo.outerHeight() < windowTop) {
                // Se sim, posiciona a modal abaixo da div
                detalhaInfo.css({
                    display: 'block',
                    top: containerTop + $(this).outerHeight(),
                    left: $(this).offset().left
                });
            } else {
                // Se não, posiciona a modal acima da div
                detalhaInfo.css({
                    display: 'block',
                    top: containerTop - detalhaInfo.outerHeight(),
                    left: $(this).offset().left
                });
            }
            const faseAtual = $(this).find('h5').text(); // Obtém o título da fase atual
            ConsultarFilaFases(faseAtual); // Chama a função de consulta passando o título da fase

        }, function() {
            timeoutId = setTimeout(() => {
                $('#detalha-info').css('display', 'none');
            }, 500);
        });

        $('#detalha-info').hover(function() {
            clearTimeout(timeoutId);
        }, function() {
            $('#detalha-info').css('display', 'none');
        });

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {}
}


async function ConsultarFilaFases(faseAtual) {
    try {
        const dados = {
            nomeFase: faseAtual
        };

        var requestData = {
            acao: "Consultar_Detalha_Fila",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        criarGraficosHover(response, faseAtual); // Chama a função para criar o modal com base na resposta

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {}
}

function criarGraficos(dados) {
    const divGraficos = document.getElementById('Graficos');
    divGraficos.innerHTML = '';

    dados.forEach((item, index) => {
        const chartContainer = document.createElement('div');
        chartContainer.className = 'chart-container d-flex col-12 mb-4 align-items-center justify-content-center';
        chartContainer.style.cursor = 'pointer';
        chartContainer.style.border = '1px solid black';
        chartContainer.id = 'chart' + index;

        const faseTitulo = document.createElement('h5');
        faseTitulo.className = 'col-2 text-center';
        faseTitulo.style.fontSize = '15px';
        faseTitulo.textContent = item.fase;
        chartContainer.appendChild(faseTitulo);

        const chartDiv = document.createElement('div');
        chartDiv.className = 'col-10';
        chartContainer.appendChild(chartDiv);

        divGraficos.appendChild(chartContainer);

        var options = {
            series: [{
                name: 'Fila',
                data: [item['Fila'].toLocaleString()]
            }, {
                name: 'Carga Atual',
                data: [item['Carga Atual'].toLocaleString()]
            }],
            chart: {
                type: 'bar',
                height: 90,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '100%',
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            dataLabels: {
                enabled: true,
                offsetX: 10,
                style: {
                    fontSize: '14px',
                    colors: ['#000']
                },
                formatter: function(val) {
                    return val;
                }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#fff']
            },
            tooltip: {
                shared: true,
                intersect: false
            },
            xaxis: {
                categories: ['Fila'],
                labels: {
                    show: false
                }
            },
            legend: {
                show: false
            }
        };

        var chart = new ApexCharts(chartDiv, options);
        chart.render();
    });
}

function criarGraficosHover(dados, faseAtual) {
    console.log(dados)
    const divGraficos2 = document.getElementById('Graficos2');
    divGraficos2.innerHTML = '';
    const titulo = document.createElement('h2');
    titulo.textContent = faseAtual;

    divGraficos2.append(titulo)

    dados.forEach((item, index) => {
        const chartContainer = document.createElement('div');
        chartContainer.className = 'chart-container d-flex col-12 mb-4 align-items-center justify-content-center';
        chartContainer.style.cursor = 'pointer';
        chartContainer.style.border = '1px solid black';
        chartContainer.id = 'chartHover' + index;

        const faseTitulo = document.createElement('h5');
        faseTitulo.className = 'col-2 text-center';
        faseTitulo.style.fontSize = '15px';
        faseTitulo.textContent = item.faseAtual;
        chartContainer.appendChild(faseTitulo);

        const chartDiv = document.createElement('div');
        chartDiv.className = 'col-10';
        chartContainer.appendChild(chartDiv);

        divGraficos2.appendChild(chartContainer);

        // Configuração do gráfico
        var options = {
            series: [{
                name: 'Quantidade de Peças',
                data: [item['pcs'].toLocaleString()] // Utiliza a quantidade de peças do item
            }],
            chart: {
                type: 'bar',
                height: 90,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '100%',
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            dataLabels: {
                enabled: true,
                offsetX: 10,
                style: {
                    fontSize: '14px',
                    colors: ['#000']
                },
                formatter: function(val) {
                    return val;
                }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#fff']
            },
            tooltip: {
                shared: true,
                intersect: false
            },
            xaxis: {
                categories: ['Quantidade'],
                labels: {
                    show: false
                }
            },
            legend: {
                show: false
            }
        };

        // Criação do gráfico
        var chart = new ApexCharts(chartDiv, options);
        chart.render(); // Renderiza o gráfico dentro do elemento chartDiv
    });
}





async function Filtrar() {
    var selectedItems = [];
    $('#checkboxContainerColecao .filtro:checked').each(function() {
        selectedItems.push($(this).val());
    });
    ConsultarFila(selectedItems)

}