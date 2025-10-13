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

    setInterval(async () => {
        await aplicarFiltros();
    }, 900000); // 14 minutos em milissegundos
});


const ConsultaColecoes = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Colecoes'
            }
        });
        console.log(response)
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
}

const ConsultaJustificativa = async (Op, Fase) => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_Justificativas',
                op: Op,
                fase: Fase
            }
        });
        console.log(response)
        VarObs = response[0]['justificativa'];
    } catch (error) {
        console.error('Erro', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function ConsultaOps(filtro, classificacao, colecao) {
    $('#loadingModal').modal('show');
    try {
        const dados = {
            "empresa": 1,
            "filtro": filtro,
            "classificar": classificacao,
            "colecao": colecao
        };

        var requestData = {
            acao: "Consultar_Ops",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        console.log(response)
        $('#Corpo').empty();
        CriarCards(response[0]['3 -Detalhamento'])
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
    $('#text1').text('')
    $('#text2').text('')
    $('#text3').text('')
    $('#text4').text('')
    $('#text5').text('')

    dados.forEach(function(item) {
        function Cor_Fundo(status) {
            switch (status) {
                case '0-Normal':
                    return 'bg-success';
                case '2-Atrasado':
                    return 'bg-danger';
                case '1-Atencao':
                    return 'bg-warning';
                default:
                    return '';
            }
        }

        var ClassCor = Cor_Fundo(item.status);
        var ClassPrioridade = item.prioridade;
        var ClassStatus = item.status;

        var divCard = $('<div class="col-sm-6 col-md-3">');
        divCard.attr('data-op', item.numeroOP);
        divCard.attr('data-fase', item.codFase);
        divCard.attr('data-quantidade', item['Qtd Pcs']);

        // Soma a quantidade
        totalQuantidade += parseInt(item['Qtd Pcs'], 10);

        var card = $('<div class="card" id="CardCorpo">');
        var cardBody = $('<div class="card-body ' + ClassCor + ' ' + ClassPrioridade + ' ' + ClassStatus + '" id="Teste">');
        cardBody.on('click', function() {
            console.log($(this).attr('class'));
        });

        var cardTitle = $('<h5 class="card-title">').text('Fase: ' + item.codFase + ' - ' + item.nomeFase);
        var cardText = $('<p class="card-text" id="Maior">').html('<strong>Numero Op: ' + item.numeroOP + '/ Qtd: ' + item['Qtd Pcs'] + 'Pçs </strong>');
        var cardText2 = $('<p class="card-text" id="Maior">').html('<strong>Engenharia: ' + item.codProduto + '</strong>');
        var cardText3 = $('<p class="card-text" id="Menor">').html('<strong>' + item.descricao + '</strong>');
        var cardText4 = $('<p class="card-text" id="Maior">').html('<strong>Tipo Op: ' + item.codTipoOP + '</strong>');
        var cardText5 = $('<p class="card-text" id="Maior">').html('<strong>Responsável: ' + item.responsavel + '</strong>');
        var cardText6 = $('<p class="card-text" id="Maior">').html('<strong>Meta: ' + item.meta + '</strong>');
        var cardText7 = $('<p class="card-text" id="Maior">').html('<strong>Dias na Fase: ' + item['dias na Fase'] + '</strong>');
        var cardText8 = $('<p class="card-text" id="justificativa">').html('<strong>Justificativa: ' + item.justificativa + '</strong>');
        var spanElement = $('<span>').attr('style', item['Status Aguardando Partes'] === 'PENDENTE' || item['Status Aguardando Partes'] === 'OK' ? 'font-size: 50px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;' : '').html(
            (item['Status Aguardando Partes'] === 'PENDENTE' ? '⚠️' : '') +
            (item['Status Aguardando Partes'] === 'OK' ? '✅' : '')
        );

        if (item.prioridade) {
            const urgenteSymbol = document.createElement('div');
            urgenteSymbol.innerHTML = item.prioridade;
            urgenteSymbol.style.backgroundColor = '#FFFF00';
            urgenteSymbol.style.color = 'black';
            urgenteSymbol.style.border = '1px solid black';
            urgenteSymbol.style.fontSize = '19px';
            urgenteSymbol.style.marginLeft = '-18px';
            urgenteSymbol.style.width = '100px';
            cardBody.append(urgenteSymbol);
        }

        // Ajuste para ocultar corretamente
        if (item.prioridade.includes('CLAUDINO')) {
            $('.Claudino').removeClass('d-none');
        }

        if (item.prioridade.includes('FAT ATRASADO')) {
            $('.FatAtrasado').removeClass('d-none');
        }

        if (item.prioridade.includes('A VISTA ANTECIPADO')) {
            $('.Avista').removeClass('d-none');
        }

        if (item.status.includes('2-Atrasado')) {
            const count = $('.card-body.2-Atrasado').length;
            $('#text5').text(`${count} Op's`);
        }
        if (item.status.includes('0-Normal')) {
            const count = $('.card-body.0-Normal').length;
            $('#text3').text(`${count} Op's`);
        }

        if (item.status.includes('1-Atencao')) {
            const count = $('.card-body.1-Atencao').length;
            $('#text4').text(`${count} Op's`);
        }

        cardBody.append(cardTitle, cardText, cardText2, cardText3, cardText4, cardText5, cardText6, cardText7, cardText8, spanElement);
        card.append(cardBody);
        divCard.append(card);
        $('#Corpo').append(divCard);

        divCard.on('mouseover', () => {
            if (item['Status Aguardando Partes'] === 'PENDENTE') {
                mostrarModalPendente(card[0], item);
            }
        });

        divCard.on('mouseout', () => {
            fecharModalPendente();
        });

        divCard.click(async function() {
            try {
                await ConsultaJustificativa(item.numeroOP, item.codFase);

                $('#NumeroOP').text(`OP: ${item.numeroOP}`);
                $('#InputJustificativa').val(`${VarObs}`);
                $('#InputJustificativa').focus();

                var rect = $(this)[0].getBoundingClientRect();
                var scrollTop = $('#Corpo').scrollTop();
                var topOffset = rect.top + scrollTop;

                $('#ModalJustificativa').modal('show')

                VarOp = item.numeroOP;
                VarFase = item.codFase;
            } catch (error) {
                console.error('Erro ao aguardar a justificativa:', error);
            }
        });
    });
    const quantidadeOps = $('#Corpo .card-body').length;
    // Atualiza a exibição total da quantidade
    $('#text2').text(`${totalQuantidade.toLocaleString()} pçs`);
    $('#text1').text(`${quantidadeOps.toLocaleString()} Op's`);
}

async function FiltrarDadosPrioridade() {
    var filterClass1 = Prioridade1.trim();
    var filterClass2 = Prioridade2.trim();
    var filterStatus = Status.trim();
    console.log(filterClass1)
    console.log(filterClass2)
    console.log(filterStatus)

    if (filterClass1 === '' && filterClass2 === '' && filterStatus === '') {
        // Se todos os parâmetros estiverem vazios, exibe todas as divs
        $('#Corpo > .col-sm-6.col-md-3').removeClass('d-none');
    } else if (filterClass1 === '' && filterClass2 === '') {
        // Se apenas o status for fornecido, filtra apenas pelo status
        $('#Corpo > .col-sm-6.col-md-3').each(function() {
            var cardBody = $(this).find('.card-body');
            var hasStatusClass = filterStatus === '' || cardBody.hasClass(filterStatus);

            if (hasStatusClass) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    } else if (filterStatus === '') {
        // Se apenas a prioridade for fornecida, filtra apenas pela prioridade
        $('#Corpo > .col-sm-6.col-md-3').each(function() {
            var cardBody = $(this).find('.card-body');
            var hasPriorityClass = cardBody.hasClass(filterClass1) || cardBody.hasClass(filterClass2);

            if (hasPriorityClass) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    } else {
        // Filtra as divs com base nos parâmetros de prioridade e status fornecidos
        $('#Corpo > .col-sm-6.col-md-3').each(function() {
            var cardBody = $(this).find('.card-body');
            var hasPriorityClass = cardBody.hasClass(filterClass1) || cardBody.hasClass(filterClass2);
            var hasStatusClass = filterStatus === '' || cardBody.hasClass(filterStatus);

            if (hasPriorityClass && hasStatusClass) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    }

    // Atualiza a contagem de OPs visíveis
    atualizarContagemOps();
}


function atualizarContagemOps() {
    const countNormal = $('.card-body.0-Normal:visible').length;
    const countAtencao = $('.card-body.1-Atencao:visible').length;
    const countAtrasado = $('.card-body.2-Atrasado:visible').length;

    $('#text3').text(`${countNormal} Op's`);
    $('#text4').text(`${countAtencao} Op's`);
    $('#text5').text(`${countAtrasado} Op's`);

    // Recalcular total de peças visíveis
    let totalQuantidade = 0;
    $('#Corpo > .col-sm-6.col-md-3:visible').each(function() {
        totalQuantidade += parseInt($(this).attr('data-quantidade'), 10);
    });
    $('#text2').text(`Total Quantidade: ${totalQuantidade.toLocaleString()} pçs`);

    // Recalcular quantidade total de OPs visíveis
    const quantidadeOps = $('#Corpo > .col-sm-6.col-md-3:visible').length;
    $('#text1').text(`${quantidadeOps.toLocaleString()} Op's`);
}




let colecoesSelecionadas = [];

async function aplicarFiltros(AcionarFunction) {
    contem = $("#InputContem").val();
    ordenacao = $("input[name='opcoesOrdenacao']:checked").data("valor");
    colecoesSelecionadas = []

    $('input[type=checkbox][id^="checkbox"]').each(function() {
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
    await $('#modalFiltros').modal('hide');
    await $('#modalLoading').modal('show');
    $('.Claudino').addClass('d-none');
    $('.FatAtrasado').addClass('d-none');
    $('.Avista').addClass('d-none');
    await ConsultaOps(filtro, ordenacao, colecoesSelecionadas);
    Prioridade1 = '';
    Prioridade2 = '';
    Status = ''
    FiltrarDadosPrioridade()
}

$('#formFiltros').on('submit', function(event) {
    event.preventDefault(); // Prevenir o envio padrão do formulário
    aplicarFiltros(); // Chamar a função aplicarFiltros
});


async function ExportarExcel() {
    $('#loadingModal').modal('show');
    try {
        const dados = {
            "empresa": 1,
            "filtro": '',
            "classificar": '',
            "colecao": ''
        };

        var requestData = {
            acao: "Consultar_Ops",
            dados: dados
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        const DetalhamentoApi = response[0]['3 -Detalhamento'];
        const colunasDesejadas = ['numeroOP', 'codProduto', 'descricao', 'Qtd Pcs', 'codFase', 'nomeFase', 'dias na Fase', 'responsavel', 'prioridade', 'justificativa', 'COLECAO', 'categoria', 'status', 'detalhado']; // substitua com os nomes das colunas desejadas

        // Filtrar dados para incluir apenas as colunas desejadas
        const dadosFiltrados = DetalhamentoApi.map(item => {
            let novoItem = {};
            colunasDesejadas.forEach(coluna => {
                novoItem[coluna] = item[coluna];
            });
            return novoItem;
        });

        const nomeArquivo = 'Dados Ops.xlsx';
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet(dadosFiltrados);

        // Adicionar a planilha ao workbook
        XLSX.utils.book_append_sheet(wb, ws, "Dados Op's");

        // Salvar o arquivo
        XLSX.writeFile(wb, nomeArquivo);

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
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

function fecharModalPendente() {
    // Oculta o modal
    $('#ModalPendencia').css('display', 'none');

    // Limpa o conteúdo dentro de DivPendencias
    $('#DivPendencias').empty();
}

async function SalvarJustificativa() {
    $('#ModalJustificativa').modal('hide');
    $('#loadingModal').modal('show');
    try {
        const dados = {
            "ordemProd": VarOp,
            "fase": VarFase,
            "justificativa": $('#InputJustificativa').val()
        };

        var requestData = {
            acao: "Cadastrar_Justificativa",
            dados: dados
        };
        const response = await $.ajax({
            type: 'PUT',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        console.log(response)
        Swal.fire({
            title: 'Justificativa Inserida',
            icon: 'success',
            showConfirmButton: false,
            timer: "3000",
        });
        atualizarJustificativaCard(VarOp, VarFase, $('#InputJustificativa').val());

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Swal.fire({
            title: 'Erro',
            icon: 'error',
            showConfirmButton: false,
            timer: "3000",
        });
    } finally {
        $('#loadingModal').modal('hide');
    }
}

function atualizarJustificativaCard(op, fase, justificativa) {
    const card = document.querySelector(`#Corpo div[data-op="${op}"][data-fase="${fase}"]`);
    console.log(card)
    if (card) {
        const justificativaElement = card.querySelector('.card-text#justificativa');
        if (justificativaElement) {
            justificativaElement.innerHTML = `<strong>Justificativa: </strong>${justificativa}`;
        } else {
            console.error('Elemento da justificativa não encontrado.');
        }
    } else {
        console.error('Card não encontrado.');
    }
}

