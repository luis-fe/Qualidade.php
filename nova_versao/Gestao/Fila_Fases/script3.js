$(document).ready(() => {
    Consulta_Colecoes();
    const $menu = $("#menu-colecoes");

    $("#dropdownToggle").on("click", function (event) {
        event.preventDefault();
        event.stopPropagation();

        const $button = $(this);
        const offset = $button.offset();

        if ($menu.is(":visible")) {
            $menu.hide();
        } else {
            $menu
                .appendTo("body")
                .css({
                    position: "absolute",
                    top: offset.top + $button.outerHeight(),
                    left: offset.left,
                    display: "block",
                });
        }
    });

    $menu.on("click", function (event) {
        event.stopPropagation();
    });

    // Fecha o menu ao clicar fora
    $(document).on("click", function () {
        $menu.hide();
    });
})
const Consulta_Colecoes = async () => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Colecoes'
            },
        });
        const $menu = $("#menu-colecoes");
        $menu.empty();

        // Adiciona a opção "Selecionar tudo" e começa marcada
        const $selectAllOption = $("<li>")
            .addClass("dropdown-item")
            .html(`
                <label>
                    <input type="checkbox" id="select-all" checked />
                    Selecionar tudo
                </label>
            `);

        $menu.append($selectAllOption);

        const ColecoesFiltradas = response.filter(item => item.COLECAO !== '-');
        ColecoesFiltradas.forEach(item => {
            const $option = $("<li>")
                .addClass("dropdown-item")
                .html(`
                    <label>
                        <input type="checkbox" value="${item.COLECAO}" checked />
                        ${item.COLECAO}
                    </label>
                `);

            $menu.append($option);
        });

        // Função para selecionar ou desmarcar todos os checkboxes
        $('#select-all').change(function () {
            const isChecked = $(this).prop('checked');
            $menu.find('input[type="checkbox"]').each(function () {
                $(this).prop('checked', isChecked);
            });
        });

        // Sincroniza a seleção de "Selecionar tudo" com os checkboxes individuais
        $menu.on('change', 'input[type="checkbox"]', function () {
            const totalCheckboxes = $menu.find('input[type="checkbox"]').length - 1; // Exclui o "Selecionar tudo"
            const checkedCheckboxes = $menu.find('input[type="checkbox"]:not(#select-all):checked').length;

            // Se todos os checkboxes individuais estiverem marcados, marca o "Selecionar tudo"
            $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
        });

    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};


async function Consulta_Fila() {
    $('#loadingModal').modal('show');
    const colecoes = $('#menu-colecoes input[type="checkbox"]:checked')
        .map(function () {
            return $(this).val();
        })
        .get(); // Retorna um array
    const dados = {
        "Colecao": colecoes
    };

    var requestData = {
        acao: "Consulta_Fila",
        dados: dados
    };

    try {
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response);
        criarGraficos(response);
        Detalha_Fila()
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error); // Exibir erro se ocorrer
        $('#loadingModal').modal('hide');
    } finally {
        $('#loadingModal').modal('hide');
    }
}


function criarGraficos(dados) {
    const divGraficos = document.getElementById('Graficos');
    divGraficos.innerHTML = '';

    dados.forEach((item, index) => {
        const chartContainer = document.createElement('div');
        chartContainer.className = 'chart-container d-flex col-12 mb-4 align-items-center justify-content-center';
        chartContainer.style.cursor = 'pointer';
        chartContainer.style.border = '1px solid black';
        chartContainer.style.borderRadius = '10px';
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
                data: [item.Fila]
            }, {
                name: 'Carga Atual',
                data: [item['Carga Atual']]
            }],
            chart: {
                type: 'bar',
                height: 150,
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
                formatter: function (val) {
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
            },
            // Definindo as cores das barras
            colors: ['#fbec5d', '#637ffb'],
            legend: {
                show: true,
                position: 'bottom', // Posiciona a legenda abaixo do gráfico
                horizontalAlign: 'center', // Alinha a legenda horizontalmente no centro
                markers: {
                    width: 10,
                    height: 10,
                    radius: 10 // Define o estilo dos marcadores na legenda (círculo)
                },
                itemMargin: {
                    horizontal: 10, // Define o espaçamento horizontal entre os itens da legenda
                    vertical: 5 // Define o espaçamento vertical entre os itens da legenda
                }
            } // Exemplo de cores: Laranja para 'Fila' e Verde para 'Carga Atual'
        };

        var chart = new ApexCharts(chartDiv, options);
        chart.render();
    });
}



async function Detalha_Fila() {
    try {
        let timeoutId;
        $('.chart-container').hover(function () {
            clearTimeout(timeoutId);

            const index = $(this).index('.chart-container');
            const detalhaInfo = $('#detalha-info');
            const container = $(this);

            // Obtém as coordenadas do container
            const offset = container.offset();
            const containerTop = offset.top;
            const containerLeft = offset.left;
            const containerHeight = container.outerHeight();
            const windowHeight = $(window).height();
            const modalHeight = detalhaInfo.outerHeight();

            let topPosition = containerTop - modalHeight - 10; // Tenta exibir acima do gráfico
            let leftPosition = containerLeft + 20; // Ajuste para a lateral

            // Se a modal ultrapassar o topo da tela, posiciona abaixo do gráfico
            if (topPosition < $(window).scrollTop()) {
                topPosition = containerTop + containerHeight + 10;
            }

            // Ajusta a posição e exibe
            detalhaInfo.css({
                display: 'block',
                top: topPosition + 'px',
                left: leftPosition + 'px'
            });

            // Obtém o título da fase atual
            const faseAtual = container.find('h5').text();
            ConsultarFilaFases(faseAtual);

        }, function () {
            timeoutId = setTimeout(() => {
                $('#detalha-info').css('display', 'none');
            }, 500);
        });

        $('#detalha-info').hover(function () {
            clearTimeout(timeoutId);
        }, function () {
            $('#detalha-info').css('display', 'none');
        });

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    }
}


async function ConsultarFilaFases(faseAtual) {
    try {
        const dados = {
            nomeFase: faseAtual
        };

        var requestData = {
            acao: "Detalha_Fila",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        criarGraficosHover(response, faseAtual)

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally { }
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
                data: [item.pcs] // Utiliza a quantidade de peças do item
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
                formatter: function (val) {
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
            },
            colors: ['#637ffb'],
        };

        // Criação do gráfico
        var chart = new ApexCharts(chartDiv, options);
        chart.render(); // Renderiza o gráfico dentro do elemento chartDiv
    });
}
