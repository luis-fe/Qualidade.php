// script1.js
$(document).ready(async () => {
    console.log('teste inicializacao');
    // ... Seus outros códigos de inicialização, se houver ...
    await ConsultaPilotos('1'); 
    
    // --- Lógica de RESET e VISIBILIDADE (do seu outro script block) ---
    // Este código deve estar fora do on('change'), mas definindo as constantes
    const $selectTipoOperacao = $('#tipoOperacao');
    const $divInformacoes = $('#div-informacoes');
    const $divTabela = $('#div-tabela');
    const $divTabela2 = $('#div-tabela2');
    const $divRecebimento = $('#divRecebimento');
    const $divDocumento = $('#div-divDocumento');

    const $inputMatricula = $('#inputMatricula');
    const $inputTag = $('#inputTag');
    
    // --- NOVO: Lógica para RESETAR o modal ao fechar (Voltar ao padrão inicial) ---
    // Usamos delegação no 'body' para garantir que funcione
    $('body').on('hidden.bs.modal', '#modalRecebimentoPiloto', function () {
        
        // 1. Resetar o SELECT (Volta para "Escolha uma opção...")
        $selectTipoOperacao.val(''); 
        
        // 2. Ocultar as DIVs de informação e tabela
        $divInformacoes.addClass('d-none');
        $divTabela.addClass('d-none');
        
        // 3. Limpar os campos de input
        $inputMatricula.val('');
        $inputTag.val('');
        
        // 4. Se houver, limpar o corpo da tabela de tags (tbody)
        $('#tabelaTagsInseridas tbody').empty();
        
        console.log("Modal fechado e resetado para o estado inicial.");
    });

    // --- Seus listeners de VISIBILIDADE (mantido) ---
    $selectTipoOperacao.on('change', function() {
        const valorSelecionado = $selectTipoOperacao.val();
        
        if (valorSelecionado === 'transferencia') {
            $divInformacoes.removeClass('d-none');
            $divTabela.removeClass('d-none');
            $divTabela2.addClass('d-none');
            $divRecebimento.removeClass('d-none')
            $divDocumento.removeClass('d-none')

        } else if (valorSelecionado === 'recebimento') {
            // Se houver lógica específica para recebimento, coloque aqui
            $divInformacoes.removeClass('d-none');
            $divTabela.addClass('d-none'); // ou adicione .addClass('d-none')
            $divTabela2.removeClass('d-none');
            $divRecebimento.addClass('d-none')
            $divDocumento.addClass('d-none')
            get_pilotos_em_transito();


        } else {
            // Estado inicial/opção desabilitada
            $divInformacoes.addClass('d-none');
            $divTabela.addClass('d-none');
            $divTabela2.addClass('d-none');

        }
    }).trigger('change'); 


    // --- Seus listeners de Teclado/Foco (Mantidos e delegados se necessário) ---
    
    // Focar no input ao abrir
    $('body').on('shown.bs.modal', '#modalRecebimentoPiloto', function () {
        $inputMatricula.focus();
    });
    
    // Pular ENTER/TAB
    $inputMatricula.on('keydown', (event) => {
        if (event.key === 'Enter' || event.key === 'Tab') { 
            event.preventDefault(); 
            $inputTag.focus(); 
        }
    });

            // Simular clique ENTER na Tag
            $('#inputTag').on('keydown', async (event) => { 
                if (event.key === 'Enter') { 
                    event.preventDefault(); 
                    
                    // 1. Aguarda a função assíncrona terminar (incluindo o AJAX e o modal)
                    await transferir_tag(); 

                    await tags_transferidas_documento_atual()

                    // 2. Garante que o input está limpo (se a lógica de limpeza estiver na função)
                    // e, em seguida, define o foco.
                    $('#inputTag').focus(); // Use o seletor $('#inputTag') diretamente
                
                }
            });
});




const ConsultaPilotos = async (empresa) => {
    $('#loadingModal').modal('show');
    try {

        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'getConsultaPilotos',
            }
        });

        $('#totalPecas').text(
                    Number(data.data[0]['EstoquePiloto']).toLocaleString('pt-BR')
                );      

        $('#totalPecasUnid2').text(
                    Number(data.data[0]['PilotoUnd2']).toLocaleString('pt-BR')
                );     
        Tabela_detalhada(data.data);


    } catch (error) {
        console.error('Erro ao consultar motivos:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};


const get_pilotos_em_transito = async (empresa) => {
    $('#loadingModal').modal('show');
    try {

        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'get_pilotos_em_transito',
            }
        });

      
        Tabela_detalhada_piloto_recebimento(data);


    } catch (error) {
        console.error('Erro ao consultar motivos:', error);
    } finally {
        $('#loadingModal').modal('hide');
    }
};




const tags_transferidas_documento_atual = async (empresa) => {
    $('#loadingModal').modal('show');
    const $valorDocumentoLabel = $('#valordocumento').text().trim() ;  
    console.log(`valor ${$valorDocumentoLabel}`) 
    try {

            const data = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'tags_transferidas_documento_atual',
                    documento: $('#valordocumento').text().trim() 

                }   
            });

        
            Tabela_detalhada_pilotos_transferidas(data);


        } catch (error) {
            console.error('Erro ao consultar:', error);
        } finally {
            $('#loadingModal').modal('hide');
        }
};


const Gerar_doc_transf = async (empresa) => {
    
    
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php?acao=gerarDoc_controle_OP', // Use o endpoint correto se requests.php for um proxy
            dataType: 'json',
            // ...
        });
        
        // --- NOVO RETORNO: Acessando a propriedade 'codigo' do objeto JSON ---
        return data.codigo; 

    } catch (error) {
        // ...
    } finally {
    }
};






async function transferir_tag() {
    $('#loadingModal').modal('show');

    try {
        const requestData = {
            acao: "TransferirPilotos",
            dados: {
                // CORREÇÃO: Usar .text() ou .html() para obter o valor da label/span
                // O .text() é mais seguro para pegar apenas o conteúdo de texto.
                "documento": $('#valordocumento').text().trim(), 
                
                "matricula": $('#inputMatricula').val(),
                "codbarras": $('#inputTag').val(),
            }
        };

        const response = await $.ajax({
            type: 'POST',
            url: 'requests.php',
            contentType: 'application/json',
            data: JSON.stringify(requestData),
        });
        
        console.log(response)
        // --- INÍCIO DA CORREÇÃO/ADICIONAL ---
            // Assumindo que a resposta (response) é um array, 
            // e você quer verificar o primeiro item:
            if (Array.isArray(response) && response.length > 0) {
                const resultado = response[0];
                
                if (resultado.Status === false) {
                    const audioErro = new Audio('./erro.mp3');
                    // **NOVO:** Toca o som de erro
                    audioErro.play();
                    
                    // Devolve a mensagem na tela quando o status é false
                    await Mensagem_Canto(resultado.Mensagem, 'error');
                                $('#inputTag').val('').focus();

                    // Não continue com o sucesso se houver erro
                    return; 
                }
            }
            // --- FIM DA CORREÇÃO/ADICIONAL ---
            
            // Se o status for true ou se não houver um status de erro claro:
            await Mensagem_Canto('Salvo com sucesso', 'success')
            
            // Opcional: Limpar o campo de TAG após a transferência bem-sucedida
            $('#inputTag').val('').focus();
        
        
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}





let searchTimeout;

function Tabela_detalhada(lista) {
    if ($.fn.DataTable.isDataTable('#tabela_detalhamento')) {
        $('#tabela_detalhamento').DataTable().destroy();
        }

    // 1. 🎯 Capturar a instância da tabela na variável 'tabela'
    const tabela = $('#tabela_detalhamento').DataTable({
        searching: true,
        paging: true,
        lengthChange: false,
        info: false,
        pageLength: 20,
        data: lista,
        dom: 'Bfrtip',
        buttons: {
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel',
                    title: 'Controle de Pilotos',
                    className: 'btn-tabelas',
                    exportOptions: {
                        columns: ':visible',
                    }
                },
                // ... outros botões
            ]
        },

        autoWidth: true,
        scrollX: true,

        columns: [
            { data: 'codEngenharia', width: '5%' },
            { data: 'descricao', width: '20%' },
            { data: 'cor', width: '10%' },
            { data: 'tamanho', width: '10%' },
            { data: 'codBarrasTag', width: '10%' },
            { data: 'status', width: '15%' },
            { data: 'numeroOP', width: '15%' }      
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

    });
}



function Tabela_detalhada_pilotos_transferidas(lista) {
   // Verifica se já existe uma instância da DataTable e a destrói para evitar erros
    if ($.fn.DataTable.isDataTable('#tabelaTagsRecebidas')) {
        $('#tabelaTagsRecebidas').DataTable().destroy();
    }

    // Inicializa a DataTable com as configurações.
    // A variável 'tabela' é opcional se não for usada posteriormente.
    const tabela = $('#tabelaTagsRecebidas').DataTable({
        searching: true,         // Habilita a busca
        paging: true,            // Habilita a paginação
        lengthChange: false,     // Desabilita a alteração de quantidade de itens por página
        info: false,             // Desabilita a exibição de informações de página
        pageLength: 7,          // Define 20 linhas por página
        data: lista,             // Fonte de dados
        
        // Remove 'buttons: {}' se não for necessário
        // Se houver botões, use: dom: 'Bfrtip' e defina o objeto buttons
        dom: 'frtip', // Removendo 'B' já que o objeto buttons estava vazio
        
        autoWidth: true,         // Permite que o DataTables ajuste automaticamente a largura das colunas
        scrollX: true,           // Habilita o scroll horizontal

        columns: [
            // Definições das colunas (mantidas do seu original)
            { data: 'codbarrastag', title: 'Código de Barras', width: '40%' },
            { data: 'dataTransferencia', title: 'Data da Transferência', width: '60%' },
        ],
        
        // Configurações de idioma (mantidas do seu original)
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
    });

    // Se você precisasse interagir com a instância da tabela depois, faria isso aqui.
    // Exemplo: tabela.on('click', 'tr', function() { /* ... */ });
}

function Tabela_detalhada_piloto_recebimento(lista) {
    // Verifica se já existe uma instância da DataTable e a destrói para evitar erros
    if ($.fn.DataTable.isDataTable('#tabelaTagsRecebidas')) {
        $('#tabelaTagsRecebidas').DataTable().destroy();
    }

    // Inicializa a DataTable com as configurações.
    // A variável 'tabela' é opcional se não for usada posteriormente.
    const tabela = $('#tabelaTagsRecebidas').DataTable({
        searching: true,         // Habilita a busca
        paging: true,            // Habilita a paginação
        lengthChange: false,     // Desabilita a alteração de quantidade de itens por página
        info: false,             // Desabilita a exibição de informações de página
        pageLength: 7,          // Define 20 linhas por página
        data: lista,             // Fonte de dados
        
        // Remove 'buttons: {}' se não for necessário
        // Se houver botões, use: dom: 'Bfrtip' e defina o objeto buttons
        dom: 'frtip', // Removendo 'B' já que o objeto buttons estava vazio
        
        autoWidth: true,         // Permite que o DataTables ajuste automaticamente a largura das colunas
        scrollX: true,           // Habilita o scroll horizontal

        columns: [
            // Definições das colunas (mantidas do seu original)
            { data: 'codbarrastag', title: 'Código de Barras', width: '40%' },
            { data: 'dataTransferencia', title: 'Data da Transferência', width: '20%' },
            { data: 'nome', title: 'Transferido por:', width: '40%' },
        ],
        
        // Configurações de idioma (mantidas do seu original)
        language: {
            paginate: {
                previous: '<i class="fa-solid fa-backward-step"></i>',
                next: '<i class="fa-solid fa-forward-step"></i>'
            },
            info: "Página _PAGE_ de _PAGES_",
            emptyTable: "Nenhum dado disponível na tabela",
            zeroRecords: "Nenhum registro encontrado"
        },
    });

    // Se você precisasse interagir com a instância da tabela depois, faria isso aqui.
    // Exemplo: tabela.on('click', 'tr', function() { /* ... */ });
}