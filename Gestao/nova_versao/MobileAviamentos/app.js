document.addEventListener('DOMContentLoaded', () => {
    const btnCamera = document.getElementById('btn-camera');
    const inputEndereco = document.getElementById('endereco');
    const readerDiv = document.getElementById('reader');
    let html5QrCode;

    // Função que roda quando um código é lido com sucesso
    const onScanSuccess = (decodedText, decodedResult) => {
        // Preenche o campo de texto com o valor do código lido
        inputEndereco.value = decodedText;
        
        // Emite um bip sonoro (feedback opcional, muito útil em WMS)
        // const audio = new Audio('beep.mp3'); audio.play();

        // Para a câmera e esconde a área de vídeo
        html5QrCode.stop().then(() => {
            readerDiv.style.display = 'none';
            html5QrCode.clear();
        }).catch((err) => {
            console.error("Erro ao parar a câmera: ", err);
        });
    };

    // Função principal do botão de câmera
    btnCamera.addEventListener('click', () => {
        // Se a câmera já estiver aberta, clicar no botão novamente a fecha
        if (readerDiv.style.display === 'block') {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    readerDiv.style.display = 'none';
                    html5QrCode.clear();
                });
            }
            return;
        }

        // Mostra a div da câmera na tela
        readerDiv.style.display = 'block';

        // Inicializa o leitor apontando para a div "reader"
        html5QrCode = new Html5Qrcode("reader");
        
        // Configurações da câmera
        const config = { 
            fps: 10, // Quadros por segundo
            qrbox: { width: 250, height: 150 } // Área de foco (ideal para códigos de barra de WMS)
        };

        // Inicia forçando o uso da câmera traseira (environment)
        html5QrCode.start(
            { facingMode: "environment" }, 
            config,
            onScanSuccess,
            (errorMessage) => {
                // Erros de leitura ignorados (acontecem enquanto o foco não ajusta)
            }
        ).catch((err) => {
            console.error("Erro ao iniciar a câmera", err);
            alert("Erro: Não foi possível acessar a câmera. Verifique se você deu permissão no navegador.");
            readerDiv.style.display = 'none';
        });
    });

    // Ação do botão de confirmar
    document.getElementById('btn-confirmar').addEventListener('click', () => {
        const endereco = inputEndereco.value;
        if (!endereco) {
            alert('Por favor, digite ou leia um endereço primeiro.');
            return;
        }
        
        alert('Endereço confirmado: ' + endereco);
        // AQUI ENTRA SUA LÓGICA PHP:
        // Você pode fazer um window.location.href = "salvar_endereco.php?end=" + endereco;
        // Ou usar Fetch API para enviar via POST sem recarregar a tela.
    });
});