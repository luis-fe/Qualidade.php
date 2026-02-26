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
    <div class="card card-etiqueta">
        <div class="card-body">
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
                <div class="d-flex justify-content-between align-items-center">
                    <strong style="font-size: 2.0rem; line-height: 1;">${item.codMaterialEdt}</strong>
                    <span class="badge bg-secondary" style="font-size: 0.8rem;">${item.localizacao}</span>
                </div>
                <div style="font-size: 1.1rem; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    ${item.nomeMaterial}
                </div>
                <div class="d-flex justify-content-between align-items-end" style="font-size: 0.9rem;">
                    <span>OP: <strong>${item.numOPConfec}</strong></span>
                    ${tagSeparador}
                    <span>Qtd: <strong class="text-success" style="font-size: 1.2rem;">${item.qtdeRequisitada}</strong></span>
                </div>
            </div>
            <div style="width: 85px; min-width: 85px; display: flex; justify-content: flex-end; align-items: center;">
                <img src="${qrUrl}" style="width: 75px; height: 75px;">
            </div>
        </div>
    </div>`;
                
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