$(document).ready(async () => {
    $('#itensPorPagina').change(function() {
        const itensPorPagina = $(this).val();
        $('#TableEmbalagens').DataTable().page.len(itensPorPagina).draw();
    });
    $('#NomeRotina').text('Consumo de Embalagens');

    ConsultaEmbalagens();

});
const hoje = new Date().toISOString().split('T')[0];
document.getElementById('dataInicio').value = hoje;
document.getElementById('dataFim').value = hoje;

$('#ButtonFiltrar').click(() => {
    ConsultaEmbalagens()
});

$('#FormCadEmbalagem').submit(function(event) {
    event.preventDefault(); // Isso evita o envio padrão do formulário

    const dadosEdit = {
        "codcaixa": $('#InputCodigoEmbalagem').val(),
        "nomecaixa": $('#InputDescricao').val(),
        "tamanhocaixa": $('#InputTamanho').val(),

    }

    CadastrarEmbalagems(dadosEdit);
});


const Mensagem = async (mensagem, icon) => {
try {
    Swal.fire({
        title: `${mensagem}`,
        icon: `${icon}`,
        showConfirmButton: false,
        timer: "3000",
    });
} catch (err) {
    console.log(err)
}
}

const ConsultaEmbalagens = () => {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Embalagens',
            dataInicio: $('#dataInicio').val(),
            dataFim: $('#dataFim').val()
        },
        success: (data) => {
            console.log(data);
            criarTabelaEmbalagens(data);
            $('#loadingModal').modal('hide');
        },
    });
}

function criarTabelaEmbalagens(listaReservas) {
    $('#Paginacao .dataTables_paginate').remove();

    if ($.fn.DataTable.isDataTable('#TableEmbalagens')) {
        $('#TableEmbalagens').DataTable().destroy();
    }

    const tabela = $('#TableEmbalagens').DataTable({
        excel: true,
        responsive: false,
        paging: true,
        info: false,
        searching: true,
        colReorder: true,
        data: listaReservas,
        lengthChange: false,
        pageLength: 10,
        fixedHeader: true,
        dom: 'Bfrtip',
        buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fa-solid fa-file-excel"></i>',
                title: 'Fila de Reposição',
                className: 'ButtonExcel'
            },
            {
                extend: 'colvis',
                text: 'Visibilidade das Colunas',
                className: 'ButtonVisibilidade'
            }
        ],
        columns: [{
                data: 'codcaixa'
            },
            {
                data: 'tamcaixa'
            },
            {
                data: 'quantidade'
            },

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

    $('#searchEmbalagens').on('keyup', function() {
        tabela.search(this.value).draw();
    });

    $('.dataTables_paginate').appendTo('#Paginacao');

    $('#Paginacao .paginate_button.previous').on('click', function() {
        tabela.page('previous').draw('page');
    });

    $('#Paginacao .paginate_button.next').on('click', function() {
        tabela.page('next').draw('page');
    });

    const paginaInicial = 1;
    tabela.page(paginaInicial - 1).draw('page');

    $('#Paginacao .paginate_button').on('click', function() {
        $('#Paginacao .paginate_button').removeClass('current');
        $(this).addClass('current');
    });
}


async function CadastrarEmbalagems(dados) {

    console.log(dados);

    var requestData = {
        acao: "Cadastrar_Embalagem",
        dados: dados
    };

    try {
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        console.log(response);
        if (response['resposta'][0].Mensagem.includes('sucesso')) {
            Mensagem('Usuario Atualizado', 'success');
            $('#FormCadEmbalagem').modal('hide');
        } else {
            Mensagem('Erro', 'error');
            $('#FormCadEmbalagem').modal('hide');
        }

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error); // Exibir erro se ocorrer
    }
}