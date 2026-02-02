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
        data: dados, // <--- Aqui é onde os dados entram na tabela
        searching: true,
        paging: true, // Alterado para true, já que você definiu pageLength
        lengthChange: false,
        info: false,
        pageLength: 10,
        columns: [
            { data: 'idServico' },
            { data: 'descricaoServico' },
            { data: 'data' },
            { data: 'hora' }
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