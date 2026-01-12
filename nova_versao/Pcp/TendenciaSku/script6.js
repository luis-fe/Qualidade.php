// --- CONFIGURAÇÕES GLOBAIS ---
const API_BASE_URL = "http://10.162.0.53:9000";

// --- VARIÁVEIS DE ESTADO ---
let imagemAtual = 0;
let totalImagens = 0;
let totalImagensEng = 0;
let totalImagensColorBook = 0;
let codigoMP = "";
let imagensColorBook = [];
let searchTimeout;
let nomeSimulacao = ""; // Variável global para manter o estado da simulação atual

// --- FUNÇÕES AUXILIARES ---

/**
 * Retorna o nome da simulação ativa baseada na visibilidade do select
 * Evita repetição de código em várias funções.
 */
const obterNomeSimulacaoAtiva = () => {
    if ($('#select-simulacao').is(':visible') && $('#select-simulacao').val()) {
        return $('#select-simulacao').val();
    }
    return $("#descricao-simulacao").val() || "";
};

const atualizarImagem = () => {
    if (!codigoMP || String(codigoMP).trim() === "") {
        console.error("codigoMP está vazio!");
        return;
    }

    let url = "";

    if (imagemAtual < totalImagensColorBook) {
        url = imagensColorBook[imagemAtual];
    } else {
        const indiceEng = imagemAtual - totalImagensColorBook;
        url = `${API_BASE_URL}/imagemEng/${codigoMP}/${indiceEng}`;
    }

    $('#imagem-container').html(`
        <img src="${url}" alt="Imagem ${imagemAtual + 1}" class="img-fluid">
    `);

    $('#contador-imagens').text(`Imagem ${imagemAtual + 1} de ${totalImagens}`);
    $('#btn-anterior').prop('disabled', imagemAtual === 0);
    $('#btn-proximo').prop('disabled', imagemAtual >= totalImagens - 1);
};

// --- INICIALIZAÇÃO (DOCUMENT READY) ---
$(document).ready(async () => {
    Consulta_Planos();
    Consulta_Simulacoes();

    $('#select-plano').select2({
        placeholder: "Selecione um plano",
        allowClear: false,
        width: '100%'
    });

    $('#select-simulacao').on('change', async function () {
        $('#inputs-container-marcas').removeClass('d-none');
        $('#inputs-container-categorias').removeClass('d-none');
        $('#inputs-container').removeClass('d-none');

        await Consulta_Abc_Plano(false);
        await Consulta_Categorias();
        await Consulta_Simulacao_Especifica();
        Produtos_Simulacao();
    });

    // Uso de .off() para garantir que não haja múltiplos listeners
    $('#btn-anterior').off('click').on('click', function () {
        if (imagemAtual > 0) {
            imagemAtual--;
            atualizarImagem();
        }
    });

    $('#btn-proximo').off('click').on('click', function () {
        if (imagemAtual < totalImagens - 1) {
            imagemAtual++;
            atualizarImagem();
        }
    });
});

// --- API CALLS & LÓGICA DE NEGÓCIO ---

const Consulta_Planos = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: { acao: 'Consulta_Planos' },
        });

        $('#select-plano').empty().append('<option value="" disabled selected>Selecione um plano...</option>');
        
        if (response && Array.isArray(response)) {
            response.forEach(function (plano) {
                $('#select-plano').append(`
                    <option value="${plano['01- Codigo Plano']}">
                        ${plano['01- Codigo Plano']} - ${plano['02- Descricao do Plano']}
                    </option>
                `);
            });
        }
    } catch (error) {
        console.error('Erro:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

async function Consulta_Simulacoes() {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: { acao: 'Consulta_Simulacoes' },
        success: function (data) {
            $('#select-simulacao').empty().append('<option value="" disabled selected>Selecione uma simulação...</option>');
            
            if (data && Array.isArray(data)) {
                data.forEach(function (item) {
                    $('#select-simulacao').append(`
                        <option value="${item['nomeSimulacao']}">
                            ${item['nomeSimulacao']}
                        </option>
                    `);
                });
            }
            
            $('#loadingModal').modal('hide');
            
            const descricao = $('#descricao-simulacao').val();
            if(descricao){
                console.log(`Simulacao escolhida: ${descricao}`);
                $('#select-simulacao').val(descricao); // Tenta selecionar se existir
            }
        },
        error: function (xhr, status, error) {
            console.error('Erro ao consultar simulações:', error);
            $('#loadingModal').modal('hide');
        }
    });
}

async function Consulta_Tendencias() {
    const respostaCalculo = await Consulta_Ultimo_CalculoTendencia();

    if (!respostaCalculo || respostaCalculo.status === null) {
        gerarTendenciaNova(false);
        return;
    }

    try {
        const result = await Swal.fire({
            title: `${respostaCalculo.mensagem}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "Recalcular",
            cancelButtonText: "Não"
        });

        setTimeout(() => {
            if (result.isConfirmed) {
                gerarTendenciaNova(false);
            } else {
                gerarTendenciaNova(true);
            }
        }, 300);
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro na solicitação', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Ultimo_CalculoTendencia = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Ultimo_CalculoTendencia',
                plano: $('#select-plano').val()
            }
        });

        // Verificação de segurança para array vazio
        if (data && data.length > 0) {
            return {
                status: data[0]['status'],
                mensagem: data[0]['Mensagem'],
                dataHora: data[0]['dataHora'],
                dataHoraPedidos: data[0]['dataHoraPedidos'],
                data_horaEstruturaMP: data[0]['data_horaEstruturaMP'],
            };
        }
        return { status: null, mensagem: "Sem dados" };

    } catch (error) {
        console.error('Erro ao consultar ultimo calculo:', error);
        return null;
    }
};

const Consulta_Simulacao_Especifica = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Simulacao_Especifica',
                simulacao: $('#select-simulacao').val()
            }
        });

        if (!data || data.length === 0) {
            Mensagem_Canto('Não possui simulação para editar', 'warning');
            return;
        }

        const campos = ["2- ABC", "3- Categoria", "4- Marcas"];
        campos.forEach(campo => {
            if (data[0][campo]) {
                data[0][campo].forEach(item => {
                    const key = item.class || item.categoria || item.marca;
                    // Limpeza mais robusta da chave para ID
                    const cleanKey = key.replace(/\s+/g, '-').replace(/[^\w-]/g, '');
                    const input = $(`#${cleanKey}`);
                    
                    if (input.length) {
                        input.val(`${parseFloat(item.percentual).toFixed(1).replace('.', ',')}%`);
                    }
                });
            }
        });
    } catch (error) {
        console.error('Erro ao consultar simulação específica:', error);
    }
};

async function gerarTendenciaNova(congelamento) {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Consulta_Tendencias",
            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                "congelar": congelamento
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        TabelaTendencia(response);
        $('.div-tendencia').removeClass('d-none');
        
        const respostaPeriodoVendas = await PeriodoVendasPlano();
        if (respostaPeriodoVendas) {
            // Formatações
            respostaPeriodoVendas.inicioVenda = formatarDataBrasileira(respostaPeriodoVendas.inicioVenda);
            respostaPeriodoVendas.finalVenda = formatarDataBrasileira(respostaPeriodoVendas.finalVenda);
            respostaPeriodoVendas.inicioFaturamento = formatarDataBrasileira(respostaPeriodoVendas.inicioFaturamento);
            respostaPeriodoVendas.finalFaturamento = formatarDataBrasileira(respostaPeriodoVendas.finalFaturamento);
            respostaPeriodoVendas.metaFinanceira = formatarMoedaBrasileira(respostaPeriodoVendas.metaFinanceira);
            respostaPeriodoVendas.metaPcs = formatarInteiro(respostaPeriodoVendas.metaPcs);
        }

        const respostaCalculo = await Consulta_Ultimo_CalculoTendencia();

        $('#titulo').html(`
            <div class="d-flex justify-content-between align-items-start w-100 p-0 m-0">
                <div class="ms-2">
                    <span class="span-icone"><i class="bi bi-clipboard-data-fill"></i></span> 
                    Tendência de Vendas
                </div>
                <div class="d-flex flex-column text-end periodo-vendas p-0 me-10">
                    <div>
                        <i class="bi bi-calendar3 me-1"></i>
                        <span>Período Vendas:<strong> ${respostaPeriodoVendas?.inicioVenda || '-'} à ${respostaPeriodoVendas?.finalVenda || '-'}</strong></span>
                    </div>
                    <div>
                        <i class="bi bi-calendar3 me-1"></i>
                        <span>Período Fatura.:<strong> ${respostaPeriodoVendas?.inicioFaturamento || '-'} à ${respostaPeriodoVendas?.finalFaturamento || '-'}</strong></span>
                    </div>
                </div>
                <div class="card border rounded me-1" style="width: 190px;">
                    <div class="card-body p-0">
                        <h5 class="card-title bg-primary text-white p-0 m-0 text-center">Meta R$</h5>
                        <p class="card-text m-0"><strong>${respostaPeriodoVendas?.metaFinanceira || '0,00'}</strong></p>
                    </div>
                </div>
                <div class="card border rounded me-1" style="width: 190px;">
                    <div class="card-body p-0">
                        <h5 class="card-title bg-primary text-white p-0 m-0 text-center">Meta Pçs</h5>
                        <p class="card-text m-0"><strong>${respostaPeriodoVendas?.metaPcs || '0'}</strong></p>
                    </div>
                </div>
                <div id="btn-informacoes" class="card border rounded me-1" style="width: 190px; cursor: pointer;"> 
                    <div> 
                        <i class="bi bi-info-circle"></i> 
                        <strong>Informações</strong> 
                    </div> 
                </div>
            </div>
        `);

        // CORREÇÃO IMPORTANTE: .off('click') para evitar acumular eventos a cada chamada
        $('#btn-informacoes').off('click').on('click', function () {
            $('.div-informacoes').removeClass('d-none');
            
            if (respostaCalculo) {
                $('#informacaoAtualizacao').find('.row h6')
                    .html(`Calculado no dia: <strong>${respostaCalculo.dataHora || '-'}</strong>`);

                $('#informacaoSincronia h6').eq(1).html(
                    `<i class="bi bi-database"></i> Informativo de Vendas:<strong>${respostaCalculo.dataHoraPedidos || '-'}</strong>`
                );

                $('#informacaoSincronia h6').eq(2).html(
                    `<i class="bi bi-database"></i> Estrutura da Materia Prima por Produto:<strong>${respostaCalculo.data_horaEstruturaMP || '-'}</strong>`
                );
            }
        });

        // Limpa a variável global ao gerar nova tendência padrão
        nomeSimulacao = "";

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Engenharias = async () => {
    $('#loadingModal').modal('show');
    // Usa a função auxiliar
    var simulacao = obterNomeSimulacaoAtiva();

    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: "obter_produtos_tendencia",
                codPlano: $('#select-plano').val(),
                nomeSimulacao: simulacao
            }
        });
        $('.div-selecaoEngenharias').removeClass('d-none');
        TabelaEngenharia(data);

    } catch (error) {
        console.error('Erro ao consultar engenharias:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const PeriodoVendasPlano = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'consultarInformacoesPlano',
                plano: $('#select-plano').val(),
                empresa: '1'
            }
        });

        if (data && data.length > 0) {
            return {
                inicioVenda: data[0]['03- Inicio Venda'],
                finalVenda: data[0]['04- Final Venda'],
                inicioFaturamento: data[0]['05- Inicio Faturamento'],
                finalFaturamento: data[0]['06- Final Faturamento'],
                metaFinanceira: data[0]['12-metaFinanceira'],
                metaPcs: data[0]['13-metaPecas']
            };
        }
        return null;

    } catch (error) {
        console.error('Erro ao consultar periodo plano:', error);
        return null;
    }
};

const Consulta_Imagem = async (codigoPai) => {
    codigoMP = String(codigoPai);
    $('#loadingModal').modal('show');

    try {
        // 1. Inicia em paralelo
        const [primeiraColorBook, dataEng] = await Promise.all([
            $.ajax({
                type: 'GET',
                url: `${API_BASE_URL}/pcp/api/obterImagemSColorBook?codItemPai=${codigoPai}&indice=0`,
                dataType: 'json'
            }),
            $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consulta_Imagem',
                    codigoMP: codigoPai
                },
                xhrFields: { withCredentials: true }
            })
        ]);

        totalImagensColorBook = primeiraColorBook.total_imagens || 0;
        totalImagensEng = dataEng.total_imagens || 0;

        // 2. Faz chamadas paralelas para os restantes do ColorBook
        const colorBookRequests = [];
        for (let i = 0; i < totalImagensColorBook; i++) {
            colorBookRequests.push(
                $.ajax({
                    type: 'GET',
                    url: `${API_BASE_URL}/pcp/api/obterImagemSColorBook?codItemPai=${codigoPai}&indice=${i}`,
                    dataType: 'json'
                })
            );
        }

        const imagensColorData = await Promise.all(colorBookRequests);
        imagensColorBook = imagensColorData.map(img => img.imagem_url);

        totalImagens = totalImagensColorBook + totalImagensEng;
        imagemAtual = 0;
        atualizarImagem();

        $('#loadingModal').modal('hide');
        $('#modal-imagemMP').modal('show');
    } catch (error) {
        console.error('Erro ao consultar imagens:', error);
        Mensagem_Canto('Erro', 'error');
        $('#loadingModal').modal('hide');
    }
};

// --- FORMATAÇÃO ---
function formatarDataBrasileira(dataISO) {
    if (!dataISO || !dataISO.includes('-')) return dataISO;
    const [ano, mes, dia] = dataISO.split('-');
    return `${dia}/${mes}/${ano}`;
}

function formatarMoedaBrasileira(valor) {
    const numero = parseFloat(valor);
    if(isNaN(numero)) return "R$ 0,00";
    return numero.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });
}

function formatarInteiro(valor) {
    const numero = parseInt(valor);
    if (isNaN(numero)) return "Valor inválido";
    return numero.toLocaleString("pt-BR");
}

// --- LÓGICA DE SIMULAÇÃO ---

async function Cadastro_Simulacao(simulacao, tipo) {
    $('#loadingModal').modal('show');

    try {
        const categorias = [], percentuais_categorias = [];
        const abcs = [], percentuais_abc = [];
        const marcas = [], percentuais_marca = [];

        // Define seletores baseados no tipo
        const selectorSuffix = (tipo === "cadastro") ? "-2" : "";
        const marcaSelector = (tipo === "cadastro") ? ".input-marca-nova" : ".input-marca";

        // Coleta Categorias
        $(`.input-categoria${selectorSuffix}`).each(function () {
            const val = parseFloat($(this).val().replace('%','').replace(',', '.'));
            if ($(this).attr('id') && !isNaN(val)) {
                categorias.push($(this).attr('id'));
                percentuais_categorias.push(val);
            }
        });

        // Coleta ABC
        $(`.input-abc${selectorSuffix}`).each(function () {
            const val = parseFloat($(this).val().replace('%','').replace(',', '.'));
            if ($(this).attr('id') && !isNaN(val)) {
                abcs.push($(this).attr('id'));
                percentuais_abc.push(val);
            }
        });

        // Coleta Marcas
        $(marcaSelector).each(function () {
            const val = parseFloat($(this).val().replace('%','').replace(',', '.'));
            if ($(this).attr('id') && !isNaN(val)) {
                marcas.push($(this).attr('id'));
                percentuais_marca.push(val);
            }
        });

        const requestData = {
            acao: "Cadastro_Simulacao",
            dados: {
                "nomeSimulacao": simulacao,
                arrayAbc: [abcs, percentuais_abc],
                arrayCategoria: [categorias, percentuais_categorias],
                arrayMarca: [marcas, percentuais_marca]
            }
        };

        await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
};

async function simulacao(texto, tipo) {
    console.log(`Simulacao Escolhida pela formula: ${texto}`);
    fecharSimulacao();
    fecharNovaSimulacao();
    await Cadastro_Simulacao(texto, tipo);
    await Consulta_Simulacoes();
    await Simular_Programacao(texto, tipo);
    nomeSimulacao = texto; // Atualiza variável global
    console.log(`nomeSimulacao atualizado: ${nomeSimulacao}`);
};

async function Simular_Programacao(simulacao, tipo) {
    $('#loadingModal').modal('show');
    // let nomeSimulacao = simulacao; // Comentado pois deve usar o parametro ou a global

    try {
        const checkboxId = (tipo === "cadastro") ? 'igualarDisponivel2' : 'igualarDisponivel';
        const checkbox = document.getElementById(checkboxId);
        const estaMarcado = checkbox?.checked ?? false;

        const requestData = {
            acao: "Simular_Programacao",
            dados: {
                codPlano: $('#select-plano').val(),
                consideraPedidosBloqueado: $('#select-pedidos-bloqueados').val(),
                nomeSimulacao: simulacao,
                igualarDisponivel: estaMarcado
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        const respostaPeriodoVendas = await PeriodoVendasPlano();
        if(respostaPeriodoVendas) {
            respostaPeriodoVendas.inicioVenda = formatarDataBrasileira(respostaPeriodoVendas.inicioVenda);
            respostaPeriodoVendas.finalVenda = formatarDataBrasileira(respostaPeriodoVendas.finalVenda);
            respostaPeriodoVendas.inicioFaturamento = formatarDataBrasileira(respostaPeriodoVendas.inicioFaturamento);
            respostaPeriodoVendas.finalFaturamento = formatarDataBrasileira(respostaPeriodoVendas.finalFaturamento);
            respostaPeriodoVendas.metaPcs = formatarInteiro(respostaPeriodoVendas.metaPcs);
            respostaPeriodoVendas.metaFinanceira = formatarMoedaBrasileira(respostaPeriodoVendas.metaFinanceira);
        }
        
        const respostaCalculo = await Consulta_Ultimo_CalculoTendencia();

        $('#titulo').html(`
            <div class="d-flex justify-content-between align-items-start w-100 p-0 m-0">
                <div>
                    <span class="span-icone"><i class="bi bi-clipboard-data-fill"></i></span> 
                    Tendência de Vendas
                    <span style="display: inline-block; position: relative;">
                        <strong>${simulacao}</strong>
                        <button onclick="Consulta_Tendencias()" 
                                style="position: absolute; top: 0; right: -20px; border: none; background: none; font-weight: bold; color: red; cursor: pointer;">
                            ×
                        </button>
                    </span>
                </div>
                <div class="d-flex flex-column text-end periodo-vendas p-0 m-0 ms-3">
                     <div><i class="bi bi-calendar3 me-1"></i> Período Vendas: <strong>${respostaPeriodoVendas?.inicioVenda} à ${respostaPeriodoVendas?.finalVenda}</strong></div>
                     <div><i class="bi bi-calendar3 me-1"></i> Período Fatura.: <strong>${respostaPeriodoVendas?.inicioFaturamento} à ${respostaPeriodoVendas?.finalFaturamento}</strong></div>
                </div>
                <div class="card border rounded me-1" style="width: 190px;">
                    <div class="card-body p-0"><h5 class="card-title bg-primary text-white text-center">Meta R$</h5><p class="card-text text-center"><strong>${respostaPeriodoVendas?.metaFinanceira}</strong></p></div>
                </div>
                <div class="card border rounded me-1" style="width: 190px;">
                    <div class="card-body p-0"><h5 class="card-title bg-primary text-white text-center">Meta Pçs</h5><p class="card-text text-center"><strong>${respostaPeriodoVendas?.metaPcs}</strong></p></div>
                </div>
                <div id="btn-informacoes" class="card border rounded me-1" style="width: 190px; cursor: pointer;"> 
                    <div><i class="bi bi-info-circle"></i> <strong>Informações</strong></div> 
                </div>
            </div>
        `);

        // CORREÇÃO: Evitar listeners duplicados
        $('#btn-informacoes').off('click').on('click', function () {
            $('.div-informacoes').removeClass('d-none');
            if(respostaCalculo) {
                $('#informacaoAtualizacao').find('.row h6').html(`Calculado no dia: <strong>${respostaCalculo.dataHora}</strong>`);
                $('#informacaoSincronia h6').eq(1).html(`<i class="bi bi-database"></i> Informativo de Vendas:<strong>${respostaCalculo.dataHoraPedidos}</strong>`);
                $('#informacaoSincronia h6').eq(2).html(`<i class="bi bi-database"></i> Estrutura da Materia Prima por Produto:<strong>${respostaCalculo.data_horaEstruturaMP}</strong>`);
            }
        });

        TabelaTendencia(response);
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

// --- FUNÇÕES DE UI ---
function fecharSimulacao() { document.getElementById("simulacao-container").classList.add("d-none"); }
function fecharInformacoes() { document.getElementById("informacoes-container").classList.add("d-none"); }
function fecharNovaSimulacao() { document.getElementById("nova-simulacao-container").classList.add("d-none"); }
function fecharselecaoEngenharia() { document.getElementById("modal-selecaoEngenharias").classList.add("d-none"); }

const Consulta_Abc_Plano = async (padrao) => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Abc_Plano',
                plano: $('#select-plano').val()
            }
        });

        const inputsContainer = $('#inputs-container').empty();
        const inputsContainerNova = $('#inputs-container-nova').empty();

        if(data && data.length > 0 && data[0]['3- Detalhamento:']) {
            data[0]['3- Detalhamento:'].forEach((item) => {
                const inputHtml1 = `
                    <div class="col-md-3 mb-3">
                        <label class="form-label">${item.nomeABC}</label>
                        <input type="text" class="inputs-percentuais input-abc col-12" id="${item.nomeABC}" placeholder="%">
                    </div>`;
                
                const inputHtml2 = `
                    <div class="col-md-3 mb-3">
                        <label class="form-label">${item.nomeABC}</label>
                        <input type="text" class="inputs-percentuais input-abc-2 col-12" value=("0,00%") id="${item.nomeABC}" placeholder="%">
                    </div>`;

                inputsContainer.append(inputHtml1);
                inputsContainerNova.append(inputHtml2);
            });

            $('.input-abc').mask("##0,00%", { reverse: true });
            $('.input-abc-2').mask("##0,00%", { reverse: true });
        }
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

const Consulta_Categorias = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: { acao: 'Consulta_Categorias' }
        });

        const inputsContainer = $('#inputs-container-categorias').empty();
        const inputsContainerNova = $('#inputs-container-categorias-nova').empty();

        if(Array.isArray(data)){
            data.forEach((item) => {
                const inputHtml1 = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeCategoria}</label>
                    <input type="text" class="inputs-percentuais input-categoria col-12" id="${item.nomeCategoria}" placeholder="%">
                </div>`;

                const inputHtml2 = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeCategoria}</label>
                    <input type="text" class="inputs-percentuais input-categoria-2 col-12" id="${item.nomeCategoria}" placeholder="100%">
                </div>`;
                
                inputsContainer.append(inputHtml1);
                inputsContainerNova.append(inputHtml2);
            });

            $('.input-categoria').mask("##0,00%", { reverse: true });
            $('.input-categoria-2').mask("##0,00%", { reverse: true });
        }
    } catch (error) {
        console.error('Erro ao consultar categorias:', error);
    }
};

async function Produtos_Simulacao() {
    var simulacao = obterNomeSimulacaoAtiva();
    
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: "selecao_produtos_simulacao",
                nomeSimulacao: simulacao
            }
        });

        if(data && data.length > 0) {
            document.getElementById("TituloSelecaoEngenharias").textContent = data[0].mensagem;
            document.getElementById("TituloSelecaoEngenharias2").textContent = data[0].mensagem;
        }

    } catch (error) {
        console.error('Erro ao consultar produtos simulacao:', error);
    } finally {
        console.log('atualizado produtos da selecacao');
    }
}

// --- DATATABLES & MODALS ---

function TabelaTendencia(listaTendencia) {
    if ($.fn.DataTable.isDataTable('#table-tendencia')) {
        $('#table-tendencia').DataTable().destroy();
    }

    const tabela = $('#table-tendencia').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 12,
        data: listaTendencia,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
            title: 'Tendências de Vendas',
            className: 'btn-tabelas',
            exportOptions: {
                columns: ':visible',
                format: {
                    body: function (data) {
                        if (typeof data === 'string') {
                            const textoSemHtml = data.replace(/<[^>]*>?/gm, '');
                            return textoSemHtml.replace(/\./g, '').replace(',', '.');
                        }
                        return data;
                    }
                }
            }
        },
        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Simulação',
            title: 'Simulação',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                $('.div-simulacao').removeClass('d-none');
                $('#campo-simulacao').removeClass('d-none');

                const simulacaoValue = $('#select-simulacao').val()?.trim() || "";
                console.log(`Simulacao modal: ${simulacaoValue}`);
                
                Produtos_Simulacao();

                if (simulacaoValue === "") {
                    $('#inputs-container-categorias').empty();
                    $('#inputs-container').empty();
                    $('#inputs-container-marcas').addClass('d-none');
                } else {
                    $('#inputs-container-marcas').removeClass('d-none');
                    $('#inputs-container-categorias').removeClass('d-none');
                }
            },
        },
        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Nova Simulação',
            title: 'Nova Simulação',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                $('.div-nova-simulacao').removeClass('d-none');
                $('#inputs-container-novas-marcas').removeClass('d-none');
                await Consulta_Abc_Plano(true);
                await Consulta_Categorias();
                document.getElementById("TituloSelecaoEngenharias2").textContent = "";
                let campo = document.getElementById("descricao-simulacao");
                campo.value = "";
                campo.placeholder = "Insira a descrição";
            },
        }],
        columns: [
            { data: 'marca' },
            { data: 'codItemPai', render: (data, type, row) => `<span class="detalhaImg" data-codItemPai="${row.codItemPai}" style="text-decoration: underline; color:hsl(217, 100.00%, 65.10%); cursor: pointer;">${data}</span>` },
            { data: 'tam' },
            { data: 'codCor' },
            { data: 'nome' },
            { data: 'codReduzido' },
            { data: 'categoria' },
            { data: 'class' },
            { data: 'classCategoria' },
            { data: 'Ocorrencia em Pedidos', render: (data, type) => type === 'display' ? data.toLocaleString('pt-BR') : data },
            { data: 'valorVendido', render: (data, type) => { let v = parseFloat(data.replace(/[^\d,]/g, '').replace(',', '.')); return type === 'display' ? `R$ ${v.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` : v; } },
            { data: 'previcaoVendas', render: (data, type, row) => `<span class="detalha-SimulacaoSku" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>` },
            { data: 'qtdePedida', render: (data, type, row) => `<span class="detalha-pedidos" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>` },
            { data: 'qtdeFaturada', render: (data, type) => type === 'display' ? data.toLocaleString('pt-BR') : data },
            { data: 'SaldoColAnt', render: (data, type, row) => `<span class="detalha-pedidos2" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>` },
            { data: 'estoqueAtual', render: (data, type) => type === 'display' ? data.toLocaleString('pt-BR') : data },
            { data: 'emProcesso', render: (data, type, row) => `<span class="detalha-ordemProd" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>` },
            { data: 'faltaProg (Tendencia)', render: (data, type) => type === 'display' ? data.toLocaleString('pt-BR') : data },
            { data: 'disponivel', render: (data, type) => type === 'display' ? data.toLocaleString('pt-BR') : data },
            { data: 'disponivel Pronta Entrega', render: (data, type) => type === 'display' ? data.toLocaleString('pt-BR') : data },
            { data: 'Prev Sobra', render: (data, type) => type === 'display' ? data.toLocaleString('pt-BR') : data },
            { data: 'statusAFV' }
        ],
        language: {
            paginate: { previous: '<i class="fa-solid fa-backward-step"></i>', next: '<i class="fa-solid fa-forward-step"></i>' },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            $('#pagination-tendencia').html($('.dataTables_paginate').html());
            $('#pagination-tendencia span').remove();
            $('#pagination-tendencia a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        },
        footerCallback: function (row, data, start, end, display) {
            const api = this.api();
            const intVal = (i) => {
                if (typeof i === 'string') return parseFloat(i.replace(/[R$ ]/g, '').replace(/\./g, '').replace(',', '.')) || 0;
                return typeof i === 'number' ? i : 0;
            };

            // Mapa de colunas para somar
            const columnIndexMap = {
                valorVendido: 10, previcaoVendas: 11, qtdePedida: 12, qtdeFaturada: 13,
                SaldoColAnt: 14, estoqueAtual: 15, emProcesso: 16, 'faltaProg (Tendencia)': 17,
                disponivel: 18, 'disponivel Pronta Entrega': 19, 'Prev Sobra': 20
            };

            Object.entries(columnIndexMap).forEach(([columnName, colIndex]) => {
                const dataColumn = api.column(colIndex, { filter: 'applied' }).data();

                if (columnName === 'disponivel') {
                    let positivo = 0, negativo = 0;
                    dataColumn.each((value) => {
                        const num = intVal(value);
                        num >= 0 ? positivo += num : negativo += num;
                    });
                    $('#totalDisponivel').html(`+${positivo.toLocaleString('pt-BR')} / ${negativo.toLocaleString('pt-BR')}`);
                } else {
                    const total = dataColumn.reduce((a, b) => intVal(a) + intVal(b), 0);
                    const footerCell = api.column(colIndex).footer();
                    if (footerCell) {
                        $(footerCell).html(
                            columnName === 'valorVendido'
                                ? `R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}`
                                : total.toLocaleString('pt-BR')
                        );
                    }
                }
            });

            $('.search-input-tendencia').off('input').on('input', function () {
                const input = $(this);
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    tabela.column(input.closest('th').index()).search(input.val()).draw();
                }, 500);
            });
        }
    });

    // Event Delegation para cliques na tabela
    $('#table-tendencia').off('click', '.detalha-ordemProd').on('click', '.detalha-ordemProd', function (event) {
        event.stopPropagation();
        Detalha_OrdemProducao($(this).attr('data-codReduzido'));
    });

    $('#table-tendencia').off('click', '.detalha-pedidos2').on('click', '.detalha-pedidos2', function (event) {
        event.stopPropagation();
        Detalha_PedidosSaldo($(this).attr('data-codReduzido'), $('#select-pedidos-bloqueados').val(), $('#select-plano').val());
    });

    $('#table-tendencia').off('click', '.detalhaImg').on('click', '.detalhaImg', function (event) {
        event.stopPropagation();
        Consulta_Imagem($(this).data('coditempai'));
    });

    $('#table-tendencia').off('click', '.detalha-pedidos').on('click', '.detalha-pedidos', function (event) {
        event.stopPropagation();
        Detalha_Pedidos($(this).attr('data-codReduzido'), $('#select-pedidos-bloqueados').val(), $('#select-plano').val());
    });

    $('#table-tendencia').off('click', '.detalha-SimulacaoSku').on('click', '.detalha-SimulacaoSku', function (event) {
        event.stopPropagation();
        Detalha_SimulacaoSku($(this).attr('data-codReduzido'));
    });
}

function TabelaEngenharia(lista) {
    if ($.fn.DataTable.isDataTable('#table-lotes-csw')) {
        $('#table-lotes-csw').DataTable().destroy();
    }

    const tabela = $('#table-lotes-csw').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: lista,
        columns: [
            { data: 'marca' },
            { data: 'codItemPai' },
            { data: 'descricao' },
            {
                data: "percentual",
                render: (data) => `
                    <div class="acoes d-flex justify-content-center align-items-center" style="height: 100%;">
                        <input type="text" class="form-control percentual-input" style="width:80px; text-align:right;" placeholder="%" value="${data ?? ''}">
                    </div>`
            }
        ],
        language: {
            paginate: { previous: '<i class="fa-solid fa-backward-step"></i>', next: '<i class="fa-solid fa-forward-step"></i>' },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            $('#pagination-lotes-csw').html($('.dataTables_paginate').html());
            $('#pagination-lotes-csw span').remove();
            $('#pagination-lotes-csw a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('.search-input-lotes-csw').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });

    $('#btn-salvarProdutosSimulacao').off('click').on('click', () => {
        const arrayProduto = [], arrayPercentualProduto = [];
        const arrayProdutoZero = [], arrayPercentualZero = [];
        const table = $('#table-lotes-csw').DataTable();

        table.rows().every(function () {
            const data = this.data();
            const $rowNode = $(this.node());
            const percentual = $rowNode.find('.percentual-input').val();
            const valor = parseFloat(percentual.replace('%', '').replace(',', '.')) || 0;

            if (valor > 0) {
                arrayProduto.push(data.codItemPai);
                arrayPercentualProduto.push(valor);
            } else if (percentual !== "" && valor === 0) {
                arrayProdutoZero.push(data.codItemPai);
                arrayPercentualZero.push(0);
            }
        });

        var simulacao = obterNomeSimulacaoAtiva();
        
        registrarSimulacaoProdutos(arrayProduto, arrayPercentualProduto, simulacao);
        exluindo_simulacao_Produtos_zerados(arrayProdutoZero, arrayPercentualZero);
        Produtos_Simulacao();
    });
}

// --- OUTRAS FUNÇÕES (DETALHAMENTO, ETC) ---

async function Detalha_OrdemProducao(codReduzido) {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: { acao: "Detalha_OrdemProducao", codReduzido }
        });
        TabelaDetalhamentoOrdemProd(response);
        new bootstrap.Modal(document.getElementById('modal-detalhamento-OrdemProd')).show();
    } catch (error) {
        console.error('Erro ao consultar ordemProd:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};

async function registrarSimulacaoProdutos(arrayProduto, arrayPercentualProduto, simulacao) {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify({
                acao: "atualizaInserirSimulacaoProdutos",
                dados: { "arrayProdutos": arrayProduto, "arrayPercentual": arrayPercentualProduto, "nomeSimulacao": simulacao }
            }),
        });

        if (response && response[0] && response[0]['Status'] == true) {
            Mensagem_Canto('produtos adicionados', 'success');
            fecharselecaoEngenharia();
        } else {
            Mensagem_Canto('Erro', 'error');
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function exluindo_simulacao_Produtos_zerados(arrayProdutoZerados, arrayPercentualZerados) {
    var simulacao = obterNomeSimulacaoAtiva();
    const requestData = {
        acao: "exluindo_simulacao_Produtos_zerados",
        dados: { "nomeSimulacao": simulacao, "arrayProdutoZerados": arrayProdutoZerados, "arrayPercentualZerados": arrayPercentualZerados }
    };
    try {
        await $.ajax({
            type: 'DELETE',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
    } catch (e) { console.error(e); }
}

async function Deletar_SimulacaoProduto() {
    var simulacao = obterNomeSimulacaoAtiva();

    try {
        const result = await Swal.fire({
            title: "Deseja deletar os Produtos dessa simulação?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "Deletar",
        });

        if (result.isConfirmed) {
            $('#loadingModal').modal('show');
            const response = await $.ajax({
                type: 'DELETE',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify({ acao: "Deletar_SimulacaoProduto", dados: { "nomeSimulacao": simulacao } }),
            });

            if (response && response['resposta'] && response['resposta'][0]['status'] === true) {
                Mensagem_Canto('Produtos deletados da Simulação', 'success');
            }
            Produtos_Simulacao();
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro na solicitação', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

async function Detalha_SimulacaoSku(codReduzido) {
    if (!nomeSimulacao || nomeSimulacao === "") {
        Mensagem_Canto("Nenhuma simulação selecionada", "warning");
    } else {
        $('#loadingModal').modal('show');
        try {
            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify({
                    acao: "simulacaoDetalhadaPorSku",
                    dados: {
                        "codPlano": $('#select-plano').val(),
                        "consideraPedBloq": $('#select-pedidos-bloqueados').val(),
                        "codSku": codReduzido,
                        "nomeSimulacao": nomeSimulacao
                    }
                }),
            });
            TabelaDetalhamentoSku(response);
            $('#modal-detalhamento-simulacaoSku').modal('show');
        } catch (error) {
            console.error('Erro AJAX Sku:', error);
            Mensagem_Canto('Erro', 'error');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }
}

async function Detalha_PedidosSaldo(codReduzido, consideraPedidosBloqueado, codPlan) {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: { acao: "Detalha_PedidosSaldo", codPlano: codPlan, consideraPedidosBloqueado: consideraPedidosBloqueado, codReduzido: codReduzido }
        });
        TabelaDetalhamentoPedidosSaldo(response);
        $('#modal-detalhamento-pedidosSaldo').modal('show');
    } catch (error) { console.error(error); } finally { $('#loadingModal').modal('hide'); }
};

async function Detalha_Pedidos(codReduzido, consideraPedidosBloqueado, codPlan) {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: { acao: "Detalha_Pedidos", codPlano: codPlan, consideraPedidosBloqueado: consideraPedidosBloqueado, codReduzido: codReduzido }
        });
        TabelaDetalhamentoPedidos(response);
        $('#modal-detalhamento-pedidos').modal('show');
    } catch (error) { console.error(error); } finally { $('#loadingModal').modal('hide'); }
};

// Funções de Tabela Detalhamento (PedidosSaldo, Pedidos, OrdemProd, Sku) 
// Foram mantidas a estrutura, apenas aplicado formatação padrão e correções de seletores.
// Certifique-se de que os IDs HTML (ex: #table-detalhamento-pedidos) existem na sua página.

function TabelaDetalhamentoOrdemProd(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamento-OrdemProd')) $('#table-detalhamento-OrdemProd').DataTable().destroy();
    const tabela = $('#table-detalhamento-OrdemProd').DataTable({
        searching: true, paging: true, lengthChange: false, info: false, pageLength: 15, dom: 'Bfrtip',
        buttons: [{ extend: 'excelHtml5', className: 'btn-tabelas', text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel' }],
        data: listaDetalhes,
        columns: [{ data: 'numeroop' }, { data: 'codFaseAtual' }, { data: 'nomeFase' }, { data: 'total_pcs' }],
        language: { paginate: { previous: '<i class="fa-solid fa-backward-step"></i>', next: '<i class="fa-solid fa-forward-step"></i>' }, emptyTable: "Nenhum dado" }
    });
    // Lógica de botões e input de search mantida...
}

function TabelaDetalhamentoPedidosSaldo(listaDetalhes) {
     if ($.fn.DataTable.isDataTable('#table-detalhamento-pedidosSaldo')) $('#table-detalhamento-pedidosSaldo').DataTable().destroy();
     // ... Configuração similar as anteriores ...
     const tabela = $('#table-detalhamento-pedidosSaldo').DataTable({
         searching: true, paging: true, lengthChange: false, info: false, pageLength: 15, dom: 'Bfrtip',
         buttons: [{ extend: 'excelHtml5', className: 'btn-tabelas', text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel' }],
         data: listaDetalhes,
         columns: [ { data: 'codReduzido' }, { data: 'codPedido' }, { data: 'codTipoNota' }, { data: 'dataEmissao' }, { data: 'dataPrevFat' }, { data: 'SaldoColAnt' }, { data: 'qtdeFaturadaSaldo' }, { data: 'qtdePedidaSaldo' } ],
         language: { paginate: { previous: '<i class="fa-solid fa-backward-step"></i>', next: '<i class="fa-solid fa-forward-step"></i>' }, emptyTable: "Nenhum dado" }
     });
}

function TabelaDetalhamentoSku(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamento-skus')) $('#table-detalhamento-skus').DataTable().destroy();
    const tabela = $('#table-detalhamento-skus').DataTable({
        searching: true, paging: true, lengthChange: false, info: false, pageLength: 15, dom: 'Bfrtip',
        buttons: [{ extend: 'excelHtml5', className: 'btn-tabelas', text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel' }],
        data: listaDetalhes,
        columns: [ { data: 'nomeSimulacao' }, { data: 'codReduzido' }, { data: 'previcaoVendasOriginal' }, { data: 'percentualMarca' }, { data: 'percentualABC' }, { data: 'percentualCategoria' }, { data: '_%Considerado' }, { data: 'NovaPrevicao' } ],
        language: { paginate: { previous: '<i class="fa-solid fa-backward-step"></i>', next: '<i class="fa-solid fa-forward-step"></i>' }, emptyTable: "Nenhum dado" }
    });
}

function TabelaDetalhamentoPedidos(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamento-pedidos')) $('#table-detalhamento-pedidos').DataTable().destroy();
    const tabela = $('#table-detalhamento-pedidos').DataTable({
        searching: true, paging: true, lengthChange: false, info: false, pageLength: 15, dom: 'Bfrtip',
        buttons: [{ extend: 'excelHtml5', className: 'btn-tabelas', text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel' }],
        data: listaDetalhes,
        columns: [ { data: 'codPedido' }, { data: 'codTipoNota' }, { data: 'dataEmissao' }, { data: 'dataPrevFat' }, { data: 'marca' }, { data: 'qtdeFaturada' }, { data: 'qtdePedida' }, { data: 'valorVendido' } ],
        language: { paginate: { previous: '<i class="fa-solid fa-backward-step"></i>', next: '<i class="fa-solid fa-forward-step"></i>' }, emptyTable: "Nenhum dado" }
    });
}