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
    <div class="card card-etiqueta shadow-sm" style="width: 10.1cm; height: 2.5cm; border: 1px solid #000;">
        <div class="card-body d-flex flex-row align-items-center" style="width: 100%; padding-left: 1cm;">
            
            <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; height: 90%; min-width: 0;">
                
                <div class="d-flex justify-content-between align-items-center">
                    <strong style="font-size: 2.2rem; line-height: 1;">${item.codMaterialEdt}</strong>
                    <span class="badge bg-secondary" style="font-size: 0.9rem;">${item.localizacao}</span>
                </div>
                
                <div style="font-size: 1.2rem; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    ${item.nomeMaterial}
                </div>
                
                <div class="d-flex justify-content-between align-items-end" style="font-size: 1rem;">
                    <span>OP: <strong>${item.numOPConfec}</strong></span>
                    ${tagSeparador}
                    <span>Qtd: <strong style="font-size: 1.4rem;">${item.qtdeRequisitada}</strong></span>
                </div>
            </div>

            <div style="width: 90px; display: flex; justify-content: flex-end;">
                <img src="${qrUrl}" style="width: 80px; height: 80px; mix-blend-mode: multiply;">
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