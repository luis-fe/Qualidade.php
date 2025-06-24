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
        url: `http://192.168.0.183:9000/pcp/api/obterImagemSColorBook?codItemPai=${codigoPai}&indice=0`,
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

    // 2. Faz chamadas paralelas para os restantes do ColorBook (índice 1+)
    const colorBookRequests = [];
    for (let i = 0; i < totalImagensColorBook; i++) {
      colorBookRequests.push(
        $.ajax({
          type: 'GET',
          url: `http://192.168.0.183:9000/pcp/api/obterImagemSColorBook?codItemPai=${codigoPai}&indice=${i}`,
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

    $('#loadingModal').modal('hide');
    $('#modal-imagemMP').modal('show');
  } catch (error) {
    console.error('Erro ao consultar imagens:', error);
    Mensagem_Canto('Erro', 'error');
    $('#loadingModal').modal('hide');
  }
};





$(document).ready(async () => {
    Consulta_Planos();
    Consulta_Simulacoes();
    $('#select-plano').select2({
        placeholder: "Selecione um plano",
        allowClear: false,
        width: '100%'
    });

    console.log('teste')

    $('#select-simulacao').select2({
        placeholder: "Selecione uma simulação",
        allowClear: false,
        width: '100%',
        dropdownParent: $('#modal-simulacao')
    });

    // Vincula evento ao clique do botão
    document.getElementById("ConfPedidosSaldo").addEventListener("click", function () {
      Detalha_PedidosGeral();
    });


    $('#select-simulacao').on('change', async function () {
        $('#inputs-container-marcas').removeClass('d-none')
        await Consulta_Abc_Plano();
        await Consulta_Categorias()
        await Consulta_Simulacao_Especifica();
    });

    $('#select-pedidos-bloqueados').select2({
        placeholder: "Pedidos Bloqueados?",
        allowClear: false,
        width: '100%'
    });

    $('#btn-vendas').addClass('btn-menu-clicado');



    
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


});


let nomeSimulacao = ''
async function simulacao(texto, tipo) {
    $('#modal-simulacao').modal('hide');
    $('#modal-nova-simulacao').modal('hide');
    await Cadastro_Simulacao(texto, tipo);
    await Consulta_Simulacoes();
    await Simular_Programacao(texto);
    nomeSimulacao = texto
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
            console.log(descricao)
            $('#select-simulacao').val(descricao);
        },

        error: function (xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
            $('#loadingModal').modal('hide');
        }
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
                $('#modal-simulacao').modal('hide')
            }
        }
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro na solicitação', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}


function Consulta_Planos() {
    $('#loadingModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'requests.php',
        dataType: 'json',
        data: {
            acao: 'Consulta_Planos',
        },
        success: function (data) {
            $('#select-plano').empty();
            $('#select-plano').append('<option value="" disabled selected>Selecione um plano...</option>');
            data.forEach(function (plano) {
                $('#select-plano').append(`
                        <option value="${plano['01- Codigo Plano']}">
                            ${plano['01- Codigo Plano']} - ${plano['02- Descricao do Plano']}
                        </option>
                    `);
            });
            $('#loadingModal').modal('hide');
        },
        error: function (xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
            $('#loadingModal').modal('hide');
        }
    });
}

async function Consulta_Tendencias() {
    const respostaCalculo = await Consulta_Ultimo_CalculoTendencia();

        if (respostaCalculo.status === null) {
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

        // Aguarda o modal fechar visualmente
        setTimeout(() => {
            if (result.isConfirmed) {
                gerarTendenciaNova(false);
            } else {
                gerarTendenciaNova(true);
            }
        }, 300); // Tempo suficiente para animação de fechamento
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem('Erro na solicitação', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }



};

async function gerarTendenciaNova (congelamento) {
      $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Consulta_Tendencias",

            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                "congelar":congelamento
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
        respostaPeriodoVendas.inicioVenda = formatarDataBrasileira(respostaPeriodoVendas.inicioVenda);
        respostaPeriodoVendas.finalVenda = formatarDataBrasileira(respostaPeriodoVendas.finalVenda);
        respostaPeriodoVendas.inicioFaturamento = formatarDataBrasileira(respostaPeriodoVendas.inicioFaturamento);
        respostaPeriodoVendas.finalFaturamento = formatarDataBrasileira(respostaPeriodoVendas.finalFaturamento);

        $('#titulo').html(`
    <div class="d-flex justify-content-between align-items-start w-100 p-0 m-0">
        <div>
            <span class="span-icone"><i class="bi bi-clipboard-data-fill"></i></span> 
            Tendência de Vendas
        </div>
    <div class="d-flex flex-column text-end periodo-vendas p-0 m-0">
            <div>
                <i class="bi bi-calendar3 me-1"></i>
                <span>Período Vendas:<strong> ${respostaPeriodoVendas.inicioVenda} à ${respostaPeriodoVendas.finalVenda}</strong></span>
            </div>
            <div>
                <i class="bi bi-calendar3 me-1"></i>
                <span>Período Fatura. :<strong> ${respostaPeriodoVendas.inicioFaturamento} à ${respostaPeriodoVendas.finalFaturamento}</strong></span>
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
        document.getElementById("ConfPedidosSaldo").classList.remove("d-none");
    }
    
};

async function Simular_Programacao(simulacao) {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Simular_Programacao",

            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                "nomeSimulacao": simulacao
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
    <div class="d-flex flex-column text-end periodo-vendas p-0 m-0">
            <div>
                <i class="bi bi-calendar3 me-1"></i>
                <span>Período Vendas:<strong> ${respostaPeriodoVendas.inicioVenda} à ${respostaPeriodoVendas.finalVenda}</strong></span>
            </div>
            <div>
                <i class="bi bi-calendar3 me-1"></i>
                <span>Período Fatura. :<strong> ${respostaPeriodoVendas.inicioFaturamento} à ${respostaPeriodoVendas.finalFaturamento}</strong></span>
            </div>
        </div>
    </div>
          `);
        TabelaTendencia(response);
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
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

const Consulta_Abc_Plano = async () => {
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
                    <input type="text" class="inputs-percentuais input-abc-2 col-12" value=("0,00%") id="nova-${item.nomeABC}" placeholder="%">
                </div>
            `;
            inputsContainer.append(inputHtml1);
            inputsContainerNova.append(inputHtml2);
        });

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
        <input type="text" class="inputs-percentuais input-categoria-2 col-12" id="${item.nomeCategoria}-2" placeholder="%">
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



function TabelaTendencia(listaTendencia) {
    if ($.fn.DataTable.isDataTable('#table-tendencia')) {
        $('#table-tendencia').DataTable().destroy();
    }

    const tabela = $('#table-tendencia').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
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
                    body: function (data, row, column, node) {
                        if (typeof data === 'string') {
                            // Remove qualquer tag HTML (ex: <span>, <i>, etc.)
                            const textoSemHtml = data.replace(/<[^>]*>?/gm, '');
                            // Substitui pontos por vazio e vírgula por ponto (ex: 1.234,56 → 1234.56)
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
                $('#modal-simulacao').modal('show');
                $('#campo-simulacao').removeClass('d-none');

                const simulacaoValue = $('#select-simulacao').val()?.trim() || "";

                if (simulacaoValue === "") {
                    $('#inputs-container-categorias').empty();
                    $('#inputs-container').empty();
                    $('#inputs-container-marcas').addClass('d-none')
                } else {
                    await Consulta_Abc_Plano();
                    await Consulta_Categorias();
                    $('#inputs-container-marcas').removeClass('d-none')
                }
            }
        },
        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Nova Simulação',
            title: 'Nova Simulação',
            className: 'btn-tabelas',
            action: async function (e, dt, node, config) {
                $('#modal-nova-simulacao').modal('show');
                $('#inputs-container-novas-marcas').removeClass('d-none');
                await Consulta_Abc_Plano();
                await Consulta_Categorias();
            },
        },
        ],
        columns: [{
            data: 'marca'
        },
        {
            data: 'codItemPai',
                        render: function (data, type, row) {
                return `<span class="detalhaImg" data-codItemPai="${row.codItemPai}" style="text-decoration: underline; color: #747474; cursor: pointer;">${data}</span>`;
            }
        },
        {
            data: 'tam'
        },
        {
            data: 'codCor'
        },
        {
            data: 'nome'
        },
        {
            data: 'codReduzido'
        },
        {
            data: 'categoria'
        },
        {
            data: 'class'
        },
        {
            data: 'classCategoria'
        },
        {
            data: 'Ocorrencia em Pedidos',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'valorVendido',
            render: function (data, type) {
                let ValorInt = parseFloat(data.replace(/[^\d,]/g, '').replace(',', '.'));
                return type === 'display' ?
                    `R$ ${ValorInt.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` :
                    ValorInt;
            }
        },
        {
            data: 'previcaoVendas',
            render: function (data, type, row) {
                return `<span class="detalha-SimulacaoSku" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;

            }
        },
        {
            data: 'qtdePedida',
            render: function (data, type, row) {
                return `<span class="detalha-pedidos" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;

            }
        },
        {
            data: 'qtdeFaturada',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'SaldoColAnt',
            render: function (data, type, row) {
                return `<span class="detalha-pedidos2" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;

            }
        },
        {
            data: 'estoqueAtual',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'emProcesso',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'faltaProg (Tendencia)',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'disponivel',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
                {
            data: 'disponivel Pronta Entrega',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'Prev Sobra',
            render: function (data, type) {
                return type === 'display' ? data.toLocaleString('pt-BR') : data;
            }
        },
        {
            data: 'statusAFV'
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
            if (typeof i === 'string') {
                return parseFloat(i.replace(/[R$ ]/g, '').replace(/\./g, '').replace(',', '.')) || 0;
            } else if (typeof i === 'number') {
                return i;
            }
            return 0;
        };

        const columnIndexMap = {
            valorVendido: 10,
            previcaoVendas: 11,
            qtdePedida: 12,
            qtdeFaturada: 13,
            SaldoColAnt: 14,
            estoqueAtual: 15,
            emProcesso: 16,
            'faltaProg (Tendencia)': 17,
            disponivel: 18,
            'disponivel Pronta Entrega': 19,
            'Prev Sobra': 20
        };

        Object.entries(columnIndexMap).forEach(([columnName, colIndex]) => {
            const dataColumn = api.column(colIndex, { filter: 'applied' }).data();

            if (columnName === 'disponivel') {
                let positivo = 0, negativo = 0;

                dataColumn.each((value) => {
                    const num = intVal(value);
                    num >= 0 ? positivo += num : negativo += num;
                });

                // ✅ ATUALIZA o elemento específico com ID totalDisponivel
                $('#totalDisponivel').html(
                    `+${positivo.toLocaleString('pt-BR')} / ${negativo.toLocaleString('pt-BR')}`
                );

            } else {
                const total = dataColumn.reduce((a, b) => intVal(a) + intVal(b), 0);

                // ✅ Atualiza normalmente o rodapé do DataTable
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
}




    });

    $('.search-input-tendencia').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });


    $('#table-tendencia').on('click', '.detalha-SimulacaoSku', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codReduzido');

        Detalha_SimulacaoSku(codReduzido);
    });

    $('#table-tendencia').on('click', '.detalha-pedidos', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codReduzido');
        const codPlan = $('#select-plano').val();
        const consideraPedidosBloqueado = $('#select-pedidos-bloqueados').val();
        Detalha_Pedidos(codReduzido, consideraPedidosBloqueado, codPlan);
    });


        $('#table-tendencia').on('click', '.detalha-pedidos2', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codReduzido');
        const codPlan = $('#select-plano').val();
        const consideraPedidosBloqueado = $('#select-pedidos-bloqueados').val();
        Detalha_PedidosSaldo(codReduzido, consideraPedidosBloqueado, codPlan);
    });

            // Evento para abrir o modal ao clicar no código
        $('#table-tendencia').on('click', '.detalhaImg', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        codigoPai = $(this).data('coditempai');
        console.log(`imagem: ${codigoPai}`)
        Consulta_Imagem(codigoPai);
        });

}


async function Detalha_SimulacaoSku(codReduzido) {
    if (nomeSimulacao === "") {
        Mensagem_Canto("Nenhuma simulação selecionada", "warning")
    } else {
        $('#loadingModal').modal('show');
        try {

            const requestData = {
                acao: "simulacaoDetalhadaPorSku",
                dados: {
                    "codPlano": $('#select-plano').val(),
                    "consideraPedBloq": $('#select-pedidos-bloqueados').val(),
                    "codSku": codReduzido,
                    "nomeSimulacao": nomeSimulacao
                }

            };

            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response)
            TabelaDetalhamentoSku(response);
            $('#modal-detalhamento-skus').modal('show');
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
            Mensagem_Canto('Erro', 'error');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

}

function TabelaDetalhamentoSku(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamento-skus')) {
        $('#table-detalhamento-skus').DataTable().destroy();
    }

    const tabela = $('#table-detalhamento-skus').DataTable({
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
                title: 'Tendências de Vendas',
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
            { data: 'nomeSimulacao' },
            { data: 'codReduzido' },
            { data: 'previcaoVendasOriginal' },
            { data: 'percentualMarca' },
            { data: 'percentualABC' },
            { data: 'percentualCategoria' },
            { data: '_%Considerado' },
            { data: 'NovaPrevicao' },
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
            $('#pagination-detalhamento-skus').html($('.dataTables_paginate').html());
            $('#pagination-detalhamento-skus span').remove();
            $('#pagination-detalhamento-skus a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    // Adiciona os botões à interface
    tabela.buttons().container().appendTo('#table-detalhamento-skus_wrapper .col-md-6:eq(0)');

    $('#itens-detalhamentoSkuSimulado').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-detalhamentoSkuSimulado').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
};




async function Detalha_Pedidos(codReduzido, consideraPedidosBloqueado, codPlan) {
            $('#loadingModal').modal('show');

    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: "Detalha_Pedidos",
                codPlano: codPlan,
                consideraPedidosBloqueado: consideraPedidosBloqueado,
                codReduzido: codReduzido
            }
        });
        console.log(response)
        TabelaDetalhamentoPedidos(response);
        $('#modal-detalhamento-pedidos').modal('show')
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    }finally {
            $('#loadingModal').modal('hide');
        }
};


async function Detalha_PedidosSaldo(codReduzido, consideraPedidosBloqueado, codPlan) {
            $('#loadingModal').modal('show');

    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: "Detalha_PedidosSaldo",
                codPlano: codPlan,
                consideraPedidosBloqueado: consideraPedidosBloqueado,
                codReduzido: codReduzido
            }
        });
        console.log(response)
        TabelaDetalhamentoPedidosSaldo(response);
        $('#modal-detalhamento-pedidosSaldo').modal('show')
    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally {
            $('#loadingModal').modal('hide');
        }
};


async function Detalha_PedidosGeral() {
   $('#loadingModal').modal('show');

  const response = await $.ajax({
    type: 'GET',
    url: 'requests.php',
    dataType: 'json',
    data: {
      acao: "Detalha_PedidosSaldoGeral",
      codPlano: $('#select-plano').val(),
      consideraPedidosBloqueado: $('#select-pedidos-bloqueados').val()
    }
  });

  //console.log("Response:", response); // para inspecionar a estrutura

  // Use a propriedade correta com os dados
  const dados = response.dados || response; // ajuste se necessário

  const ws = XLSX.utils.json_to_sheet(dados);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, "Pedidos");

  XLSX.writeFile(wb, "Conf_Pedidos_Saldo.xlsx");
   $('#loadingModal').modal('hide');
}




function TabelaDetalhamentoPedidos(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamento-pedidos')) {
        $('#table-detalhamento-pedidos').DataTable().destroy();
    }

    const tabela = $('#table-detalhamento-pedidos').DataTable({
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
                title: 'Tendências de Vendas',
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
            { data: 'codPedido' },
            { data: 'codTipoNota' },
            { data: 'dataEmissao' },
            { data: 'dataPrevFat' },
            { data: 'marca' },
            { data: 'qtdeFaturada' },
            { data: 'qtdePedida' },
            { data: 'valorVendido' }
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
            $('#pagination-detalhamento-pedidos').html($('.dataTables_paginate').html());
            $('#pagination-detalhamento-pedidos span').remove();
            $('#pagination-detalhamento-pedidos a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    // Adiciona os botões à interface
    tabela.buttons().container().appendTo('#table-detalhamento-pedidos_wrapper .col-md-6:eq(0)');

    $('#itens-detalhamento-pedidos').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-detalhamento-pedidos').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function TabelaDetalhamentoPedidosSaldo(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamento-pedidosSaldo')) {
        $('#table-detalhamento-pedidosSaldo').DataTable().destroy();
    }

    const tabela = $('#table-detalhamento-pedidosSaldo').DataTable({
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
                title: 'Tendências de Vendas',
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
            { data: 'codReduzido' },
            { data: 'codPedido' },
            { data: 'codTipoNota' },
            { data: 'dataEmissao' },
            { data: 'dataPrevFat' },
            { data: 'SaldoColAnt' },
            { data: 'qtdeFaturadaSaldo' },
            { data: 'qtdePedidaSaldo' },
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
            $('#pagination-detalhamento-pedidos').html($('.dataTables_paginate').html());
            $('#pagination-detalhamento-pedidos span').remove();
            $('#pagination-detalhamento-pedidos a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    // Adiciona os botões à interface
    tabela.buttons().container().appendTo('#table-detalhamento-pedidos_wrapper .col-md-6:eq(0)');

    $('#itens-detalhamento-pedidos').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-detalhamento-pedidos').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
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
            mensagem: data[0]['Mensagem']
        };


    } catch (error) {
        console.error('Erro ao consultar planos:', error);
        return null; // ou algum valor padrão indicando erro

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
            finalFaturamento: data[0]['06- Final Faturamento']
        };


    } catch (error) {
        console.error('Erro ao consultar planos:', error);
        return null; // ou algum valor padrão indicando erro

    }
};

function formatarDataBrasileira(dataISO) {
    if (!dataISO || !dataISO.includes('-')) return dataISO; // fallback seguro
    const [ano, mes, dia] = dataISO.split('-');
    return `${dia}/${mes}/${ano}`;
}