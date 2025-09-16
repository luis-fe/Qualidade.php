// Variáveis globais
let imagemAtual = 0;
let totalImagens = 0;
let codigoMP = "";

// Atualiza a imagem no modal
const atualizarImagem = () => {
  if (!codigoMP || codigoMP.trim() === "") {
    console.error("codigoMP está vazio!");
    return;
  }

  const baseURL = "http://192.168.0.183:9000";
  const url = `${baseURL}/imagem/${codigoMP}/${imagemAtual}`;

  console.log("Imagem carregada de:", url);

  $('#imagem-container').html(`
    <img src="${url}" alt="Imagem ${imagemAtual + 1}" class="img-fluid">
  `);

  $('#contador-imagens').text(`Imagem ${imagemAtual + 1} de ${totalImagens}`);
  $('#btn-anterior').prop('disabled', imagemAtual === 0);
  $('#btn-proximo').prop('disabled', imagemAtual >= totalImagens - 1);
};


const Consulta_Imagem = async (codigoMP) => {
  // Mostra o modal de loading
  $('#loadingModal').modal('show');
  
  try {
    const data = await $.ajax({
      type: 'GET',
      url: 'requests.php',
      dataType: 'json',
      data: {
        acao: 'Consulta_Imagem',
        codigoMP: codigoMP // Corrigido para passar explicitamente o parâmetro
      },
      xhrFields: {
        withCredentials: true
      }
    });

    if (data.imagem_url && data.total_imagens) {
      // Atualiza variáveis globais (se necessário)
      imagemAtual = 0;
      totalImagens = data.total_imagens;
      atualizarImagem();

      // Fecha o loading e abre o modal principal
      $('#loadingModal').modal('hide');
      $('#modal-imagemMP').modal('show');
    } else {
      $('#imagem-container').html(`<p>Imagem não encontrada.</p>`);
      $('#loadingModal').modal('hide');
    }

  } catch (error) {
    console.error('Erro na solicitação AJAX:', error);
    Mensagem_Canto('Erro', 'error');
    $('#loadingModal').modal('hide');
  }
}









$(document).ready(async () => {
    Consulta_Planos();
    Consulta_Simulacoes();
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


  $('#table-analise').on('click', '.codMP', function () {
    const codigoMPCompleto = $(this).data('codmp');
    codigoMP = codigoMPCompleto.substring(0, 9);
    console.log(`A imagem desejada é do codigo ${codigoMP}`)
    Consulta_Imagem(codigoMP);
  });



});

let nomeSimulacao = ''
async function simulacao(texto, tipo) {
    $('#modal-simulacao').modal('hide');
    $('#modal-nova-simulacao').modal('hide');
    $('#loadingModal').modal('show');
    await Cadastro_Simulacao(texto, tipo);
    await Consulta_Simulacoes();
    await Simular_Programacao(texto);
    nomeSimulacao = texto;
    $('#loadingModal').modal('hide');
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

const Consulta_Naturezas = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Naturezas'
            },
        });
        TabelaNaturezas(response);
        $('.div-naturezas').removeClass('d-none');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
};

async function Simular_Programacao(simulacao) {
    try {
        const requestData = {
            acao: "Simular_Programacao",

            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedBloq": $('#select-pedidos-bloqueados').val(),
                "nomeSimulacao": simulacao,
                "empresa": "1"
            }

        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        if (response === null) {

        } else {
            TabelaAnalise(response);
            $('#titulo').html(`
            <span class="span-icone"><i class="bi bi-bag-check"></i></span>
            Análise de Materiais
          `);

            $('#titulo').html(`
            <span class="span-icone"><i class="bi bi-bag-check"></i></span>
            Análise de Materiais - 
            <span style="display: inline-block; position: relative;">
              <strong>${simulacao}</strong>
              <button onclick="Selecionar_Calculo()" 
                      style="position: absolute; top: 0; right: -20px; border: none; background: none; font-weight: bold; color: red; cursor: pointer;">
                ×
              </button>
            </span>
          `);
        }

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

async function Consulta_Simulacoes() {
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
            const descricao = $('#descricao-simulacao').val();
            console.log(descricao)
            $('#select-simulacao').val(descricao);
        },

        error: function (xhr, status, error) {
            console.error('Erro ao consultar planos:', error);
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





const Consulta_Comprometidos = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Comprometidos'
            },
        });
        TabelaComprometido(response);
        $('.div-comprometido').removeClass('d-none');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
};

const Consulta_Comprometidos_Compras = async () => {
    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consulta_Comprometidos_Compras'
            },
        });
        TabelaComprometidoCompras(response);
        $('.div-comprometido-compras').removeClass('d-none');
    } catch (error) {
        console.error('Erro:', error);
    } finally {
    }
};

async function Analise_Materiais(congelar) {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Analise_Materiais",
            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                "congelar": congelar
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });

        await $('.div-analise').removeClass('d-none');
        await TabelaAnalise(response);
        await Consulta_Naturezas();
        await Consulta_Comprometidos();
        Consulta_Comprometidos_Compras();
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
Análise de Materiais
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

async function Detalha_Necessidade(codReduzido, nomeSimulacao) {
    $('#loadingModal').modal('show');
    try {
        const requestData = {
            acao: "Detalha_Necessidade",
            dados: {
                "codPlano": $('#select-plano').val(),
                "consideraPedidosBloqueado": $('#select-pedidos-bloqueados').val(),
                "codComponente": codReduzido,
                "nomeSimulacao": nomeSimulacao
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
            data: '02-codCompleto',
            render: function (data, type, row) {
                return `<span class="codMP" data-codmp="${data}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
            }
        },
        {
            data: '03-descricaoComponente'
        },
        {
            data: '01-codReduzido',
            render: function (data, type, row) {
                return `<span class="codReduzido" data-codigoReduzido="${data}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;
            }
        },
        {
            data: '10-Necessidade Compra (Tendencia)',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '12-Necessidade Ajustada Compra (Tendencia)',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '08-estoqueAtual',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '09-SaldoPedCompras',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '07-EmRequisicao',
            render: function (data, type) {
                if (type === 'display') {
                    // Exibe os dados formatados com separador de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outros tipos, retorna o número bruto
                return parseFloat(data.replace(/\./g, '').replace(',', '.')) || 0;
            }
        },
        {
            data: '04-fornencedorPreferencial'
        },
        {
            data: '05-unidade'
        },
        {
            data: '14-Lote Mínimo'
        },
        {
            data: '11-Lote Mutiplo'
        },
        {
            data: '13-LeadTime'
        },
        {
            data: 'fatorConversao'
        },
        {
            data: '15-CodSubstituto'
        },
        {
            data: '16-NomeSubstituto'
        },
        {
            data: '17-SaldoSubs.'
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
            $('#pagination-analise').html($('.dataTables_paginate').html());
            $('#pagination-analise span').remove();
            $('#pagination-analise a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }

        
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

    $('#table-analise tbody').on('click', 'tr', function (event) {
        // Verifica se o clique foi em um hiperlink
        if ($(event.target).hasClass('codReduzido')) {
            return; // Não executa o código para seleção de linha
        }

        const isSelected = $(this).hasClass('selected');
        $('#table-analise tbody tr').removeClass('selected');

        if (!isSelected) {
            $(this).addClass('selected');
            const rowData = tabela.row(this).data();
            const filtro = rowData['01-codReduzido'];
            filtrarTabelas(filtro);
        } else {
            // Remove o filtro e reseta a tabela de naturezas
            filtrarTabelas('');
        }
    });

    // Clique no hiperlink "codReduzido"
    $('#table-analise').on('click', '.codReduzido', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codigoReduzido');
        Detalha_Necessidade(codReduzido, nomeSimulacao);
    });

        // Evento para abrir o modal ao clicar no código
        $('#table-analise').on('click', '.codMP', function () {
        const codigoMPCompleto = $(this).data('codmp');
        const codigoMP = codigoMPCompleto.substring(0,9);
        console.log(codigoMPCompleto)
        console.log(codigoMP)
        Consulta_Imagem(codigoMP);
        });

}


function TabelaNaturezas(listaNaturezas) {
    if ($.fn.DataTable.isDataTable('#table-naturezas')) {
        $('#table-naturezas').DataTable().destroy();
    }

    const tabela = $('#table-naturezas').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaNaturezas,
        columns: [{
            data: 'CodComponente'
        },
        {
            data: 'nome'
        },
        {
            data: 'natureza'
        },
        {
            data: 'estoqueAtual',
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
            $('#pagination-naturezas').html($('.dataTables_paginate').html());
            $('#pagination-naturezas span').remove();
            $('#pagination-naturezas a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('#itens-naturezas').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-naturezas').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function TabelaComprometido(listaComprometido) {
    if ($.fn.DataTable.isDataTable('#table-comprometido')) {
        $('#table-comprometido').DataTable().destroy();
    }

    const tabela = $('#table-comprometido').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaComprometido,
        columns: [{
            data: 'CodComponente'
        },
        {
            data: 'nomeMaterial'
        },
        {
            data: 'OP'
        },
        {
            data: 'EmRequisicao',
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
            $('#pagination-comprometido').html($('.dataTables_paginate').html());
            $('#pagination-comprometido span').remove();
            $('#pagination-comprometido a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('#itens-comprometido').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-comprometido').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function TabelaComprometidoCompras(listaComprometido) {
    if ($.fn.DataTable.isDataTable('#table-comprometido-compras')) {
        $('#table-comprometido-compras').DataTable().destroy();
    }

    const tabela = $('#table-comprometido-compras').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 10,
        data: listaComprometido,
        columns: [{
            data: 'CodComponente'
        },
        {
            data: 'nome'
        },
        {
            data: 'numero'
        },
        {
            data: 'tipo'
        },
        {
            data: 'SaldoPedCompras',
            render: function (data, type) {
                if (type === 'display') {
                    // Formata o número para o formato brasileiro com separadores de milhares
                    return data.toLocaleString('pt-BR');
                }
                // Para ordenação e outras operações, retorna o número diretamente
                return data;
            }
        },
        {
            data: 'dataPrevisao'
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
            $('#pagination-comprometido-compras').html($('.dataTables_paginate').html());
            $('#pagination-comprometido-compras span').remove();
            $('#pagination-comprometido-compras a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    $('#itens-comprometido-compras').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-comprometido-compras').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
    });
}

function TabelaDetalhamento(listaDetalhes) {
        // Atualiza o título com base no primeiro item da lista
    if (listaDetalhes.length > 0) {
        const cod = listaDetalhes[0]["11-CodComponente"] || "Sem código";
        document.getElementById("titulo-detalhamento").textContent = `Detalhamento - ${cod}`;
    } else {
        document.getElementById("titulo-detalhamento").textContent = "Detalhamento - (Sem dados)";
    }




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
        }, footerCallback: function (row, data, start, end, display) {
            const api = this.api();

            // Helper para converter strings para número
            const intVal = (i) => {
                if (typeof i === 'string') {
                    // Remover "R$", pontos e substituir vírgula por ponto
                    return parseFloat(i.replace(/[R$ ]/g, '').replace(/[\.]/g, '').replace(',', '.')) || 0;
                } else if (typeof i === 'number') {
                    return i;
                }
                return 0;
            };

            // Colunas que precisam de total
            const columnsToSum = ['14-Necessidade faltaProg (Tendencia)'];

            columnsToSum.forEach((columnName, idx) => {
                const colIndex = idx + 14; // Índice da coluna no DataTables

                // Total considerando todos os dados após filtro
                const total = api.column(colIndex, {
                    filter: 'applied'
                }).data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);

                // Atualizar o rodapé da coluna
                $(api.column(colIndex).footer()).html(
                    columnName === 'valorVendido' ? `R$ ${total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })}` : total.toLocaleString('pt-BR')
                );
            });
        },
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

function filtrarTabelas(filtro) {
    const TabelaNaturezas = $('#table-naturezas').DataTable();
    const tabelaComprometido = $('#table-comprometido').DataTable();
    const tabelaCompras = $('#table-comprometido-compras').DataTable();

    if (filtro === '') {
        TabelaNaturezas.column(0).search('').draw();
        tabelaComprometido.column(0).search('').draw();
        tabelaCompras.column(0).search('').draw();
    } else {
        TabelaNaturezas.column(0).search(`^${filtro}$`, true, false).draw();
        tabelaComprometido.column(0).search(`^${filtro}$`, true, false).draw();
        tabelaCompras.column(0).search(`^${filtro}$`, true, false).draw();
    }


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



function fecharSimulacao() {
    document.getElementById("simulacao-container").classList.add("d-none");
}


function fecharNovaSimulacao() {
    document.getElementById("nova-simulacao-container").classList.add("d-none");
}


function fecharselecaoEngenharia() {
    document.getElementById("modal-selecaoEngenharias").classList.add("d-none");
}

function formatarDataBrasileira(dataISO) {
    if (!dataISO || !dataISO.includes('-')) return dataISO; // fallback seguro
    const [ano, mes, dia] = dataISO.split('-');
    return `${dia}/${mes}/${ano}`;
}


function formatarMoedaBrasileira(valor) {
    // Garante que seja número
    const numero = parseFloat(valor);
    
    return numero.toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });
}
const Consulta_Engenharias = async () => {
    $('#loadingModal').modal('show');

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
                acao: "obter_produtos_tendencia",
                codPlano: $('#select-plano').val(),
                nomeSimulacao:  simulacao
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
    var simulacao = $('#select-simulacao').val()

    if ($('#select-simulacao').is(':visible')) {
    console.log("Tá aparecendo! 👀");
} else {
    simulacao = $("#descricao-simulacao").val();
}
    
    registrarSimulacaoProdutos(arrayProduto, arrayPercentualProduto, simulacao)
    exluindo_simulacao_Produtos_zerados(arrayProdutoZero, arrayPercentualZero)
    Produtos_Simulacao();

    }
);

}
async function exluindo_simulacao_Produtos_zerados(arrayProdutoZerados, arrayPercentualZerados) {


        var simulacao = $('#select-simulacao').val()

        if ($('#select-simulacao').is(':visible')) {
        console.log("Tá aparecendo! 👀");
    } else {
        simulacao = $("#descricao-simulacao").val();
    }


            const dados = {
                "nomeSimulacao": simulacao,
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
            metaFinanceira: data[0]['12-metaFinanceira']
        };


    } catch (error) {
        console.error('Erro ao consultar planos:', error);
        return null; // ou algum valor padrão indicando erro

    }
};