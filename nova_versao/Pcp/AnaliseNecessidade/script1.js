let cacheDescricao = ''


$(document).ready(async () => {
    Consulta_Planos();
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

    $('#btn-vendas').addClass('btn-menu-clicado')


     // Aqui está o onsubmit do form
    $('#form-simulacao').on('submit', async function (e) {
        e.preventDefault();


        const inputDescricao = document.getElementById('select-simulacao');

        console.log('Valor da descrição:', inputDescricao.value);
        cacheDescricao = inputDescricao.value;

        await Cadastro_Simulacao();
        await Consulta_Simulacoes();

        //await Simular_Programacao(inputDescricao.value);
        $('#loadingModal').modal('show');

                // Fecha o modal
        $('#modal-simulacao').modal('hide');
        $('#loadingModal').modal('hide');
    
    });


    $('#form-cad_simulacao').on('submit', async function (e) {
    e.preventDefault();

    await Cadastro_Simulacao2();
    await Consulta_Simulacoes();
    $('#descricao-simulacao').val('');


    $('#modal-cad_simulacao').modal('hide');
    });

    $(document).on('click', '#btn-zerar-categorias', function () {
    $('.input-categoria2').val('0,00%');
    
    });



});

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


function abrirModal(mensagem) {
  return new Promise((resolve) => {
    const modalEl = document.getElementById('modal-question');
    const modal = new bootstrap.Modal(modalEl);
    $('#modal-mensagem').text(mensagem);

    const btnSim = document.getElementById('btn-sim');
    const btnNao = document.getElementById('btn-nao');

    if (!btnSim || !btnNao) {
      console.error("Botões do modal não encontrados");
      resolve('nao');
      return;
    }

    // Reset estilo padrão
    const resetBtnStyle = (btn) => {
      btn.style.backgroundColor = '#6c757d'; // cinza Bootstrap
      btn.style.color = '#fff';
      btn.style.borderColor = '#6c757d';
    };

    const highlightBtn = (btn) => {
      btn.style.backgroundColor = '#198754'; // verde Bootstrap
      btn.style.color = '#fff';
      btn.style.borderColor = '#198754';
    };

    const cleanup = () => {
      btnSim.onclick = null;
      btnNao.onclick = null;
    };

    modalEl.addEventListener('shown.bs.modal', () => {
      resetBtnStyle(btnSim);
      resetBtnStyle(btnNao);
      highlightBtn(btnNao);

      btnSim.onclick = () => {
        cleanup();
        resolve('sim');
        modal.hide();
      };

      btnNao.onclick = () => {
        cleanup();
        resolve('nao');
        modal.hide();
      };

      btnNao.focus();
    }, { once: true });

    modal.show();
  });
}



async function Analise_Materiais() {
    const codPlano = $('#select-plano').val();     
    const respostaCalculo = await Consulta_UltimoCalculo_(codPlano);
    console.log(`o retorno foi ${respostaCalculo.status}`)
    if (respostaCalculo.status === false) {
        ChamadaatualizarAnalise(false);
    } else {
        console.log(`o retorno foi ${respostaCalculo.mensagem}`)

        const respostaModal = await abrirModal(respostaCalculo.mensagem);
        console.log(`o retorno resposta do modal foi ${respostaModal}`)

        if(respostaModal == 'sim'){
            ChamadaatualizarAnalise(false);
        }else{
            ChamadaatualizarAnalise(true);
        }
    }
}


async function ChamadaatualizarAnalise(congelamento) {
        $('#loadingModal').modal('show');
        try {
            const requestData = {
                acao: "Analise_Materiais",
                dados: {
                    "codPlano": $('#select-plano').val(),
                    "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                    "congelar":congelamento
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
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
            Mensagem_Canto('Erro', 'error');
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
                    await Consulta_Abc_Plano_();
                    await Consulta_Categorias_();
                    await Consulta_Simulacao_Especifica_();
                    $('#inputs-container-marcas').removeClass('d-none')
                }
            }
        },
         {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Nova Simulação',
            title: 'Nova Simulação',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                $('#modal-cad_simulacao').modal('show');
                await Consulta_Abc2_();
                Consulta_Categorias2_(); 
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
            data: '17-SaldoSubs'
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


const Consulta_Simulacao_Especifica_ = async () => {
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

const Consulta_Abc_Plano_ = async () => {
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

        data[0]['3- Detalhamento:'].forEach((item) => {
            const inputHtml = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeABC}</label>
                    <input type="text" class="inputs-percentuais input-abc col-12" id="${item.nomeABC}">
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


const Consulta_UltimoCalculo_ = async (plano) => {
    console.log(`o parametro da funcao é:  ${plano}`)
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'ConsultaUltimoCalculo',
                plano
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

const Consulta_Abc2_ = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Abc',
            }
        });

        const inputsContainer = $('#inputs-Cadcontainer');
        inputsContainer.empty();

        data.forEach((item) => {
            const inputHtml = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeABC}</label>
                    <input type="text" class="inputs-percentuais input-abc2 col-12" id="${item.nomeABC}" placeholder="%" value="0%">
                </div>
            `;
            inputsContainer.append(inputHtml);
        });

        $('.input-abc2').mask("##0,00%", {
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

        data.forEach((item) => {
            const inputHtml = `
                    <div class="col-md-3 mb-3">
                        <label class="form-label">${item.nomeCategoria}</label>
                        <input type="text" class="inputs-percentuais input-categoria col-12" id="${item.nomeCategoria}" placeholder="%">
                    </div>
                `;
            inputsContainer.append(inputHtml);
        });

        $('.input-categoria').mask("##0,00%", {
            reverse: true
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

const Consulta_Categorias2_ = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Categorias',
            }
        });

        const inputsContainer = $('#inputs-Cadcontainer-Cadcategorias');
        inputsContainer.empty();

        data.forEach((item) => {
            const inputHtml = `
                    <div class="col-md-3 mb-3">
                        <label class="form-label">${item.nomeCategoria}</label>
                        <input type="text" class="inputs-percentuais input-categoria2 col-12" id="${item.nomeCategoria}" placeholder="%" value="10000%">
                    </div>
                `;
            inputsContainer.append(inputHtml);
        });

        $('.input-categoria2').mask("##0,00%", {
            reverse: true
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};





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
            data: '11-CodComponente'
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
        }
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

async function Cadastro_Simulacao2() {
    $('#loadingModal').modal('show');
    try {
        const categorias = [];
        const percentuais_categorias = [];

        $('.input-categoria2').each(function () {
            const categoria = $(this).attr('id');
            const percentual = parseFloat($(this).val().replace(',', '.'));

            if (categoria && !isNaN(percentual)) {
                categorias.push(categoria);
                percentuais_categorias.push(percentual);
            }
        });

        const abcs = [];
        const percentuais_abc = [];

        $('.input-abc2').each(function () {
            const abc = $(this).attr('id');
            const percentual = parseFloat($(this).val().replace(',', '.'));

            if (abc && !isNaN(percentual)) {
                abcs.push(abc);
                percentuais_abc.push(percentual);
            }
        });

        const marcas = [];
        const percentuais_marca = [];

        $('.input-marca2').each(function () {
            const marca = $(this).attr('id');
            const percentual = parseFloat($(this).val().replace(',', '.'));

            if (marca && !isNaN(percentual)) {
                marcas.push(marca);
                percentuais_marca.push(percentual);
            }
        });

        const requestData = {
            acao: "Cadastro_Simulacao",
            dados: {
                "nomeSimulacao": $('#descricao-simulacao').val(),
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
        $('#loadingModal').modal('hide');
    }
};

async function Consulta_Simulacoes() {
    $('#loadingModal').modal('show');
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
            $('#loadingModal').modal('hide');
            const descricao = $('#descricao-simulacao').val();
            console.log(descricao)
            $('#select-simulacao').val(descricao);
        },

        error: function (xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
            $('#loadingModal').modal('hide');
        }
    });
}

