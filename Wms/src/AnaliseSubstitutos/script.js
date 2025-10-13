$(document).ready(async () => {
    $('#itensPorPagina').change(function() {
        const itensPorPagina = $(this).val();
        $('#TableTags').DataTable().page.len(itensPorPagina).draw();
    });
    $('#NomeRotina').text('Análise Substitutos');
    await ConsultaCategorias();
    ConsultaSubstitutos();

});

const ConsultaSubstitutos = () => {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Substitutos'
        },
        success: (data) => {
            console.log(data);
            criarTabelaTags(data);
            $('#loadingModal').modal('hide');
        },
    });
}

const ConsultaCategorias = () => {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Categorias'
        },
        success: (data) => {
            console.log(data);
            $('.dropdown-menu').empty();
            $('.dropdown-menu').append('<label><input type="checkbox" id="selectAll"> Selecionar Todos</label><br>');
            data.forEach(item => {
                $('.dropdown-menu').append(`<label><input type="checkbox" class="filtro" value="${item.categoria}"> ${item.categoria}</label><br>`);
            });
            $('#loadingModal').modal('hide');
        },
    });
}

function criarTabelaTags(listaReservas) {
    $('#Paginacao .dataTables_paginate').remove();

    if ($.fn.DataTable.isDataTable('#TableTags')) {
        $('#TableTags').DataTable().destroy();
    }

    const tabela = $('#TableTags').DataTable({
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
                data: null,
                render: function(data, type, row, meta) {
                    return '<input type="checkbox" class="rowCheckbox" id="checkbox_' + meta.row + '">';
                }
            },
            {data: '2-numeroOP'},
            {data: '3-codProduto'},
            {data: '4-cor'},
            {data: '6-codigoPrinc'},
            {data: '7-nomePrinc'},
            {data: '8-codigoSub'},
            {data: '9-nomeSubst'},
            {data: '1-categoria'},
            {data: '10-aplicacao'},
            {data: 'considera'},
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
//--Criando a paginação da tabela
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

    //-- Adicionando Filtro de Categoria na tabela, na coluna 8 da tabela
    $('#selectAll').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.filtro').prop('checked', isChecked);
        atualizarFiltro();
    });

    $('.filtro').on('change', function() {
        $('#selectAll').prop('checked', $('.filtro:checked').length === $('.filtro').length);
        atualizarFiltro();
    });

    function atualizarFiltro() {
        var filtros = [];
        $('.filtro:checked').each(function() {
            filtros.push($(this).val());
        });
        tabela.columns(8).search(filtros.join('|'), true, false).draw();
    }

    //-- Pesquisa qualquer palavra na tabela
    $('#searchFila').on('keyup', function() {
        tabela.search(this.value).draw();
    });

    // -- percorre quais checkbox estão selecionados para chamar a função de salvar
    $('#Selecionar').on('click', async function() {
        const arrayOP = [];
        const arraycor = [];
        const arraydesconsidera = [];


        tabela.rows().every(function(rowIdx) {
            const checkbox = $(this.node()).find('.rowCheckbox');
            if (checkbox.is(':checked')) {
                const row = this.data();
                arrayOP.push(row['2-numeroOP']);
                arraycor.push(row['4-cor']);
                arraydesconsidera.push(row['considera'] === 'sim' ? '-' : 'sim');
            }
        });

        const dadosSelecionados = {
            arrayOP: arrayOP,
            arraycor: arraycor,
            arraydesconsidera: arraydesconsidera
        };


        console.log(dadosSelecionados)
        SalvarSubstitutos(dadosSelecionados)
    });

}


const SalvarSubstitutos = (Dados) => {
    $('#loadingModal').modal('show');
$.ajax({
    type: 'PUT',
    url: 'requests.php',
    contentType: 'application/json',
    data: JSON.stringify({
        acao: 'Salvar_Substitutos',
        dados: Dados
    }),
    success: function(response) {
        console.log(response);
        if(response.status == true){
            Swal.fire({
            title: 'Op Atualizada',
            icon: 'success',
            showConfirmButton: false,
            timer: "3000",
        });
        $('#loadingModal').modal('hide');
        ConsultaSubstitutos();
        }
    },
    error: function(xhr, status, error) {
        console.error('Erro na solicitação:', status, error);
        console.error('Resposta completa:', xhr.responseText);
    }
});
}