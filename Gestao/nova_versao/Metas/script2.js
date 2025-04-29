$(document).ready(async () => {
    await Consulta_Planos();
    Consultar_Tipo_Op()
    $('#select-plano').select2({
        placeholder: "Selecione um plano",
        allowClear: false,
        width: '100%'
    });

    $('#select-lote').select2({
        placeholder: "Selecione um lote",
        allowClear: false,
        width: '100%'
    });

    $('#select-plano').on('change', function () {
        Consulta_Lotes();
    });

    $('#select-lote').on('change', function () {
        Consulta_Metas(false);
    });

    const hoje = new Date().toISOString().split('T')[0];
    document.getElementById('data-inicial').value = hoje;
    document.getElementById('data-final').value = hoje;
});

const Consulta_Planos = async () => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Planos',
                plano: $('#select-plano').val()
            },
        });
        $('#select-plano').empty();
        $('#select-plano').append('<option value="" disabled selected>Selecione um plano...</option>');
        response.forEach(function (plano) {
            $('#select-plano').append(`
                    <option value="${plano['01- Codigo Plano']}">
                        ${plano['01- Codigo Plano']} - ${plano['02- Descricao do Plano']}
                    </option>
                `);
        });
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consultar_Tipo_Op = async () => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Tipo_Op',
            },
        });
        console.log(response)
        const divTiposOps = $('#TiposOps');
        divTiposOps.empty();
        response.forEach(opcao => {
            const checkbox = $('<div class="form-check">')
                .append(
                    $('<input class="form-check-input" type="checkbox">')
                        .attr('value', opcao['Tipo Producao'])
                        .attr('id', `checkbox${opcao['Tipo Producao']}`)
                )
                .append(
                    $('<label class="form-check-label">')
                        .attr('for', `checkbox${opcao['Tipo Producao']}`)
                        .text(opcao['Tipo Producao'])
                );
            divTiposOps.append(checkbox);
        });
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Previsao_Categoria = async (Fase) => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Previsao_Categoria',
                fase: Fase
            },
        });
        TabelaPrevisaoCategorias(response);
        $('#modal-previsao-categorias').modal('show')
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};




const Consulta_Lotes = async () => {
    try {
        $('#loadingModal').modal('show');

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Lotes',
                plano: $('#select-plano').val()
            },
        });
        console.log(response)
        $('#select-lote').empty();
        $('#select-lote').append('<option value="" disabled selected>Selecione um lote...</option>');
        response.forEach(function (lote) {
            $('#select-lote').append(`
                        <option value="${lote['lote']}">
                            ${lote['lote']} - ${lote['nomelote']}
                        </option>
                    `);
        });
        $('#div-selecionar-lote').removeClass('d-none');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

let TiposOpsSelecionados = [];
async function Consulta_Metas(congelado) {
    TiposOpsSelecionados = [];
    $('input[type=checkbox][id^="checkbox"]').each(function () {
        // Verificar se o checkbox está marcado
        if ($(this).is(':checked')) {
            // Obter o valor do checkbox
            var tiposOps = $(this).val();

            // Verificar se a coleção já existe no array
            if (!TiposOpsSelecionados.includes(tiposOps)) {
                // Adicionar o valor ao array de coleções selecionadas
                TiposOpsSelecionados.push(tiposOps);
            }
        }
    });
    console.log(TiposOpsSelecionados)
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Consulta_Metas",
            dados: {
                codigoPlano: $('#select-plano').val(),
                arrayCodLoteCsw: [$('#select-lote').val()],
                dataMovFaseIni: $('#data-inicial').val(),
                dataMovFaseFim: $('#data-final').val(),
                congelado: congelado,
                ArrayTipoProducao: TiposOpsSelecionados
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        TabelaMetas(response[0]['1-Detalhamento']);
        $('.div-metas').removeClass('d-none')
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
};

async function Consulta_Falta_Produzir_Categoria(Fase, Plano) {

    $('#loadingModal').modal('show');

    try {
         const requestData = {
             acao: "ConsultaFaltaProduzirCategoria_Fase",
             dados: {
                 codigoPlano: Plano,
                 arrayCodLoteCsw: [$('#select-lote').val()],
                 nomeFase: Fase,
                 ArrayTipoProducao: TiposOpsSelecionados.length > 0 ? TiposOpsSelecionados : []
             }
         };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            dataType: 'json',
            data: JSON.stringify(requestData)
        });

        TabelaFaltaProduzirCategorias(response);
        console.log(response)
        $('modal-falta-produzir-categorias').modal('show');

    } catch (error) {
        console.error('Erro no detalha falta Produzir:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}
