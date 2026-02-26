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
                const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${qrData}`;

                // Se houver separador na URL, monta a tag dele, senão deixa vazio
                const tagSeparador = primeiroNomeSeparador 
                    ? `<span title="${separadorCompleto}">Sep: <strong>${primeiroNomeSeparador}</strong></span>` 
                    : ``;

        const cardHTML = `
            <div class="card card-etiqueta shadow-sm" style="width: 10.1cm; height: 2.5cm; overflow: hidden; border: 1px solid #030303; background-color: #fff; margin-bottom: 5px;">
                <div class="card-body d-flex flex-row align-items-center" style="height: 100%; padding: 0.1rem 0.2rem 0.1rem 1cm;">
                    
                    <div class="d-flex flex-column justify-content-between h-100" style="flex-grow: 1; padding-right: 5px; min-width: 0;">
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="text-primary" style="font-size: 2.0rem; line-height: 1;">${item.codMaterialEdt}</strong>
                            <strong class="badge bg-secondary" style="font-size: 0.9rem;">${item.localizacao}</strong>
                        </div>
                        
                        <div class="text-truncate text-dark fw-bold" style="font-size: 1.1rem; margin: 2px 0;" title="${item.nomeMaterial}">
                            ${item.nomeMaterial}
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-end" style="font-size: 0.9rem;">
                            <span><strong>OP:</strong> <strong>${item.numOPConfec}</strong></span>
                            ${tagSeparador}
                            <span><strong>Qtd:</strong> <strong class="text-success" style="font-size: 1.2rem;">${item.qtdeRequisitada}</strong></span>
                        </div>
                        
                    </div>

                    <div class="d-flex justify-content-center align-items-center" style="width: 85px; min-width: 85px;">
                        <img src="${qrUrl}" alt="QR" style="width: 75px; height: 75px; mix-blend-mode: multiply;">
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