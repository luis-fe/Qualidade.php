let imagemAtual = 0;
let totalImagens = 0;
let totalImagensEng = 0;
let totalImagensColorBook = 0;
let codigoMP = "";
let imagensColorBook = [];
let searchTimeout;




const atualizarImagem = () => {
  if (!codigoMP || String(codigoMP).trim() === "") {
    console.error("codigoMP está vazio!");
    return;
  }

  const baseURL = "http://10.162.0.53:9000";
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





});



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
            return;
        }

        const campos = ["2- ABC", "3- Categoria", "4- Marcas"];
        campos.forEach(campo => {
            if (data[0][campo]) {
                data[0][campo].forEach(item => {
                    const key = item.class || item.categoria || item.marca;
                    const input = $(`#${key.replace(/\s+/g, '-').replace(/[^\w-]/g, '')}`);
                    console.log(`cadastro:${input.length},item${item}`)
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
        respostaPeriodoVendas.metaFinanceira = formatarMoedaBrasileira(respostaPeriodoVendas.metaFinanceira);
        respostaPeriodoVendas.metaPcs = formatarInteiro(respostaPeriodoVendas.metaPcs);

        $('#titulo').html(`
<div class="d-flex justify-content-between align-items-start w-100 p-0 m-0">

    <!-- Título -->
    <div class="ms-2">
        <span class="span-icone"><i class="bi bi-clipboard-data-fill"></i></span> 
        Tendência de Vendas
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
    
    });



        nomeSimulacao = "";
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
       // document.getElementById("ConfPedidosSaldo").classList.remove("d-none");
    }
    
};





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
            data: 'marca'
        },
        {
            data: 'codItemPai',
                        render: function (data, type, row) {
                return `<span class="detalhaImg" data-codItemPai="${row.codItemPai}" style="text-decoration: underline; color:hsl(217, 100.00%, 65.10%); cursor: pointer;">${data}</span>`;
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
            render: function (data, type, row) {
                return `<span class="detalha-ordemProd" data-codReduzido="${row.codReduzido}" style="text-decoration: underline; color: blue; cursor: pointer;">${data}</span>`;

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

            $('.search-input-tendencia').on('input', function () {
                const input = $(this);
                clearTimeout(searchTimeout);

                searchTimeout = setTimeout(() => {
                    tabela
                        .column(input.closest('th').index())
                        .search(input.val())
                        .draw();
                }, 500); // espera 500ms após parar de digitar
            });
                $('#table-tendencia').on('click', '.detalha-ordemProd', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codReduzido');
        console.log(`codigo reduzido escolhido ${codReduzido}`)
        Detalha_OrdemProducao(codReduzido);
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


            $('#table-tendencia').on('click', '.detalha-pedidos', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codReduzido');
        const codPlan = $('#select-plano').val();
        const consideraPedidosBloqueado = $('#select-pedidos-bloqueados').val();
        Detalha_Pedidos(codReduzido, consideraPedidosBloqueado, codPlan);
    });

        $('#table-tendencia').on('click', '.detalha-SimulacaoSku', function (event) {
        event.stopPropagation(); // Impede a propagação do clique
        const codReduzido = $(this).attr('data-codReduzido');

        Detalha_SimulacaoSku(codReduzido);
    });

}




    });

}

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

    // 2. Faz chamadas paralelas para os restantes do ColorBook (índice 1+)
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

    $('#loadingModal').modal('hide');
    $('#modal-imagemMP').modal('show');
  } catch (error) {
    console.error('Erro ao consultar imagens:', error);
    Mensagem_Canto('Erro', 'error');
    $('#loadingModal').modal('hide');
  }
};

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

//let nomeSimulacao = ''
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


async function Simular_Programacao(simulacao, tipo) {
    $('#loadingModal').modal('show');
    let nomeSimulacao = simulacao;

    try {
        // Captura o checkbox correto com base no tipo
        const checkboxId = (tipo === "cadastro") ? 'igualarDisponivel2' : 'igualarDisponivel';
        const checkbox = document.getElementById(checkboxId);
        const estaMarcado = checkbox?.checked ?? false;

        console.log('Checkbox está marcado?', estaMarcado);

        const requestData = {
            acao: "Simular_Programacao",
            dados: {
                codPlano: $('#select-plano').val(),
                consideraPedidosBloqueado: $('#select-pedidos-bloqueados').val(),
                nomeSimulacao: simulacao,
                igualarDisponivel: estaMarcado // envia o status para o backend, se quiser
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
        respostaPeriodoVendas.metaPcs = formatarInteiro(respostaPeriodoVendas.metaPcs);
        respostaPeriodoVendas.metaFinanceira = formatarMoedaBrasileira(respostaPeriodoVendas.metaFinanceira);


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
    
    });   

        TabelaTendencia(response);
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error');
    } finally {
        $('#loadingModal').modal('hide');
    }
}


function fecharSimulacao() {
    document.getElementById("simulacao-container").classList.add("d-none");
}
function fecharInformacoes() {
    document.getElementById("informacoes-container").classList.add("d-none");
}

function fecharNovaSimulacao() {
    document.getElementById("nova-simulacao-container").classList.add("d-none");
}


function fecharselecaoEngenharia() {
    document.getElementById("modal-selecaoEngenharias").classList.add("d-none");
}




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
        <input type="text" class="inputs-percentuais input-categoria-2 col-12" id="${item.nomeCategoria}" placeholder="100%">
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


async function Detalha_OrdemProducao(codReduzido) {
            $('#loadingModal').modal('show');

    try {
        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: "Detalha_OrdemProducao",
                codReduzido
                    }
        });
        console.log(response)
        TabelaDetalhamentoOrdemProd(response);
        const modal = new bootstrap.Modal(document.getElementById('modal-detalhamento-OrdemProd'));
        modal.show();
    } catch (error) {
        console.error('Erro ao consultar ordemProd:', error);
    }finally {
            $('#loadingModal').modal('hide');
        }
};


function TabelaDetalhamentoOrdemProd(listaDetalhes) {
    if ($.fn.DataTable.isDataTable('#table-detalhamento-OrdemProd')) {
        $('#table-detalhamento-OrdemProd').DataTable().destroy();
    }

    const tabela = $('#table-detalhamento-OrdemProd').DataTable({
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
            { data: 'numeroop' },
            {data: 'codFaseAtual'},
            {data: 'nomeFase'},
            { data: 'total_pcs' },
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
            $('#pagination-detalhamento-ordemProd').html($('.dataTables_paginate').html());
            $('#pagination-detalhamento-ordemProd span').remove();
            $('#pagination-detalhamento-ordemProd a').off('click').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('previous')) tabela.page('previous').draw('page');
                if ($(this).hasClass('next')) tabela.page('next').draw('page');
            });
            $('.dataTables_paginate').hide();
        }
    });

    // Adiciona os botões à interface
    tabela.buttons().container().appendTo('#table-detalhamento-ordemProd .col-md-6:eq(0)');

    $('#itens-detalhamento-ordemProd').on('input', function () {
        const valor = parseInt($(this).val(), 10);
        if (!isNaN(valor) && valor > 0) {
            tabela.page.len(valor).draw();
        }
    });

    $('.search-input-detalhamento-ordemProd').on('input', function () {
        tabela.column($(this).closest('th').index()).search($(this).val()).draw();
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


async function Deletar_SimulacaoProduto() {


    
    var simulacao = $('#select-simulacao').val()

        if ($('#select-simulacao').is(':visible')) {
        console.log("Tá aparecendo! 👀");
    } else {
        simulacao = $("#descricao-simulacao").val();
    }


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
                "nomeSimulacao": simulacao,
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

async function Detalha_SimulacaoSku(codReduzido) {
    if (nomeSimulacao === "") {
        Mensagem_Canto("Nenhuma simulação selecionada", "warning");
    } else {
        $('#loadingModal').modal('show'); // ainda pode ser jQuery se esse modal for BS4
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

            console.log(response);
            TabelaDetalhamentoSku(response);

            $('#modal-detalhamento-simulacaoSku').modal('show')


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