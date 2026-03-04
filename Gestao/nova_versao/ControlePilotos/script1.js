// script1.js
$(document).ready(async () => {
    console.log('teste inicializacao');
    // ... Seus outros códigos de inicialização, se houver ...
    await ConsultaPilotos('1'); 

    
    // --- Lógica de RESET e VISIBILIDADE (do seu outro script block) ---
    // Este código deve estar fora do on('change'), mas definindo as constantes
    const $selectTipoOperacao = $('#tipoOperacao');
    const $selectTipoInv = $('#tipoLocalInv');


    const $divInformacoes = $('#div-informacoes');
    const $informacoesInvLocal = $('#div-informacoesInvLocal');

    const $divInformacoesReceb = $('#div-informacoesReceb');
    const $divTabela = $('#div-tabela');
    const $divTabela2 = $('#div-tabela2');
    const $divTabelaInv = $('#div-tabelaInv');

    const $divRecebimento = $('#divRecebimento');
    const $divDocumento = $('#div-divDocumento');

    const $inputMatricula = $('#inputMatricula');
    const $inputMatriculaInv = $('#inputMatriculaInv');

    const $inputTag = $('#inputTag');
        const $inputTagInv = $('#inputTagInv');


    popularComboBoxDestinos();

    // --- NOVO: Lógica para RESETAR o modal ao fechar (Voltar ao padrão inicial) ---
    // Usamos delegação no 'body' para garantir que funcione
    $('body').on('hidden.bs.modal', '#modalRecebimentoPiloto', function () {
        
        // 1. Resetar o SELECT (Volta para "Escolha uma opção...")
        $selectTipoOperacao.val(''); 
        
        // 2. Ocultar as DIVs de informação e tabela
        $divInformacoes.addClass('d-none');
        $divInformacoesReceb.addClass('d-none');

        $divTabela.addClass('d-none');
        $divTabela2.addClass('d-none');

        
        // 3. Limpar os campos de input
        $inputMatricula.val('');
        $inputTag.val('');
        
        // 4. Se houver, limpar o corpo da tabela de tags (tbody)
        $('#tabelaTagsInseridas tbody').empty();

        ConsultaPilotos('1');
        
        console.log("Modal fechado e resetado para o estado inicial.");
    });



    // --- NOVO: Lógica para RESETAR o modal ao fechar (Voltar ao padrão inicial) ---
    // Usamos delegação no 'body' para garantir que funcione
    $('body').on('hidden.bs.modal', '#modalInventariar', function () {
        
        // 1. Resetar o SELECT (Volta para "Escolha uma opção...")
        $selectTipoInv.val(''); 
        
        // 2. Ocultar as DIVs de informação e tabela

        $divTabelaInv.addClass('d-none');
        $informacoesInvLocal.addClass('d-none');
        
        // 3. Limpar os campos de input
        $inputMatriculaInv.val('');
        $inputTagInv.val('');
        

        ConsultaPilotos('1');
        
    });

    // --- Seus listeners de VISIBILIDADE (mantido) ---
    $selectTipoOperacao.on('change', function() {
        const valorSelecionado = $selectTipoOperacao.val();
        
        if (valorSelecionado === 'transferencia') {
            $divInformacoes.removeClass('d-none');
            $divTabela.removeClass('d-none');
            $divTabela2.addClass('d-none');
            $divRecebimento.removeClass('d-none')
            $divInformacoesReceb.addClass('d-none');
            $divDocumento.removeClass('d-none')

        } else if (valorSelecionado === 'recebimento') {
            // Se houver lógica específica para recebimento, coloque aqui
            $divInformacoes.addClass('d-none');
            $divInformacoesReceb.removeClass('d-none');
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
const $labelLocal = $('#labelLocal');
// 2. O evento 'change' é definido:
$selectTipoInv.on('change', function() {
    // O valor não é estritamente necessário se só for para mostrar/ocultar
    const valorLocal = $selectTipoInv.val(); 

    // 2. Lógica para mostrar/ocultar (mantida do passo anterior)
        if (valorLocal !== "" && valorLocal !== null) {
            $informacoesInvLocal.removeClass('d-none');
        } else {
        $informacoesInvLocal.addClass('d-none'); // Esconder se voltar para o placeholder
        }

        const textoVisivel = $selectTipoInv.find('option:selected').text();
        $labelLocal.text(textoVisivel);
        $divTabelaInv.removeClass('d-none'); // ou adicione .addClass('d-none')
        $inputMatriculaInv.focus(); 

    });

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


        // Pular ENTER/TAB
    $inputMatriculaInv.on('keydown', (event) => {
        if (event.key === 'Enter' || event.key === 'Tab') { 
            event.preventDefault(); 
            $inputTagInv.focus(); 
        }
    });

// Simular clique ENTER na Tag
$('#inputTag').on('keydown', async (event) => {
    if (event.key === 'Enter') {
        event.preventDefault();

        // **NOVA VERIFICAÇÃO DE ENTRADA**
        const inputValue = $('#inputTag').val().trim();

        // Expressão regular que verifica se o valor contém APENAS dígitos (0-9).
        // Se NÃO for correspondente (ou seja, contém letras ou outros caracteres), 
        // o `!` inverte o resultado para true.
        if (!/^\d+$/.test(inputValue)) {
            // Exibir a mensagem de erro. Você precisará de uma função ou lógica para isso.
            Mensagem_Canto("A entrada deve conter apenas números.",'error'); 
            
            // Opcional: Limpar o campo de entrada e focar nele novamente.
            $('#inputTag').val('');
            $('#inputTag').focus();
            
            return; // Interrompe a execução aqui.
        }
        // FIM DA NOVA VERIFICAÇÃO

        // 1. Aguarda a função assíncrona terminar (incluindo o AJAX e o modal)
        await transferir_tag(); 

        await tags_transferidas_documento_atual();

        // 2. Garante que o input está limpo e, em seguida, define o foco.
        $('#inputTag').focus();
    }
});




                        // Simular clique ENTER na Tag
            $('#inputTagReceb').on('keydown', async (event) => { 
                if (event.key === 'Enter') { 
                    event.preventDefault(); 

                            // **NOVA VERIFICAÇÃO DE ENTRADA**
        const inputValue = $('#inputTagReceb').val().trim();

        // Expressão regular que verifica se o valor contém APENAS dígitos (0-9).
        // Se NÃO for correspondente (ou seja, contém letras ou outros caracteres), 
        // o `!` inverte o resultado para true.
        if (!/^\d+$/.test(inputValue)) {
            // Exibir a mensagem de erro. Você precisará de uma função ou lógica para isso.
            Mensagem_Canto("A entrada deve conter apenas números.",'error'); 
            
            // Opcional: Limpar o campo de entrada e focar nele novamente.
            $('#inputTagReceb').val('');
            $('#inputTagReceb').focus();
            
            return; // Interrompe a execução aqui.
        }
                    
                    // 1. Aguarda a função assíncrona terminar (incluindo o AJAX e o modal)
                    await receber_tag(); 

                    await get_pilotos_em_transito()

                    // 2. Garante que o input está limpo (se a lógica de limpeza estiver na função)
                    // e, em seguida, define o foco.
                    $('#inputTagReceb').focus(); // Use o seletor $('#inputTag') diretamente
                
                }
            });


              // Simular clique ENTER na Tag
            $('#inputTagInv').on('keydown', async (event) => { 
                if (event.key === 'Enter') { 
                    event.preventDefault(); 

                            // **NOVA VERIFICAÇÃO DE ENTRADA**
        const inputValue = $('#inputTagInv').val().trim();

        // Expressão regular que verifica se o valor contém APENAS dígitos (0-9).
        // Se NÃO for correspondente (ou seja, contém letras ou outros caracteres), 
        // o `!` inverte o resultado para true.
        if (!/^\d+$/.test(inputValue)) {
            // Exibir a mensagem de erro. Você precisará de uma função ou lógica para isso.
            Mensagem_Canto("A entrada deve conter apenas números.",'error'); 
            
            // Opcional: Limpar o campo de entrada e focar nele novamente.
            $('#inputTagInv').val('');
            $('#inputTagInv').focus();
            
            return; // Interrompe a execução aqui.
        }
                    
                    // 1. Aguarda a função assíncrona terminar (incluindo o AJAX e o modal)
                   await inventariar_local_pilotos_(); 

                    await get_pilotos_em_inventario()

                    // 2. Garante que o input está limpo (se a lógica de limpeza estiver na função)
                    // e, em seguida, define o foco.
                    $('#inputTagInv').focus(); // Use o seletor $('#inputTag') diretamente
                
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
                
        $('#totalPecasTransito').text(
                    Number(data.data[0]['em Transito']).toLocaleString('pt-BR')
                );  

        $('#totalPecasMontagem').text(
                    Number(data.data[0]['na Montagem']).toLocaleString('pt-BR')
                ); 
        
        $('#totalPecasEAT').text(
                    Number(data.data[0]['Sala EAT']).toLocaleString('pt-BR')
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

const get_pilotos_em_inventario = async (empresa) => {
    $('#loadingModal').modal('show');
    try {

        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'get_pilotos_inv_dia',
            }
        });

      
        Tabela_detalhada_piloto_inventariada(data);


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


const obter_fasesDestinos = async (empresa) => {
    try {
        const data = await $.ajax({
            type: 'GET',
            url: 'requests.php?acao=fases_destinos',
            dataType: 'json',
            // ... (inclua 'data: { empresa: empresa }' se necessário)
        });
        
        // --- ALTERAÇÃO AQUI: Retorna o 'data' completo, que é o array de objetos ---
        return data; 

    } catch (error) {
        console.error("Erro ao carregar destinos:", error);
        return [];
    }
};

// 2. Função para popular o ComboBox
const popularComboBoxDestinos = async () => {
    // 1. Obtém o array de objetos
    const destinos = await obter_fasesDestinos('123'); // Use o valor correto para 'empresa'

    // 2. Seleciona o elemento <select>
    const selectElement = document.getElementById('selectDestino');

    // Limpa opções (mantendo o placeholder)
    selectElement.innerHTML = '<option selected disabled value="">Selecione um Destino...</option>';

    // 3. Itera sobre o array de OBJETOS e cria as opções
    if (Array.isArray(destinos)) {
        destinos.forEach(destino => {
            const option = document.createElement('option');
            
            // --- ALTERAÇÕES AQUI: Usando as propriedades do objeto ---
            // O valor a ser enviado ao servidor será o "codFase"
            option.value = destino.codFase; 
            
            // O texto visível para o usuário será o "fase"
            option.textContent = destino.fase;
            
            selectElement.appendChild(option);
        });
    } else {
        console.warn("O retorno de destinos não é um array válido.");
    }
};

// 4. Chama a função após o carregamento do documento
$(document).ready(function() {
    popularComboBoxDestinos();
});




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


async function receber_tag() {
    $('#loadingModal').modal('show');

    try {
        const requestData = {
            acao: "ReceberPilotos",
            dados: {
                // CORREÇÃO: Usar .text() ou .html() para obter o valor da label/span
                // O .text() é mais seguro para pegar apenas o conteúdo de texto.                
                "matricula": $('#inputMatriculaReceb').val(),
                "codbarras": $('#inputTagReceb').val(),
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
                                $('#inputTagReceb').val('').focus();

                    // Não continue com o sucesso se houver erro
                    return; 
                }
            }
            // --- FIM DA CORREÇÃO/ADICIONAL ---
            
            // Se o status for true ou se não houver um status de erro claro:
            await Mensagem_Canto('Salvo com sucesso', 'success')
            
            // Opcional: Limpar o campo de TAG após a transferência bem-sucedida
            $('#inputTagReceb').val('').focus();
        
        
    } catch (error) {
        console.error('Erro na solicitação AJAX:', error);
        Mensagem_Canto('Erro', 'error')
    } finally {
        $('#loadingModal').modal('hide');
    }
}


async function inventariar_local_pilotos_() {
    $('#loadingModal').modal('show');

    try {
        const requestData = {
            acao: "inventariar_local_pilotos",
            dados: {
                // CORREÇÃO: Usar .text() ou .html() para obter o valor da label/span
                // O .text() é mais seguro para pegar apenas o conteúdo de texto.                
                "matricula": $('#inputMatriculaInv').val(),
                "codbarras": $('#inputTagInv').val(),
                "local": $('#labelLocal').text(),

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
                                $('#inputTagInv').val('').focus();

                    // Não continue com o sucesso se houver erro
                    return; 
                }
            }
            // --- FIM DA CORREÇÃO/ADICIONAL ---
            
            // Se o status for true ou se não houver um status de erro claro:
            await Mensagem_Canto('Salvo com sucesso', 'success')
            
            // Opcional: Limpar o campo de TAG após a transferência bem-sucedida
            $('#inputTagInv').val('').focus();
        
        
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
            // A classe do seu input é: search-input search-input-pesquisa
            // Seletor CORRETO (assumindo que ambas as classes estão no MESMO elemento):
            $('.search-input.search-input-pesquisa').on('input', function () {
                tabela.column($(this).closest('th').index()).search($(this).val()).draw();
            });
            }



function Tabela_detalhada_pilotos_transferidas(lista) {
   // Verifica se já existe uma instância da DataTable e a destrói para evitar erros
    if ($.fn.DataTable.isDataTable('#tabelaTagsInseridas')) {
        $('#tabelaTagsInseridas').DataTable().destroy();
    }

    // Inicializa a DataTable com as configurações.
    // A variável 'tabela' é opcional se não for usada posteriormente.
    const tabela = $('#tabelaTagsInseridas').DataTable({
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

}



function Tabela_detalhada_piloto_inventariada(lista) {
    // Verifica se já existe uma instância da DataTable e a destrói para evitar erros
    if ($.fn.DataTable.isDataTable('#tabelaTagsInv')) {
        $('#tabelaTagsInv').DataTable().destroy();
    }

    // Inicializa a DataTable com as configurações.
    // A variável 'tabela' é opcional se não for usada posteriormente.
    const tabela = $('#tabelaTagsInv').DataTable({
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
            { data: 'codBarrasTag', title: 'Código de Barras', width: '40%' },
            { data: 'local', title: 'Local', width: '20%' },
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

}