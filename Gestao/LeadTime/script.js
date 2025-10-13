const Consulta_Planos_Disponiveis = async () => {
    $('#loadingModal').modal('show');
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Planos_Disponiveis',
            }
        });
        await Tabela_Planos(data);
        $('#ModalPlanosDisponiveis').modal('show')
    } catch (error) {
        console.error('Erro ao consultar chamados:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

function FormatarData(date) {
    const parts = date.split('/');
    return `${parts[2]}-${parts[1]}-${parts[0]}`;
}


function Tabela_Planos(listaPlanos) {
    if ($.fn.DataTable.isDataTable('#table-planos-disponiveis')) {
        $('#table-planos-disponiveis').DataTable().destroy();
    }

    const tabela = $('#table-planos-disponiveis').DataTable({
        searching: true,
        paging: false,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaPlanos,
        columns: [{
                data: '01- Codigo Plano'
            },
            {
                data: '02- Descricao do Plano'
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                    <div" style="display: flex; justify-content: space-around; align-items: center; height: 100%;">
                        <button class="btn" style="background-color: var(--CorMenu); color: var(--Branco);" onclick="$('#codigoPlano').val('${row['01- Codigo Plano']}'); ConsultaLote(); $('#ModalPlanosDisponiveis').modal('hide')">Selecionar</button>
                    </div>
                `;
                }
            }
        ],
        language: {
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
    });
}

$(document).ready(() => {

    $('#NomeRotina').text('Metas');
    $('#loadingModal').modal('hide')
    const hoje = new Date().toISOString().split('T')[0];
    document.getElementById('data-inicial').value = hoje;
    document.getElementById('data-final').value = hoje;


});

const Consulta_Faccionistas = async () => {
    $('#loadingModal').modal('show');
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Faccionistas',
            }
        });
        const $select = $('#select-faccionista');
        $select.empty();
        $select.append('<option value="">Selecione o Faccionista (csw):</option>');

        data.forEach(item => {
            $select.append(`<option value="${item.codFaccionista}">${item.nomeFaccionista}</option>`);
        });
        // Ativar o Select2 para o elemento
        $('#select-faccionista').select2({
            theme: 'bootstrap4',
            width: '100%',
            height: '30px',
            dropdownParent: $('#cadastrar-faccionista')
        });
        $('.select2-selection__rendered').addClass('form-control')
    } catch (error) {
        console.error('Erro ao consultar chamados:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};


async function ConsultarMetas(congelado) {
    $('#loadingModal').modal('show');
    try {
        const dados = {
            codigoPlano: $('#codigoPlano').val(),
            arrayCodLoteCsw: [$('#SelectLote').val()],
            dataMovFaseIni: $('#data-inicial').val(),
            dataMovFaseFim: $('#data-final').val(),
            congelado: congelado
        };

        const requestData = {
            acao: "Consultar_Metas",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response);
        CriarTabelaMetas(response);
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function ConsultarRealizadoGeral() {
    $('#loadingModal').modal('show');
    try {
        const dados = {
            codigoPlano: $('#codigoPlano').val(),
            arrayCodLoteCsw: [$('#SelectLote').val()],
            dataMovFaseIni: $('#data-inicial').val(),
            dataMovFaseFim: $('#data-final').val(),
        };

        const requestData = {
            acao: "Realizado_Geral",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response);
        CriarTabelaRealizado(response);
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}



async function CadastrarFaccionista(congelado) {
    $('#loadingModal').modal('show');
    try {
        const dados = {
            codFaccionista: $('#select-faccionista').val(),
            apelido: $('#apelido-faccionista').val(),
            ArrayCategorias: [$('#select-categoria').val()],
            ArrayCapacidade: [$('#capacidade-dia').val()],
        };

        const requestData = {
            acao: "Cadastrar_Faccionista",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response);
        $('#cadastrar-faccionista').modal('hide')
        Mensagem('Cadastro Realizado', 'success');

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function ConsultarMetasCategorias(congelado) {
    $('#loadingModal').modal('show');
    try {
        const dados = {
            codigoPlano: $('#codigoPlano').val(),
            arrayCodLoteCsw: [$('#SelectLote').val()],
            dataMovFaseIni: $('#data-inicial').val(),
            dataMovFaseFim: $('#data-final').val(),
            congelado: congelado
        };

        const requestData = {
            acao: "Consultar_Metas_Categorias",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response);
        CriarTabelaMetasCategorias(response);
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

function CriarTabelaMetas(listaMetas) {

    if ($.fn.DataTable.isDataTable('#table-metas')) {
        $('#table-metas').DataTable().destroy();
    }

    const tabela = $('#table-metas').DataTable({
        paging: false,
        info: false,
        searching: true,
        colReorder: true,
        data: listaMetas,
        lengthChange: false,
        pageLength: 10,
        fixedHeader: true,
        dom: 'Bfrtip',
        buttons: [{
                text: '<i class="fa-solid fa-filter"></i>',
                title: 'Filtros',
                className: 'ButtonModal',
                action: function(e, dt, node, config) {
                    $('#filtrosModal').modal('show');
                }
            },
            {
                text: '<i class="fa-solid fa-user-plus"></i>',
                title: 'Adicionar Faccionista',
                className: 'ButtonModal',
                action: async function(e, dt, node, config) {
                    await Consulta_Faccionistas();
                    ConsultaCategoria()
                    $('#cadastrar-faccionista').modal('show');
                },

            },
        ],
        columns: [{
                data: '01-nomeFac'
            },
            {
                data: '03- categoria'
            },
            {
                data: '04- AcordadoDia',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: '06-FaltaProgramar',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: '07-Fila',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: '08-Carga',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: '09-Falta Produzir',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: '10-dias'
            },
            {
                data: '11-Meta Dia',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: 'Realizado',
                render: function(data, type, row) {
                    const realizado = parseInt(data);
                    const meta = parseInt(row['11-Meta Dia']);
                    let icon = '';

                    if (realizado < meta) {
                        icon = ' <i class="fas fa-arrow-down" style="color: red; float: right;"></i>';
                    } else if (realizado > meta) {
                        icon = ' <i class="fas fa-arrow-up" style="color: green; float: right;"></i>';
                    }

                    return realizado.toLocaleString() + icon;
                }
            }
        ],
        language: {
            paginate: {
                first: 'Primeira',
                previous: '<',
                next: '>',
                last: 'Última',
            },
        },
    });

    window.filtrarTabelaMetas = function(categoria) {
        tabela.column(1).search(categoria, true, false).draw();
    };
}

function CriarTabelaRealizado(listaMetas) {

    if ($.fn.DataTable.isDataTable('#table-realizado')) {
        $('#table-realizado').DataTable().destroy();
    }

    const tabela = $('#table-realizado').DataTable({
        paging: false,
        info: false,
        searching: true,
        colReorder: true,
        data: listaMetas,
        lengthChange: false,
        pageLength: 10,
        fixedHeader: true,
        columns: [{
                data: 'Realizado'
            },
            {
                data: null, // Use null quando você não tem um único campo de dados para esta coluna
                render: function(data, type, row) {
                    return row.data + ' - ' + row.dia;
                }
            }
        ],
    });
}



function CriarTabelaMetasCategorias(listaMetas) {
    if ($.fn.DataTable.isDataTable('#table-categorias')) {
        $('#table-categorias').DataTable().destroy();
    }
    const tabela = $('#table-categorias').DataTable({
        paging: false,
        info: false,
        searching: true,
        colReorder: true,
        data: listaMetas,
        lengthChange: false,
        pageLength: 10,
        fixedHeader: true,
        columns: [{
                data: 'categoria'
            },
            {
                data: 'FaltaProgramar',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: 'Fila',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: 'Carga Atual',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: 'Falta Produzir',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: 'dias'

            },
            {
                data: 'Meta Dia',
                render: function(data) {
                    return parseInt(data).toLocaleString();
                }
            },
            {
                data: 'Realizado',
                render: function(data, type, row) {
                    const realizado = parseInt(data);
                    const meta = parseInt(row['Meta Dia']);
                    let icon = '';

                    if (realizado < meta) {
                        icon = ' <i class="fas fa-arrow-down" style="color: red; float: right;"></i>';
                    } else if (realizado > meta) {
                        icon = ' <i class="fas fa-arrow-up" style="color: green; float: right;"></i>';
                    }

                    return realizado.toLocaleString() + icon;
                }
            }
        ],
        language: {
            paginate: {
                first: 'Primeira',
                previous: '<',
                next: '>',
                last: 'Última',
            },
        },
    });
    let ultimaCategoriaClicada = null;

    $('#table-categorias tbody').on('click', 'tr', function() {
        const categoria = $(this).find('td').first().text().trim();

        // Adiciona um console.log para verificar o clique
        console.log('Linha clicada:', categoria);

        if (ultimaCategoriaClicada === categoria) {
            window.filtrarTabelaMetas('');
            ultimaCategoriaClicada = null;
            $(this).removeClass('categoria-selecionada');
        } else {
            window.filtrarTabelaMetas(categoria);
            ultimaCategoriaClicada = categoria;
            $(this).addClass('categoria-selecionada');
        }
    });


}

async function ConsultaLote() {
    if ($('#codigoPlano').val() === '') {
        Mensagem('Campo Vazio', 'warning')
    } else {
        try {
            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Lotes',
                    plano: $('#codigoPlano').val()
                }
            });

            const $select = $('#SelectLote');
            $select.empty();
            $select.append('<option value="">Selecione o Lote:</option>');

            response.forEach(item => {
                $select.append(`<option value="${item.lote}">${item.lote} - ${item.nomelote}</option>`);
            });
            // Ativar o Select2 para o elemento
            $('#SelectLote').select2({
                theme: 'bootstrap4',
                width: '100%',
                height: '30px',

            });
            $('.select2-selection__rendered').addClass('form-control')

            $('#DescPlano').val(response[0]['nomelote']);
            $('#selects').removeClass('d-none');
        } catch (error) {
            console.error('Erro ao consultar chamados:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
    }
}

async function ConsultaCategoria() {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Categorias',
                plano: $('#codigoPlano').val()
            }
        });

        const $select = $('#select-categoria');
        $select.empty();
        $select.append('<option value="">Selecione a categoria:</option>');

        response.forEach(item => {
            $select.append(`<option value="${item.categoria}">${item.categoria}</option>`);
        });
        // Ativar o Select2 para o elemento
        $('#select-categoria').select2({
            theme: 'bootstrap4',
            width: '100%',
            height: '30px',
            dropdownParent: $('#cadastrar-faccionista')
        });
        $('.select2-selection__rendered').addClass('form-control')
    } catch (error) {
        console.error('Erro ao consultar chamados:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}



async function SelecaoLote(congelado) {

    await ConsultarMetasCategorias(congelado)
    await ConsultarMetas(congelado);
    ConsultarRealizadoGeral()
    $('#table-metas').removeClass('d-none');
    $('#table-categorias').removeClass('d-none');
    $('#table-realizado').removeClass('d-none');

}