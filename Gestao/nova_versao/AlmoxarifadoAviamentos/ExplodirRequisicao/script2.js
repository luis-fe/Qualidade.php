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
                const qrData = encodeURIComponent(item.numOPConfec + '||' + item.codMaterialEdt);
                const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=${qrData}`;

                // Se houver separador na URL, monta a tag dele, senão deixa vazio
                const tagSeparador = primeiroNomeSeparador 
                    ? `<span title="${separadorCompleto}">Sep: <strong>${primeiroNomeSeparador}</strong></span>` 
                    : ``;

const cardHTML = `
            <div class="card card-etiqueta" style="border: none; background-color: #fff; margin: 0; padding: 0; border-radius: 0; width: 10.9cm; height: 2.8cm; page-break-after: always; box-sizing: border-box;">
                <div class="card-body d-flex flex-row align-items-center justify-content-between p-1" style="height: 100%; gap: 0.2cm; padding-left: 1cm !important; padding-right: 0.1cm !important;">
            
                    <div class="d-flex flex-column justify-content-center" style="width: 1.5cm; overflow: hidden; font-family: Arial, sans-serif;">
                
                <div style="height: 1.6cm; display: flex; flex-direction: column; justify-content: flex-start;">
                    <strong style="font-size: 1.75rem; line-height: 1; color: #000; display: block; margin: 0;">
                        ${item.codMaterialEdt}
                    </strong>
                    <div style="font-size: 1.1rem; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;">
                        ${item.nomeMaterial}
                    </div>
                </div>
                
                <div style="height: 0.25cm;"></div>

                <div class="d-flex justify-content-between align-items-center" style="font-size: 1.0rem; flex: 1;">
                    <strong>OP: <strong>${item.numOPConfec}</strong></strong>
                    <strong>${tagSeparador}</strong>
                    <strong>Qtd: <strong style="font-size: 1.5rem;">${item.qtdeRequisitada}</strong></strong>
                </div>
            </div>

            <div style="width: 80px; display: flex; flex-direction: column; align-items: flex-end; height: 100%;">
                <div style="height: 0.0cm;"></div>
                <div style="display: flex; align-items: center; justify-content: center;">
                    <img src="${qrUrl}" style="width: 90px; height: 90px; mix-blend-mode: multiply; display: block;">
                </div>
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