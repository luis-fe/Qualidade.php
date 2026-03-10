$(document).ready(function () {
    // Carrega os dados ao abrir a página
    buscarDados();

    // Evento de clique no botão filtrar
    $('#btnFiltrar').on('click', function() {
        buscarDados();
    });
});

function buscarDados() {
    // 1. Garantir que estamos pegando os valores dos IDs corretos
    const dInicio = $('#dataInicio').val();
    const dFim = $('#dataFim').val();

    $.ajax({
        url: 'requests.php',
        type: 'GET',
        data: {
            // AJUSTE: O case deve ser idêntico ao 'case' do switch no PHP
            acao: 'Produtividade_Aviamentos', 
            dataInicio: dInicio,
            dataFim: dFim
        },
        dataType: 'json',
        beforeSend: function() {
            $('#table-metas tbody').html('<tr><td colspan="3" class="text-center"><div class="spinner-border spinner-border-sm" role="status"></div> Processando...</td></tr>');
        },
        success: function (data) {
            let linhas = '';

            // 2. Validar se o retorno é um array e se tem itens
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(item => {
                    // 3. Uso de colchetes para chaves com pontos ou espaços
                    linhas += `
                        <tr>
                            <td>${item.usuario || 'N/A'}</td>
                            <td>${item["qtd.Kit Reposto"] ?? 0}</td>
                            <td>${item["qtd Enderecos"] ?? 0}</td>
                        </tr>`;
                });
            } else {
                // Caso a API retorne um erro dentro do JSON ou array vazio
                const msg = data.message ? data.message : 'Nenhum registro para este período.';
                linhas = `<tr><td colspan="3" class="text-center">${msg}</td></tr>`;
            }

            $('#table-metas tbody').html(linhas);
        },
        error: function (xhr, status, error) {
            console.error("Erro detalhado:", xhr.responseText);
            $('#table-metas tbody').html('<tr><td colspan="3" class="text-center text-danger">Erro ao consultar API: ' + error + '</td></tr>');
        }
    });
}