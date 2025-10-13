$(document).ready(async () => {
    await Consulta_Planos();
    Consultar_Tipo_Op()
    $('#select-plano').select2({
        placeholder: "Selecione um plano",
        allowClear: false,
        width: '100%'
    });

    $('#select-lote').select2({
        placeholder: "Selecione um lote",
        allowClear: false,
        width: '100%'
    });

    $('#select-plano').on('change', function () {
        Consulta_Lotes();
    });

    $('#select-lote').on('change', function () {
        Consulta_Metas(false);
    });

    const hoje = new Date().toISOString().split('T')[0];
    document.getElementById('data-inicial').value = hoje;
    document.getElementById('data-final').value = hoje;
});

const Consulta_Planos = async () => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Planos',
                plano: $('#select-plano').val()
            },
        });
        $('#select-plano').empty();
        $('#select-plano').append('<option value="" disabled selected>Selecione um plano...</option>');
        response.forEach(function (plano) {
            $('#select-plano').append(`
                    <option value="${plano['01- Codigo Plano']}">
                        ${plano['01- Codigo Plano']} - ${plano['02- Descricao do Plano']}
                    </option>
                `);
        });
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consultar_Tipo_Op = async () => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Tipo_Op',
            },
        });
        console.log(response)
        const divTiposOps = $('#TiposOps');
        divTiposOps.empty();
        response.forEach(opcao => {
            const checkbox = $('<div class="form-check">')
                .append(
                    $('<input class="form-check-input" type="checkbox">')
                        .attr('value', opcao['Tipo Producao'])
                        .attr('id', `checkbox${opcao['Tipo Producao']}`)
                )
                .append(
                    $('<label class="form-check-label">')
                        .attr('for', `checkbox${opcao['Tipo Producao']}`)
                        .text(opcao['Tipo Producao'])
                );
            divTiposOps.append(checkbox);
        });
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Previsao_Categoria = async (Fase) => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Previsao_Categoria',
                fase: Fase
            },
        });
        TabelaPrevisaoCategorias(response);
        $('#modal-previsao-categorias').modal('show')
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Falta_Produzir_Categoria = async (Fase, Plano) => {

    $('#loadingModal').modal('show');

    try {
         const requestData = {
             acao: "ConsultaFaltaProduzirCategoria_Fase",
             dados: {
                 codigoPlano: Plano,
                 arrayCodLoteCsw: [$('#select-lote').val()],
                 nomeFase: Fase,
                 ArrayTipoProducao: TiposOpsSelecionados.length > 0 ? TiposOpsSelecionados : []
             }
         };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(requestData)
        });

        TabelaFaltaProduzirCategorias(response);
        console.log(response)
        $('#modal-falta-produzir-categorias').modal('show');

    } catch (error) {
        console.error('Erro no detalha falta Produzir:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}


const Consulta_Lotes = async () => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Lotes',
                plano: $('#select-plano').val()
            },
        });
        console.log(response)
        $('#select-lote').empty();
        $('#select-lote').append('<option value="" disabled selected>Selecione um lote...</option>');
        response.forEach(function (lote) {
            $('#select-lote').append(`
                        <option value="${lote['lote']}">
                            ${lote['lote']} - ${lote['nomelote']}
                        </option>
                    `);
        });
        $('#div-selecionar-lote').removeClass('d-none');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

let TiposOpsSelecionados = [];
async function Consulta_Metas(congelado) {
    TiposOpsSelecionados = [];
    $('input[type=checkbox][id^="checkbox"]').each(function () {
        // Verificar se o checkbox está marcado
        if ($(this).is(':checked')) {
            // Obter o valor do checkbox
            var tiposOps = $(this).val();

            // Verificar se a coleção já existe no array
            if (!TiposOpsSelecionados.includes(tiposOps)) {
                // Adicionar o valor ao array de coleções selecionadas
                TiposOpsSelecionados.push(tiposOps);
            }
        }
    });
    console.log(TiposOpsSelecionados)
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Consulta_Metas",
            dados: {
                codigoPlano: $('#select-plano').val(),
                arrayCodLoteCsw: [$('#select-lote').val()],
                dataMovFaseIni: $('#data-inicial').val(),
                dataMovFaseFim: $('#data-final').val(),
                congelado: congelado,
                ArrayTipoProducao: TiposOpsSelecionados
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        TabelaMetas(response[0]['1-Detalhamento']);
        $('.div-metas').removeClass('d-none')
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consultar_Realizados = async (Fase) => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Realizados',
                Fase: Fase,
                dataInicial: $('#data-inicial').val(),
                dataFinal: $('#data-final').val()
            },
        });
        TabelaRealizado(response);
        $('#modal-realizado').modal('show')
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consultar_Cronograma = async (Fase) => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Cronograma',
                plano: $('#select-plano').val(),
                fase: Fase

            },
        });
        console.log(response);
        $('#data-inicial-cronograma').val(FormatarData(response[0]['DataInicio']));
        $('#data-final-cronograma').val(FormatarData(response[0]['DataFim']));
        $('#modal-cronograma').modal('show');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};


function FormatarData(dataBR) {
    const partes = dataBR.split('/'); // Divide "DD/MM/YYYY" em [DD, MM, YYYY]
    return `${partes[2]}-${partes[1]}-${partes[0]}`; // Retorna "YYYY-MM-DD"
}


function TabelaMetas(listaMetas) {
    if ($.fn.DataTable.isDataTable('#table-metas')) {
        $('#table-metas').DataTable().destroy();
    }

    const tabela = $('#table-metas').DataTable({
        searching: true,
        paging: false,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaMetas,
        dom: 'Bfrtip',
        buttons: [
            {
                text: '<i class="bi bi-funnel"></i>',
                className: 'button-filtros',
                action: function (e, dt, node, config) {
                    $('#modal-filtros').modal('show');
                },
                attr: {
                    title: 'Filtros'
                },
                init: function (api, node, config) {
                    $(node).css({
                        'border-radius': '10px',
                    });
                }
            }
        ],
        columns: [
            {
                data: 'apresentacao',
                visible: false
            },
            {
                data: 'codFase',
                visible: false
            },
            {
                data: 'nomeFase',
                render: (data, type, row) => `<span class="faseClicado" data-Fase="${row.nomeFase}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`
            },
            {
                data: 'previsao',
                render: (data, type, row) => `<span class="previsaoClicado" data-Fase="${row.nomeFase}" style="text-decoration: underline; color: blue; cursor: pointer;">${parseInt(data).toLocaleString()}</span>`
            },
            {
                data: 'FaltaProgramar',
                render: data => parseInt(data).toLocaleString()
            },
            {
                data: 'Carga Atual',
                render: data => parseInt(data).toLocaleString()
            },
            {
                data: 'Fila',
                render: data => parseInt(data).toLocaleString()
            },
            {
                data: 'Falta Produzir',
                render: (data, type, row) => `<span class="faltaProduzirClicado" data-Fase="${row.nomeFase}" style="text-decoration: underline; color: blue; cursor: pointer;">${parseInt(data).toLocaleString()}</span>`
            },
            {
                data: 'dias',
                render: (data, type, row) => `<span class="diasClicado" data-teste="${row.codFase}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`
            },
            {
                data: 'Meta Dia',
                render: data => parseInt(data).toLocaleString()
            },
            {
                data: 'Realizado',
                render: (data, type, row) => {
                    const realizado = Math.floor(Number(data)); // Remove casas decimais
                    const meta = Math.floor(Number(row['Meta Dia']));
                    let icon = '';

                    if (realizado < meta) {
                        icon = '<i class="fas fa-arrow-down text-danger float-end"></i>';
                    } else if (realizado > meta) {
                        icon = '<i class="fas fa-arrow-up text-success float-end"></i>';
                    }

                    return `
                        <span class="realizadoClicado" data-fase="${row.nomeFase}" 
                            style="text-decoration: underline; color: blue; cursor: pointer;">
                            ${realizado.toLocaleString()}
                        </span> ${icon}
                    `;
                }
            },
            {
                data: null, // Coluna para a porcentagem
                render: function (data, type, row) {
                    const realizado = parseInt(row['Realizado']);
                    const meta = parseInt(row['Meta Dia']);
                    if (meta === 0) {
                        return '0%';
                    }
                    const percent = (realizado / meta * 100).toFixed(2);
                    return `${percent}%`;
                }
            }
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
    });

    $('.search-input-metas').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });

    $('#table-metas').on('click', '.realizadoClicado', function () {
        const Fase = $(this).data('fase');
        Consultar_Realizados(Fase);
        $('#titulo-realizado').html(`${Fase}`)
    });

    $('#table-metas').on('click', '.diasClicado', function () {
        const Fase = $(this).attr('data-teste')
        console.log(Fase)
        Consultar_Cronograma(Fase);
    });

    $('#table-metas').on('click', '.previsaoClicado', function () {
        const Fase = $(this).attr('data-Fase')
        Consulta_Previsao_Categoria(Fase);
    });


    $('#table-metas').on('click', '.faltaProduzirClicado', function () {
        const Plano = $('#select-plano').val()
        const Fase = $(this).attr('data-Fase')
        Consulta_Falta_Produzir_Categoria(Fase, Plano);
    });

    $('#table-metas').on('click', '.faseClicado', function (event) {
        event.preventDefault();
        event.stopPropagation();
        const Plano = $('#select-plano').val();
        const Fase = $(this).attr('data-fase'); 
        Consulta_Falta_Produzir_Categoria(Fase, Plano);
    });
    
}

function TabelaRealizado(listaRealizado) {
    if ($.fn.DataTable.isDataTable('#table-realizado')) {
        $('#table-realizado').DataTable().destroy();
    }

    const dadosFiltrados = listaRealizado.filter(item => !/^Total:/.test(item.dataBaixa));
    const tabela = $('#table-realizado').DataTable({
        searching: true,
        paging: false,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: dadosFiltrados,
        columns: [
            {
                data: 'dataBaixa',
            },
            {
                data: 'dia'
            },
            {
                data: 'Realizado',
                render: data => parseInt(data).toLocaleString()
            },
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
    });
}

function TabelaPrevisaoCategorias(listaPrevisao) {
    if ($.fn.DataTable.isDataTable('#table-previsao-categorias')) {
        $('#table-previsao-categorias').DataTable().destroy();
    }
    const tabela = $('#table-previsao-categorias').DataTable({
        searching: true,
        paging: false,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaPrevisao,
        columns: [
            {
                data: 'categoria',
            },
            {
                data: 'previsao',
                render: data => parseInt(data).toLocaleString()
            },
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
    });
}

function TabelaFaltaProduzirCategorias(listaFaltaProduzir) {
    if ($.fn.DataTable.isDataTable('#table-falta-produzir-categorias')) {
        $('#table-falta-produzir-categorias').DataTable().destroy();
    }
    const tabela = $('#table-falta-produzir-categorias').DataTable({
        searching: true,
        paging: false,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaFaltaProduzir,
        columns: [
            {
                data: 'categoria',
            },
            {
                data: 'Carga',
                render: data => parseInt(data).toLocaleString()
            },
            {
                data: 'Fila',
                render: data => parseInt(data).toLocaleString()
            },
            {
                data: 'FaltaProgramar',
                render: data => parseInt(data).toLocaleString()
            },
            {
                data: 'faltaProduzir',
                render: data => parseInt(data).toLocaleString()
            },
            {
                data: 'dias',
                render: data => parseInt(data).toLocaleString()
            },
            {
                data: 'metaDiaria',
                render: data => parseInt(data).toLocaleString()
            },
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
    });
}
