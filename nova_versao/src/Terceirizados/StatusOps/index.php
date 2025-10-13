<?php
include_once("requests.php");
include_once("../../../templates/Loading.php");
include_once("../../../templates/header.php");
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="style2.css">
<style>
    .search-container {
        max-width: 300px;
        margin: 5px auto;
        border-radius: 10px;
        transition: box-shadow 0.3s ease;
    }

    .search-container:hover {
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .input-group .form-control {
        height: 45px;
        /* ajuste conforme necessário */
    }

    .input-group .btn {
        height: 45px;
        /* mesma altura do input */
    }

    .ops-list {
        max-height: 440px;
        overflow-y: auto;
        margin-top: 10px;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px;
    }

    .op-item {
        padding: 10px;
        border-bottom: 1px solid lightgray;
    }

    .op-item:last-child {
        border-bottom: none;
    }

    .btn-op {
        width: 100%;
        display: block;
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        transition: background-color 0.3s ease;
        cursor: pointer;
    }

    .btn-op:hover {
        background-color: #e0e0e0;
    }

    .op-details {
        text-align: left;
    }

    .op-number {
        font-weight: bold;
    }

    .op-priority {
        color: #ff5722;
    }

    .op-status {
        color: #4caf50;
    }

    .btn-primary {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-top-right-radius: 25px;
        border-bottom-right-radius: 25px;
        background-color: lightgray;
        border: 0.5px solid black;
        padding: 10px 20px;
        color: black;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    #qrCodeModal {
        display: none;
        position: fixed;
        z-index: 999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 60px;
    }

    .modal-content {
        background-color: #fefefe;
        margin: 20% auto;
        border: 1px solid #888;
        width: 80%;
    }
</style>

<div class="titulo" style="padding: 10px; text-align: left; border-bottom: 1px solid black; color: black; font-size: 15px; font-weight: 600;">
    <i class="icon ph-bold ph-target"></i> Status das Op's
</div>
<div class="corpo" style="text-align: left;">
    <div class="menu-tela" style="border-bottom: 1px solid lightgray; margin-top: 10px; padding-left: 10px;">
        <button class="btn btn-menus" id="btn-ops" onclick="$('#btn-ops').addClass('btn-menu-clicado'); $('#btn-faccionistas').removeClass('btn-menu-clicado');">
            <i class="icon ph-bold ph-folders" style="margin-right: 5px;"></i>Por Op
        </button>
    </div>
    <div class="search-container">
        <form id="searchForm">
            <div class="input-group mb-3">
                <input type="text" id="input-search-op" class="form-control" placeholder="Pesquisar Op" aria-label="Pesquisa">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="button-qrCode">
                        <i class="icon ph-bold ph-qr-code"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="ops-list" id="opsList">
        <!-- Os itens de OPs serão inseridos aqui -->
    </div>
</div>

<div id="qrCodeModal" class="modal">
    <div class="modal-content">
        <button type="button" class="btn-close" aria-label="Close" style="margin-left: 10px; margin-bottom: 10px"></button>
        <div id="reader" style="width: 100%; height: 50px;"></div>
        <div id="result" style="margin-top: 10px;"></div>
    </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="max-height: 80vh; overflow: auto">
            <div class="modal-header">
                <h5 class="modal-title" id="TituloModal" style="color: black;"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="statusSelect">Selecione um Status:</label>
                    <select id="statusSelect" class="form-control">
                        <!-- As opções serão inseridas aqui -->
                    </select>
                </div>
                <div class="form-group mt-1">
                    <label for="justificativa">Justificativa:</label>
                    <textarea id="justificativa" class="form-control" rows="4" placeholder="Digite sua justificativa aqui..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="Inserir_Status()">Salvar</button>
            </div>
        </div>
    </div>
</div>


<?php include_once("../../../templates/footer.php"); ?>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    $(document).ready(async () => {
        $('#loadingModal').modal('show');
        await Consultar_Faccionistas();
        await Consultar_Status_Disponiveis();
        $('#loadingModal').modal('hide');
        $('#btn-ops').addClass('btn-menu-clicado')
    });

    const html5QrCode = new Html5Qrcode("reader");
    const opsList = $('#opsList');
    let currentOffset = 0;
    const limit = 10;
    let allOps = [];

    async function loadOps(ops) {
        const itemsToLoad = ops.slice(currentOffset, currentOffset + limit);
        if (itemsToLoad.length === 0) return;

        itemsToLoad.forEach((op) => {
            const opItem = $(`
            <div class="op-item">
                <button class="btn-op" data-op-id="${op.numeroOP}" onclick="const opId = this.getAttribute('data-op-id'); openStatusModal(opId)">
                    <div class="op-details">
                        <span class="op-number">Número Op: ${op.numeroOP}</span><br>
                        <span class="op-number">Qtd: ${op.carga}</span><br>
                        <span class="op-priority">Prioridade: ${op.prioridade}</span><br>
                        <span class="op-status">Status: ${op.status}</span><br>
                        <span class="op-number">Fac: ${op.apelidofaccionista}</span><br>
                        <span class="op-number">Dias na fase: ${op.leadtime}</span><br>
                    </div>
                </button>
            </div>
        `);

            // Define o estilo diretamente no botão se o status for "Recolhido"
            if (op.status === "Recolhido") {
                opItem.find('.btn-op').css({
                    backgroundColor: 'lightGreen', // Cor de fundo verde
                    border: 'none', // Remover borda
                    cursor: 'pointer' // Cursor de ponteiro
                });
                opItem.find('.op-priority').css({
                    color: 'black',
                    fontWeight: '600'
                })
                opItem.find('.op-status').css({
                    color: 'black',
                    fontWeight: '600'
                })

            }

            if (op.status === "Nao Iniciada") {
                opItem.find('.btn-op').css({
                    backgroundColor: '#D2691E',
                    border: 'none',
                    cursor: 'pointer',
                    color: 'white'
                });
                opItem.find('.op-priority').css({
                    color: 'white',
                    fontWeight: '600'
                })
                opItem.find('.op-status').css({
                    color: 'white',
                    fontWeight: '600'
                })

            }

            opsList.append(opItem);
            currentOffset++;
        });
    }



    function openStatusModal(opId) {
        $('#statusModal').modal('show'); // Mostra a modal
        $('#TituloModal').text(`${opId}`); // Preenche o título da modal
    }

    $('#input-search-op').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterOps(searchTerm);
    });

    $('#input-search-op').on('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const searchTerm = $(this).val().toLowerCase();
            filterOps(searchTerm);
        }
    });

    function filterOps(searchTerm) {
        opsList.empty();

        if (searchTerm === '') {
            currentOffset = 0;
            loadOps(allOps);
            return;
        }

        const filteredOps = allOps.filter(op =>
            op.numeroOP.toString().toLowerCase().includes(searchTerm) ||
            op.apelidofaccionista.toString().toLowerCase().includes(searchTerm) ||
            op.status.toString().toLowerCase().includes(searchTerm)
        );

        currentOffset = 0;
        loadOps(filteredOps);
    }

    $('#button-qrCode').on('click', () => {
        $('#qrCodeModal').show();
        html5QrCode.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                },
                (decodedText) => {
                    $('#input-search-op').val(decodedText); // Atualiza o campo de pesquisa com o QR code
                    filterOps(decodedText.toLowerCase()); // Filtra a OP correspondente
                    closeModal();
                })
            .catch(err => {
                console.error(err);
            });
    });

    $(window).on('click', (event) => {
        if ($(event.target).is('#qrCodeModal')) {
            closeModal();
        }
    });

    function closeModal() {
        $('#qrCodeModal').hide();
        html5QrCode.stop();
    }

    async function Consultar_Faccionistas() {
        $('#loadingModal').modal('show');

        try {
            const requestData = {
                acao: "Consultar_Faccionistas",
                dados: {}
            };

            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });

            allOps = response[0]["2- Detalhamento:"]; // Armazena todas as OPs
            currentOffset = 0;
            loadOps(allOps); // Carrega as OPs

        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
            alert('Ocorreu um erro ao buscar os dados. Tente novamente.');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    async function Inserir_Status() {
        $('#loadingModal').modal('show');

        try {
            const requestData = {
                acao: "Inserir_Status",
                dados: {
                    "statusTerceirizado": $('#statusSelect').val(),
                    "numeroOP": $('#TituloModal').text(),
                    "usuario": Usuario,
                    "justificativa": $('#justificativa').val(),
                }
            };

            const response = await $.ajax({
                type: 'POST',
                url: 'requests.php',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
            });
            console.log(response)
            if (response[0]['Mensagem'] === 'Apontado com sucesso') {
                await Consultar_Faccionistas
                Mensagem('Status Apontado', 'success')
            } else {
                Mensagem('Erro', 'error')
            }
        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
            alert('Ocorreu um erro ao buscar os dados. Tente novamente.');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }



    async function Consultar_Status_Disponiveis() {
        $('#loadingModal').modal('show');
        try {
            const response = await $.ajax({
                type: 'GET',
                url: 'requests.php',
                dataType: 'json',
                data: {
                    acao: 'Consultar_Status',
                }
            });

            $('#statusSelect').empty(); // Limpa o select existente

            // Adiciona a opção não clicável
            $('#statusSelect').append($('<option>', {
                value: '',
                text: 'Selecione um Status',
                disabled: true,
                selected: true
            }));

            // Adiciona as opções ao select
            response.forEach(status => {
                $('#statusSelect').append($('<option>', {
                    value: status.statusterceirizado,
                    text: status.statusterceirizado
                }));
            });

        } catch (error) {
            console.error('Erro na solicitação AJAX:', error);
            alert('Ocorreu um erro ao buscar os dados. Tente novamente.');
        } finally {
            $('#loadingModal').modal('hide');
        }
    }

    opsList.on('scroll', () => {
        if (opsList.scrollTop() + opsList.innerHeight() >= opsList[0].scrollHeight) {
            loadOps(allOps);
        }
    });
</script>
