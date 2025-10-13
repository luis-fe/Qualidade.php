$(document).ready(() => {
    $('#NomeRotina').text('Qr Code');
})

document.getElementById('InputNumeroCaixas').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        if ($('#InputNumeroCaixas').val() == '') {
            Mensagem('Quantidade não Informada!', 'warning');
        } else {
            const dados = {
                'QuantidadeImprimir': parseInt($('#InputNumeroCaixas').val())
            };
            enviarDados(dados);
        }
    }
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

$('#ButtonImprimir').click(() => {
    if ($('#InputNumeroCaixas').val() == '') {
        Mensagem('Quantidade não Informada!', 'warning');
    } else {
        const dados = {
            'QuantidadeImprimir': parseInt($('#InputNumeroCaixas').val())
        };
        enviarDados(dados);
    }
});

async function enviarDados(dados) {
    $.ajax({
        type: 'PUT',
        url: 'requests.php',
        contentType: 'application/json',
        data: JSON.stringify({
            acao: 'Imprimir_QrCodes',
            dados: dados
        }),
        success: function(response) {
            console.log(response);
            if(response.resposta.message == ' ok!'){
                Mensagem('Imprimindo', 'success')
            } else {
                Mensagem('Erro!', 'error')
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro na solicitação:', status, error);
            console.error('Resposta completa:', xhr.responseText);
        }
    });
}