$(document).ready(async () => {
    Consulta_Planos();
    Consulta_Simulacoes();
    $('#select-plano').select2({
        placeholder: "Selecione um plano",
        allowClear: false,
        width: '100%'
    });

    $('#select-pedidos-bloqueados').select2({
        placeholder: "Pedidos Bloqueados?",
        allowClear: false,
        width: '100%'
    });

    $('#select-simulacao').select2({
        placeholder: "Selecione uma simulação",
        allowClear: false,
        width: '100%',
        dropdownParent: $('#modal-simulacao')
    });

    $('#select-simulacao').on('change', async function () {
        $('#inputs-container-marcas').removeClass('d-none')
        await Consulta_Abc_Plano();
        await Consulta_Categorias()
        await Consulta_Simulacao_Especifica();
    });

    $('#btn-vendas').addClass('btn-menu-clicado')
});

let nomeSimulacao = ''
async function simulacao(texto, tipo) {
    $('#modal-simulacao').modal('hide');
    $('#modal-nova-simulacao').modal('hide');
    $('#loadingModal').modal('show');
    await Cadastro_Simulacao(texto, tipo);
    await Consulta_Simulacoes();
    await Simular_Programacao(texto);
    nomeSimulacao = texto;
    $('#loadingModal').modal('hide');
};

const Consulta_Planos = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Planos'
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

const Consulta_Naturezas = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Naturezas'
            },
        });
        TabelaNaturezas(response);
        $('.div-naturezas').removeClass('d-none');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
};

async function Simular_Programacao(simulacao) {
    try {
        const requestData = {
            acao: "Simular_Programacao",

            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedBloq": $('#select-pedidos-bloqueados').val(),
                "nomeSimulacao": simulacao,
                "empresa": "1"
            }

        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        if (response === null) {

        } else {
            TabelaAnalise(response);
            $('#titulo').html(`
            <span class="span-icone"><i class="bi bi-bag-check"></i></span>
            Análise de Materiais
          `);

            $('#titulo').html(`
            <span class="span-icone"><i class="bi bi-bag-check"></i></span>
            Análise de Materiais - 
            <span style="display: inline-block; position: relative;">
              <strong>${simulacao}</strong>
              <button onclick="Selecionar_Calculo()" 
                      style="position: absolute; top: 0; right: -20px; border: none; background: none; font-weight: bold; color: red; cursor: pointer;">
                ×
              </button>
            </span>
          `);
        }

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
};

async function Cadastro_Simulacao(simulacao, tipo) {
    try {
        const categorias = [];
        const percentuais_categorias = [];

        const abcs = [];
        const percentuais_abc = [];

        const marcas = [];
        const percentuais_marca = [];

        if (tipo === "cadastro") {
            $('.input-categoria-2').each(function () {
                const categoria = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (categoria && !isNaN(percentual)) {
                    categorias.push(categoria);
                    percentuais_categorias.push(percentual);
                }
            });

            $('.input-abc-2').each(function () {
                const abc = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (abc && !isNaN(percentual)) {
                    abcs.push(abc);
                    percentuais_abc.push(percentual);
                }
            });

            $('.input-marca-nova').each(function () {
                const marca = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (marca && !isNaN(percentual)) {
                    marcas.push(marca);
                    percentuais_marca.push(percentual);
                }
            });
        } else {
            $('.input-categoria').each(function () {
                const categoria = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (categoria && !isNaN(percentual)) {
                    categorias.push(categoria);
                    percentuais_categorias.push(percentual);
                }
            });

            $('.input-abc').each(function () {
                const abc = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (abc && !isNaN(percentual)) {
                    abcs.push(abc);
                    percentuais_abc.push(percentual);
                }
            });

            $('.input-marca').each(function () {
                const marca = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (marca && !isNaN(percentual)) {
                    marcas.push(marca);
                    percentuais_marca.push(percentual);
                }
            });
        }


        const requestData = {
            acao: "Cadastro_Simulacao",
            dados: {
                "nomeSimulacao": simulacao,
                arrayAbc: [
                    abcs,
                    percentuais_abc

                ],
                arrayCategoria: [
                    categorias,
                    percentuais_categorias
                ],
                arrayMarca: [
                    marcas,
                    percentuais_marca
                ]
            }
        };
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
    }
};

async function Consulta_Simulacoes() {
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Simulacoes',
        },
        success: function (data) {
            $('#select-simulacao').empty();
            $('#select-simulacao').append('<option value="" disabled selected>Selecione uma simulação...</option>');
            data.forEach(function (item) {
                $('#select-simulacao').append(`
                        <option value="${item['nomeSimulacao']}">
                            ${item['nomeSimulacao']}
                        </option>
                    `);
            });
            const descricao = $('#descricao-simulacao').val();
            console.log(descricao)
            $('#select-simulacao').val(descricao);
        },

        error: function (xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
        }
    });
}


const Consulta_Ultimo_Calculo = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Ultimo_Calculo',
                plano: $('#select-plano').val()
            }
        });
        return {
            status: data[0]['status'],
            mensagem: data[0]['Mensagem']
        };


    } catch (error) {
        console.error('Erro ao consultar planos:', error);
        return null; // ou algum valor padrão indicando erro

    }
};

async function Selecionar_Calculo() {
    const respostaCalculo = await Consulta_Ultimo_Calculo();

    if (respostaCalculo.status === null) {
        Analise_Materiais(false);
        return;
    }

    try {
        const result = await Swal.fire({
            title: `${respostaCalculo.mensagem}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "Recalcular",
            cancelButtonText: "Não"
        });

        // Aguarda o modal fechar visualmente
        setTimeout(() => {
            if (result.isConfirmed) {
                Analise_Materiais(false);
            } else {
                Analise_Materiais(true);
            }
        }, 300); // Tempo suficiente para animação de fechamento
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro na solicitação', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}





const Consulta_Comprometidos = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Comprometidos'
            },
        });
        TabelaComprometido(response);
        $('.div-comprometido').removeClass('d-none');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
};

const Consulta_Comprometidos_Compras = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Comprometidos_Compras'
            },
        });
        TabelaComprometidoCompras(response);
        $('.div-comprometido-compras').removeClass('d-none');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
};

async function Analise_Materiais(congelar) {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Analise_Materiais",
            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                "congelar": congelar
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        await $('.div-analise').removeClass('d-none');
        await TabelaAnalise(response);
        await Consulta_Naturezas();
        await Consulta_Comprometidos();
        Consulta_Comprometidos_Compras();
        $('#titulo').html(`
            <span class="span-icone"><i class="bi bi bi-bag-check"></i></span>
            Análise de Materiais
          `);
        nomeSimulacao = "";
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Detalha_Necessidade(codReduzido) {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Detalha_Necessidade",
            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                "codComponente": codReduzido
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        TabelaDetalhamento(response);
        $('#modal-detalhamento').modal('show')
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}


async function TabelaAnalise(listaAnalise) {
    if ($.fn.DataTable.isDataTable('#table-analise')) {
        $('#table-analise').DataTable().destroy();
    }

    const tabela = $('#table-analise').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaAnalise,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
            title: 'Necessidade de Materiais',
            className: 'btn-tabelas'
        },
        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Simulação',
            title: 'Simulação',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                $('#modal-simulacao').modal('show');
                $('#campo-simulacao').removeClass('d-none');

                const simulacaoValue = $('#select-simulacao').val()?.trim() || "";

                if (simulacaoValue === "") {
                    $('#inputs-container-categorias').empty();
                    $('#inputs-container').empty();
                    $('#inputs-container-marcas').addClass('d-none')
                } else {
                    await Consulta_Abc_Plano();
                    await Consulta_Categorias();
                    $('#inputs-container-marcas').removeClass('d-none')
                }
            }
        },
        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Nova Simulação',
            title: 'Nova Simulação',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                $('#modal-nova-simulacao').modal('show');
                $('#inputs-container-novas-marcas').removeClass('d-none');
                await Consulta_Abc_Plano();
                await Consulta_Categorias();
            },
        },
        ],
        columns: [{
            data: '02-codCompleto'
        },
        {
            data: '03-descricaoComponente'
        },
        {
            data: '01-codReduzido',
            render: function (data, type, row) {
                return `<span class="codReduzido" data-codigoReduzido="${data}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
            }
        },
        {
            data: '10-Necessidade Compra (Tendencia)',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '12-Necessidade Ajustada Compra (Tendencia)',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '08-estoqueAtual',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '09-SaldoPedCompras',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '07-EmRequisicao',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '04-fornencedorPreferencial'
        },
        {
            data: '05-unidade'
        },
        {
            data: '14-Lote Mínimo'
        },
        {
            data: '11-Lote Mutiplo'
        },
        {
            data: '13-LeadTime'
        },
        {
            data: 'fatorConversao'
        },
        {
            data: '15-CodSubstituto'
        },
        {
            data: '16-NomeSubstituto'
        },
        {
            data: '17-SaldoSubs.'
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
        drawCallback: function () {
            $('#pagination-analise').html($('.dataTables_paginate').html());
            $('#pagination-analise span').remove();
            $('#pagination-analise a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }

        
    });

    $('.search-input-analise').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });

    $('#itens-analise').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('#table-analise tbody').on('click', 'tr', function (event) {
        // Verifica se o clique foi em um hiperlink
        if ($(event.target).hasClass('codReduzido')) {
            return; // Não executa o código para seleção de linha
        }

        const isSelected = $(this).hasClass('selected');
        $('#table-analise tbody tr').removeClass('selected');

        if (!isSelected) {
            $(this).addClass('selected');
            const rowData = tabela.row(this).data();
            const filtro = rowData['01-codReduzido'];
            filtrarTabelas(filtro);
        } else {
            // Remove o filtro e reseta a tabela de naturezas
            filtrarTabelas('');
        }
    });

    // Clique no hiperlink "codReduzido"
    $('#table-analise').on('click', '.codReduzido', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codigoReduzido');
        Detalha_Necessidade(codReduzido);
    });
}


function TabelaNaturezas(listaNaturezas) {
    if ($.fn.DataTable.isDataTable('#table-naturezas')) {
        $('#table-naturezas').DataTable().destroy();
    }

    const tabela = $('#table-naturezas').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaNaturezas,
        columns: [{
            data: 'CodComponente'
        },
        {
            data: 'nome'
        },
        {
            data: 'natureza'
        },
        {
            data: 'estoqueAtual',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
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
        drawCallback: function () {
            $('#pagination-naturezas').html($('.dataTables_paginate').html());
            $('#pagination-naturezas span').remove();
            $('#pagination-naturezas a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('#itens-naturezas').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-naturezas').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function TabelaComprometido(listaComprometido) {
    if ($.fn.DataTable.isDataTable('#table-comprometido')) {
        $('#table-comprometido').DataTable().destroy();
    }

    const tabela = $('#table-comprometido').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaComprometido,
        columns: [{
            data: 'CodComponente'
        },
        {
            data: 'nomeMaterial'
        },
        {
            data: 'OP'
        },
        {
            data: 'EmRequisicao',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
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
        drawCallback: function () {
            $('#pagination-comprometido').html($('.dataTables_paginate').html());
            $('#pagination-comprometido span').remove();
            $('#pagination-comprometido a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('#itens-comprometido').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-comprometido').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function TabelaComprometidoCompras(listaComprometido) {
    if ($.fn.DataTable.isDataTable('#table-comprometido-compras')) {
        $('#table-comprometido-compras').DataTable().destroy();
    }

    const tabela = $('#table-comprometido-compras').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaComprometido,
        columns: [{
            data: 'CodComponente'
        },
        {
            data: 'nome'
        },
        {
            data: 'numero'
        },
        {
            data: 'tipo'
        },
        {
            data: 'SaldoPedCompras',
            render: function (data, type) {
                if (type === 'display') {
                    // Formata o número para o formato brasileiro com separadores de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outras operações, retorna o número diretamente
                return data;
            }
        },
        {
            data: 'dataPrevisao'
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
        drawCallback: function () {
            $('#pagination-comprometido-compras').html($('.dataTables_paginate').html());
            $('#pagination-comprometido-compras span').remove();
            $('#pagination-comprometido-compras a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('#itens-comprometido-compras').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-comprometido-compras').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function TabelaDetalhamento(listaDetalhes) {
        // Atualiza o título com base no primeiro item da lista
    if (listaDetalhes.length > 0) {
        const cod = listaDetalhes[0]["11-CodComponente"] || "Sem código";
        document.getElementById("titulo-detalhamento").textContent = `Detalhamento - ${cod}`;
    } else {
        document.getElementById("titulo-detalhamento").textContent = "Detalhamento - (Sem dados)";
    }




    if ($.fn.DataTable.isDataTable('#table-detalhamento')) {
        $('#table-detalhamento').DataTable().destroy();
    }

    

    const tabela = $('#table-detalhamento').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaDetalhes,
        columns: [{
            data: '01-codEngenharia'
        },
        {
            data: '04-tam'
        },
        {
            data: '05-codCor'
        },
        {
            data: '03-nome'
        },
        {
            data: '02-codReduzido'
        },
        {
            data: '07-Ocorrencia em Pedidos',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: '09-previcaoVendas',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: '06-qtdePedida',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: '10-faltaProg (Tendencia)',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'class'
        },
        {
            data: 'classCategoria'
        },
        {
            data: '08-statusAFV'
        },
        {
            data: '12-unid'
        },
        {
            data: '13-consumoUnit'
        },
        {
            data: '14-Necessidade faltaProg (Tendencia)',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
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
        drawCallback: function () {
            $('#pagination-detalhamento').html($('.dataTables_paginate').html());
            $('#pagination-detalhamento span').remove();
            $('#pagination-detalhamento a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }, footerCallback: function (row, data, start, end, display) {
            const api = this.api();

            // Helper para converter strings para número
            const intVal = (i) => {
                if (typeof i === 'string') {
                    // Remover "R$", pontos e substituir vírgula por ponto
                    return parseFloat(i.replace(/[R$ ]/g, '').replace(/[\.]/g, '').replace(',', '.')) || 0;
                } else if (typeof i === 'number') {
                    return i;
                }
                return 0;
            };

            // Colunas que precisam de total
            const columnsToSum = ['14-Necessidade faltaProg (Tendencia)'];

            columnsToSum.forEach((columnName, idx) => {
                const colIndex = idx + 14; // Índice da coluna no DataTables

                // Total considerando todos os dados após filtro
                const total = api.column(colIndex, {
                    filter: 'applied'
                }).data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Atualizar o rodapé da coluna
                $(api.column(colIndex).footer()).html(
                    columnName === 'valorVendido' ? `R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` : total.toLocaleString('pt-BR')
                );
            });
        },
    });

    $('#itens-detalhamento').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-detalhamento').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function filtrarTabelas(filtro) {
    const TabelaNaturezas = $('#table-naturezas').DataTable();
    const tabelaComprometido = $('#table-comprometido').DataTable();
    const tabelaCompras = $('#table-comprometido-compras').DataTable();

    if (filtro === '') {
        TabelaNaturezas.column(0).search('').draw();
        tabelaComprometido.column(0).search('').draw();
        tabelaCompras.column(0).search('').draw();
    } else {
        TabelaNaturezas.column(0).search(`^${filtro}$`, true, false).draw();
        tabelaComprometido.column(0).search(`^${filtro}$`, true, false).draw();
        tabelaCompras.column(0).search(`^${filtro}$`, true, false).draw();
    }


}

async function Deletar_Simulacao() {

    try {
        const result = await Swal.fire({
            title: "Deseja deletar a simulação?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "Deletar",
        });

        if (result.isConfirmed) {
            $('#loadingModal').modal('show');

            const dados = {
                "nomeSimulacao": $('#select-simulacao').val(),
            };
            const requestData = {
                acao: "Deletar_Simulacao",
                dados: dados
            };
            const response = await $.ajax({
                type: 'DELETE',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });

            console.log(response)

            if (response['resposta'][0]['status'] === true) {
                Mensagem_Canto('Simulação deletada', 'success');
                Consulta_Simulacoes();
                $('#modal-simulacao').modal('hide')
            }
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro na solicitação', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

const Consulta_Abc_Plano = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Abc_Plano',
                plano: $('#select-plano').val()
            }
        });

        const inputsContainer = $('#inputs-container');
        inputsContainer.empty();
        const inputsContainerNova = $('#inputs-container-nova');
        inputsContainerNova.empty();

        data[0]['3- Detalhamento:'].forEach((item) => {
            const inputHtml1 = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeABC}</label>
                    <input type="text" class="inputs-percentuais input-abc col-12" id="${item.nomeABC}" placeholder="%">
                </div>
            `;
            const inputHtml2 = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeABC}</label>
                    <input type="text" class="inputs-percentuais input-abc-2 col-12" value=("0,00%") id="nova-${item.nomeABC}" placeholder="%">
                </div>
            `;
            inputsContainer.append(inputHtml1);
            inputsContainerNova.append(inputHtml2);
        });

        $('.input-abc').mask("##0,00%", {
            reverse: true
        });

        $('.input-abc-2').mask("##0,00%", {
            reverse: true
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
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

        const inputsContainer = $('#inputs-container');
        inputsContainer.empty();
        const inputsContainerNova = $('#inputs-container-nova');
        inputsContainerNova.empty();


        data.forEach((item) => {
            const inputHtml = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeABC}</label>
                    <input type="text" class="inputs-percentuais input-abc col-12" id="${item.nomeABC}" placeholder="%">
                </div>
            `;
            inputsContainer.append(inputHtml);
        });

        $('.input-abc').mask("##0,00%", {
            reverse: true
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

const Consulta_Categorias = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Categorias',
            }
        });

        const inputsContainer = $('#inputs-container-categorias');
        inputsContainer.empty();
        const inputsContainerNova = $('#inputs-container-categorias-nova');
        inputsContainerNova.empty();

        data.forEach((item) => {
            const inputHtml1 = `
    <div class="col-md-3 mb-3">
        <label class="form-label">${item.nomeCategoria}</label>
        <input type="text" class="inputs-percentuais input-categoria col-12" id="${item.nomeCategoria}" placeholder="%">
    </div>
`;

            const inputHtml2 = `
    <div class="col-md-3 mb-3">
        <label class="form-label">${item.nomeCategoria}</label>
        <input type="text" class="inputs-percentuais input-categoria-2 col-12" id="${item.nomeCategoria}-2" placeholder="%">
    </div>
`;
            inputsContainer.append(inputHtml1);
            inputsContainerNova.append(inputHtml2);
        });

        $('.input-categoria').mask("##0,00%", {
            reverse: true
        });

        $('.input-categoria-2').mask("##0,00%", {
            reverse: true
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

const Consulta_Simulacao_Especifica = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Simulacao_Especifica',
                simulacao: $('#select-simulacao').val()
            }
        });

        if (!data) {
            Mensagem_Canto('Não possui simulação para editar', 'warning');
            $('#modal-simulacao').modal('hide');
            return;
        }

        const campos = ["2- ABC", "3- Categoria", "4- Marcas"];
        campos.forEach(campo => {
            if (data[0][campo]) {
                data[0][campo].forEach(item => {
                    const key = item.class || item.categoria || item.marca;
                    const input = $(`#${key.replace(/\s+/g, '-').replace(/[^\w-]/g, '')}`);
                    if (input.length) {
                        input.val(`${parseFloat(item.percentual).toFixed(1).replace('.', ',')}%`);
                    }
                });
            }
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

