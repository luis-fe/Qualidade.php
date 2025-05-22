let arrayCategoriaMP = ''

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
});

document.addEventListener('DOMContentLoaded', function () {
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });
});

 const menorSugestaoPC = Math.min(...listaDetalhes.map(item => parseFloat(item.Sugestao_PCs)));


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

async function AnaliseProgramacaoPelaMP(arrayCategoriaMP) {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "CalculoPcs_baseaado_MP",
            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                arrayCategoriaMP: typeof arrayCategoriaMP !== 'undefined' ? arrayCategoriaMP : [] // Garante que esteja definido
            }
        };
        
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        TabelaAnalise(response);
        $('.div-analise').removeClass('d-none');

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}
    const categoriasMP = [
    "-", "CADARCO", "ELASTICOS", "ENTRETELA", "ETIQUETAS",
    "GOLAS", "MALHA", "RIBANA", "TECIDO PLANO", "ZIPER"
  ];

  function carregarCheckboxes() {
    const container = document.getElementById('categoriaCheckboxes');
    container.innerHTML = ''; // limpa checkboxes anteriores
    categoriasMP.forEach((categoria, index) => {
      const checkbox = document.createElement('div');
      checkbox.className = 'form-check';

      checkbox.innerHTML = `
        <input class="form-check-input" type="checkbox" value="${categoria}" id="categoria${index}">
        <label class="form-check-label" for="categoria${index}">
          ${categoria}
        </label>
      `;
      container.appendChild(checkbox);
    });
  }

    function confirmarCategoria() {
        arrayCategoriaMP = Array.from(
            document.querySelectorAll('#categoriaCheckboxes input:checked')
        ).map(el => el.value);

        console.log("Categorias selecionadas:", arrayCategoriaMP);
        AnaliseProgramacaoPelaMP(arrayCategoriaMP);
        // Fecha o modal
        bootstrap.Modal.getInstance(document.getElementById('categoriaModal')).hide();

    
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
        dom: '<"top"B>rt',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
            title: 'Necessidade de Materiais',
            className: 'btn-tabelas'
        },
                 {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Selecionar Categoria MP.',
            title: 'Selecionar Categoria MP.',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                carregarCheckboxes(); // <-- Chamada direta
                $('#categoriaModal').modal('show');

            },
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
                    await Consulta_Simulacao_Especifica();
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
                await Consulta_Abc2();
                Consulta_Categorias2(); 
            },
        },

        ],
        columns: [{
            data: 'categoria'
        },
        {
            data: 'marca'
        },
        {
            data: 'codEngenharia',
            
        },
        {
            data: 'codReduzido',

        },
        {
            data: 'nome',
        },
        {
            data: 'codCor',

        },
        {
            data: 'tam',

        },
        {
            data: 'faltaProg (Tendencia)',

        },
        {
            data: 'Sugestao_PCs',
            render: function (data, type, row) {
                return `<span class="detalhamentoSku" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
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


      // Clique no hiperlink "codReduzido"
    $('#table-analise').on('click', '.detalhamentoSku', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codReduzido');
        const codPlan = $('#select-plano').val();
        const consideraPedidosBloqueado =  $('#select-pedidos-bloqueados').val();
        console.log(`Teste2 Plano selecionado: ${codPlan}, reduzido: ${codReduzido}`)
        
        Detalhar_Sku(codReduzido,consideraPedidosBloqueado, codPlan);
    });

}




async function Detalhar_Sku(codReduzido) {
    $('#loadingModal').modal('show');
        console.log('Valor da descrição da simulacao detalhado:');
    try {

        const requestData = {
            acao: "detalharSku_x_AnaliseEmpenho",

            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedBloq": $('#select-pedidos-bloqueados').val(),
                "codReduzido": codReduzido,
               // "nomeSimulacao":  cacheDescricao
            }

        };

const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response)
        TabeldetalhamentoSku(response);
        $('#modal-detalhamentoSku').modal('show');
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error');
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

const Consulta_Abc2 = async () => {
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

const Consulta_Categorias2 = async () => {
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

function TabeldetalhamentoSku(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamentoSku')) {
        $('#table-detalhamentoSku').DataTable().destroy();
    }

    const tabela = $('#table-detalhamentoSku').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 15,
        dom: 'Bfrtip', // <-- necessário para os botões aparecerem
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
                title: 'Detalhamento Cálculo do SKU',
                className: 'btn-tabelas',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            if (typeof data === 'string') {
                                return data.replace(/\./g, '').replace(',', '.');
                            }
                            return data;
                        }
                    }
                }
            }
        ],
        data: listaDetalhes,
        columns: [
            { data: 'codReduzido' },
            { data: 'CodComponente' },
            { data: 'descricaoComponente' },
            { data: 'estoqueAtualMP',
                 render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                    return new Intl.NumberFormat('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(data);
                    }
                    // Para ordenação e exportação, retorna o valor original numérico
                    return parseFloat(data);
                }
             },
            { data: 'EmRequisicao',
                 render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                    return new Intl.NumberFormat('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(data);
                    }
                    // Para ordenação e exportação, retorna o valor original numérico
                    return parseFloat(data);
                }
             },
            {
                data: 'EstoqueAtualMPLiquido',
                render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                    return new Intl.NumberFormat('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(data);
                    }
                    // Para ordenação e exportação, retorna o valor original numérico
                    return parseFloat(data);
                }
            },
            { data: 'faltaProg (Tendencia)MP_total',
                 render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                    return new Intl.NumberFormat('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(data);
                    }
                    // Para ordenação e exportação, retorna o valor original numérico
                    return parseFloat(data);
                }
            },
            { data: 'EstoqueDistMP',
                 render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                    return new Intl.NumberFormat('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(data);
                    }
                    // Para ordenação e exportação, retorna o valor original numérico
                    return parseFloat(data);
                }
             },
            { data: 'faltaProg (Tendencia)' },
            {
                data: 'Sugestao_PCs',
                render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return new Intl.NumberFormat('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(data);
                    }
                    return parseFloat(data);
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
        drawCallback: function () {
            $('#pagination-detalhamentoSku').html($('.dataTables_paginate').html());
            $('#pagination-detalhamentoSku span').remove();
            $('#pagination-detalhamentoSku a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        },
        rowCallback: function(row, data) {
            if (parseFloat(data.Sugestao_PCs) === menorSugestaoPC) {
                $(row).css('background-color', '#ffcccc');
            }
        }
    });

    // Adiciona os botões à interface
    tabela.buttons().container().appendTo('#table-detalhamentoSkuSimulado_wrapper .col-md-6:eq(0)');

    $('#itens-detalhamentoSkuSimulado').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    
    
    $('.search-input-detalhamentoSkuSimulado').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });



}
