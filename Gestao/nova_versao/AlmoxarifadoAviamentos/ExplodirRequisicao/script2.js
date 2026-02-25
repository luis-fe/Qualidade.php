$(document).ready(async () => {
    // Busca os dados ao carregar a página
    await Consultar_requisicao();
});

async function Consultar_requisicao() {
    try {
        $('#loadingModal').modal('show');

        // Captura o separador diretamente da URL da página
        const urlParams = new URLSearchParams(window.location.search);
        const separadorCompleto = urlParams.get('separador') || '';
        
        // Pega apenas o primeiro nome para caber no card (Ex: "LUIS FERNANDO G." -> "LUIS")
        const primeiroNomeSeparador = separadorCompleto.split(' ')[0];

        const response = await $.ajax({
            type: 'GET',
            url: 'requests.php',
            dataType: 'json',
            data: {
                acao: 'Consultar_requisicao',
                codEmpresa: '1',
                codRequisicao: $('#numRequisicao').val() || '',
            },
        });

        // Limpa o container
        $('#container-cards').empty();

        if (response && response.length > 0) {
            
            response.forEach(item => {
                const qrData = encodeURIComponent(item.codMaterialEdt);
                const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=65x65&data=${qrData}`;

                // Se houver separador na URL, monta a tag dele, senão deixa vazio
                const tagSeparador = primeiroNomeSeparador 
                    ? `<span title="${separadorCompleto}">Sep: <strong>${primeiroNomeSeparador}</strong></span>` 
                    : ``;

                const cardHTML = `
                    <div class="card shadow-sm" style="width: 10.1cm; height: 2.4cm; overflow: hidden; border: 1px solid #adb5bd; background-color: #fff;">
                        <div class="card-body d-flex flex-row align-items-center p-1" style="height: 100%;">
                            
                            <div class="d-flex flex-column justify-content-between h-100" style="width: calc(100% - 65px); padding-right: 5px; min-width: 0;">
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-primary" style="font-size: 1.25rem;">${item.codMaterialEdt}</strong>
                                    <strong class="badge bg-secondary" style="font-size: 0.55rem;">${item.localizacao}</strong>
                                </div>
                                
                                <div class="text-truncate text-muted fw-bold" style="font-size: 1.25rem; line-height: 1.5; " title="${item.nomeMaterial}">
                                    <strong>${item.nomeMaterial}</strong>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-end mt-0" style="font-size: 0.95rem;">
                                    <span>OP: <strong>${item.numOPConfec}</strong></span>
                                    
                                    ${tagSeparador}
                                    
                                    <span>Qtd: <strong class="text-success" style="font-size: 1.05rem;">${item.qtdeRequisitada}</strong></span>
                                </div>
                                
                            </div>

                            <div class="d-flex justify-content-center align-items-center" style="width: 65px; min-width: 65px; height: 65px;">
                                <img src="${qrUrl}" alt="QR Code ${item.codMaterialEdt}" style="max-width: 100%; max-height: 100%; mix-blend-mode: multiply;">
                            </div>
                            
                        </div>
                    </div>
                `;
                
                $('#container-cards').append(cardHTML);
            });

        } else {
            $('#container-cards').html('<div class="alert alert-warning w-100">Nenhum material encontrado para esta requisição.</div>');
        }

    } catch (error) {
        console.error('Erro na requisição AJAX:', error);
        alert('Erro ao comunicar com o servidor.');
    } finally {
        $('#loadingModal').modal('hide');
    }
}

function abrirModalImpressao() {
    window.print();
}