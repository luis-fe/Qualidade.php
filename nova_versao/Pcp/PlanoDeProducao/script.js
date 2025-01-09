let ColecoesSelecionadas = [];
let LotesSelecionados = [];
let NotasSelecionadas = [];
$(document).ready(async () => {
    Consulta_Planos();

    $('#input-distribuicao').mask("##0,00%", {
        reverse: true
    });

    $('#select-abc').select2({
        placeholder: "Selecione um parâmetro",
        allowClear: true,
        width: '100%',
        dropdownParent: $('#modal-abc')
    });
});

function alterna_button_selecionado(button) {
    $(button).closest('.d-flex').find('button').removeClass('btn-menu-clicado');
    $(button).addClass('btn-menu-clicado');
}

// *** Funções de Consultas
const Consulta_Planos = async () => {
    $('#loadingModal').modal('show');
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Planos',
            }
        });
        TabelaPlanos(data);
        $('#loadingModal').modal('hide');
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Colecoes = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Colecoes',
                plano: $('#codigo-plano').val()
            }
        });
        TabelaColecoes(data);
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally { }
};

const Consulta_Colecoes_Csw = async () => {
    $('#loadingModal').modal('show');
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Colecoes_Csw',
            }
        });
        await $('#modal-colecoes').modal('show');
        TabelaColecoesCsw(data);

    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Lotes = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Lotes',
                plano: $('#codigo-plano').val()
            }
        });
        TabelaLotes(data);
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Lotes_Csw = async () => {
    $('#loadingModal').modal('show');
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Lotes_Csw',
            }
        });
        await $('#modal-lotes').modal('show');
        TabelaLotesCsw(data);

    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};



const Consulta_Notas = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Notas',
                plano: $('#codigo-plano').val()
            }
        });
        TabelaNotas(data);
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally { }
};

const Consulta_Notas_Csw = async () => {
    $('#loadingModal').modal('show');
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Notas_Csw',
            }
        });
        await $('#modal-notas').modal('show');
        TabelaNotasCsw(data);

    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Abc_Plano = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Abc_Plano',
                plano: $('#codigo-plano').val()
            }
        });
        TabelaAbc(data[0]['3- Detalhamento:']);
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally {
    }
};

const Consulta_Abc = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Abc',
            }
        });
        $('#select-abc').empty();
        $('#select-abc').append('<option value="" disabled selected>Selecione um parâmetro...</option>');
        data.forEach(function (abc) {
            $('#select-abc').append(`
                            <option value="${abc['nomeABC']}">
                                ${abc['nomeABC']}
                            </option>
                        `);
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally {
    }
};

// *** Funções de Cadastro

async function Cadastrar_Plano() {
    $('#loadingModal').modal('show');
    try {
        if ($('#descricao-plano').val() == '') {
            Mensagem('Descrição Obrigatória!', 'warning')
        } else if ($('#codigo-plano').val() == '') {
            Mensagem('Código Obrigatório!', 'warning')
        } else {
            const dados = {
                "codigoPlano": $('#codigo-plano').val(),
                "descricaoPlano": $('#descricao-plano').val(),
                "iniVendas": $('#inicio-venda').val(),
                "fimVendas": $('#final-venda').val(),
                "iniFat": $('#inicio-faturamento').val(),
                "fimFat": $('#final-faturamento').val()
            }
            var requestData = {
                acao: "Cadastrar_Plano",
                dados: dados
            };
            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response);
            if (response['resposta'][0]['Status'] == true) {
                Mensagem_Canto('Plano Cadastrado', 'success');
                $('.btn-menu').removeClass('disabled');
                $('#btn-salvar-edicao').removeClass('d-none');
                $('#btn-salvar').addClass('d-none');
                await Consulta_Planos()
            } else {
                Mensagem_Canto('Plano já existe', 'error')
            }
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Vincular_Colecoes() {
    $('#loadingModal').modal('show');
    try {

        const dados = {
            "codPlano": $('#codigo-plano').val(),
            "arrayColecao": ColecoesSelecionadas,
        }

        var requestData = {
            acao: "Vincular_Colecoes",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),

        });
        console.log(ColecoesSelecionadas);
        if (response['status'] == true) {
            await Consulta_Colecoes();
            $('#loadingModal').modal('hide');
            Mensagem_Canto('Coleções Adicionadas', 'success');
            $('#modal-colecoes').modal('hide');
            ColecoesSelecionadas = []
        } else {
            Mensagem_Canto('Erro', 'error');
            ColecoesSelecionadas = []
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Cadastrar_Abc() {
    $('#loadingModal').modal('show');
    try {

        const dados = {
            "codPlano": $('#codigo-plano').val(),
            "nomeABC": $('#select-abc').val(),
            "perc_dist": $('#input-distribuicao').val().replace('%', '').trim(),
        }

        var requestData = {
            acao: "Cadastrar_Abc",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),

        });
        console.log(response);
        if (response['resposta'] !== null) {
            await Consulta_Abc_Plano();
            $('#loadingModal').modal('hide');
            Mensagem_Canto('Parâmetro adicionado', 'success')
        } else {
            Mensagem_Canto('Erro', 'error')
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#modal-abc').modal('hide');
    }
}

async function Vincular_Lotes() {
    $('#loadingModal').modal('show');
    try {

        const dados = {
            "codigoPlano": $('#codigo-plano').val(),
            "arrayCodLoteCsw": LotesSelecionados,
        }

        var requestData = {
            acao: "Vincular_Lotes",
            dados: dados
        };

        const response = await $.ajax({
            type: 'PUT',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),

        });
        if (response[0]['Status'] == true) {
            await Consulta_Lotes();
            $('#loadingModal').modal('hide');
            Mensagem_Canto('Lotes Adicionados', 'success');
            $('#modal-lotes').modal('hide');
            LotesSelecionados = []
        } else {
            Mensagem_Canto('Erro', 'error');
            LotesSelecionados = []
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {

    }
}

async function Vincular_Notas() {
    $('#loadingModal').modal('show');
    try {

        const dados = {
            "codigoPlano": $('#codigo-plano').val(),
            "arrayTipoNotas": NotasSelecionadas,
        }

        var requestData = {
            acao: "Vincular_Notas",
            dados: dados
        };

        const response = await $.ajax({
            type: 'PUT',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),

        });
        console.log(NotasSelecionadas);
        if (response[0]['Status'] == true) {
            await Consulta_Notas();
            $('#loadingModal').modal('hide');
            Mensagem_Canto('Notas Adicionadas', 'success');
            NotasSelecionadas = []
        } else {
            Mensagem_Canto('Erro', 'error');
            NotasSelecionadas = []
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#modal-notas').modal('hide');
    }
}

async function Alterar_Plano() {
    $('#loadingModal').modal('show');
    try {
        if ($('#descricao-plano').val() == '') {
            Mensagem('Descrição Obrigatória!', 'warning')
        } else {
            const dados = {
                "codigoPlano": $('#codigo-plano').val(),
                "descricaoPlano": $('#descricao-plano').val(),
                "iniVendas": $('#inicio-venda').val(),
                "fimVendas": $('#final-venda').val(),
                "iniFat": $('#inicio-faturamento').val(),
                "fimFat": $('#final-faturamento').val()
            }
            var requestData = {
                acao: "Alterar_Plano",
                dados: dados
            };
            const response = await $.ajax({
                type: 'PUT',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response);
            if (response[0]['Status'] == true) {
                Mensagem_Canto('Plano Editado', 'success');
                await Consulta_Planos()
            } else {
                Mensagem_Salva('Erro', 'error')
            }
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Deletar_Notas(Nota) {
    const arrayTipoNotas = [Nota];

    try {
        const result = await Swal.fire({
            title: "Deseja deletar o tipo de nota?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "Deletar",
        });

        if (result.isConfirmed) {
            $('#loadingModal').modal('show');

            const dados = {
                "codigoPlano": $('#codigo-plano').val(),
                "arrayTipoNotas": arrayTipoNotas
            };
            const requestData = {
                acao: "Deletar_Notas",
                dados: dados
            };
            const response = await $.ajax({
                type: 'DELETE',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });

            if(response['resposta'][0]['Status'] === true){
                Mensagem_Canto('Tipo de nota deletado', 'success');
                Consulta_Notas();
            }
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro na solicitação', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Deletar_Lotes(Lote) {
    const arrayCodLoteCsw = [Lote];

    try {
        const result = await Swal.fire({
            title: "Deseja deletar o lote?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "Deletar",
        });

        if (result.isConfirmed) {
            $('#loadingModal').modal('show');

            const dados = {
                "codigoPlano": $('#codigo-plano').val(),
                "arrayCodLoteCsw": arrayCodLoteCsw
            };
            const requestData = {
                acao: "Deletar_Lotes",
                dados: dados
            };
            const response = await $.ajax({
                type: 'DELETE',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });

            if(response['resposta'][0]['Status'] === true){
                Mensagem_Canto('Lote deletado', 'success');
                Consulta_Lotes();
            }
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro na solicitação', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Deletar_Colecoes(Colecao) {
    const arrayColecao = [Colecao];

    try {
        const result = await Swal.fire({
            title: "Deseja deletar a coleção?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "Deletar",
        });

        if (result.isConfirmed) {
            $('#loadingModal').modal('show');

            const dados = {
                "codPlano": $('#codigo-plano').val(),
                "arrayColecao": arrayColecao
            };
            const requestData = {
                acao: "Deletar_Colecoes",
                dados: dados
            };
            const response = await $.ajax({
                type: 'DELETE',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });

            if(response['resposta'][0]['Status'] === true){
                Mensagem_Canto('Coleção deletada', 'success');
                Consulta_Colecoes();
            }
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro na solicitação', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}


function TabelaPlanos(listaPlanos) {
    if ($.fn.DataTable.isDataTable('#table-planos')) {
        $('#table-planos').DataTable().destroy();
    }

    const tabela = $('#table-planos').DataTable({
        searching: false,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaPlanos,
        columns: [{
            data: null,
            render: function (row) {
                return `
                        <div class="acoes">
                        <button 
                            class="btn-table" 
                            style="background-color: yellow; color: black; border-color: yellow" 
                            onclick="
                             $('.div-tabela').addClass('d-none');
                             $('.div-cadastro-plano').removeClass('d-none');
                             $('#btn-novo-plano').addClass('d-none');
                             $('#btn-voltar').removeClass('d-none');
                             $('#btn-salvar-edicao').removeClass('d-none');
                             $('.btn-menu').removeClass('disabled');
                             $('#codigo-plano').val('${row['01- Codigo Plano']}');
                             $('#descricao-plano').val('${row['02- Descricao do Plano']}');
                             $('#inicio-venda').val('${row['03- Inicio Venda']}');
                             $('#final-venda').val('${row['04- Final Venda']}');
                             $('#inicio-faturamento').val('${row['05- Inicio Faturamento']}');
                             $('#final-faturamento').val('${row['06- Final Faturamento']}');
                             $('#codigo-plano').attr('disabled', true);
                             $('#codigo-plano').css('cursor', 'not-allowed');
                             async function atualizarTabelas (){
                                $('#loadingModal').modal('show');
                                await Consulta_Colecoes();
                                await Consulta_Lotes();
                                await Consulta_Notas();
                                await Consulta_Abc_Plano();
                                await Consulta_Abc();
                                $('#loadingModal').modal('hide');
                            }
                                
                            atualizarTabelas()
                            ">
                            <i class="bi bi-pencil-square" title="Editar" id="btnEditar"></i>
                        </button> 
                    </div>
                    `;
            }
        }, {
            data: '01- Codigo Plano'
        }, {
            data: '02- Descricao do Plano'
        }, {
            data: '03- Inicio Venda'
        }, {
            data: '04- Final Venda'
        },
        {
            data: '05- Inicio Faturamento'
        }, {
            data: '06- Final Faturamento'
        }
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
        drawCallback: function () {
            const paginateHtml = $('.dataTables_paginate').html();
            $('#pagination-planos').html(paginateHtml);
            $('#pagination-planos span').remove();

            $('#pagination-planos a').off('click').on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            // Atualiza a quantidade de itens por página
            $('#itens-planos').on('input', function () {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        }
    });
}

function TabelaColecoes(listaColecoes) {
    if ($.fn.DataTable.isDataTable('#table-colecoes')) {
        $('#table-colecoes').DataTable().destroy();
    }

    const tabela = $('#table-colecoes').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaColecoes,
        columns: [{
            data: null,
            render: function (row) {
                return `
                        <div class="acoes" style="text-align: center">
                        <button 
                            class="btn-table" 
                            style="background-color: #b81414; color: white; border-color: #b81414" 
                            onclick="Deletar_Colecoes('${row.codColecao}')">
                            <i class="bi bi-trash3" title="Excluir" id="btn-excluir"></i>
                        </button> 
                    </div>
                    `;
            }
        },
        {
            data: 'codColecao'
        },
        {
            data: 'nomeColecao'
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
        drawCallback: function () {
            const paginateHtml = $('.dataTables_paginate').html();
            $('#pagination-colecoes').html(paginateHtml);
            $('#pagination-colecoes span').remove();

            $('#pagination-colecoes a').off('click').on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            // Atualiza a quantidade de itens por página
            $('#itens-colecoes').on('input', function () {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        }
    });

    $('.search-table-colecoes').off('keyup change').on('keyup change', function () {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });

    $('.search-table-colecoes').on('click', function (e) {
        e.stopPropagation();
    });

    $('.search-table-colecoes').on('keydown', function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

function TabelaColecoesCsw(listaColecoes) {
    if ($.fn.DataTable.isDataTable('#table-colecoes-csw')) {
        $('#table-colecoes-csw').DataTable().destroy();
    }

    const tabela = $('#table-colecoes-csw').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaColecoes,
        columns: [{
            data: null,
            render: () => `
                    <div class="acoes d-flex justify-content-center align-items-center" style="height: 100%;">
                        <input type="checkbox" class="row-checkbox">
                    </div>`
        },
        {
            data: 'codColecao'
        },
        {
            data: 'nome'
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
        drawCallback: function () {
            $('#pagination-colecoes-csw').html($('.dataTables_paginate').html());
            $('#pagination-colecoes-csw span').remove();
            $('#pagination-colecoes-csw a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('.search-table-colecoes-csw').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });

    $('#btn-selecionar-colecoes').off('click').on('click', () => {
        ColecoesSelecionadas = tabela.rows().nodes().toArray()
            .filter(row => $(row).find('.row-checkbox').is(':checked'))
            .map(row => String(tabela.row(row).data().codColecao));
        if (ColecoesSelecionadas.length === 0) {
            Mensagem('Nenhuma Coleção selecionada!', 'warning');
        } else {
            Vincular_Colecoes();

        }
    });
}


function TabelaLotes(listaLotes) {
    if ($.fn.DataTable.isDataTable('#table-lotes')) {
        $('#table-lotes').DataTable().destroy();
    }

    const tabela = $('#table-lotes').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaLotes,
        columns: [{
            data: null,
            render: function (row) {
                return `
                        <div class="acoes" style="text-align: center">
                        <button 
                            class="btn-table" 
                            style="background-color: #b81414; color: white; border-color: #b81414" 
                                onclick="Deletar_Lotes('${row.lote}')">
                            <i class="bi bi-trash3" title="Excluir" id="btn-excluir"></i>
                        </button> 
                    </div>
                    `;
            }
        },
        {
            data: 'lote'
        },
        {
            data: 'nomelote'
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
        drawCallback: function () {
            const paginateHtml = $('.dataTables_paginate').html();
            $('#pagination-lotes').html(paginateHtml);
            $('#pagination-lotes span').remove();

            $('#pagination-lotes a').off('click').on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            // Atualiza a quantidade de itens por página
            $('#itens-lotes').on('input', function () {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        }
    });
    $('.search-table-lotes').off('keyup change').on('keyup change', function () {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });

    $('.search-table-lotes').on('click', function (e) {
        e.stopPropagation();
    });

    $('.search-table-lotes').on('keydown', function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

function TabelaLotesCsw(listaLotes) {
    if ($.fn.DataTable.isDataTable('#table-lotes-csw')) {
        $('#table-lotes-csw').DataTable().destroy();
    }

    const tabela = $('#table-lotes-csw').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaLotes,
        columns: [{
            data: null,
            render: () => `
                    <div class="acoes d-flex justify-content-center align-items-center" style="height: 100%;">
                        <input type="checkbox" class="row-checkbox">
                    </div>`
        },
        {
            data: 'codLote'
        },
        {
            data: 'nomeLote'
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
        drawCallback: function () {
            $('#pagination-lotes-csw').html($('.dataTables_paginate').html());
            $('#pagination-lotes-csw span').remove();
            $('#pagination-lotes-csw a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('.search-input-lotes-csw').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });

    $('#btn-selecionar-lotes').off('click').on('click', () => {
        LotesSelecionados = tabela.rows().nodes().toArray()
            .filter(row => $(row).find('.row-checkbox').is(':checked'))
            .map(row => tabela.row(row).data().codLote);
        if (LotesSelecionados.length === 0) {
            Mensagem('Nenhum lote selecionado!', 'warning');
        } else {
            Vincular_Lotes();

        }
    });
}

function TabelaNotas(listaNotas) {
    if ($.fn.DataTable.isDataTable('#table-notas')) {
        $('#table-notas').DataTable().destroy();
    }

    const tabela = $('#table-notas').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaNotas,
        columns: [{
            data: null,
            render: function (row) {
                return `
                        <div class="acoes" style="text-align: center">
                        <button 
                            class="btn-table" 
                            style="background-color: #b81414; color: white; border-color: #b81414" 
                            onclick="
                            Deletar_Notas(${row['tipo nota']})
                            ">
                            <i class="bi bi-trash3" title="Excluir" id="btn-excluir"></i>
                        </button> 
                    </div>
                    `;
            }
        },
        {
            data: 'tipo nota'
        },
        {
            data: 'Descricao'
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
        drawCallback: function () {
            const paginateHtml = $('.dataTables_paginate').html();
            $('#pagination-notas').html(paginateHtml);
            $('#pagination-notas span').remove();

            $('#pagination-notas a').off('click').on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            // Atualiza a quantidade de itens por página
            $('#itens-notas').on('input', function () {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        }
    });
    $('.search-table-notas').off('keyup change').on('keyup change', function () {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });

    $('.search-table-notas').on('click', function (e) {
        e.stopPropagation();
    });

    $('.search-table-notas').on('keydown', function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}

function TabelaNotasCsw(listaNotas) {
    if ($.fn.DataTable.isDataTable('#table-notas-csw')) {
        $('#table-notas-csw').DataTable().destroy();
    }

    const tabela = $('#table-notas-csw').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaNotas,
        columns: [{
            data: null,
            render: () => `
                    <div class="acoes d-flex justify-content-center align-items-center" style="height: 100%;">
                        <input type="checkbox" class="row-checkbox">
                    </div>`
        },
        {
            data: 'codigo'
        },
        {
            data: 'descricao'
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
        drawCallback: function () {
            $('#pagination-notas-csw').html($('.dataTables_paginate').html());
            $('#pagination-notas-csw span').remove();
            $('#pagination-notas-csw a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('.search-table-notas-csw').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });

    $('#btn-selecionar-notas').off('click').on('click', () => {
        NotasSelecionadas = tabela.rows().nodes().toArray()
            .filter(row => $(row).find('.row-checkbox').is(':checked'))
            .map(row => tabela.row(row).data().codigo);
        if (NotasSelecionadas.length === 0) {
            Mensagem('Nenhuma nota selecionada!', 'warning');
        } else {
            Vincular_Notas();

        }
    });
}

function TabelaAbc(listaAbc) {
    if ($.fn.DataTable.isDataTable('#table-abc')) {
        $('#table-abc').DataTable().destroy();
    }

    const tabela = $('#table-abc').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaAbc,
        columns: [{
            data: null,
            render: function (row) {
                return `
                        <div class="acoes" style="text-align: center">
                        <button 
                            class="btn-table" 
                            style="background-color: yellow; color: black; border-color: yellow" 
                            onclick="
                                $('#select-abc').val('${row['nomeABC']}').change();
                                let valor = '${row['perc_dist']}'; // Valor vindo da tabela (exemplo: '20.1 %')
                                
                                // Remove espaços e '%' e converte para número
                                valor = parseFloat(valor.replace('%', '').replace(',', '.').trim()) * 10; 
                                
                                // Formata para o padrão brasileiro com vírgula
                                valor = valor.toFixed(1).replace('.', ','); // Resultado: '20,1'
                                
                                // Aplica o valor ao campo e força a máscara
                                $('#input-distribuicao').val(valor).trigger('input');
                                $('#modal-abc').modal('show');
                            ">
                            <i class="bi bi-pencil-square" title="Editar" id="btnEditar"></i>
                        </button> 
                    </div>
                    `;
            }
        },
        {
            data: 'nomeABC'
        },
        {
            data: 'perc_dist',
            render: function (data, type, row) {
                return data + ' %';
            }
        },
        {
            data: 'perc_Acumulado',
            render: function (data, type, row) {
                return data + ' %';
            }
        }
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
        drawCallback: function () {
            const paginateHtml = $('.dataTables_paginate').html();
            $('#pagination-abc').html(paginateHtml);
            $('#pagination-abc span').remove();

            $('#pagination-abc a').off('click').on('click', function (e) {
                e.preventDefault();

                if ($(this).hasClass('previous') && tabela.page() > 0) {
                    tabela.page(tabela.page() - 1).draw('page');
                } else if ($(this).hasClass('next') && tabela.page() < tabela.page.info().pages - 1) {
                    tabela.page(tabela.page() + 1).draw('page');
                }
            });

            // Atualiza a quantidade de itens por página
            $('#itens-abc').on('input', function () {
                const pageLength = parseInt($(this).val(), 10);
                if (!isNaN(pageLength) && pageLength > 0) {
                    tabela.page.len(pageLength).draw();
                }
            });

            $('.dataTables_paginate').hide();
        }
    });

    $('.search-table-abc').off('keyup change').on('keyup change', function () {
        const columnIndex = $(this).closest('th').index();
        const searchTerm = $(this).val();
        tabela.column(columnIndex).search(searchTerm).draw();
    });

    $('.search-table-abc').on('click', function (e) {
        e.stopPropagation();
    });

    $('.search-table-abc').on('keydown', function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            e.stopPropagation();
        }
    });
}
