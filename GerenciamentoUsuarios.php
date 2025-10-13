<?php
include_once("./utils/session.php");
include_once("./templates/headers.php");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/TelaInicial.css">
    <link rel="stylesheet" href="./css/GerenciamentoUsuarios.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

</head>
<body>
    <div class="container">
        <div class="title">
            <h3>Gerenciamento de Usuários</h3>
            <i class="bi bi-person-fill-add" title="Adicionar Usuário" id="addUser" style="flex-shrink: 0; font-size: 30px; cursor: pointer; margin-right: 25px; color: rgb(17, 45, 126)"></i>
            <i class="bi bi-x-square-fill" title="Fechar" id="FecharRotina" style="color: black"></i>
        </div>
        <div class="corpo">
            <div class="Tabela">
                <table id="TabelaUsuarios" class="display">
                    <thead>
                        <tr>
                            <th>Código Usuário</th>
                            <th>Nome Usuário</th>
                            <th>Situação</th>
                            <th>Função</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                </table>
                <div class="botoes" style="display: flex; margin: 20px auto; width: 80%; justify-content: space-around;">
            <div id="PaginacaoUsuarios" class="dataTables_paginate" style="width: 100%;"></div>
        </div>
            </div>
           
        </div>
    </div>

    <div class="modal" id="editModal">
    <div class="modal-content">
    <span class="close-modaledit" onclick="fecharModal(editModal)">&times;</span>
        <h2>Editar Usuário</h2>
        <form id="editForm" >
        <div class="Inputs">
            <input type="text" id="matriculaUsuario" name="matriculaUsuario" placeholder="Matrícula do Usuário" required>
            </div>
            <div class="Inputs">
            <input type="text" id="nomeUsuario" name="nomeUsuario" placeholder="Nome do Usuário" required>
            </div>
            <div class="Inputs">
            <select id="Funcao">
    <option disabled selected value="">Função</option>
    <option value="ADMINISTRADOR">ADMINISTRADOR</option>
    <option value="OPERADOR">OPERADOR</option>
</select>
            </div>
            <div class="Inputs">
            <select id="Situacao">
            <option disabled selected value="">Situação</option>
                <option value="ATIVO">ATIVO</option>
                <option value="INATIVO">INATIVO</option>
            </select>
            </div>
            <div class="buttonedit">
            <button type="submit" id="SalvarEditUser">Salvar</button>
            </div>
            
        </form>
    </div>
</div>


<div class="modal" id="newModal">
    <div class="Newmodal-content">
        <span class="Newclose-modaledit" onclick="fecharModal(newModal)">&times;</span>
        <h2>Novo Usuário</h2>
        <form id="newForm">
        <div class="Inputs">
            <input type="text" id="NewmatriculaUsuario" name="NewmatriculaUsuario" placeholder="Matrícula do Usuário" required>
            </div>
            <div class="Inputs">
            <input type="text" id="NewnomeUsuario" name="NewnomeUsuario" placeholder="Nome do Usuário" required>
            </div>
            <div class="Inputs">
            <select id="NewFuncao">
    <option disabled selected value="">Função</option>
    <option value="ADMINISTRADOR">ADMINISTRADOR</option>
    <option value="OPERADOR">OPERADOR</option>
</select>
            </div>
            <div class="Inputs">
            <select id="NewSituacao">
            <option disabled selected value="">Situação</option>
                <option value="ATIVO">ATIVO</option>
                <option value="INATIVO">INATIVO</option>
            </select>
            </div>
            <div class="Inputs">
            <input type="password" id="NewPassword" name="NewPassword" placeholder="Senha" required>
            </div>
            <div class="Newbuttonedit">
            <button type="submit" id="SalvarNewUser">Salvar</button>
            </div>
            
        </form>
    </div>
</div>
<div id="modalSuccess" class="modal">
    <div class="modal-contentSucess">
        <p>Cadastrado com sucesso!</p>
        <i class="bi bi-check-circle-fill"></i>
    </div>
</div>
<div id="modalError" class="modal">
    <div class="modal-contentError">
        <p>Erro</p>
        <i class="bi bi-x-circle-fill"></i>
    </div>
</div>
    
</body>

<script>
    const btnFecharRotina = document.getElementById("FecharRotina");

    function fecharRotina() {
        window.location.replace("TelaInicial.php");
    };

    btnFecharRotina.addEventListener('click', () => {
        fecharRotina();
    });

    const ApiGetUsuarios = "http://10.162.0.190:5000/api/UsuariosPortal";
const ApiEditUsuario = "http://10.162.0.190:5000/api/UsuariosPortal";
const Token = "a40016aabcx9";
const ModalEdit = document.getElementById('editModal');
const newModal = document.getElementById('newModal');
const ButtonAddUser = document.getElementById('addUser');
const ButtonEditUser = document.getElementById('SalvarEditUser');
const modalSuccess = document.getElementById('modalSuccess');
const modalError = document.getElementById('modalError');



function fecharModal(element) {
    element.style.display = 'none';
}

function abrirModalEdit(matricula, nome, funcao, situacao) {
    const inputMatricula = document.getElementById('matriculaUsuario');
    const inputNome = document.getElementById('nomeUsuario');
    const inputFuncao = document.getElementById('Funcao');
    const inputSituacao = document.getElementById('Situacao');
    ModalEdit.style.display = 'block';
    inputMatricula.value = matricula;
    inputNome.value = nome;
    inputFuncao.value = funcao;
    inputSituacao.value = situacao;

}

function abrirModalNew() {
    newModal.style.display = 'block';
}

ButtonAddUser.addEventListener('click', abrirModalNew);



async function obterUsuarios() {
    try {
        const response = await fetch(ApiGetUsuarios, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': Token
            },
        });

        if (response.ok) {
            const data = await response.json();
            console.log(data);
            criarTabelaUsuarios(data);
        } else {
            throw new Error('Erro no retorno');
        }
    } catch (error) {
        console.error(error.message);
    }
}

function criarTabelaUsuarios(listaUsuarios) {
    // Remova os elementos paginadores antigos
    $('#PaginacaoUsuarios .dataTables_paginate').remove();

    // Destruir a tabela existente, se houver
    if ($.fn.DataTable.isDataTable('#TabelaUsuarios')) {
        $('#TabelaUsuarios').DataTable().destroy();
    }

    // Crie a tabela
    tabela = $('#TabelaUsuarios').DataTable({
        paging: true,
        info: false,
        searching: true,
        colReorder: true,
        colResize: true,
        columns: [
            { data: 'codigo' },
            { data: 'nome' },

            { data: 'situacao' },
            { data: 'funcao' },
            {
                data: null,
                render: function (data, type, row) {
                    return `
                        <div class="acoes">
                            <i class="bi bi-pencil editar acao" title="Editar" onclick="abrirModalEdit('${row.codigo}', '${row.nome}', '${row.funcao}', '${row.situacao}')"></i>
                            <i class="bi bi-trash excluir acao" title="Excluir"></i>
                        </div>
                    `;
                }
            }
        ],
        language: {
            paginate: {
                first: 'Primeira',
                previous: 'Anterior',
                next: 'Próxima',
                last: 'Última',
            },
            lengthMenu: 'Mostrar_MENU_Itens Por Página',
        }
    });

    tabela.clear().rows.add(listaUsuarios).draw();

    // Adicionar a div .dataTables_paginate ao final da tabela
    $('.dataTables_paginate').appendTo('#PaginacaoUsuarios');

    // Adicionar eventos de clique aos botões padrão do DataTable
    $('#PaginacaoUsuarios .paginate_button.previous').on('click', function () {
        tabela.page('previous').draw('page');
    });

    $('#PaginacaoUsuarios .paginate_button.next').on('click', function () {
        tabela.page('next').draw('page');
    });

    // Forçar a página inicial
    const paginaInicial = 1;
    tabela.page(paginaInicial - 1).draw('page');
}

function openModal(Modal) {
    Modal.style.display = 'flex';
    setTimeout(() => {
        fecharModal(Modal);
    }, 3000);
}

async function cadastroUser() {
    const matriculaUsuario = document.getElementById('NewmatriculaUsuario');
    const nomeUsuario = document.getElementById('NewnomeUsuario');
    const funcao = document.getElementById('NewFuncao');
    const situacao = document.getElementById('NewSituacao');
    const password = document.getElementById('NewPassword');
    const dados = {
        "codigo": matriculaUsuario.value,
        "nome": nomeUsuario.value,
        "funcao": funcao.value,
        "situacao": situacao.value,
        "senha": password.value,
    };

    try {
        const response = await fetch(ApiGetUsuarios, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': Token
            },
            body: JSON.stringify(dados),
        });

        if (response.ok) {
            await obterUsuarios();
            openModal(modalSuccess);
            matriculaUsuario.value = "";
            nomeUsuario.value = "";
            funcao.value = "";
            situacao.value = "";
            password.value = "";
            fecharModal(newModal);
        } else {
            throw new Error('Erro no retorno');
        }
    } catch (error) {
        console.error(error.message);
        openModal(modalError);
        matriculaUsuario.value = "";
            nomeUsuario.value = "";
            funcao.value = "";
            situacao.value = "";
            password.value = "";
        obterUsuarios();
        fecharModal(newModal);
    }
}

async function EditarUsuarios() {
    const InputEditar = document.getElementById('matriculaUsuario');
    const InputUsuario = document.getElementById('nomeUsuario');
    const InputFuncao = document.getElementById('Funcao');
    const InputSituacao = document.getElementById('Situacao');
    console.log(InputEditar.value)

    dados = {
        "codigo": InputEditar.value,
        "nome": InputUsuario.value,
        "funcao": InputFuncao.value,
        "situacao": InputSituacao.value,
    }
  
    try {
        const response = await fetch(`${ApiGetUsuarios}/${InputEditar.value}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': Token
            },
            body: JSON.stringify(dados),
        });

        if (response.ok) {
            const data = await response.json();
            ModalEdit.style.display =  'none';
            openModal(modalSuccess);
            await obterUsuarios();  
            
        } else {
            throw new Error('Erro ao obter os dados da API');
            ModalEdit.style.display =  'none';
            openModal(modalError);
            await obterUsuarios();  
        
        }
    } catch (error) {
        console.error(error);
        ModalEdit.style.display =  'none';
        openModal(modalError);
        await obterUsuarios();  
    
    }
};

document.getElementById('newForm').addEventListener('submit', function (event) {
    // Prevenir o envio padrão do formulário
    event.preventDefault();

    // Chamar a função cadastroUser
    cadastroUser();
});

document.getElementById('editForm').addEventListener('submit', function (event) {
    // Prevenir o envio padrão do formulário
    event.preventDefault();

    // Chamar a função cadastroUser
    EditarUsuarios();
});

// Chame a função para obter e exibir os dados após o carregamento da página
$(document).ready(function () {
    obterUsuarios();
});

</script>
</html>
