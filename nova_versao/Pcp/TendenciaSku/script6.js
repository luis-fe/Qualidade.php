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
            },
            
        },
        {
            text: '<i class="bi bi-funnel-fill" style="margin-right: 5px;"></i> Nova Simulação',
            title: 'Nova Simulação',
            className: 'btn-tabelas',
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
}




    });

}



function formatarDataBrasileira(dataISO) {
    if (!dataISO || !dataISO.includes('-')) return dataISO; // fallback seguro
    const [ano, mes, dia] = dataISO.split('-');
    return `${dia}/${mes}/${ano}`;
}


function fecharSimulacao() {
    document.getElementById("simulacao-container").classList.add("d-none");
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



async function Produtos_Simulacao() {
   
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: "selecao_produtos_simulacao",
                nomeSimulacao:  $('#select-simulacao').val()
            }
        }); 

        console.log(data)
        console.log(data[0].mensagem);

        document.getElementById("TituloSelecaoEngenharias").textContent = data[0].mensagem;


    } catch (error) {
        console.error('Erro ao consultar planos:', error);
    } finally {
                
        console.log('atualizado produtos da selecacao');

    }

    
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
    const simulacao = $('#select-simulacao').val()
    
    registrarSimulacaoProdutos(arrayProduto, arrayPercentualProduto, simulacao)
    exluindo_simulacao_Produtos_zerados(arrayProdutoZero, arrayPercentualZero)
    Produtos_Simulacao();

    }
);

}