$(document).ready(async () => {
    await Consulta_Planos();
    await Consulta_Marcas();

    await $('#select-plano').select2({
        placeholder: "Selecione um plano",
        allowClear: true,
        width: '100%'
    });

    $('#select-marca').select2({
        placeholder: "Selecione uma marca",
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modal-metas')
    });

    $('#select-marca-categoria').select2({
        placeholder: "Selecione uma marca",
        allowClear: true,
        width: '100%',
    });

    $('#input-meta-rs').mask("#.##0,00", {
        reverse: true
    });

    $('#input-meta-peca').mask("#.##0", {
        reverse: true
    });

    $('#input-meta-rs-categoria').mask("#.##0,00", {
        reverse: true
    });

    $('#input-meta-peca-categoria').mask("#.##0", {
        reverse: true
    });

    $('#btn-metas').addClass('btn-menu-clicado')

});

function alterna_button_selecionado(button) {
    $(button).closest('.d-flex').find('button').removeClass('btn-menu-clicado');
    $(button).addClass('btn-menu-clicado');
}

$('#select-plano').on('change', function() {
    Consulta_Metas();
    $('#opcoes').removeClass('d-none');
    $('#selecao-plano').removeClass('mt-3');
    $('.pagination-metas').removeClass('d-none');
    $('.pagination-metas').addClass('d-md-flex');
    $('.div-tabela').removeClass('d-none')
});


$('#select-marca-categoria').on('change', function() {
    Consulta_Metas_Categoria();
    $('.pagination-metas-categoria').removeClass('d-none');
    $('.pagination-metas-categoria').addClass('d-md-flex');
});



function Consulta_Planos() {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Planos',
        },
        success: function(data) {
            $('#select-plano').empty();
            $('#select-plano').append('<option value="" disabled selected>Selecione um plano...</option>');
            data.forEach(function(plano) {
                $('#select-plano').append(`
                        <option value="${plano['01- Codigo Plano']}">
                            ${plano['01- Codigo Plano']} - ${plano['02- Descricao do Plano']}
                        </option>
                    `);
            });
            $('#loadingModal').modal('hide');
        },
        error: function(xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
            $('#loadingModal').modal('hide');
        }
    });
}

function Consulta_Marcas() {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Marcas',
        },
        success: function(data) {
            console.log(data);

            $('#select-marca').empty();
            $('#select-marca-categoria').empty();
            $('#select-marca').append('<option value="" disabled selected>Selecione uma marca...</option>');
            $('#select-marca-categoria').append('<option value="" disabled selected>Selecione uma marca...</option>');
            data.forEach(function(marca) {
                $('#select-marca').append(`
                        <option value="${marca['marca']}">
                            ${marca['marca']}
                        </option>
                    `);
                $('#select-marca-categoria').append(`
                        <option value="${marca['marca']}">
                            ${marca['marca']}
                        </option>
                    `);
            });
            $('#loadingModal').modal('hide');
        },
        
        error: function(xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
            $('#loadingModal').modal('hide');
        }
    });
}

function Consulta_Metas() {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Metas',
            plano: $('#select-plano').val()
        },
        success: function(data) {
            if (data === null) {
                TabelaMetas(data);
            } else {
                const dadosFiltrados = data.filter(item => item.marca !== 'TOTAL');
                TabelaMetas(dadosFiltrados);
            }
            $('#loadingModal').modal('hide');

        },
        error: function(xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
            $('#loadingModal').modal('hide');
        }
    });
};

function Consulta_Metas_Categoria() {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Metas_Categoria',
            plano: $('#select-plano').val(),
            marca: $('#select-marca-categoria').val()
        },
        success: function(data) {
            console.log(data[0]['4- DetalhamentoCategoria'])
            const dadosFiltrados = data[0]['4- DetalhamentoCategoria'].filter(item => item.nomeCategoria !== '-');
            TabelaMetasCategoria(dadosFiltrados);
            $('#loadingModal').modal('hide');

        },
        error: function(xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
            $('#loadingModal').modal('hide');
        }
    });
};

async function Salvar_Metas() {
    $('#loadingModal').modal('show');

    try {
        const requestData = {
            acao: "Salvar_Metas",
            dados: {
                "codPlano": $('#select-plano').val(),
                "marca": $('#select-marca').val(),
                "metaFinanceira": parseFloat($('#input-meta-rs').val().replace(/\./g, '').replace(',', '.')),
                "metaPecas": parseInt($('#input-meta-peca').val().replace(/\./g, ''))
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        await Mensagem_Canto('Salvo com sucesso', 'success')
        Consulta_Metas();
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
        $('#modal-metas').modal('hide');
    }
}

async function Salvar_Metas_Categoria() {
    $('#loadingModal').modal('show');

    try {
        const requestData = {
            acao: "Salvar_Metas_Categoria",
            dados: {
                "codPlano": $('#select-plano').val(),
                "marca": $('#select-marca-categoria').val(),
                "nomeCategoria": $('#input-categoria').val(),
                "metaFinanceira": parseFloat($('#input-meta-rs-categoria').val().replace(/\./g, '').replace(',', '.')),
                "metaPecas": parseInt($('#input-meta-peca-categoria').val().replace(/\./g, ''))
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response)
        await Mensagem_Canto('Salvo com sucesso', 'success')
        Consulta_Metas_Categoria();
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
        $('#modal-metas-categoria').modal('hide');
    }
}



function TabelaMetas(listaMetas) {
    if ($.fn.DataTable.isDataTable('#table-metas')) {
        $('#table-metas').DataTable().destroy();
    }

    const tabela = $('#table-metas').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: [],
        columns: [{
                data: null,
                render: function(row) {
                    return `
                <div class="acoes">
                    <button 
                        class="btn-table" 
                        style="background-color: yellow; color: black; border-color: yellow" 
                        onclick="
                            $('#modal-metas').modal('show'); 
                            $('#input-meta-rs').val('${row['metaFinanceira'].replace(/[^\d,.-]/g, '')}'); 
                            $('#input-meta-peca').val('${row['metaPecas']}');
                            $('#select-marca').val('${row['marca']}').change();
                            $('.modal-title').text('Editar Meta');
                        ">
                        <i class="bi bi-pencil-square" title="Editar" id="btnEditar"></i>
                    </button> 
                </div>
                `;
                }
            },

            {
                data: 'marca'
            },
            {
                data: 'metaFinanceira'
            },
            {
                data: 'metaPecas'
            },
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>',
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function() {
            const paginateHtml = $('.dataTables_paginate').html();

            $('#pagination-metas').html(paginateHtml);

            $('#pagination-metas span').remove();

            $('#pagination-metas a').off('click').on('click', function(e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            $('#itens-metas').on('input', function() {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        }
    });

    if (!listaMetas || listaMetas.length === 0) {
        tabela.clear().draw();
        $('#btn-meta-categoria').addClass('disabled');
        $('.div-meta').removeClass('d-none');
        $('.div-meta-categoria').addClass('d-none');
        return;
    } else {
        $('#btn-meta-categoria').removeClass('disabled');
        $('.div-meta').removeClass('d-none');
        $('.div-meta-categoria').addClass('d-none');
    }

    const formatacao = listaMetas.map(metas => ({
        marca: metas['marca'],
        metaFinanceira: metas['metaFinanceira'],
        metaPecas: metas['metaPecas']
    }));

    tabela.clear().rows.add(formatacao).draw();

    $('.search-input').off('keyup change').on('keyup change', function() {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });

    $('.search-input').on('click', function(e) {
        e.stopPropagation();
    });

    $('.search-input').on('keydown', function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

function TabelaMetasCategoria(listaMetas) {
    if ($.fn.DataTable.isDataTable('#table-metas-categoria')) {
        $('#table-metas-categoria').DataTable().destroy();
    }

    const tabela = $('#table-metas-categoria').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaMetas,
        columns: [{
                data: null,
                render: function(row) {
                    return `
                <div class="acoes">
                    <button 
                        class="btn-table" 
                        style="background-color: yellow; color: black; border-color: yellow" 
                        onclick="
                            $('#modal-metas-categoria').modal('show'); 
                            $('#input-meta-rs-categoria').val('${row['metaFinanceira'].replace(/[^\d,.-]/g, '')}'); 
                            $('#input-meta-peca-categoria').val('${row['metaPc']}');
                            $('#input-categoria').val('${row['nomeCategoria']}');
                        ">
                        <i class="bi bi-pencil-square" title="Editar" id="btnEditar"></i>
                    </button> 
                </div>
                `;
                }
            },

            {
                data: 'nomeCategoria'
            },
            {
                data: 'metaFinanceira'
            },
            {
                data: 'metaPc'
            },
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>',
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function() {
            const paginateHtml = $('.dataTables_paginate').html();

            $('#pagination-metas-categoria').html(paginateHtml);

            $('#pagination-metas-categoria span').remove();

            $('#pagination-metas-categoria a').off('click').on('click', function(e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            $('#itens-metas-categoria').on('input', function() {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        }
    });

    $('.search-input').off('keyup change').on('keyup change', function() {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });

    $('.search-input').on('click', function(e) {
        e.stopPropagation();
    });

    $('.search-input').on('keydown', function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}