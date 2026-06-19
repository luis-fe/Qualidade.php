$(document).ready(async () => {
    await ConsultarServicoAutomacao();
});

const ConsultarServicoAutomacao = async () => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'ConsultarServicoAutomacao'
            },
        });

        // Passamos o response (os dados) para a função
        Tabela(response);

    } catch (error) {
        console.error('Erro ao consultar serviço:', error);
    } finally {
        // Pequeno delay para garantir que o modal feche após o processamento
        setTimeout(() => {
            $('#loadingModal').modal('hide');
        }, 500);
    }
};

function Tabela(dados) {
    // 1. Destruir a tabela se ela já existir
    if ($.fn.DataTable.isDataTable('#table-metas')) {
        $('#table-metas').DataTable().destroy();
    }

    // 2. Inicializar com os dados recebidos do AJAX
    $('#table-metas').DataTable({
        data: dados, 
        searching: true,
        paging: true, 
        lengthChange: false,
        info: false,
        pageLength: 10,
        columns: [
            { data: 'idServico' },
            { data: 'descricaoServico' },
            { data: 'data' },
            { data: 'hora' },
            // Adicionei a coluna que faltava. Confirme se o nome da chave no seu JSON é 'proximaAtualizacao'
            { data: 'proximaAtualizacao', defaultContent: '-' }, 
            { 
                // Coluna de Ações (Botão)
                data: null, 
                orderable: false, // Desabilita ordenação para a coluna de botões
                searchable: false, // Desabilita pesquisa pelo botão
                className: "text-center", // Centraliza o conteúdo da célula
                render: function(data, type, row) {
                    // O parâmetro 'row' contém o objeto JSON inteiro da linha atual.
                    // Pegamos o idServico dessa linha e injetamos no onclick do botão.
                    return `
                        <button class="btn btn-primary btn-sm" onclick="atualizarServico(${row.idServico})">
                            <i class="bi bi-arrow-repeat"></i> Atualizar
                        </button>
                    `;
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
            zeroRecords: "Nenhum registro encontrado",
            search: "Pesquisar:"
        }
    });
}

// Função que será chamada ao clicar no botão "Atualizar" de qualquer linha
function atualizarServico(idServico) {
    console.log("Iniciando sincronização para o serviço ID: " + idServico);
    
    // Aqui você pode disparar a requisição AJAX para forçar a atualização do serviço
    // Exemplo:
    /*
    $.ajax({
        type: 'POST',
        url: 'requests.php',
        data: { acao: 'AtualizarServico', id: idServico },
        success: function(res) {
            alert("Serviço atualizado!");
            ConsultarServicoAutomacao(); // Recarrega a tabela após atualizar
        }
    });
    */
}