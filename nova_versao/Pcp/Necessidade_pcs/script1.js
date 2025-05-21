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

async function AnaliseProgramacaoPelaMP() {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "CalculoPcs_baseaado_MP",
            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                "arrayCategoriaMP":arrayCategoriaMP.value
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
                await AnaliseProgramacaoPelaMP();
                 // Fecha o modal
                bootstrap.Modal.getInstance(document.getElementById('categoriaModal')).hide();
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
            data: 'Sugestao_PCs'
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

    

}


