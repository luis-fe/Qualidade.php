$(document).ready(() => {
    $('#itensPorPagina').change(function () {
        const itensPorPagina = $(this).val();
        $('#TableChamados').DataTable().page.len(itensPorPagina).draw();
    });
    $('#NomeRotina').text('Chamados');
    ConsultaChamados();
    ConsultaAreas();
    
});

//-ACRESCENTAR A IMAGEM NA DIV

$('#InputFoto').change(function () {
    if (this.files && this.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#ImagemPreview').attr('src', e.target.result);
            $('#PreviewFoto').show();
            $('#BtnRemoverFoto').removeClass('d-none');
        }

        reader.readAsDataURL(this.files[0]);
    }

});
//- REMOVER A IMAGEM

$('#BtnRemoverFoto').click(function () {
    $('#ImagemPreview').attr('src', '');
    $('#PreviewFoto').hide();
    $('#InputFoto').val('');
    $('#BtnRemoverFoto').addClass('d-none');
});


const ConsultaChamados = () => {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Chamados'
        },
        success: (data) => {
            console.log(data);
            CriarTabelaChamados(data);
            $('#loadingModal').modal('hide');
        },
        error: (error) => {
            console.error('Erro ao consultar chamados:', error);
        }
    });
}

//- CONSULTA AREAS DE CHAMADOS E ACRESCENTA NA SELECT
const ConsultaAreas = () => {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Areas'
        },
        success: (data) => {
            $('#SelectAreaChamado').empty();
            const defaultOption = $('<option>', {
                value: '',
                text: 'Área de Chamado',
                disabled: true,
                selected: true
            });
            $('#SelectAreaChamado').append(defaultOption);
            data.forEach(area => {
                $('#SelectAreaChamado').append(`<option value="${area.area}">${area.area}</option>`);
            });
            $('#loadingModal').modal('hide');
        },
        error: (error) => {
            console.error('Erro ao consultar áreas:', error);
            $('#loadingModal').modal('hide');
        }
    });
}

function CriarTabelaChamados(listaChamados) {
    $('#Paginacao .dataTables_paginate').remove();
    if ($.fn.DataTable.isDataTable('#TableChamados')) {
        $('#TableChamados').DataTable().destroy();
    }

    const tabela = $('#TableChamados').DataTable({
        responsive: true,
        paging: true,
        info: false,
        searching: true,
        colReorder: true,
        data: listaChamados,
        lengthChange: false,
        pageLength: 10,
        fixedHeader: true,
        columns: [
            { data: 'id_chamado' },
            { data: 'descricao_chamado', className: 'descricao' },
            { data: 'solicitante' },
            { data: 'data_chamado' },
            { data: 'data_finalizacao_chamado' },
            { data: 'tipo_chamado' },
            { data: 'status_chamado' },
            {
                data: null,
                render: function (data, type, row) {
                    return `
                        <div class="acoes">
                            <i class="fa-solid fa-pen-to-square" style="font-size: 25px;" title="Editar Usuário"
                                onclick="AbrirModalEdit('${row.codigo}', '${row.nome}', '${row.funcao}', '${row.situacao}')"></i>
                        </div>`;
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
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        }
    });

    $('#search').on('keyup', function () {
        tabela.search(this.value).draw();
    });

    $('.dataTables_paginate').appendTo('#Paginacao');

    $('#Paginacao .paginate_button.previous').on('click', function () {
        tabela.page('previous').draw('page');
    });

    $('#Paginacao .paginate_button.next').on('click', function () {
        tabela.page('next').draw('page');
    });

    const paginaInicial = 1;
    tabela.page(paginaInicial - 1).draw('page');

    $('#Paginacao .paginate_button').on('click', function () {
        $('#Paginacao .paginate_button').removeClass('current');
        $(this).addClass('current');
    });
}

$('#FormCadChamados').submit(async function(event) {
    event.preventDefault(); // Isso evita o envio padrão do formulário
    const dataAtual = new Date();
    const ano = dataAtual.getFullYear().toString();
    const mes = (dataAtual.getMonth() + 1).toString().padStart(2, '0'); // +1 porque os meses são baseados em zero
    const dia = dataAtual.getDate().toString().padStart(2, '0');
    const dataFormatada = `${ano}/${mes}/${dia}`;
    const DadosCadastro = {
        "solicitante": usuario,
        "data_chamado": dataFormatada,
        "tipo_chamado": $('#SelectTipoChamado').val(),
        "area": $('#SelectAreaChamado').val(),
        "descricao_chamado": $('#InputDescricaoChamado').val(),
    };

    if ($('#InputFoto')[0].files.length > 0) {
        // Obtém a primeira imagem selecionada
        const imagemSelecionada = $('#InputFoto')[0].files[0];
        
        // Envia a imagem selecionada para a função enviarImagemParaAPI
        enviarImagemParaAPI(imagemSelecionada);
        console.log('Imagem selecionada:', imagemSelecionada);
    } else {
        console.log('Nenhuma imagem selecionada.');
    }

    // Chama a função para cadastrar o chamado após verificar a imagem
    CadastrarNovoChamado(DadosCadastro);
});

async function enviarImagemParaAPI(formData) {
    $('#loadingModal').modal('show');
    console.log(formData)
    try {
        // Substitua 'sua_url' pela URL da sua API que recebe a imagem
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify({
                acao: 'Cadastrar_Imagem',
                dados: formData,
            }),
        });

        $('#loadingModal').modal('hide');
    } catch (error) {
        console.error('Erro ao enviar imagem para API:', error);
    }
}

async function CadastrarNovoChamado(dados) {
    
    $('#loadingModal').modal('show');
    var requestData = {
        acao: "Cadastrar_Chamados",
        dados: dados
    };

    try {
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        $('#loadingModal').modal('hide');

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error); // Exibir erro se ocorrer
    }
}

