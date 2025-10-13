$(document).ready(async () => {
    $('#itensPorPagina').change(function () {
        const itensPorPagina = $(this).val();
        $('#TableUsuarios').DataTable().page.len(itensPorPagina).draw();
    });
    $('#NomeRotina').text("Usuários");
    ConsultaClientes();

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


const ConsultaClientes = () => {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consultar_Usuarios'
        },
        success: (data) => {
            console.log(data);
            criarTabelaUsuarios(data);
            $('#loadingModal').modal('hide');
        },
    });
}

$('#FormCadUsuario').submit(function(event) {
    event.preventDefault(); // Isso evita o envio padrão do formulário

    const DadosCadastro = {
        "codigo": $('#InputMatricula').val(),
        "nome": $('#InputNomeUsuario').val(),
        "funcao": $('#SelectFuncao').val(),
        "situacao": $('#SelectSituacao').val(),
        "senha": $('#InputSenhaUsuario').val(),
    };

    CadastrarUsuario(DadosCadastro);
});

$('#FormEditarUsuario').submit(function(event) {
    event.preventDefault(); // Isso evita o envio padrão do formulário

    const dadosEdit = {
        "codigo": $('#InputEditMatricula').val(),
        "nome": $('#InputEditNomeUsuario').val(),
        "funcao": $('#SelectEditFuncao').val(),
        "situacao": $('#SelectEditSituacao').val(),
    }

    AtualizarUsuario(dadosEdit, $('#InputEditMatricula').val(),);
});

function AbrirModalEdit(Matricula, Nome, Funcao, Situacao){
    console.log(Matricula)
    $('#InputEditMatricula').val(Matricula);
    $('#InputEditNomeUsuario').val(Nome);
    $('#SelectEditFuncao').val(Funcao);
    $('#SelectEditSituacao').val(Situacao);
    $('#modalEditarUsuario').modal('show');
}

async function CadastrarUsuario(dados) {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'PUT',
        url: 'requests.php',
        contentType: 'application/json',
        data: JSON.stringify({
            acao: 'Cadastrar_Usuario',
            dados: dados
        }),
        success: function (response) {
            console.log(response);
            if (response['resposta'].message.includes('existe')) {
                $('#loadingModal').modal('hide');
                Mensagem('Usuario já Existe', 'warning');
                $('#modalCadUsuario').modal('hide');
            } else if (response['resposta'].message.includes('criado')) {
                $('#loadingModal').modal('hide');
                Mensagem('Usuario Cadastrado', 'success');
                ConsultaClientes();
                $('#modalCadUsuario').modal('hide');
            }
            else {
                $('#loadingModal').modal('hide');
                Mensagem('Erro', 'error');
                $('#modalCadUsuario').modal('hide');
            }

        },
    });
}

async function AtualizarUsuario(dados, matricula) {
    $('#loadingModal').modal('show');
console.log(dados);
console.log(matricula);
    var requestData = {
        acao: "Atualizar_Usuarios",
        matricula: matricula,
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
        if (response['resposta'].message.includes('sucesso')) {
            $('#loadingModal').modal('hide');
            Mensagem('Usuario Atualizado', 'success');
            $('#modalEditarUsuario').modal('hide');
            
        }
        else {
            $('#loadingModal').modal('hide');
            Mensagem('Erro', 'error');
            $('#modalEditarUsuario').modal('hide');
        }

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error); // Exibir erro se ocorrer
        $('#loadingModal').modal('hide');
    }
}





function criarTabelaUsuarios(listaUsuarios) {
    $('#Paginacao .dataTables_paginate').remove();
    // Destruir a tabela existente, se houver
    if ($.fn.DataTable.isDataTable('#TableUsuarios')) {
        $('#TableUsuarios').DataTable().destroy();
    }

    // Criar a tabela
    const tabela = $('#TableUsuarios').DataTable({
        responsive: true,
        paging: true,
        info: false,
        searching: true,
        colReorder: true,
        data: listaUsuarios, // Corrigido para listaUsuarios
        lengthChange: false,
        pageLength: 10,
        fixedHeader: true,
        columns: [
            { data: 'codigo' },
            { data: 'nome' },
            { data: 'funcao' },
            { data: 'situacao' },
            {
                data: null,
                render: function (data, type, row) {
                    return `
                <div class="acoes">
                    <i class="fa-solid fa-user-pen" style="font-size: 25px; color:""darkyellow" title="Editar Usuário"
                    onclick="AbrirModalEdit('${row.codigo}', '${row.nome}', '${row.funcao}', '${row.situacao}')"
                    ></i>
                </div>

            `;
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

    $('#searchUsuarios').on('keyup', function() {
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
