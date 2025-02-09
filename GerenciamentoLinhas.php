<?php
include_once("./utils/session.php");
include_once("./templates/headers.php");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/TelaInicial.css">
    <link rel="stylesheet" href="./css/GerenciamentoLinhas.css">
    <link rel="stylesheet" href="./css/GerenciamentoUsuarios.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

</head>
<body>
<div class="container">
        <div class="title">
            <h3>Gerenciamento de Linhas</h3>
            <i class="bi bi-plus-circle-fill" title="Adicionar Linha" id="addLinha" style="flex-shrink: 0; font-size: 30px; cursor: pointer; margin-right: 25px; color: rgb(17, 45, 126)"></i>
            <i class="bi bi-x-square-fill" title="Fechar" id="FecharRotina" style="color: black"></i>
        </div>
        <div class="corpo">
            <div class="Tabela">
                <table id="TabelaLinhas" class="display">
                    <thead>
                        <tr>
                            <th>Código Linha</th>
                            <th>Operador 1</th>
                            <th>Operador 2</th>
                            <th>Operador 3</th>
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

<div id="ModalNovoUsuario" class="ModalNovoUsuario">
        <div class="ModalNovoUsuario-content">
            <span id="FecharModalNovoUsuario" class="fechar">&times;</span>
            <h3>Cadastro Linha</h3>
            <form id="NovaLinhaForm" action="" method="post" onsubmit="event.preventDefault(); CadastrarLinha();">
                <label for="text" id="NovaLinha" class="NovaLinha">Nº Linha:</label>
                <input type="text" name="InputLinha" id="InputLinha" placeholder="Digite a Linha para Cadastro!" required>
    
                <label for="Operador1" class="LabelSituacaoNovoUsuario">Selecione o 1º Operador</label>
                <select id="Operador1" name="Operador1" required>
                    <option value="">Selecione o Operador 1</option>
                </select>
    
                <label for="Operador2" class="LabelSituacaoNovoUsuario">Selecione o 2º Operador</label>
                <select id="Operador2" name="Operador2" required>
                    <option value="">Selecione o Operador 2</option>
                </select>
    
                <label for="Operador3" class="LabelSituacaoNovoUsuario">Selecione o 3º Operador</label>
                <select id="Operador3" name="Operador3" required>
                    <option value="">Selecione o Operador 3</option>
                </select>
                <div class="ButtonSalvar">
                <button type="submit" id="salvarNovoUsuario">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="ModalEditarUsuario" class="ModalEditarUsuario">
        <div class="ModalEditarUsuario-content">
            <span id="FecharModalEditarUsuario" class="fechar">&times;</span>
            <h3>Editar Linha</h3>
            <form id="EditarUsuarioForm" action="" method="post" onsubmit="event.preventDefault(); EditarLinhas();">
                <label for="text" id="EditarLinha" class="EditarLinha">Nº Linha:</label>
                <input type="text" name="InputEditarLinha" id="InputEditarLinha" required>
    
                <label for="Operador1" class="LabelSituacaoNovoUsuario">Selecione o 1º Operador</label>
                <select id="EditarOperador1" name="EditarOperador1" required>
                    <option value="">Selecione o Operador 1</option>
                </select>
    
                <label for="Operador2" class="LabelSituacaoNovoUsuario">Selecione o 2º Operador</label>
                <select id="EditarOperador2" name="EditarOperador2" required>
                    <option value="">Selecione o Operador 2</option>
                </select>
    
                <label for="Operador3" class="LabelSituacaoNovoUsuario">Selecione o 3º Operador</label>
                <select id="EditarOperador3" name="EditarOperador3" required>
                    <option value="">Selecione o Operador 3</option>
                </select>
                <div class="ButtonSalvar">
                <button type="submit" id="salvarEdicaoUsuario">Salvar</button>
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

const modalSuccess = document.getElementById('modalSuccess');
const modalError = document.getElementById('modalError');

    const btnFecharRotina = document.getElementById("FecharRotina");
    function fecharRotina() {
        window.location.replace("TelaInicial.php");
    };

    btnFecharRotina.addEventListener('click', () => {
        fecharRotina();
    });

    const GetUsuarios = "http://10.162.0.190:5000/api/UsuariosPortal";
const ApiGetLinhas = "http://10.162.0.190:5000/api/linhasPadrao";
const CadLinhas = "http://10.162.0.190:5000/api/NovaLinha";
const ApiEditUsuarios = "http://10.162.0.190:5000/api/AtualizarLinha";
const addLinha = document.getElementById('addLinha');
const ModalNewLinha = document.getElementById('ModalNovoUsuario');
const ModalEditarUsuario = document.getElementById('ModalEditarUsuario');
const Token = "a40016aabcx9";
let selectedOperador1 = '';
let selectedOperador2 = '';
let selectedOperador3 = '';

//---------------------------------------------------OBTENDO AS LINHAS E INSERINDO NA TABELA-------------------------------------------------//

async function ObterLinhas() {
    try {
        const response = await fetch(ApiGetLinhas, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': Token
            },
        });

        if (response.ok) {
            const data = await response.json();
            console.log(data);
            criarTabelaLinhas(data);
        } else {
            throw new Error('Erro no retorno');
        }
    } catch (error) {
        console.error(error.message);
    }
}

function criarTabelaLinhas(listaLinhas) {
    // Remova os elementos paginadores antigos
    $('#PaginacaoUsuarios .dataTables_paginate').remove();

    // Destruir a tabela existente, se houver
    if ($.fn.DataTable.isDataTable('#TabelaLinhas')) {
        $('#TabelaLinhas').DataTable().destroy();
    }

    // Crie a tabela
    tabela = $('#TabelaLinhas').DataTable({
        paging: true,
        info: false,
        searching: true,
        colReorder: true,
        colResize: true,
        columns: [
            { data: 'Linha' },
            { data: 'operador1' },
            { data: 'operador2' },
            { data: 'operador3' },
            {
                data: null,
                render: function (data, type, row) {
                    return `
                        <div class="acoes">
                            <i class="bi bi-pencil editar acao" title="Editar" onclick="abrirModalEdit('${row.Linha}', '${row.operador1}', '${row.operador2}', '${row.operador3}')"></i>
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
            lengthMenu: 'Mostrar _MENU_ itens por página',
        }
    });

    tabela.clear().rows.add(listaLinhas).draw();

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
};

//--------------------------------------------FUNÇÕES PARA RECUPERAR OS USUÁRIOS E INSERIR NAS SELECT'S------------------------------------//

async function ApiGetUsuarios() {
    const SelectOperador1 = document.getElementById('Operador1');
    const SelectOperador2 = document.getElementById('Operador2');
    const SelectOperador3 = document.getElementById('Operador3');
    const EditarOperador1 = document.getElementById('EditarOperador1');
    const EditarOperador2 = document.getElementById('EditarOperador2');
    const EditarOperador3 = document.getElementById('EditarOperador3');

    try {
        const response = await fetch(GetUsuarios, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': Token
            },
        });

        if (response.ok) {
            const data = await response.json();
            const usuarios = data;

            // Adiciona as opções com base nos usuários da API
            usuarios.forEach(usuario => {
                const option1 = document.createElement('option');
                const option2 = document.createElement('option');
                const option3 = document.createElement('option');
                const option4 = document.createElement('option');
                const option5 = document.createElement('option');
                const option6 = document.createElement('option');

                option1.value = usuario.nome;
                option2.value = usuario.nome;
                option3.value = usuario.nome;
                option4.value = usuario.nome;
                option5.value = usuario.nome;
                option6.value = usuario.nome;

                option1.textContent = usuario.nome;
                option2.textContent = usuario.nome;
                option3.textContent = usuario.nome;
                option4.textContent = usuario.nome;
                option5.textContent = usuario.nome;
                option6.textContent = usuario.nome;

                SelectOperador1.appendChild(option1);
                SelectOperador2.appendChild(option2);
                SelectOperador3.appendChild(option3);
                EditarOperador1.appendChild(option4);
                EditarOperador2.appendChild(option5);
                EditarOperador3.appendChild(option6);
            });

            [SelectOperador1, SelectOperador2, SelectOperador3].forEach(select => {
select.addEventListener('change', (event) => {
    const selectedText = event.target.options[event.target.selectedIndex].text;
    const previouslySelectedText = select.getAttribute('data-selected-text');

    [SelectOperador1, SelectOperador2, SelectOperador3].forEach(otherSelect => {
        if (previouslySelectedText && otherSelect !== select) {
            Array.from(otherSelect.options).forEach(option => {
                if (option.text === previouslySelectedText) {
                    option.disabled = false;
                }
            });
        }
    });

    [SelectOperador1, SelectOperador2, SelectOperador3].forEach(otherSelect => {
        if (otherSelect !== select) {
            Array.from(otherSelect.options).forEach(option => {
                if (option.text === selectedText) {
                    option.disabled = true;
                }
            });
        }
    });

    // Update the data-selected-text attribute with the currently selected text
    select.setAttribute('data-selected-text', selectedText);
});
});

        } else {
            throw new Error('Erro no retorno da API');
        }
    } catch (error) {
        console.error(error);
    }
}


async function bloquearOpcoesIniciais(selects, Operador1, Operador2, Operador3) {
    selects.forEach(select => {
        Array.from(select.options).forEach(option => {
            if (option.value === Operador1 || option.value === Operador2 || option.value === Operador3) {
                option.disabled = true;
            }
        });
    });
}

function bloquearDesbloquearOpcoes(select, opcoesSelecionadas, desbloquear) {
    Array.from(select.options).forEach(option => {
        if (opcoesSelecionadas.includes(option.value)) {
            option.disabled = !desbloquear;
        }
    });
}

function bloquearDesbloquearOpcoes(select, opcoesSelecionadas) {
    Array.from(select.options).forEach(option => {
        option.disabled = opcoesSelecionadas.includes(option.value);
    });
}

function preencherOpcoesBloqueadas() {
    const tabela = document.getElementById('TabelaLinhas');
    const linhas = tabela.getElementsByTagName('tr');
    const EditarOperador1 = document.getElementById('EditarOperador1');
    const EditarOperador2 = document.getElementById('EditarOperador2');
    const EditarOperador3 = document.getElementById('EditarOperador3');

    // Limpar opções bloqueadas
    [EditarOperador1, EditarOperador2, EditarOperador3].forEach(select => {
        Array.from(select.options).forEach(option => {
            option.disabled = false;
        });
    });

    // Preencher as opções bloqueadas com base na tabela
    for (let i = 1; i < linhas.length; i++) {
        const linha = linhas[i];


            const colunas = linha.getElementsByTagName('td');
            const Operador_1 = colunas[2].textContent.trim();
            const Operador_2 = colunas[3].textContent.trim();
            const Operador_3 = colunas[4].textContent.trim();

            // Bloquear as opções em todas as selects correspondentes à linha selecionada
            bloquearDesbloquearOpcoes(EditarOperador1, [Operador_2, Operador_3]);
            bloquearDesbloquearOpcoes(EditarOperador2, [Operador_1, Operador_3]);
            bloquearDesbloquearOpcoes(EditarOperador3, [Operador_1, Operador_2]);
        }
}

function fecharModal(element) {
    element.style.display = 'none';
}


function openModal(Modal) {
    Modal.style.display = 'flex';
    setTimeout(() => {
        fecharModal(Modal);
    }, 3000);
}

//-----------------------------------------------------FUNÇÃO PARA CADASTRAR NOVA LINHA--------------------------------------------------------//

async function CadastrarLinha() {
    const InputLinha = document.getElementById('InputLinha');
    const Operador1 = document.getElementById('Operador1');
    const Operador2 = document.getElementById('Operador2');
    const Operador3 = document.getElementById('Operador3');

    const Salvar = {
        "linha": InputLinha.value,
        "operador1": Operador1.value,
        "operador2": Operador2.value,
        "operador3": Operador3.value
    };

    try {
        const response = await fetch(CadLinhas, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': Token
            },
            body: JSON.stringify(Salvar),
        });

        if (response.ok) {
            const data = await response.json();
            openModal(modalSuccess);
            ModalNewLinha.style.display = 'none';
            await ObterLinhas();
        } else {
            throw new Error('Erro No Retorno');
            openModal(modalError);
            ModalNewLinha.style.display = 'none';
            await ObterLinhas();
        }
    } catch (error) {
        console.error(error);
        openModal(modalError);
        ModalNewLinha.style.display = 'none';
        await ObterLinhas();
    }
};

//------------------------------------------------------FUNÇÃO PARA EDITAR AS LINHAS----------------------------------------------------------//

async function EditarLinhas() {
    const InputEditar = document.getElementById('InputEditarLinha');
    const EditarOperador1 = document.getElementById('EditarOperador1');
    const EditarOperador2 = document.getElementById('EditarOperador2');
    const EditarOperador3 = document.getElementById('EditarOperador3');

    dados = {
        "linha": InputEditar.value,
        "operador1": EditarOperador1.value,
        "operador2": EditarOperador2.value,
        "operador3": EditarOperador3.value,
    }

    try {
        const response = await fetch(ApiEditUsuarios, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': Token
            },
            body: JSON.stringify(dados),
        });

        if (response.ok) {
            const data = await response.json();
            openModal(modalSuccess);
            ModalEditarUsuario.style.display =  'none';
            EditarOperador1.innerHTML;
            EditarOperador2.innerHTML;
            EditarOperador3.innerHTML;
            await ObterLinhas();  
            
        } else {
            throw new Error('Erro ao obter os dados da API');
            openModal(modalError);
            ModalEditarUsuario.style.display =  'none';
            await ObterLinhas();  
        
        }
    } catch (error) {
        console.error(error);
        openModal(modalError);
        ModalEditarUsuario.style.display =  'none';
        await ObterLinhas();  
    }
}

async function abrirModalEdit(Linha, Operador1, Operador2, Operador3) {
    const InputEditarLinha = document.getElementById('InputEditarLinha');
    const EditarOperador1 = document.getElementById('EditarOperador1');
    const EditarOperador2 = document.getElementById('EditarOperador2');
    const EditarOperador3 = document.getElementById('EditarOperador3');

    // Preencher os campos do modal com os dados da linha
    InputEditarLinha.value = Linha;
    EditarOperador1.value = Operador1;
    EditarOperador2.value = Operador2;
    EditarOperador3.value = Operador3;

    // Exibir o modal de edição
    document.getElementById('ModalEditarUsuario').style.display = 'block';

    // Armazenar as opções selecionadas inicialmente
    selectedOperador1 = EditarOperador1.options[EditarOperador1.selectedIndex].text;
    selectedOperador2 = EditarOperador2.options[EditarOperador2.selectedIndex].text;
    selectedOperador3 = EditarOperador3.options[EditarOperador3.selectedIndex].text;

    [EditarOperador1, EditarOperador2, EditarOperador3].forEach(select => {
        // Armazenar o texto selecionado inicialmente para cada select
        select.setAttribute('data-selected-text', select.options[select.selectedIndex].text);
    });

    // Bloquear as opções iniciais
    bloquearOpcoesIniciais([EditarOperador1, EditarOperador2, EditarOperador3], Operador1, Operador2, Operador3);
}


[EditarOperador1, EditarOperador2, EditarOperador3].forEach(select => {
    select.addEventListener('change', (event) => {
        const selectedText = event.target.options[event.target.selectedIndex].text;
        const previouslySelectedText = select.getAttribute('data-selected-text');

        // Habilitar a opção anterior e desabilitar a nova opção nas outras selects
        [EditarOperador1, EditarOperador2, EditarOperador3].forEach(otherSelect => {
            if (previouslySelectedText && otherSelect !== select) {
                Array.from(otherSelect.options).forEach(option => {
                    if (option.text === previouslySelectedText) {
                        option.disabled = false;
                    }
                });
            }
        });

        [EditarOperador1, EditarOperador2, EditarOperador3].forEach(otherSelect => {
            if (otherSelect !== select) {
                Array.from(otherSelect.options).forEach(option => {
                    if (option.text === selectedText) {
                        option.disabled = true;
                    }
                });
            }
        });

        // Atualizar o atributo data-selected-text com o texto selecionado atual
        select.setAttribute('data-selected-text', selectedText);
    });
});


addLinha.addEventListener('click', () => {
    ModalNewLinha.style.display = "flex";
    console.log('botão clicado')
});

document.addEventListener('DOMContentLoaded', async () => {
    await ApiGetUsuarios();
    ObterLinhas(); 
});


</script>
</html>
