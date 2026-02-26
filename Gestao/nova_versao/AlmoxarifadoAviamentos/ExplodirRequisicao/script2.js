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
    <div class="card card-etiqueta" style="width: 10.1cm; height: 2.6cm; box-sizing: border-box; overflow: hidden; background-color: #fff; border: 1px solid #000;">
        <div class="card-body d-flex flex-row align-items-center" style="height: 100%; padding: 0.1cm 0.2cm 0.1cm 1cm; box-sizing: border-box;">
            
            <div class="d-flex flex-column justify-content-between h-100" style="flex-grow: 1; min-width: 0; overflow: hidden;">
                
                <div class="d-flex justify-content-between align-items-center">
                    <strong style="font-size: 2.2rem; line-height: 1; color: #000;">${item.codMaterialEdt}</strong>
                    <span style="font-size: 0.9rem; border: 1px solid #000; padding: 2px 5px;">${item.localizacao}</span>
                </div>
                
                <div style="font-size: 1.2rem; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 100%;">
                    ${item.nomeMaterial}
                </div>
                
                <div class="d-flex justify-content-between align-items-end" style="font-size: 1rem;">
                    <span>OP: <strong>${item.numOPConfec}</strong></span>
                    ${tagSeparador}
                    <span>Qtd: <strong style="font-size: 1.3rem;">${item.qtdeRequisitada}</strong></span>
                </div>
                
            </div>

            <div style="width: 80px; min-width: 80px; text-align: right; margin-left: auto;">
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