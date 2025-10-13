let Prioridade1 = '';
let Prioridade2 = '';
let Status = '';
let VarObs = '';
let VarOp = '';
let VarFase = '';

$(document).ready(async () => {
    $('#NomeRotina').text("Gestão de Op's");
    await ConsultaOps('', '', '');
    ConsultaColecoes();
});

const ConsultaColecoes = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: { acao: 'Consulta_Colecoes' }
        });
        console.log(response);
        const divColecoes = $('#colecoes');
        divColecoes.empty();
        response.forEach(opcao => {
            const checkbox = $('<div class="form-check">')
                .append(
                    $('<input class="form-check-input" type="checkbox">')
                        .attr('value', opcao.COLECAO)
                        .attr('id', `checkbox${opcao.COLECAO}`)
                )
                .append(
                    $('<label class="form-check-label">')
                        .attr('for', `checkbox${opcao.COLECAO}`)
                        .text(opcao.COLECAO)
                );
            divColecoes.append(checkbox);
        });
    } catch (error) {
        console.error('Erro ao consultar grades:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const ConsultaJustificativa = async (Op, Fase) => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: { acao: 'Consulta_Justificativas', op: Op, fase: Fase }
        });
        console.log(response);
        VarObs = response[0]['justificativa'];
    } catch (error) {
        console.error('Erro', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

async function ConsultaOps(filtro, classificacao, colecao) {
    $('#loadingModal').modal('show');
    try {
        const dados = {
            "empresa": 1,
            "filtro": filtro,
            "classificar": classificacao,
            "colecao": colecao
        };
        const requestData = { acao: "Consulta_Ops", dados };
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        console.log(response);
        $('#Corpo').empty();
        CriarCards(response[0]['3 -Detalhamento']);
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

function CriarCards(dados) {
    console.log(dados);
    let totalQuantidade = 0;

    // Resetando valores
    $('#text1').text('');
    $('#text2').text('');
    $('#text3').text('');
    $('#text4').text('');
    $('#text5').text('');

    dados.forEach(item => {
        function Cor_Fundo(status) {
            const statusClasses = {
                '0-Normal': 'bg-success',
                '2-Atrasado': 'bg-danger',
                '1-Atencao': 'bg-warning'
            };
            return statusClasses[status] || '';
        }

        const ClassCor = Cor_Fundo(item.status);
        const ClassPrioridade = item.prioridade;
        const ClassStatus = item.status;

        const divCard = $('<div class="card-ops">')
            .attr('data-op', item.numeroOP)
            .attr('data-fase', item.codFase)
            .attr('data-quantidade', item['Qtd Pcs']);

        totalQuantidade += parseInt(item['Qtd Pcs'], 10);

        const card = $('<div class="card" id="CardCorpo">');
        const cardBody = $('<div class="card-body-infos ' + ClassCor + ' ' + ClassPrioridade + ' ' + ClassStatus + '" id="Teste">');

        const cardTitle = $('<h5 class="card-title">')
            .html('<strong>Fase: ' + item.codFase + ' - ' + item.nomeFase + ' teste</strong>');

        const cardText = $('<p class="card-text" id="Maior">').html('<strong>Numero Op: ' + item.numeroOP + '/ Qtd: ' + item['Qtd Pcs'] + 'Pçs </strong>');
        const cardText2 = $('<p class="card-text" id="Maior">').html('<strong>Engenharia: ' + item.codProduto + '</strong>');
        const cardText3 = $('<p class="card-text" id="Menor">').html('<strong>' + item.descricao + '</strong>');
        const cardText4 = $('<p class="card-text" id="Maior">').html('<strong>Tipo Op: ' + item.codTipoOP + '</strong>');
        const cardText5 = $('<p class="card-text" id="Maior">').html('<strong>Responsável: ' + item.responsavel + '</strong>');
        const cardText6 = $('<p class="card-text" id="Maior">').html('<strong>Meta: ' + item.meta + '</strong>');
        const cardText7 = $('<p class="card-text" id="Maior">').html('<strong>Dias na Fase: ' + item['dias na Fase'] + '</strong>');
        const cardText8 = $('<p class="card-text" id="justificativa">').html('<strong>Justificativa: ' + item.justificativa + '</strong>');
        var spanElement = $('<span>').attr('style', item['Status Aguardando Partes'] === 'PENDENTE' || item['Status Aguardando Partes'] === 'OK' ? 'font-size: 50px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;' : '').html(
            (item['Status Aguardando Partes'] === 'PENDENTE' ? '⚠️' : '') +
            (item['Status Aguardando Partes'] === 'OK' ? '✅' : '')
        );
        // Adiciona o símbolo de urgência, se presente
        if (item.prioridade) {
            const urgenteSymbol = $('<div>')
                .text(item.prioridade)
                .css({
                    backgroundColor: '#FFFF00',
                    color: 'black',
                    border: '1px solid black',
                    fontSize: '19px',
                    marginLeft: '-25px',
                    marginBottom: '10px',
                    maxWidth: '250px',
                    textAlign: 'center',
                    fontWeight: '500'
                });
            cardBody.append(urgenteSymbol);
        }

        // Condicional para mostrar classes específicas de prioridade
        ['CLAUDINO', 'FAT ATRASADO', 'A VISTA ANTECIPADO'].forEach(className => {
            if (item.prioridade.includes(className)) {
                $(`.${className}`).removeClass('d-none');
            }
        });

        // Atualização de contagem de status
        updateStatusCount(item);

        cardBody.append(cardTitle, cardText, cardText2, cardText3, cardText4, cardText5, cardText6, cardText7, cardText8, spanElement);
        card.append(cardBody);
        divCard.append(card);
        $('#Corpo').append(divCard);

        // Hover e click para justificar
        setupCardHoverAndClick(divCard, item);
    });

    // Atualiza a exibição total da quantidade
    $('#text2').text(`${totalQuantidade.toLocaleString()} pçs`);
    $('#text1').text(`${$('#Corpo .card-body-infos').length.toLocaleString()} Op's`);
}

function updateStatusCount(item) {
    if (item.status.includes('2-Atrasado')) {
        const count = $('.card-body-infos.2-Atrasado').length;
        $('#text5').text(`${count} Op's`);
    }
    if (item.status.includes('0-Normal')) {
        const count = $('.card-body-infos.0-Normal').length;
        $('#text3').text(`${count} Op's`);
    }
    if (item.status.includes('1-Atencao')) {
        const count = $('.card-body-infos.1-Atencao').length;
        $('#text4').text(`${count} Op's`);
    }
}

function setupCardHoverAndClick(divCard, item) {
    divCard.on('mouseover', () => {
        if (item['Status Aguardando Partes'] === 'PENDENTE') {
            mostrarModalPendente(divCard[0], item);
        }
    });

    divCard.on('mouseout', () => {
        $('#ModalPendencia').css('display', 'none');
        $('#DivPendencias').empty();
    });

    divCard.click(async function () {
        try {
            await ConsultaJustificativa(item.numeroOP, item.codFase);

            $('#NumeroOP').text(`OP: ${item.numeroOP}`);
            $('#InputJustificativa').val(VarObs).focus();

            $('#modal-justificativa').modal('show');

            VarOp = item.numeroOP;
            VarFase = item.codFase;
        } catch (error) {
            console.error('Erro ao aguardar a justificativa:', error);
        }
    });
}

function mostrarModalPendente(card, item) {
    var rect = card.getBoundingClientRect();
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const pendencias = item.estaPendente; // Supondo que 'estaPendente' seja uma lista de pendências

    // Inicialmente posiciona o modal no topo e à direita da divCard
    $('#ModalPendencia').css({
        'top': rect.top + scrollTop + 'px',
        'left': rect.right + 'px',
        'display': 'block',
        'position': 'absolute' // Assegura que o modal seja posicionado corretamente
    });

    // Limpa o conteúdo anterior dentro de DivPendencias
    $('#DivPendencias').empty();

    // Adiciona as pendências à DivPendencias
    pendencias.forEach(pendencia => {
        const label = $('<label>').text(pendencia).css('font-size', '22px');
        $('#DivPendencias').append(label);
    });

    // Reajusta a posição se o modal ultrapassar a área visível
    var modal = $('#ModalPendencia');
    var modalRect = modal[0].getBoundingClientRect();

    if (modalRect.right > window.innerWidth) {
        // Se ultrapassar a direita da tela, ajusta para a esquerda
        modal.css('left', rect.left - modalRect.width + 'px');
    }

    if (modalRect.bottom > window.innerHeight) {
        // Se ultrapassar o fundo da tela, ajusta para cima
        modal.css('top', rect.bottom + scrollTop - modalRect.height + 'px');
    }

    if (modalRect.left < 0) {
        // Se ultrapassar a esquerda da tela, ajusta para a direita
        modal.css('left', '0px');
    }

    if (modalRect.top < 0) {
        // Se ultrapassar o topo da tela, ajusta para baixo
        modal.css('top', scrollTop + 'px');
    }
}

// Filtros
async function FiltrarDadosPrioridade() {
    const filterClass1 = Prioridade1.trim();
    const filterClass2 = Prioridade2.trim();
    const filterStatus = Status.trim();
    console.log(filterClass1, filterClass2, filterStatus);

    if (filterClass1 === '' && filterClass2 === '' && filterStatus === '') {
        // Exibe todas as divs se os filtros estiverem vazios
        $(".card-ops").show();
    } else {
        $(".card-body-infos").each(function () {
            const statusClass = $(this).attr('class');
            const hasPrioridade = filterClass1 && statusClass.includes(filterClass1) ||
                filterClass2 && statusClass.includes(filterClass2);
            const hasStatus = filterStatus && statusClass.includes(filterStatus);

            if ((filterClass1 || filterClass2) && filterStatus) {
                // Filtrar por prioridade e status
                if (hasPrioridade && hasStatus) {
                    $(this).closest(".card-ops").show();
                } else {
                    $(this).closest(".card-ops").hide();
                }
            } else if (filterClass1 || filterClass2) {
                // Filtrar apenas por prioridade
                if (hasPrioridade) {
                    $(this).closest(".card-ops").show();
                } else {
                    $(this).closest(".card-ops").hide();
                }
            } else if (filterStatus) {
                // Filtrar apenas por status
                if (hasStatus) {
                    $(this).closest(".card-ops").show();
                } else {
                    $(this).closest(".card-ops").hide();
                }
            }
        });
    }

    atualizarContagemOps();
}




function atualizarContagemOps() {
    const countNormal = $('.card-body-infos.0-Normal:visible').length;
    const countAtencao = $('.card-body-infos.1-Atencao:visible').length;
    const countAtrasado = $('.card-body-infos.2-Atrasado:visible').length;

    $('#text3').text(`${countNormal} Op's`);
    $('#text4').text(`${countAtencao} Op's`);
    $('#text5').text(`${countAtrasado} Op's`);

    // Recalcular total de peças visíveis
    let totalQuantidade = 0;
    $('.card-ops:visible').each(function () {
        totalQuantidade += parseInt($(this).attr('data-quantidade'), 10);
    });
    $('#text2').text(`${totalQuantidade.toLocaleString()} pçs`);

    // Recalcular quantidade total de OPs visíveis
    const quantidadeOps = $('.card-ops:visible').length;
    $('#text1').text(`${quantidadeOps.toLocaleString()} Op's`);
}

let colecoesSelecionadas = [];

async function aplicarFiltros() {
    contem = $("#InputContem").val();
    ordenacao = $("input[name='opcoesOrdenacao']:checked").val();
    colecoesSelecionadas = []

    $('input[type=checkbox][id^="checkbox"]').each(function () {
        // Verificar se o checkbox está marcado
        if ($(this).is(':checked')) {
            // Obter o valor do checkbox
            var colecao = $(this).val();

            // Verificar se a coleção já existe no array
            if (!colecoesSelecionadas.includes(colecao)) {
                // Adicionar o valor ao array de coleções selecionadas
                colecoesSelecionadas.push(colecao);
            }
        }
    });
    const filtro = `${contem}`
    await $('#modal-filtros').modal('hide');
    await $('#modalLoading').modal('show');
    $('.Claudino').addClass('d-none');
    $('.FatAtrasado').addClass('d-none');
    $('.Avista').addClass('d-none');
    console.log(filtro);
    console.log(ordenacao);
    console.log(colecoesSelecionadas)
    await ConsultaOps(filtro, ordenacao, colecoesSelecionadas);
    Prioridade1 = '';
    Prioridade2 = '';
    Status = ''
    FiltrarDadosPrioridade()
}

$('#formFiltros').on('submit', function (event) {
    event.preventDefault(); // Prevenir o envio padrão do formulário
    aplicarFiltros(); // Chamar a função aplicarFiltros
});
