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
                const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=${qrData}`;

                // Se houver separador na URL, monta a tag dele, senão deixa vazio
                const tagSeparador = primeiroNomeSeparador 
                    ? `<span title="${separadorCompleto}">Sep: <strong>${primeiroNomeSeparador}</strong></span>` 
                    : ``;

const cardHTML = `
    <div class="card card-etiqueta" style="background-color: #fff; border: none;">
        <div class="card-body-custom d-flex flex-row align-items-start" style="height: 100%;">
            
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between; height: 100%; min-width: 0; padding-right: 5px;">
                
                <div>
                    <strong style="font-size: 1.2rem; line-height: 1; color: #000; display: block;">
                        ${item.codMaterialEdt}
                    </strong>
                    <div style="font-size: 1.1rem; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 2px;">
                        ${item.nomeMaterial}
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-end" style="font-size: 0.95rem; width: 100%;">
                    <span>OP: <strong>${item.numOPConfec}</strong></span>
                    ${tagSeparador}
                    <span>Qtd: <strong style="font-size: 1.3rem;">${item.qtdeRequisitada}</strong></span>
                </div>
            </div>

            <div style="width: 95px; display: flex; flex-direction: column; align-items: flex-end; height: 100%;">
                
                <div style="background-color: #000; color: #fff; font-weight: bold; font-size: 0.9rem; padding: 0px 0px; border-radius: 3px; text-align: center; width: auto; white-space: nowrap; margin-top: 0.5cm; margin-bottom: 5px;">
                    ${item.localizacao}
                </div>

                <div style="margin-top: auto;">
                    <img src="${qrUrl}" style="width: 70px; height: 70px; mix-blend-mode: multiply;">
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