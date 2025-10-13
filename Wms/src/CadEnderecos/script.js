let Imprimir = false;
let Metodo = '';
let Acao = '';
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

$(document).ready(() => {
    $('#NomeRotina').text('Cadastro de Endereços');
})

$('#ButtonReservaOp').click(() => {
    $('.selecao-enderecos3').removeClass('d-none');
    $('.div-botoes3').removeClass('d-none');
    $('.selecao-enderecos2').addClass('d-none');
    $('.div-botoes2').addClass('d-none');

});

$('#BotaoCancelarReserva').click(() => {
    $('.selecao-enderecos3').addClass('d-none');
    $('.div-botoes3').addClass('d-none');
    finalizarCadastro('selecao-enderecos3');
})

$('#BotaoCancelarEnderecos').click(() => {
    $('.selecao-enderecos2').addClass('d-none');
    $('.div-botoes2').addClass('d-none');
    finalizarCadastro('selecao-enderecos2');
})

$('#FormCadEnderecos3').submit(async function(event) {
    event.preventDefault();
    $('#loadingModal').modal('show');
    const Dados = {
        "ruaInicial": $('#inputRuaInicialReserva').val(),
        "ruaFinal": $('#inputRuaFinalReserva').val(),
        "modulo": $('#InputModuloInicialReserva').val(),
        "moduloFinal": $('#InputModuloFinalReserva').val(),
        "posicao": $('#InputPosicaoInicialReserva').val(),
        "posicaoFinal": $('#InputPosicaoFinalReserva').val(),
        "tipo": '',
        "natureza": '',
        "empresa": empresa,
        "imprimir": false,
        "enderecoReservado": 'sim',
    };
    console.log(Dados)
    $.ajax({
        type: 'PUT',
        url: 'requests.php',
        contentType: 'application/json',
        data: JSON.stringify({
            acao: 'Cadastrar_Endereco',
            dados: Dados
        }),
        success: function(response) {
            console.log(response);
            if (response.resposta.message.includes('criado')) {
                $('#loadingModal').modal('hide');
                Mensagem('Endereço Reservado', 'success');
                finalizarCadastro('selecao-enderecos3');
                $('.selecao-enderecos3').addClass('d-none');
                $('.div-botoes3').addClass('d-none');
                
            } else {
                finalizarCadastro('selecao-enderecos3');
                $('#loadingModal').modal('hide');
                Mensagem('Erro!', 'error')
                $('#selecao-enderecos3').addClass('d-none');
                $('.div-botoes3').addClass('d-none');
                
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro na solicitação:', status, error);
            console.error('Resposta completa:', xhr.responseText);
            $('#loadingModal').modal('show');
        }
    });

})

$('#FormCadEnderecos').submit(async function(event) {
    event.preventDefault(); // Isso evita o envio padrão do formulário
    if ($('#radioIncluir').is(':checked')) {
        $('.selecao-enderecos2').removeClass('d-none');
        $('.div-botoes2').removeClass('d-none');
        $('.selecao-enderecos3').addClass('d-none');
        $('.div-botoes3').addClass('d-none');
        $('#BotaoPersistir').text('CADASTRAR');
        $('#SelectNatureza').prop('disabled', true);
        $('#OpcoesEstoque').prop('disabled', true);
        $('#radioIncluir').prop('disabled', true);
        $('#radioExcluir').prop('disabled', true);
        Metodo = 'PUT';
        Acao = 'Cadastrar_Endereco';

    } else if ($('#radioExcluir').is(':checked')) {
        $('.selecao-enderecos2').removeClass('d-none');
        $('.div-botoes2').removeClass('d-none');
        $('.selecao-enderecos3').addClass('d-none');
        $('.div-botoes3').addClass('d-none');
        $('#BotaoPersistir').text('EXCLUIR');
        $('#SelectNatureza').prop('disabled', true);
        $('#OpcoesEstoque').prop('disabled', true);
        $('#radioIncluir').prop('disabled', true);
        $('#radioExcluir').prop('disabled', true);
        Metodo = 'DELETE';
        Acao = 'Deletar_Endereco';
        Imprimir = false;
        $
    } else {
        Mensagem('Incluir ou Excluir não selecionado', 'warning');
    }
});

$('#FormCadEnderecos2').submit(async function(event) {
    event.preventDefault(); // Isso evita o envio padrão do formulário para ambos os métodos
    
    if (Metodo === 'DELETE') {
        Cadastrar_Excluir_Endereco(Metodo, Acao);
    } else if (Metodo === 'PUT') {
        Swal.fire({
            title: "Deseja Imprimir os Endereços?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Imprimir"
        }).then((result) => {
            if (result.isConfirmed) {
                Imprimir = true;
                Cadastrar_Excluir_Endereco(Metodo, Acao, 'Imprimindo Endereços');
            } else {
                Imprimir = false;
                Cadastrar_Excluir_Endereco(Metodo, Acao, 'Endereço Cadastrado');
            }
        });
    }
});

function Cadastrar_Excluir_Endereco(Metodo, acao, mensagem) {
    $('#loadingModal').modal('show');

    const Dados = {
        "ruaInicial": $('#inputRuaInicial').val(),
        "ruaFinal": $('#inputRuaFinal').val(),
        "modulo": $('#InputModuloInicial').val(),
        "moduloFinal": $('#InputModuloFinal').val(),
        "posicao": $('#InputPosicaoInicial').val(),
        "posicaoFinal": $('#InputPosicaoFinal').val(),
        "tipo": $('#OpcoesEstoque').val(),
        "natureza": $('#SelectNatureza').val(),
        "empresa": empresa,
        "imprimir": Imprimir,
        "enderecoReservado": '',
    };

    $.ajax({
        type: `${Metodo}`,
        url: 'requests.php',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({
            acao: `${acao}`,
            dados: Dados
        }),
        success: function(response) {
            console.log(response);
            $('#loadingModal').modal('hide');

            if (response.status === true) {
                if (response.resposta.message?.includes('criado')) {
                    Mensagem(mensagem, 'success');
                } else if (response.resposta.message?.includes('exceto')) {
                    Mensagem('Endereço Excluído', 'success');
                } else {
                    Mensagem('Operação concluída', 'success');
                }

                finalizarCadastro('selecao-enderecos2');

                // 👉 Abre o PDF se a URL vier na resposta
                if (response.pdf_url) {
                    window.open(response.pdf_url, '_blank');
                }

            } else {
                Mensagem('Erro: ' + (response.message || 'Erro inesperado'), 'error');
                finalizarCadastro('selecao-enderecos2');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro na solicitação:', status, error);
            console.error('Resposta completa:', xhr.responseText);
            $('#loadingModal').modal('hide');
            Mensagem('Erro na comunicação com o servidor', 'error');
        }
    });
}

function finalizarCadastro(div) {
    $('.selecao-enderecos2').addClass('d-none');
    $('.div-botoes').addClass('d-none');
    $('.div-botoes2').addClass('d-none')
    $('#BotaoPersistir').text('');
    $('#SelectNatureza').prop('disabled', false);
    $('#OpcoesEstoque').prop('disabled', false);
    $('#radioIncluir').prop('disabled', false);
    $('#radioExcluir').prop('disabled', false);
    $('#SelectNatureza').val('');
    $('#OpcoesEstoque').val('');
    $('#radioIncluir').val('');
    $('#radioExcluir').val('');
    $('#radioIncluir').prop('checked', false);
    $('#radioExcluir').prop('checked', false);
    $(`.${div} input`).each(function() {
        $(this).val('');
        $(this).prop('checked', false); // Para desmarcar checkboxes e radio buttons
    });
}