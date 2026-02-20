$(document).ready(async () => {
    await ConsultarEnderecos();

    // Fica escutando a troca dos botões radio para esconder/mostrar as seções
    $('input[name="tipoInsercao"]').on('change', function() {
        const valorSelecionado = $(this).val();
        
        if (valorSelecionado === 'individual') {
            $('#section-massa').addClass('d-none');
            $('#section-individual').removeClass('d-none');
        } else {
            $('#section-individual').addClass('d-none');
            $('#section-massa').removeClass('d-none');
        }
    });
});


// Função chamada ao clicar no botão do cabeçalho
function abrirModalInserirEndereco() {
    // Reseta o modal para o estado padrão (Individual) sempre que abrir
    $('#radioIndividual').prop('checked', true).trigger('change');
    
    // Limpa os campos para não trazer lixo da inserção anterior
    $('#modalInserirEndereco input[type="text"]').val('');
    
    // Mostra o modal
    $('#modalInserirEndereco').modal('show');
}

// Função chamada ao clicar no botão Salvar dentro do Modal
function salvarEnderecos() {
    const tipo = $('input[name="tipoInsercao"]:checked').val();
    let payload = { tipo: tipo };

    if (tipo === 'individual') {
        payload.rua = $('#indRua').val();
        payload.quadra = $('#indQuadra').val();
        payload.posicao = $('#indPosicao').val();
        
        if (!payload.rua || !payload.quadra || !payload.posicao) {
            alert("Preencha todos os campos do endereço individual.");
            return;
        }
    } else {
        payload.ruaIni = $('#masRuaIni').val();
        payload.ruaFim = $('#masRuaFim').val();
        payload.quadraIni = $('#masQuadraIni').val();
        payload.quadraFim = $('#masQuadraFim').val();
        payload.posicaoIni = $('#masPosicaoIni').val();
        payload.posicaoFim = $('#masPosicaoFim').val();
        
        // Validação básica se pelo menos um intervalo foi preenchido
        if (!payload.ruaIni || !payload.ruaFim) {
            alert("Preencha pelo menos a Rua Inicial e Final para a inserção em massa.");
            return;
        }
    }

    console.log("Dados prontos para envio:", payload);
    alert("Função AJAX de salvamento em desenvolvimento!");
    // Aqui entrará o seu $.ajax()
}

const ConsultarEnderecos = async () => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'ConsultarEnderecos'
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
            { data: 'endereco' },
            { data: 'rua' },
            { data: 'quadra' },
            { data: 'posicao' }
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