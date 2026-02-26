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
                const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=${qrData}`;

                // Se houver separador na URL, monta a tag dele, senão deixa vazio
                const tagSeparador = primeiroNomeSeparador 
                    ? `<span title="${separadorCompleto}">Sep: <strong>${primeiroNomeSeparador}</strong></span>` 
                    : ``;

const cardHTML = `
    <div class="card card-etiqueta" style="background-color: #fff; border: none; overflow: hidden; width: 10.0cm; height: 3.0cm;">
        <div class="card-body-custom d-flex flex-row align-items-start" style="height: 100%; padding: 0.2cm 0.3cm 0.2cm 1.0cm; box-sizing: border-box;">
            
            <div style="flex: 1; display: flex; flex-direction: column; height: 100%; min-width: 0;">
                
                <div style="height: 1.6cm; display: flex; flex-direction: column; justify-content: flex-start;">
                    <strong style="font-size: 1.8rem; line-height: 1; color: #000; display: block; margin: 0;">
                        ${item.codMaterialEdt}
                    </strong>
                    <div style="font-size: 1.2rem; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;">
                        ${item.nomeMaterial}
                    </div>
                </div>
                
                <div style="height: 0.5cm;"></div>

                <div class="d-flex justify-content-between align-items-center" style="font-size: 0.95rem; flex: 1;">
                    <strong>OP: <strong>${item.numOPConfec}</strong></strong>
                    <strong>${tagSeparador}</strong>
                    <strong>Qtd: <strong style="font-size: 1.3rem;">${item.qtdeRequisitada}</strong></strong>
                </div>
            </div>

            <div style="width: 80px; display: flex; flex-direction: column; align-items: flex-end; height: 100%;">
                
                <div style="background-color: #000; color: #fff; font-weight: bold; font-size: 0.85rem; padding: 2px 6px; border-radius: 3px; text-align: center; white-space: nowrap; line-height: 1.2;">
                    ${item.localizacao}
                </div>

                <div style="height: 0.5cm;"></div>

                <div style="display: flex; align-items: center; justify-content: center;">
                    <img src="${qrUrl}" style="width: 70px; height: 70px; mix-blend-mode: multiply; display: block;">
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