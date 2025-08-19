
$(document).ready(async () => {
    $(".accordion").accordion({
        collapsible: true,
        active: false,
        heightStyle: "content"
    });


    document.getElementById('data-inicial').value = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
    document.getElementById('data-final').value = new Date().toISOString().split('T')[0];
    await Consulta_Empresas();
    await Consulta_Area();
    await Consulta_Centro_Custos();
    await Consulta_Grupo_Gastos();
    $('#select-area').val('PRODUCAO').trigger('change');
    $('#select-grupo-gastos').val('GASTOS GERAIS FABRICACAO').trigger('change');

    Cosulta_Resumos();
});

const Consulta_Empresas = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Empresas',
            }
        });
        const $select = $('#select-empresas');
        $select.empty();
        $select.append(`<option value="">Empresas</option>`);
        response.forEach(item => {
            $select.append(`<option value="${item.codEmpresa}">${item.nomeEmpresa}</option>`);
        });
        $('#select-empresas').val('1').trigger('change');
    } catch (error) {
        console.error('Erro ao consultar resumo:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Area = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Area',
            }
        });

        const $select = $('#select-area');
        $select.empty();
        $select.append(`<option value="">GERAL</option>`);

        response.forEach(item => {
            $select.append(`<option value="${item.nomeArea}">${item.nomeArea}</option>`);
        });
        $select.select2({
            placeholder: $select.data('placeholder'),
            width: '100%',
            allowClear: false,
        });

    } catch (error) {
        console.error('Erro ao consultar áreas:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};


const Consulta_Centro_Custos = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Centro_Custos',
            }
        });
        const $select = $('#select-centro-custos');
        $select.empty();
        $select.append(`<option value="">GERAL</option>`);
        response.forEach(item => {
            $select.append(`<option value="${item.nomeCentroCusto}">${item.nomeCentroCusto}</option>`);
        });

        $select.select2({
            placeholder: $select.data('placeholder'),
            width: '100%',
            allowClear: false,
        });
    } catch (error) {
        console.error('Erro ao consultar resumo:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Grupo_Gastos = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Grupo_Gastos',
            }
        });
        const $select = $('#select-grupo-gastos');
        $select.empty();
        $select.append(`<option value="">GERAL</option>`);
        response.forEach(item => {
            $select.append(`<option value="${item.GRUPO}">${item.GRUPO}</option>`);
        });
        $select.select2({
            placeholder: $select.data('placeholder'),
            width: '100%',
            allowClear: false,
        });

    } catch (error) {
        console.error('Erro ao consultar resumo:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Cosulta_Resumos = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Cosulta_Resumos',
                dataInicial: $('#data-inicial').val(),
                dataFinal: $('#data-final').val(),
                fase: $('#select-centro-custos').val(),
                area: $('#select-area').val(),
                empresa: $('#select-empresas').val(),
                grupoContas: $('#select-grupo-gastos').val(),
            }
        });

        const container = $('#container-cards');
        container.empty();

        let totalOrcado = 0;
        let totalRealizado = 0;

        // Agrupa os itens por nomeArea
        const agrupadoPorArea = {};
        response.forEach(item => {
            if (!agrupadoPorArea[item.nomeArea]) {
                agrupadoPorArea[item.nomeArea] = [];
            }
            agrupadoPorArea[item.nomeArea].push(item);
        });

        let chartIndex = 0;

        for (const nomeArea in agrupadoPorArea) {
            const areaItems = agrupadoPorArea[nomeArea];

            // Adiciona o título da área
            container.append(`
                <div class="col-12 mt-3 mb-3">
                    <h5 class="card-setor">${nomeArea}</h5>
                </div>
            `);

            areaItems.forEach(item => {
                const valorOrcado = item.valorOrcado || 0;
                const valorRealizado = item.valor || 0;

                totalOrcado += valorOrcado;
                totalRealizado += valorRealizado;

                const percentual = (valorOrcado > 0) ? (valorRealizado / valorOrcado) * 100 : 0;
                const idChart = `chart-${chartIndex++}`;

                let corGrafico = (valorRealizado > valorOrcado) ? '#ff4d4d' : '#33cc33';

                const cardHtml = `
                    <div class="col-md-4 col-lg-4 mb-3">
                        <div class="card card-clickable" data-nome-fase="${item.nomeCentroCusto}" data-area="${item.nomeArea}" data-orcado="${valorOrcado}" data-realizado="${valorRealizado}" style="cursor: pointer">
                            <div class="card-setor">${item.nomeCentroCusto}</div>
                            <div class="chart-container d-flex flex-column align-items-center">
                                <div id="${idChart}"></div>
                                <div class="text-center mt-2 w-100 px-3">
                                    <div class="d-flex justify-content-between">
                                        <p class="mb-1"><strong>R$ 0,00</strong></p>
                                        <p class="mb-1"><strong>R$ ${valorOrcado.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                container.append(cardHtml);

                const options = {
                    chart: {
                        type: 'radialBar',
                        height: 500,
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -90,
                            endAngle: 90,
                            hollow: {
                                size: '40%',
                            },
                            track: {
                                background: '#e0e0e0'
                            },
                            dataLabels: {
                                name: { show: false },
                                value: {
                                    fontSize: '20px',
                                    show: true,
                                    formatter: function (val) {
                                        return val.toFixed(1) + '%';
                                    }
                                }
                            }
                        }
                    },
                    series: [percentual],
                    colors: [corGrafico]
                };

                new ApexCharts(document.querySelector(`#${idChart}`), options).render();
            });
        }

        const percentualGeral = (totalOrcado > 0) ? (totalRealizado / totalOrcado) * 100 : 0;
        const corGeral = (totalRealizado > totalOrcado) ? '#ff4d4d' : '#33cc33';
        const diferenca = totalOrcado - totalRealizado;

        $('.card-clickable').on('click', function () {
            const nomeFase = $(this).data('nome-fase');
            const Orcado = $(this).data('orcado');
            const realizado = $(this).data('realizado');
            const area = $(this).data('area');
            console.log(area)
            Consulta_Detalhamento(nomeFase, Orcado, realizado, area);
            $('.accordion').addClass('d-none');
        });

        $('#orcado-geral').html(totalOrcado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        $('#realizado-geral').html(totalRealizado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        $('#card-diferenca').html(diferenca.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        $('#card-percentual').html(percentualGeral.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%');
        $('#card-percentual').css('color', corGeral);

    } catch (error) {
        console.error('Erro ao consultar resumo:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};


const Consulta_Detalhamento = async (fase, orcado, realizado, area) => {
    $('#loadingModal').modal('show');
    const diferenca = (orcado - realizado);
    const corGeral = (realizado > orcado) ? '#ff4d4d' : '#33cc33';

    try {
        const [detalhes, orcamentos] = await Promise.all([
            $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consulta_Detalhamento',
                    dataInicial: $('#data-inicial').val(),
                    dataFinal: $('#data-final').val(),
                    area: area,
                    fase: fase,
                    grupoContas: $('#select-grupo-gastos').val(),
                    empresa: $('#select-empresas').val()
                }
            }),
            $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consulta_Orcado',
                    dataInicial: $('#data-inicial').val(),
                    dataFinal: $('#data-final').val(),
                    area: area,
                    fase: fase,
                    grupoContas: $('#select-grupo-gastos').val(),
                    empresa: $('#select-empresas').val()
                }
            })
        ]);

        // 🔄 Mapeia orçados por codContaContabil
        const mapaOrcados = {};
        orcamentos.forEach(item => {
            mapaOrcados[item.codContaContabil] = item;
        });

        // 🔍 Coleta os códigos que já existem no detalhamento
        const codigosDetalhados = new Set(detalhes.map(item => item.codContaContabil));

        // ✅ Adiciona valorOrcado nos itens do detalhamento
        const detalhesComOrcado = detalhes.map(item => {
            const orcado = mapaOrcados[item.codContaContabil];
            return {
                ...item,
                valorOrcado: orcado ? orcado.valorOrcado : 0
            };
        });

        // ➕ Insere os itens orçados que não existem no detalhamento
        orcamentos.forEach(item => {
            if (!codigosDetalhados.has(item.codContaContabil)) {
                detalhesComOrcado.push({
                    codContaContabil: item.codContaContabil,
                    nomeContaContabil: item.nomeContaContabil || '-',
                    nomeCentroCusto: item.nomeCentroCusto || '-',
                    nomeArea: item.nomeArea || '-',
                    valor: 0,
                    valorOrcado: item.valorOrcado || 0
                });
            }
        });

        console.log(detalhesComOrcado);

        // 🧮 Agrupamento e tabela
        Tabela_Contas(detalhesComOrcado);
        Tabela_Contas_Detalhadas(detalhesComOrcado);

        // 📊 Atualiza valores na tela
        $('#container-info').addClass('d-none');
        $('#div-detalhamento').removeClass('d-none');
        $("#btn-voltar").removeClass('d-none');

        $('#detalha-orcado').html(orcado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        $('#detalha-realizado').html(realizado.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        $('#detalha-diferenca').html(diferenca.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }));
        $('#titulo-detalha-orcado').html(`ORÇADO ${fase}`);
        $('#titulo-detalha-realizado').html(`REAL. ${fase}`);
        $('#detalha-diferenca').css('color', corGeral);

    } catch (error) {
        console.error('Erro ao consultar detalhamento:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};




function Tabela_Contas(listaContas) {

    // 🔄 Agrupamento por codContaContabil
    const agrupado = {};
    listaContas.forEach(item => {
        const cod = item.codContaContabil;

        if (!agrupado[cod]) {
            agrupado[cod] = {
                codContaContabil: cod,
                nomeContaContabil: item.nomeContaContabil || '-',
                valor: 0,
                valorOrcado: item.valorOrcado
            };
        }

        agrupado[cod].valor += parseFloat(item.valor || 0);
    });

    // Converte o agrupamento em array
    const dadosAgrupados = Object.values(agrupado);

    // 🔄 Destroi instância antiga do DataTable, se houver
    if ($.fn.DataTable.isDataTable('#table-contas')) {
        $('#table-contas').DataTable().destroy();
    }

    const tabela = $('#table-contas').DataTable({
        data: dadosAgrupados,
        columns: [
            { data: 'codContaContabil', title: 'Código' },
            { data: 'nomeContaContabil', title: 'Item' },
            {
                data: 'valorOrcado',
                title: 'Orçado',
                render: {
                    display: function (val) {
                        return val.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        });
                    },
                    sort: function (val) {
                        return val;
                    }
                }
            },
            {
                data: 'valor',
                title: 'Realizado',
                render: {
                    display: function (val) {
                        return val.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        });
                    },
                    sort: function (val) {
                        return val;
                    }
                }
            },
            {
                data: null,
                title: '% Realizado',
                render: function (data, type, row) {
                    const valor = row.valor || 0;
                    const valorOrcado = row.valorOrcado || 0;

                    if (valorOrcado === 0 && valor > 0) return 'Não Orçado';
                    if (valor === 0) return "0" + '%';

                    const percentual = (valor / valorOrcado) * 100;
                    return percentual.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + '%';
                },
                createdCell: function (td, cellData, rowData, row, col) {
                    const valor = rowData.valor || 0;
                    const valorOrcado = rowData.valorOrcado || 0;

                    // Cor vermelha se realizado maior que orçado
                    if (valorOrcado === 0 && valor > 0) {
                        td.style.backgroundColor = '#ff4d4d'; // vermelho claro
                    } else if (valor > valorOrcado) {
                        td.style.backgroundColor = '#ff4d4d'; // vermelho claro
                    } else {
                        td.style.backgroundColor = '#33cc33'; // verde claro
                    }
                }
            }
        ],
        paging: false,
        searching: true,
        lengthChange: false,
        info: false,
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>',
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado",
        },
    });

    $('#table-contas tbody').off('click').on('click', 'tr', function (event) {
        const isSelected = $(this).hasClass('selected');
        $('#table-contas tbody tr').removeClass('selected');

        if (!isSelected) {
            $(this).addClass('selected');
            const rowData = tabela.row(this).data();
            const filtro = rowData['codContaContabil'];
            filtrarTabelas(filtro);
        } else {
            filtrarTabelas('');
        }
    });
};

function filtrarTabelas(filtro) {
    if (!tabelaDetalhamentoContas) return;

    if (filtro === '') {
        tabelaDetalhamentoContas.column(5).search('').draw();
    } else {
        tabelaDetalhamentoContas.column(5).search(`^${filtro}$`, true, false).draw();
    }
}


let tabelaDetalhamentoContas;

function Tabela_Contas_Detalhadas(listaDetalhamentoContas) {
    // Filtro: remove itens com dataLcto vazia, nula ou somente espaços
    const dadosFiltrados = listaDetalhamentoContas.filter(item => item.dataLcto && item.dataLcto.trim() !== '');

    if ($.fn.DataTable.isDataTable('#table-contas-detalhadas')) {
        $('#table-contas-detalhadas').DataTable().destroy();
    }

    tabelaDetalhamentoContas = $('#table-contas-detalhadas').DataTable({
        data: dadosFiltrados,
        dom: 'Bfrtip', // 'B' para botões, 'f' para filtro, 'r' para processamento, 't' para tabela, 'i' para informação, 'p' para paginação
        buttons: [
        {
                    extend: 'excelHtml5',
        text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
        className: 'btn-tabelas',
        filename: 'Relatorio_Contas_Detalhadas', // <- Nome do arquivo
        exportOptions: {
            columns: ':visible',
            format: {
                body: function (data, row, column, node) {
                    if (column === 2 && typeof data === 'string') {
                        return data
                            .replace('R$ ', '')      // remove o "R$ "
                            .replace(/\./g, '')      // remove pontos
                            .replace(',', '.');      // troca vírgula por ponto
                    }
                    return data;
                }
            }
        },
            
        }
    ],
        columns: [
            { data: 'dataLcto', title: 'data' },
            { data: 'descricaoItem', title: 'descricao' },
            {
                data: 'valor',
                title: 'valor',
                render: {
                    display: function (val) {
                        return val.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        });
                    },
                    sort: function (val) {
                        return val;
                    }
                }
            },
            { data: 'codDocumento', title: 'documento' },
            { data: 'nomeFornecedor', title: 'fornecedor' },
            {
                data: 'codContaContabil',
                title: 'codContaContabil',
                visible: false
            },
        ],
        paging: false,
        searching: true,
        lengthChange: false,
        info: false,
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>',
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado",
        },
    });
}
