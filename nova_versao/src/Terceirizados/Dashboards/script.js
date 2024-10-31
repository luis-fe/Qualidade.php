$(document).ready(async () => {
    await Consultar_Dados(false, "", "", "barras");
    await Consultar_Faccionistas();
    Consultar_Categorias();

    $(document).on('change', '.option-checkbox-faccionista', function() {
        // Se o checkbox for selecionado, desmarcar os outros
        if (this.checked) {
            console.log('Checkbox selecionado: ' + this.value); // Log do valor do checkbox
            $('.option-checkbox-faccionista').not(this).prop('checked', false);
        }
    });

    $(document).on('change', '.option-checkbox-categoria', function() {
        // Se o checkbox for selecionado, desmarcar os outros
        if (this.checked) {
            console.log('Checkbox selecionado: ' + this.value); // Log do valor do checkbox
            $('.option-checkbox-categoria').not(this).prop('checked', false);
        }
    });
});

function AtualizarDados() {
    const checkbox_faccionista = $('.option-checkbox-faccionista:checked').val();
    const checkbox_categoria = $('.option-checkbox-categoria:checked').val();

    if (!checkbox_faccionista && !checkbox_categoria) {
        Consultar_Dados(true, "", "", "barras");
    } else if (checkbox_faccionista && !checkbox_categoria) {
        Consultar_Dados(true, "especial", checkbox_faccionista, "barras");
    } else {
        Consultar_Dados(true, checkbox_categoria || "", checkbox_faccionista || "", "empilhado");
    }

    $('#modal-filtros').modal('hide');
}






async function Consultar_Dados(Congelado, Categoria, Faccionista, formato) {
    $('#loadingModal').modal('show');

    try {
        const requestData = {
            acao: "Consulta_Dados",
            dados: {
                "categoria": Categoria,
                "congelamento": Congelado,
                "apelidoFaccionista": Faccionista
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        console.log(response);
        $('#total_pecas').text(parseInt(response[0]['1- TotalPeças:']).toLocaleString());
        $('#total_ops').text(response[0]['2- TotalOPs']);

        FunctionGraficoFaccionistas(response[0]["3- Distribuicao:"], formato);
        FunctionGraficoCategorias(response[0]["3.1- ResumoCategoria"]);
        FunctionGraficoStatus(response[0]["3.2- ResumoStatus"]);

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        alert('Ocorreu um erro ao buscar os dados. Tente novamente.');
    } finally {
        $('#loadingModal').modal('hide');
    }
}


async function Consultar_Faccionistas() {
    $('#loadingModal').modal('show');

    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Faccionistas',
            }
        });

        console.log(response)

        // Verifique se os dados foram recebidos corretamente
        if (response && response.length > 0) {
            // Limpe as opções anteriores, se houver
            $('#faccionistas-options').empty();

            // Itera sobre os dados recebidos da API
            response.forEach(faccionista => {
                // Cria uma nova opção de checkbox para cada faccionista
                const checkboxOption = `
        <li class="faccionista-item">
            <div class="form-check">
                <input class="form-check-input option-checkbox-faccionista" type="checkbox" id="faccionista-${faccionista['02- nome']}" value="${faccionista['02- nome']}">
                <label class="form-check-label" for="faccionista-${faccionista['02- nome']}">
                    ${faccionista['01- codfaccionista']} - ${faccionista['02- nome']}
                </label>
            </div>
        </li>
    `;

                // Adiciona cada opção à lista de faccionistas
                $('#faccionistas-options').append(checkboxOption);
            });
        } else {
            // Caso não haja dados, exiba uma mensagem
            $('#faccionistas-options').html('<li>Nenhum faccionista encontrado.</li>');
        }

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        alert('Ocorreu um erro ao buscar os dados. Tente novamente.');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Consultar_Categorias() {
    $('#loadingModal').modal('show');

    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Categorias',
            }
        });

        console.log(response)

        // Verifique se os dados foram recebidos corretamente
        if (response && response.length > 0) {
            // Limpe as opções anteriores, se houver
            $('#categorias-options').empty();

            // Itera sobre os dados recebidos da API
            response.forEach(categoria => {
                // Cria uma nova opção de checkbox para cada categoria
                const checkboxOption = `
        <li class="categoria-item">
            <div class="form-check">
                <input class="form-check-input option-checkbox-categoria" type="checkbox" id="categoria-${categoria.categoria}" value="${categoria.categoria}">
                <label class="form-check-label" for="categoria-${categoria.categoria}">
                    ${categoria.categoria}
                </label>
            </div>
        </li>
    `;

                // Adiciona cada opção à lista de categorias
                $('#categorias-options').append(checkboxOption);
            });
        } else {
            // Caso não haja dados, exiba uma mensagem
            $('#categorias-options').html('<li>Nenhuma categoria encontrado.</li>');
        }

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        alert('Ocorreu um erro ao buscar os dados. Tente novamente.');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

// Função de Filtro de Pesquisa
$(document).on('input', '#search-faccionistas', function() {
    const searchValue = $(this).val().toLowerCase();

    // Filtra as opções com base no valor digitado
    $('#faccionistas-options .faccionista-item').filter(function() {
        const itemText = $(this).text().toLowerCase();
        $(this).toggle(itemText.indexOf(searchValue) > -1);
    });
});

$(document).on('input', '#search-categorias', function() {
    const searchValue = $(this).val().toLowerCase();

    // Filtra as opções com base no valor digitado
    $('#categorias-options .categoria-item').filter(function() {
        const itemText = $(this).text().toLowerCase();
        $(this).toggle(itemText.indexOf(searchValue) > -1);
    });
});

let chart = null; // Variável global para o gráfico

function FunctionGraficoFaccionistas(data, formato) {
    // Verifica se a data está no formato esperado
    if (!Array.isArray(data) || data.length === 0) {
        console.error("Dados inválidos:", data);
        return; // Sai da função se os dados não forem válidos
    }

    // Ordena os dados em ordem decrescente com base na carga
    const sortedData = data.sort((a, b) => b.carga - a.carga);

    const nomes = sortedData.map(item => item.apelidofaccionista);
    const valores = sortedData.map(item => item.carga);

    let series = [];
    if (formato === 'empilhado') {
        // Verifica se 'status_resumo' está presente no primeiro item
        if (!sortedData[0].status_resumo) {
            console.error("status_resumo não está definido para os dados:", sortedData[0]);
            return; // Sai da função se 'status_resumo' estiver faltando
        }

        // Extrai os nomes e valores dos status de todos os itens
        const statusNames = Object.keys(sortedData[0].status_resumo);
        const statusValues = statusNames.map(status => {
            return sortedData.map(item => item.status_resumo[status]);
        });

        series = statusNames.map((status, index) => ({
            name: status,
            data: statusValues[index] // Mapeia os valores de cada status
        }));
    } else {
        // Caso o formato seja 'barra', usamos os valores totais
        series = [{
            name: 'Total',
            data: valores
        }];
    }

    // Calcula a altura do gráfico com base na quantidade de dados
    const totalHeight = sortedData.length * 50; // Altura da barra ajustada para 50px por item

    const options = {
        series: series,
        chart: {
            type: 'bar',
            height: totalHeight > 400 ? totalHeight : 400,
            stacked: formato === 'empilhado',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 4,
            }
        },
        dataLabels: {
            enabled: true,
            formatter: val => `${val.toLocaleString()}`,
            offsetX: 5,
            style: {
                fontSize: '13px',
                colors: ['#fff'],
            },
        },
        xaxis: {
            categories: nomes
        },
        colors: ['#002955', '#002955', '#002955', '#8498FF', '#0DAF1D', '#EE8207', '#EE8207', '#002955', '#0DAF1D'],
        grid: {
            show: true,
            strokeDashArray: 4,
        },
    };

    // Destroi o gráfico existente antes de criar um novo
    if (chart !== null) {
        chart.destroy();
        chart = null;
    }

    // Prepara o container para o novo gráfico
    const chartContainer = document.querySelector("#barChart");
    chartContainer.innerHTML = '';

    // Cria uma nova instância do gráfico
    chart = new ApexCharts(chartContainer, options);

    // Renderiza o gráfico
    chart.render().then(() => {
        console.log("Gráfico renderizado com sucesso!");
    }).catch(error => {
        console.error("Erro ao renderizar o gráfico:", error);
    });
}



let chartCategorias = null; // Variável global para o gráfico de categorias
let chartStatus = null; // Variável global para o gráfico de status

function FunctionGraficoCategorias(data) {
    console.log(data);

    // Ordena os dados em ordem decrescente com base na carga
    const sortedData = data.sort((a, b) => b.carga - a.carga);

    const nomes = sortedData.map(item => item.categoria);
    const valores = sortedData.map(item => item.carga);

    console.log(nomes, valores);

    const options = {
        series: valores,
        chart: {
            width: 380,
            type: 'pie',
        },
        labels: nomes,
        dataLabels: {
            enabled: true,
            formatter: function(val, {
                seriesIndex
            }) {
                return valores[seriesIndex].toLocaleString(); // Formata o valor correspondente
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    // Se o gráfico já existir, destrua-o antes de criar um novo
    if (chartCategorias !== null) {
        chartCategorias.destroy();
        chartCategorias = null;
    }

    // Certifique-se de que o container esteja vazio e pronto para o novo gráfico
    const chartContainer = document.querySelector("#donutChart");
    chartContainer.innerHTML = ''; // Limpa o container

    // Cria uma nova instância do gráfico
    chartCategorias = new ApexCharts(chartContainer, options);
    chartCategorias.render();
}

function FunctionGraficoStatus(data) {
    console.log(data);

    // Ordena os dados em ordem decrescente com base na carga
    const sortedData = data.sort((a, b) => b.carga - a.carga);

    const nomes = sortedData.map(item => item.status);
    const valores = sortedData.map(item => item.carga);

    console.log(nomes, valores);

    const options = {
        series: valores,
        chart: {
            width: 380,
            type: 'pie',
        },
        labels: nomes,
        dataLabels: {
            enabled: true,
            formatter: function(val, {
                seriesIndex
            }) {
                return valores[seriesIndex].toLocaleString(); // Formata o valor correspondente
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    // Se o gráfico já existir, destrua-o antes de criar um novo
    if (chartStatus !== null) {
        chartStatus.destroy();
        chartStatus = null;
    }

    // Certifique-se de que o container esteja vazio e pronto para o novo gráfico
    const chartContainer = document.querySelector("#pieChart");
    chartContainer.innerHTML = ''; // Limpa o container

    // Cria uma nova instância do gráfico
    chartStatus = new ApexCharts(chartContainer, options);
    chartStatus.render();
}
