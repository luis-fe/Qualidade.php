let arrayCategoriaMP = ''
let menorSugestaoPC = null;
let nomeSimulacao = '';
let imagemAtual = 0;
let totalImagens = 0;
let totalImagensEng = 0;
let totalImagensColorBook = 0;
let codigoMP = "";
let imagensColorBook = [];


const atualizarImagem = () => {
  if (!codigoMP || String(codigoMP).trim() === "") {
    console.error("codigoMP está vazio!");
    return;
  }

  const baseURL = "http://192.168.0.183:9000";
  let url = "";

  if (imagemAtual < totalImagensColorBook) {
    url = imagensColorBook[imagemAtual];
  } else {
    const indiceEng = imagemAtual - totalImagensColorBook;
    url = `${baseURL}/imagemEng/${codigoMP}/${indiceEng}`;
  }

  $('#imagem-container').html(`
    <img src="${url}" alt="Imagem ${imagemAtual + 1}" class="img-fluid">
  `);

  $('#contador-imagens').text(`Imagem ${imagemAtual + 1} de ${totalImagens}`);
  $('#btn-anterior').prop('disabled', imagemAtual === 0);
  $('#btn-proximo').prop('disabled', imagemAtual >= totalImagens - 1);
};

const Consulta_Imagem = async (codigoPai) => {
  codigoMP = String(codigoPai);
  $('#loadingModal').modal('show');

  try {
    // 1. Inicia em paralelo: consulta total da ColorBook e da imagemEng
    const [primeiraColorBook, dataEng] = await Promise.all([
      $.ajax({
        type: 'GET',
        url: `http://10.162.0.53:9000/pcp/api/obterImagemSColorBook?codItemPai=${codigoPai}&indice=0`,
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

    // Pega totais
    totalImagensColorBook = primeiraColorBook.total_imagens || 0;
    totalImagensEng = dataEng.total_imagens || 0;

    // 2. Faz chamadas paralelas para o restante do ColorBook (índice 0+)
    const colorBookRequests = [];
    for (let i = 0; i < totalImagensColorBook; i++) {
      colorBookRequests.push(
        $.ajax({
          type: 'GET',
          url: `http://10.162.0.53:9000/pcp/api/obterImagemSColorBook?codItemPai=${codigoPai}&indice=${i}`,
          dataType: 'json'
        })
      );
    }

    const imagensColorData = await Promise.all(colorBookRequests);
    imagensColorBook = imagensColorData.map(img => img.imagem_url);

    // 3. Atualiza totais e imagem inicial
    totalImagens = totalImagensColorBook + totalImagensEng;
    imagemAtual = 0;
    atualizarImagem();

    // Garante que modal de detalhamento seja ocultado e imagem exibido
    $('#modal-imagemMP').modal('show');

  } catch (error) {
    console.error('Erro ao consultar imagens:', error);
    Mensagem_Canto('Erro', 'error');
  } finally {
    $('#loadingModal').modal('hide');
  }
};

$(document).ready(async () => {
    Consulta_Planos();
    Consulta_Simulacoes();
    carregarCheckboxes();
    $('#select-plano').select2({
        placeholder: "Selecione um plano",
        allowClear: false,
        width: '100%'
    });

    $('#select-pedidos-bloqueados').select2({
        placeholder: "Pedidos Bloqueados?",
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

    $('#btn-vendas').addClass('btn-menu-clicado')
});

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

// Limpa tudo ao fechar modal
$('#modal-imagemMP').on('hidden.bs.modal', function () {
  imagemAtual = 0;
  totalImagens = 0;
  codigoMP = "";
  $('#imagem-container').html('');
  $('#contador-imagens').text('');
});


document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});


async function simulacao(texto, tipo) {
    console.log(`Simulacao Escolhida pela formula: ${texto}`)
    fecharSimulacao();
    fecharNovaSimulacao();
    await Cadastro_Simulacao(texto, tipo);
    await Consulta_Simulacoes();
    await Simular_Programacao(texto, tipo);
    nomeSimulacao = texto;
    console.log(`nomeSimulacao: ${nomeSimulacao}`)
};

const Consulta_Planos = async () => {
    $('#loadingModal').modal('show');
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Planos'
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

async function Detalhar_Sku(codReduzido) {
    $('#loadingModal').modal('show');
    console.log('Valor da descrição da simulacao detalhado:');
    try {

        const requestData = {
            acao: "detalharSku_x_AnaliseEmpenho",

            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedBloq": $('#select-pedidos-bloqueados').val(),
                "codReduzido": codReduzido,
                "arrayCategoriaMP": arrayCategoriaMP,
                "nomeSimulacao": nomeSimulacao
            }

        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        if (response === null) {
            Mensagem_Canto("Não há dados para visualizar")
        } else {
            TabeldetalhamentoSku(response);
            $('#modal-detalhamentoSku').modal('show');
        }

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}
async function Simular_Programacao(simulacao) {
    try {
        const requestData = {
            acao: "Simular_Programacao",

            dados: {
                codPlano: $('#select-plano').val(),
                consideraPedidosBloqueado: $('#select-pedidos-bloqueados').val(),
                arrayCategoriaMP: arrayCategoriaMP || [],
                nomeSimulacao: simulacao
            }

        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        const respostaPeriodoVendas = await PeriodoVendasPlano();
        respostaPeriodoVendas.inicioVenda = formatarDataBrasileira(respostaPeriodoVendas.inicioVenda);
        respostaPeriodoVendas.finalVenda = formatarDataBrasileira(respostaPeriodoVendas.finalVenda);
        respostaPeriodoVendas.inicioFaturamento = formatarDataBrasileira(respostaPeriodoVendas.inicioFaturamento);
        respostaPeriodoVendas.finalFaturamento = formatarDataBrasileira(respostaPeriodoVendas.finalFaturamento);
        respostaPeriodoVendas.metaFinanceira = formatarMoedaBrasileira(respostaPeriodoVendas.metaFinanceira);
        respostaPeriodoVendas.metaPcs = formatarInteiro(respostaPeriodoVendas.metaPcs);
        respostaPeriodoVendas.metaFinanceira = formatarMoedaBrasileira(respostaPeriodoVendas.metaFinanceira);
        const respostaCalculo = await Consulta_Ultimo_CalculoTendencia();


        $('#titulo').html(`
            <div class="d-flex justify-content-between align-items-start w-100 p-0 m-0">
                <div>
                    <span class="span-icone"><i class="bi bi-clipboard-data-fill"></i></span> 
                    Necessidade x Pçs a Programar
                    <span style="display: inline-block; position: relative;">
                        <strong>${simulacao}</strong>
                        <button onclick="Consulta_Tendencias()" 
                                style="position: absolute; top: 0; right: -20px; border: none; background: none; font-weight: bold; color: red; cursor: pointer;">
                            ×
                        </button>
                    </span>
                </div>
                <div class="d-flex flex-column text-end periodo-vendas p-0 m-0 ms-3">
                    <div>
                        <i class="bi bi-calendar3 me-1"></i>
                        <span>Período Vendas: <strong>${respostaPeriodoVendas.inicioVenda} à ${respostaPeriodoVendas.finalVenda}</strong></span>
                    </div>
                    <div>
                        <i class="bi bi-calendar3 me-1"></i>
                        <span>Período Fatura.: <strong>${respostaPeriodoVendas.inicioFaturamento} à ${respostaPeriodoVendas.finalFaturamento}</strong></span>
                    </div>
                </div>
    <!-- Novo Card -->
    <div class="card border rounded me-1" style="width: 190px;">
      <div class="card-body p-0">
            <h5 class="card-title bg-primary text-white p-0 m-0 text-center">Meta R$</h5>
            <p class="card-text m-0">
            <strong>${respostaPeriodoVendas.metaFinanceira}</strong>
            </p>
        </div>
    </div>

  
    </div>
    <div class="card border rounded me-1" style="width: 190px;">
      <div class="card-body p-0">
        <h5 class="card-title bg-primary text-white p-0 m-0 text-center">Meta Pçs</h5>
        <p class="card-text m-0">
          <strong>${respostaPeriodoVendas.metaPcs}</strong>
        </p>
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

            $('#btn-informacoes').on('click', function () {
    
        $('.div-informacoes').removeClass('d-none');
        $('#informacaoAtualizacao')
        .find('.row h6')
        .html(`Calculado no dia: <strong>${respostaCalculo.dataHora}</strong>`);

        $('#informacaoSincronia h6').eq(1).html(
        `<i class="bi bi-database"></i> Informativo de Vendas:<strong>${respostaCalculo.dataHoraPedidos}</strong>`
        );

        
        $('#informacaoSincronia h6').eq(2).html(
        `<i class="bi bi-database"></i> Estrutura da Materia Prima por Produto:<strong>${respostaCalculo.data_horaEstruturaMP}</strong>`
        );
    
    });

    TabelaAnalise(response);

    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
    }
};

async function Cadastro_Simulacao(simulacao, tipo) {
    $('#loadingModal').modal('show');



    try {


        const categorias = [];
        const percentuais_categorias = [];

        const abcs = [];
        const percentuais_abc = [];

        const marcas = [];
        const percentuais_marca = [];

        if (tipo === "cadastro") {
            $('.input-categoria-2').each(function () {
                const categoria = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (categoria && !isNaN(percentual)) {
                    categorias.push(categoria);
                    percentuais_categorias.push(percentual);
                }
            });

            $('.input-abc-2').each(function () {
                const abc = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (abc && !isNaN(percentual)) {
                    abcs.push(abc);
                    percentuais_abc.push(percentual);
                }
            });

            $('.input-marca-nova').each(function () {
                const marca = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (marca && !isNaN(percentual)) {
                    marcas.push(marca);
                    percentuais_marca.push(percentual);
                }
            });
        } else {
            $('.input-categoria').each(function () {
                const categoria = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (categoria && !isNaN(percentual)) {
                    categorias.push(categoria);
                    percentuais_categorias.push(percentual);
                }
            });

            $('.input-abc').each(function () {
                const abc = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (abc && !isNaN(percentual)) {
                    abcs.push(abc);
                    percentuais_abc.push(percentual);
                }
            });

            $('.input-marca').each(function () {
                const marca = $(this).attr('id');
                const percentual = parseFloat($(this).val().replace(',', '.'));

                if (marca && !isNaN(percentual)) {
                    marcas.push(marca);
                    percentuais_marca.push(percentual);
                }
            });
        }


        const requestData = {
            acao: "Cadastro_Simulacao",
            dados: {
                "nomeSimulacao": simulacao,
                arrayAbc: [
                    abcs,
                    percentuais_abc

                ],
                arrayCategoria: [
                    categorias,
                    percentuais_categorias
                ],
                arrayMarca: [
                    marcas,
                    percentuais_marca
                ]
            }
        };
        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
};

const Consulta_Abc_Plano = async (padrão) => {
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

        const inputsContainer = $('#inputs-container');
        inputsContainer.empty();
        const inputsContainerNova = $('#inputs-container-nova');
        inputsContainerNova.empty();

        data[0]['3- Detalhamento:'].forEach((item) => {
            const inputHtml1 = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeABC}</label>
                    <input type="text" class="inputs-percentuais input-abc col-12" id="${item.nomeABC}" placeholder="%">
                </div>
            `;

            const inputHtml2 = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeABC}</label>
                    <input type="text" class="inputs-percentuais input-abc-2 col-12" value=("0,00%") id="${item.nomeABC}" placeholder="%">
                </div>
            `;

            inputsContainer.append(inputHtml1);
            inputsContainerNova.append(inputHtml2);

            }
        );

        

        $('.input-abc').mask("##0,00%", {
            reverse: true
        });

        $('.input-abc-2').mask("##0,00%", {
            reverse: true
        });

    
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

const Consulta_Abc = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Abc',
            }
        });

        const inputsContainer = $('#inputs-container');
        inputsContainer.empty();
        const inputsContainerNova = $('#inputs-container-nova');
        inputsContainerNova.empty();


        data.forEach((item) => {
            const inputHtml = `
                <div class="col-md-3 mb-3">
                    <label class="form-label">${item.nomeABC}</label>
                    <input type="text" class="inputs-percentuais input-abc col-12" id="${item.nomeABC}" placeholder="%">
                </div>
            `;
            inputsContainer.append(inputHtml);
        });

        $('.input-abc').mask("##0,00%", {
            reverse: true
        });
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
            data: {
                acao: 'Consulta_Categorias',
            }
        });

        const inputsContainer = $('#inputs-container-categorias');
        inputsContainer.empty();
        const inputsContainerNova = $('#inputs-container-categorias-nova');
        inputsContainerNova.empty();

        data.forEach((item) => {
            const inputHtml1 = `
    <div class="col-md-3 mb-3">
        <label class="form-label">${item.nomeCategoria}</label>
        <input type="text" class="inputs-percentuais input-categoria col-12" id="${item.nomeCategoria}" placeholder="%">
    </div>
`;

            const inputHtml2 = `
    <div class="col-md-3 mb-3">
        <label class="form-label">${item.nomeCategoria}</label>
        <input type="text" class="inputs-percentuais input-categoria-2 col-12" id="${item.nomeCategoria}-2" value="100,00%" placeholder="%">
    </div>
`;
            inputsContainer.append(inputHtml1);
            inputsContainerNova.append(inputHtml2);
        });

        $('.input-categoria').mask("##0,00%", {
            reverse: true
        });

        $('.input-categoria-2').mask("##0,00%", {
            reverse: true
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }
};

async function Consulta_Simulacoes() {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Simulacoes',
        },
        success: function (data) {
            $('#select-simulacao').empty();
            $('#select-simulacao').append('<option value="" disabled selected>Selecione uma simulação...</option>');
            data.forEach(function (item) {
                $('#select-simulacao').append(`
                        <option value="${item['nomeSimulacao']}">
                            ${item['nomeSimulacao']}
                        </option>
                    `);
            });
            $('#loadingModal').modal('hide');
            const descricao = $('#descricao-simulacao').val();
            console.log(`Simulacao escolhida: ${descricao}`)
            $('#select-simulacao').val(descricao);
        },

        error: function (xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
            $('#loadingModal').modal('hide');
        }
    });
}

const Consulta_Ultimo_Calculo = async () => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Ultimo_Calculo',
                plano: $('#select-plano').val()
            }
        });
        return {
            status: data[0]['status'],
            mensagem: data[0]['Mensagem']
        };


    } catch (error) {
        console.error('Erro ao consultar planos:', error);
        return null; // ou algum valor padrão indicando erro

    }
};

async function Selecionar_Calculo() {
    const respostaCalculo = await Consulta_Ultimo_Calculo();

    if (respostaCalculo.status === null) {
        Analise_Materiais(false);
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

        // Aguarda o modal fechar visualmente
        setTimeout(() => {
            if (result.isConfirmed) {
                Analise_Materiais(false);
            } else {
                Analise_Materiais(true);
            }
        }, 300); // Tempo suficiente para animação de fechamento
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro na solicitação', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}



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

        if (!data) {
            Mensagem_Canto('Não possui simulação para editar', 'warning');
            $('#modal-simulacao').modal('hide');
            return;
        }

        const campos = ["2- ABC", "3- Categoria", "4- Marcas"];
        campos.forEach(campo => {
            if (data[0][campo]) {
                data[0][campo].forEach(item => {
                    const key = item.class || item.categoria || item.marca;
                    const input = $(`#${key.replace(/\s+/g, '-').replace(/[^\w-]/g, '')}`);
                    if (input.length) {
                        input.val(`${parseFloat(item.percentual).toFixed(1).replace('.', ',')}%`);
                    }
                });
            }
        });
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
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
        return {
            inicioVenda: data[0]['03- Inicio Venda'],
            finalVenda: data[0]['04- Final Venda'],
            inicioFaturamento: data[0]['05- Inicio Faturamento'],
            finalFaturamento: data[0]['06- Final Faturamento'],
            metaFinanceira: data[0]['12-metaFinanceira'],
            metaPcs: data[0]['13-metaPecas']

        };


    } catch (error) {
        console.error('Erro ao consultar planos:', error);
        return null; // ou algum valor padrão indicando erro

    }
};

async function Analise_Materiais() {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Analise_Materiais",
            dados: {
                codPlano: $('#select-plano').val(),
                consideraPedidosBloqueado: $('#select-pedidos-bloqueados').val(),
                arrayCategoriaMP: arrayCategoriaMP || [],
                nomeSimulacao
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        TabelaAnalise(response);
        $('.div-analise').removeClass('d-none');
        const respostaPeriodoVendas = await PeriodoVendasPlano();
        respostaPeriodoVendas.inicioVenda = formatarDataBrasileira(respostaPeriodoVendas.inicioVenda);
        respostaPeriodoVendas.finalVenda = formatarDataBrasileira(respostaPeriodoVendas.finalVenda);
        respostaPeriodoVendas.inicioFaturamento = formatarDataBrasileira(respostaPeriodoVendas.inicioFaturamento);
        respostaPeriodoVendas.finalFaturamento = formatarDataBrasileira(respostaPeriodoVendas.finalFaturamento);
        respostaPeriodoVendas.metaFinanceira = formatarMoedaBrasileira(respostaPeriodoVendas.metaFinanceira);

        $('#titulo').html(`
<div class="d-flex justify-content-between align-items-start w-100 p-0 m-0">

    <!-- Título -->
    <div class="ms-2">
        <span class="span-icone"><i class="bi bi-clipboard-data-fill"></i></span> 
        Necessidade x Pçs a Programar
    </div>

    <!-- Períodos -->
    <div class="d-flex flex-column text-end periodo-vendas p-0 me-10">
        <div>
            <i class="bi bi-calendar3 me-1"></i>
            <span>Período Vendas:<strong> ${respostaPeriodoVendas.inicioVenda} à ${respostaPeriodoVendas.finalVenda}</strong></span>
        </div>
        <div>
            <i class="bi bi-calendar3 me-1"></i>
            <span>Período Fatura.:<strong> ${respostaPeriodoVendas.inicioFaturamento} à ${respostaPeriodoVendas.finalFaturamento}</strong></span>
        </div>
    </div>
    <!-- Novo Card -->
    <div class="card border rounded me-1" style="width: 190px;">
      <div class="card-body p-0">
        <h5 class="card-title bg-primary text-white p-0 m-0 text-center">Meta R$</h5>
        <p class="card-text m-0">
          <strong>${respostaPeriodoVendas.metaFinanceira}</strong>
        </p>
      </div>
  
    </div>
</div>            
          `);
        nomeSimulacao = "";
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}

const categoriasMP = [
    "-", "PRODUTO REVENDA","ITENS CAMISARIA","TAGS/TRAVANEL","CADARCO/CORDAO", "ELASTICOS", "ENTRETELA", "ETIQUETAS",
    "GOLAS", "MALHA", "MOLETOM", "RIBANA", "TECIDO PLANO", "ZIPER"
];

function carregarCheckboxes() {
    const container = document.getElementById('categoriaCheckboxes');
    container.innerHTML = ''; // limpa checkboxes anteriores
    categoriasMP.forEach((categoria, index) => {
        const checkbox = document.createElement('div');
        checkbox.className = 'form-check';

        checkbox.innerHTML = `
        <input class="form-check-input" type="checkbox" value="${categoria}" id="categoria${index}">
        <label class="form-check-label" for="categoria${index}">
          ${categoria}
        </label>
      `;
        container.appendChild(checkbox);
    });
}

function confirmarCategoria() {
    arrayCategoriaMP = $('#categoriaCheckboxes input:checked').map((_, el) => el.value).get();
    Selecionar_Calculo();
    $('#modal-categoria').modal('hide');
}


function formatarMoedaBrasileira(valor) {
    // Garante que seja número
    const numero = parseFloat(valor);
    
    return numero.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });
}


async function Detalha_Necessidade(codReduzido) {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Detalha_Necessidade",
            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                "codComponente": codReduzido
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        TabelaDetalhamento(response);
        $('#modal-detalhamento').modal('show')
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}


function formatarDataBrasileira(dataISO) {
    if (!dataISO || !dataISO.includes('-')) return dataISO; // fallback seguro
    const [ano, mes, dia] = dataISO.split('-');
    return `${dia}/${mes}/${ano}`;
}

async function TabelaAnalise(listaAnalise) {
    if ($.fn.DataTable.isDataTable('#table-analise')) {
        $('#table-analise').DataTable().destroy();
    }

    const tabela = $('#table-analise').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaAnalise,
        dom: 'Bfrtip',
        buttons: [{
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
            title: 'Necessidade de Materiais',
            className: 'btn-tabelas'
        },
        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Selecionar Categoria MP.',
            title: 'Selecionar Categoria MP.',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                $('#modal-categoria').modal('show');

            },
        },
        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Simulação',
            title: 'Simulação',
            className: 'btn-tabelas',
                 action: async function (e, dt, node, config) {
                $('.div-simulacao').removeClass('d-none');
                $('#campo-simulacao').removeClass('d-none');

                const simulacaoValue = $('#select-simulacao').val()?.trim() || "";
                console.log(`Simulacao do teste ao clicar no modal de simulacao: ${simulacaoValue}`)
                Produtos_Simulacao();


                if (simulacaoValue === "") {
                    $('#inputs-container-categorias').empty();
                    $('#inputs-container').empty();
                    $('#inputs-container-marcas').addClass('d-none')
                    Produtos_Simulacao();

                } else {
                   // await Consulta_Abc_Plano();
                   // await Consulta_Categorias();
                    $('#inputs-container-marcas').removeClass('d-none')
                    $('#inputs-container-categorias').removeClass('d-none')
                    Produtos_Simulacao();



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
                document.getElementById("TituloSelecaoEngenharias2").textContent = ""
            let campo = document.getElementById("descricao-simulacao");
                campo.value = ""; // limpa o campo
                campo.placeholder = "Insira a descrição"; // coloca placeholder            
            },
 
        },
        ],
        columns: [{
            data: 'categoria'
        },
        {
            data: 'marca'
        },
        {
            data: 'codEngenharia',

        },
        {
            data: 'codReduzido',

        },
        {
            data: 'nome',
        },
        {
            data: 'codCor',

        },
        {
            data: 'tam',

        },
        {
            data: 'faltaProg (Tendencia)',

        },
        {
            data: 'Sugestao_PCs',
            render: function (data, type, row) {
                return `<span class="detalhamentoSku" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
            }

        },
        {
            data: 'disponivel'
        }
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            $('#pagination-analise').html($('.dataTables_paginate').html());
            $('#pagination-analise span').remove();
            $('#pagination-analise a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        },footerCallback: function (row, data, start, end, display) {
                const api = this.api();

                const intVal = (i) => {
                    if (typeof i === 'string') {
                        return parseFloat(i.replace(/[R$\s]/g, '').replace(/\./g, '').replace(',', '.')) || 0;
                    }
                    return typeof i === 'number' ? i : 0;
                };

                const columnsToSum = [7,8]; // mantém o outro totalizador (faltaProg, por exemplo)
                const disponivelColIndex = 9; // índice da coluna "disponivel"

                columnsToSum.forEach(colIndex => {
                    const total = api
                        .column(colIndex, { filter: 'applied' })
                        .data()
                        .reduce((a, b) => intVal(a) + intVal(b), 0);

                    $(api.column(colIndex).footer()).html(
                        Math.round(total).toLocaleString('pt-BR') // Arredondando para inteiro
                    );
                });

                // Lógica separada para coluna "disponivel"
                let totalNegativos = 0;
                let totalPositivos = 0;

                api.column(disponivelColIndex, { filter: 'applied' }).data().each(function (value) {
                    const val = intVal(value);
                    if (val < 0) {
                        totalNegativos += val;
                    } else {
                        totalPositivos += val;
                    }
                });

                const formattedNeg = Math.round(totalNegativos).toLocaleString('pt-BR');
                const formattedPos = Math.round(totalPositivos).toLocaleString('pt-BR');

                $(api.column(disponivelColIndex).footer()).html(
                    `<span style="color: red;">${formattedNeg}</span> / <span style="color: green;">+${formattedPos}</span>`
                );

            },
    });

    $('.search-input-analise').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });

    $('#itens-analise').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('#table-analise').on('click', '.detalhamentoSku', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codReduzido');
        Detalhar_Sku(codReduzido);
    });
}



function TabelaDetalhamento(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamento')) {
        $('#table-detalhamento').DataTable().destroy();
    }

    const tabela = $('#table-detalhamento').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaDetalhes,
        columns: [{
            data: '01-codEngenharia'
        },
        {
            data: '04-tam'
        },
        {
            data: '05-codCor'
        },
        {
            data: '03-nome'
        },
        {
            data: '02-codReduzido'
        },
        {
            data: '07-Ocorrencia em Pedidos',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: '09-previcaoVendas',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: '06-qtdePedida',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: '10-faltaProg (Tendencia)',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'class'
        },
        {
            data: 'classCategoria'
        },
        {
            data: '08-statusAFV'
        },
        {
            data: '11-CodComponente'
        },
        {
            data: '12-unid'
        },
        {
            data: '13-consumoUnit'
        },
        {
            data: '14-Necessidade faltaProg (Tendencia)',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            $('#pagination-detalhamento').html($('.dataTables_paginate').html());
            $('#pagination-detalhamento span').remove();
            $('#pagination-detalhamento a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('#itens-detalhamento').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-detalhamento').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

async function Deletar_Simulacao() {

    try {
        const result = await Swal.fire({
            title: "Deseja deletar a simulação?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "Deletar",
        });

        if (result.isConfirmed) {
            $('#loadingModal').modal('show');

            const dados = {
                "nomeSimulacao": $('#select-simulacao').val(),
            };
            const requestData = {
                acao: "Deletar_Simulacao",
                dados: dados
            };
            const response = await $.ajax({
                type: 'DELETE',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });

            console.log(response)

            if (response['resposta'][0]['status'] === true) {
                Mensagem_Canto('Simulação deletada', 'success');
                Consulta_Simulacoes();
                $('#modal-simulcao').modal('hide')
            }
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro na solicitação', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

function TabeldetalhamentoSku(listaDetalhes) {

if (listaDetalhes.length > 0) {
    const cod = listaDetalhes[0]["codReduzido"] || "Sem código";
    const cod2 = listaDetalhes[0]["codItemPai"] || "Sem código";
    const cod3 = listaDetalhes[0]["nome"] || "Sem código";

    // Cria o link que chama a função Consulta_Imagem
    const link = `<a href="#" onclick="Consulta_Imagem('${cod2}'); return false;">${cod2}</a>`;

    document.getElementById("titulo-detalhamento").innerHTML = `Detalhamento: ${link} ${cod3} (${cod})`;
} else {
    document.getElementById("titulo-detalhamento").textContent = "Detalhamento: (Sem dados)";
}


    if ($.fn.DataTable.isDataTable('#table-detalhamentoSku')) {
        $('#table-detalhamentoSku').DataTable().destroy();
    }

    let valoresNumericos = listaDetalhes
        .map(l => {
            let valor = l.Sugestao_PCs;

            // Converte "1.234,56" → 1234.56 (caso venha formatado como string)
            if (typeof valor === 'string') {
                valor = valor.replace(/\./g, '').replace(',', '.');
            }

            return parseFloat(valor);
        })
        .filter(v => !isNaN(v));

    let menorSugestaoPC = Math.min(
        ...listaDetalhes
            .filter(l => l.obs === "Restringe") // <-- só valores "Restringe"
            .map(l => parseFloat(l.Sugestao_PCs))
            .filter(v => !isNaN(v))
    );
    const tabela = $('#table-detalhamentoSku').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 15,
        dom: 'Bfrtip', // <-- necessário para os botões aparecerem
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
                title: 'Detalhamento Cálculo do SKU',
                className: 'btn-tabelas',
                exportOptions: {
                    columns: ':visible',
                    format: {
                        body: function (data, row, column, node) {
                            if (typeof data === 'string') {
                                return data.replace(/\./g, '').replace(',', '.');
                            }
                            return data;
                        }
                    }
                }
            }
        ],
        data: listaDetalhes,
        columns: [
            {data:'codEditado'},
            { data: 'CodComponente' },
            { data: 'descricaoComponente' },
            {
                data: 'estoqueAtualMP',
                render: function (data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return new Intl.NumberFormat('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(data);
                    }
                    // Para ordenação e exportação, retorna o valor original numérico
                    return parseFloat(data);
                }
            },
            {
                data: 'EmRequisicao',
                render: function (data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return new Intl.NumberFormat('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(data);
                    }
                    // Para ordenação e exportação, retorna o valor original numérico
                    return parseFloat(data);
                }
            },
            {
                data: 'EstoqueAtualMPLiquido',
                render: function (data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return new Intl.NumberFormat('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(data);
                    }
                    // Para ordenação e exportação, retorna o valor original numérico
                    return parseFloat(data);
                }
            },
            {
                data: 'faltaProg (Tendencia)MP_total',
                render: function (data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return new Intl.NumberFormat('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(data);
                    }
                    // Para ordenação e exportação, retorna o valor original numérico
                    return parseFloat(data);
                }
            },
            {
                data: 'EstoqueDistMP',
                render: function (data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return new Intl.NumberFormat('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }).format(data);
                    }
                    // Para ordenação e exportação, retorna o valor original numérico
                    return parseFloat(data);
                }
            },
            { data: 'faltaProg (Tendencia)' },
            {
                data: 'Sugestao_PCs',
                render: function (data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return new Intl.NumberFormat('pt-BR', {
                            minimumFractionDigits: 0,
                            maximumFractionDigits: 0
                        }).format(data);
                    }
                    return parseFloat(data);
                }
            },
            { data: 'obs' },

        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
        drawCallback: function () {
            $('#pagination-detalhamentoSku').html($('.dataTables_paginate').html());
            $('#pagination-detalhamentoSku span').remove();
            $('#pagination-detalhamentoSku a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        },


        rowCallback: function (row, data) {
            let valorLinha = data.Sugestao_PCs;

            if (typeof valorLinha === 'string') {
                valorLinha = valorLinha.replace(/\./g, '').replace(',', '.');
            }

            valorLinha = parseFloat(valorLinha);

            const isRestricao = data.obs === "Restringe";

            if (!isNaN(valorLinha) && isRestricao && Math.abs(valorLinha - menorSugestaoPC) < 0.001) {
                $(row).addClass('linha-destacada');
            }
        }

    });


    $('#itens-detalhamentoSku').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-detalhamentoSku').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function fecharSimulacao() {
    document.getElementById("simulacao-container").classList.add("d-none");
}


function fecharNovaSimulacao() {
    document.getElementById("nova-simulacao-container").classList.add("d-none");
}


function fecharselecaoEngenharia() {
    document.getElementById("modal-selecaoEngenharias").classList.add("d-none");
}


async function Produtos_Simulacao() {

    var simulacao = $('#select-simulacao').val()

        if ($('#select-simulacao').is(':visible')) {
        console.log("Tá aparecendo! 👀");
    } else {
        simulacao = $("#descricao-simulacao").val();
    }


   
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

        console.log(data)
        console.log(data[0].mensagem);

        document.getElementById("TituloSelecaoEngenharias").textContent = data[0].mensagem;
        document.getElementById("TituloSelecaoEngenharias2").textContent = data[0].mensagem;


    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally {
                
        console.log('atualizado produtos da selecacao');

    }

    
}

const Consulta_Engenharias = async () => {
    $('#loadingModal').modal('show');
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: "obter_produtos_tendencia",
                codPlano: $('#select-plano').val(),
                nomeSimulacao:  $('#select-simulacao').val()
            }
        });
        $('.div-selecaoEngenharias').removeClass('d-none');
        TabelaEngenharia(data);

    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally {
        $('#loadingModal').modal('hide');
        
    }
};

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
        {
            data: 'marca'
        },
        {
            data: 'codItemPai'
        },
                {
            data: 'descricao'
        },
                {
             data: "percentual",
                    render: (data, type, row) => `
                        <div class="acoes d-flex justify-content-center align-items-center" style="height: 100%;">
                            <input type="text" 
                                class="form-control percentual-input" 
                                style="width:80px; text-align:right;" 
                                placeholder="%" 
                                value="${data ?? ''}">
                        </div>`
        }
        ],
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
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
    const arrayProduto = [];
    const arrayPercentualProduto = [];

    const arrayProdutoZero = [];
    const arrayPercentualZero = [];

    // Pega instância do DataTable
    const table = $('#table-lotes-csw').DataTable();

    // Percorre todas as linhas visíveis
    table.rows().every(function () {
        const data = this.data(); // dados da linha

        // acha o input dentro dessa linha
        const $rowNode = $(this.node());
        const percentual = $rowNode.find('.percentual-input').val();

        // transforma em número (ignora símbolo % e vírgula)
        const valor = parseFloat(percentual.replace('%','').replace(',','.')) || 0;

        if (valor > 0) {
            // exemplo: supondo que o código do produto esteja na coluna 1
            const codProduto = data.codItemPai; 
            
            arrayProduto.push(codProduto);
            arrayPercentualProduto.push(valor);
        } 
        else if (percentual !== "" && valor === 0) {
            const codProduto = data.codItemPai; 
            // capturar os que foram zerados manualmente
            arrayProdutoZero.push(codProduto);
            arrayPercentualZero.push(0);
        }
    });

    console.log("Produtos:", arrayProduto);
    console.log("Percentuais:", arrayPercentualProduto);
    const simulacao = $('#select-simulacao').val()
    
    registrarSimulacaoProdutos(arrayProduto, arrayPercentualProduto, simulacao)
    exluindo_simulacao_Produtos_zerados(arrayProdutoZero, arrayPercentualZero)
    Produtos_Simulacao();

    }
);

}


async function registrarSimulacaoProdutos(arrayProduto, arrayPercentualProduto, simulacao) {
        $('#loadingModal').modal('show');

    try{
             const requestData = {
            acao: "atualizaInserirSimulacaoProdutos",
            dados: {
                "arrayProdutos": arrayProduto,
                "arrayPercentual": arrayPercentualProduto,
                "nomeSimulacao": simulacao
            }

        };

            const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        if (response[0]['Status'] == true) {
            $('#loadingModal').modal('hide');
            Mensagem_Canto('produtos adicionados', 'success');
            fecharselecaoEngenharia();        
        } else {
            Mensagem_Canto('Erro', 'error'); 
        }
        
        
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
    
}

async function exluindo_simulacao_Produtos_zerados(arrayProdutoZerados, arrayPercentualZerados) {

            const dados = {
                "nomeSimulacao": $('#select-simulacao').val(),
                "arrayProdutoZerados": arrayProdutoZerados,
                "arrayPercentualZerados": arrayPercentualZerados,
            };
            const requestData = {
                acao: "exluindo_simulacao_Produtos_zerados",
                dados: dados
            };
            const response = await $.ajax({
                type: 'DELETE',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });

            console.log(response)

    
}


async function Deletar_SimulacaoProduto() {

    try {
        const result = await Swal.fire({
            title: "Deseja deletar os Produtos dessa simulação?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: "Deletar",
        });

        if (result.isConfirmed) {
            $('#loadingModal').modal('show');

            const dados = {
                "nomeSimulacao": $('#select-simulacao').val(),
            };
            const requestData = {
                acao: "Deletar_SimulacaoProduto",
                dados: dados
            };
            const response = await $.ajax({
                type: 'DELETE',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });

            console.log(response)

            if (response['resposta'][0]['status'] === true) {
                Mensagem_Canto('Produtos  deletados da Simulação', 'success');
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


async function registrarSimulacaoProdutos(arrayProduto, arrayPercentualProduto, simulacao) {
        $('#loadingModal').modal('show');

    try{
             const requestData = {
            acao: "atualizaInserirSimulacaoProdutos",
            dados: {
                "arrayProdutos": arrayProduto,
                "arrayPercentual": arrayPercentualProduto,
                "nomeSimulacao": simulacao
            }

        };

            const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        if (response[0]['Status'] == true) {
            $('#loadingModal').modal('hide');
            Mensagem_Canto('produtos adicionados', 'success');
            fecharselecaoEngenharia();        
        } else {
            Mensagem_Canto('Erro', 'error'); 
        }
        
        
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
    
}


function formatarInteiro(valor) {
    // Garante que seja número inteiro
    const numero = parseInt(valor);

    // Verifica se a conversão foi bem-sucedida
    if (isNaN(numero)) {
        return "Valor inválido";
    }

    // Formata como número inteiro no padrão pt-BR
    return numero.toLocaleString("pt-BR");
}


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
        return {
            status: data[0]['status'],
            mensagem: data[0]['Mensagem'],
            dataHora: data[0]['dataHora'],
            dataHoraPedidos: data[0]['dataHoraPedidos'],
            data_horaEstruturaMP: data[0]['data_horaEstruturaMP'],
 
        };


    } catch (error) {
        console.error('Erro ao consultar planos:', error);
        return null; // ou algum valor padrão indicando erro

    }
};

function fecharInformacoes() {
    document.getElementById("informacoes-container").classList.add("d-none");
}