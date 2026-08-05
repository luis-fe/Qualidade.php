/* ============================================================
   Apontamento Qualidade — tela mobile
   Frame de apontamento (OP + data) e câmera para registro fotográfico.
   ============================================================ */

// Maior lado da foto gravada. A câmera do celular entrega 3000px+, o que
// estoura o POST em base64 sem ganho nenhum de leitura do defeito.
const LADO_MAXIMO = 1280;
const QUALIDADE_JPEG = 0.72;

// Onde o nome do responsável fica guardado entre um apontamento e outro.
// localStorage e não uma variável solta: um refresh acidental no meio do
// turno não pode obrigar o operador a digitar o nome de novo.
const CHAVE_RESPONSAVEL = 'apontamentoQualidade:responsavel';
const MINIMO_NOME = 3;

const video = document.getElementById('cameraVideo');
const canvas = document.getElementById('cameraCanvas');
const palco = document.querySelector('.camera-palco');
const cortina = document.getElementById('cameraCortina');
const cortinaIcone = document.getElementById('cameraCortinaIcone');
const cortinaTexto = document.getElementById('cameraCortinaTexto');
const btnLigar = document.getElementById('btnLigarCamera');
const btnVirar = document.getElementById('btnVirarCamera');
const btnCapturar = document.getElementById('btnCapturar');
const btnLimpar = document.getElementById('btnLimparFotos');
const btnApontar = document.getElementById('btnApontar');
const campoOp = document.getElementById('campoOp');
const campoData = document.getElementById('campoData');
const campoArquivo = document.getElementById('campoArquivo');
const contador = document.getElementById('cameraContador');
const listaFotos = document.getElementById('fotos');

const blocoResponsavel = document.getElementById('blocoResponsavel');
const responsavelNome = document.getElementById('responsavelNome');
const btnTrocarResponsavel = document.getElementById('btnTrocarResponsavel');
const elementoModal = document.getElementById('modalResponsavel');
const formResponsavel = document.getElementById('formResponsavel');
const campoResponsavel = document.getElementById('campoResponsavel');
const responsavelAviso = document.getElementById('responsavelAviso');

let stream = null;
let cameraTraseira = true;
let fotos = [];

let modalResponsavel = null;
let responsavelGravado = '';
// Preenchidos enquanto o modal está aberto: quem pediu o nome fica esperando
// o fechamento, seja por confirmar, cancelar, Esc ou toque fora
let resolverResponsavel = null;
let responsavelConfirmado = '';

/* ------------------------------------------------------------
   Cabeçalho — o título congelado fica logo abaixo da navbar
   ------------------------------------------------------------ */

// A navbar do headerGestao.php é sticky no topo; o título precisa parar
// exatamente na altura dela, que muda com a fonte e a largura da tela.
function ajustarTituloFixo() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    document.documentElement.style.setProperty(
        '--altura-navbar',
        `${Math.round(navbar.getBoundingClientRect().height)}px`
    );
}

/* ------------------------------------------------------------
   Data do apontamento
   ------------------------------------------------------------ */

// toISOString() converte para UTC e, à noite, devolveria o dia seguinte
function dataDeHoje() {
    const agora = new Date();
    const mes = String(agora.getMonth() + 1).padStart(2, '0');
    const dia = String(agora.getDate()).padStart(2, '0');

    return `${agora.getFullYear()}-${mes}-${dia}`;
}

/* ------------------------------------------------------------
   Responsável pelo apontamento
   ------------------------------------------------------------ */

function normalizarNome(valor) {
    // Espaços repetidos no meio do nome viram um só, senão o mesmo operador
    // entra na API com grafias diferentes
    return String(valor || '').trim().replace(/\s+/g, ' ').toUpperCase();
}

// Usuário já identificado na página pelo login do próprio sistema.
// Existindo, o modal nunca é aberto — o nome vem de quem entrou.
function usuarioDaPagina() {
    if (window.usuarioAtivo && window.usuarioAtivo.nome) {
        return normalizarNome(window.usuarioAtivo.nome);
    }

    const info = document.getElementById('info-usuario-logado');
    const nome = document.getElementById('header-nome-usuario');

    if (info && !info.classList.contains('d-none') && nome) {
        return normalizarNome(nome.textContent);
    }

    return '';
}

function responsavelAtual() {
    return usuarioDaPagina() || responsavelGravado;
}

// localStorage pode estar bloqueado (navegação privada, política do
// aparelho); nesse caso o nome vale só enquanto a página estiver aberta
function lerResponsavelGravado() {
    try {
        return normalizarNome(localStorage.getItem(CHAVE_RESPONSAVEL));
    } catch (erro) {
        console.log(erro);
        return '';
    }
}

function gravarResponsavel(nome) {
    responsavelGravado = nome;

    try {
        localStorage.setItem(CHAVE_RESPONSAVEL, nome);
    } catch (erro) {
        console.log(erro);
    }

    renderizarResponsavel();
}

function renderizarResponsavel() {
    const atual = responsavelAtual();

    responsavelNome.textContent = atual;
    blocoResponsavel.classList.toggle('oculto', atual === '');

    // Quem veio do login da página não é trocado por aqui
    btnTrocarResponsavel.classList.toggle('oculto', usuarioDaPagina() !== '');
}

function pedirResponsavel() {
    return new Promise(resolve => {
        resolverResponsavel = resolve;
        responsavelConfirmado = '';
        responsavelAviso.textContent = '';
        campoResponsavel.value = responsavelGravado;

        modalResponsavel.show();
    });
}

// Devolve o nome a usar no apontamento, ou string vazia se o operador
// desistiu de informar
async function garantirResponsavel() {
    const atual = responsavelAtual();

    if (atual) {
        return atual;
    }

    return await pedirResponsavel();
}

/* ------------------------------------------------------------
   Câmera
   ------------------------------------------------------------ */

function mostrarCortina(icone, texto, comBotao) {
    cortinaIcone.className = `bi ${icone}`;
    cortinaTexto.textContent = texto;
    btnLigar.classList.toggle('oculto', !comBotao);
    cortina.classList.remove('oculto');
    btnVirar.classList.add('oculto');
    btnCapturar.disabled = true;
}

function esconderCortina() {
    cortina.classList.add('oculto');
    btnCapturar.disabled = false;
}

function cameraDisponivel() {
    return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
}

function pararCamera() {
    if (!stream) return;

    stream.getTracks().forEach(faixa => faixa.stop());
    stream = null;
    video.srcObject = null;
}

async function ligarCamera() {
    // getUserMedia só existe em contexto seguro (https ou localhost). Em
    // http a galeria/câmera nativa do celular é o caminho que sobra.
    if (!cameraDisponivel()) {
        const semTls = location.protocol !== 'https:' && location.hostname !== 'localhost';

        mostrarCortina(
            'bi-camera-video-off-fill',
            semTls
                ? 'A câmera ao vivo exige acesso por HTTPS. Use o botão da galeria para tirar a foto pelo aplicativo do celular.'
                : 'Este navegador não permite abrir a câmera. Use o botão da galeria.',
            false
        );
        return;
    }

    pararCamera();

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: cameraTraseira ? { ideal: 'environment' } : { ideal: 'user' },
                width: { ideal: 1920 },
                height: { ideal: 1080 }
            },
            audio: false
        });

        video.srcObject = stream;
        await video.play();

        esconderCortina();
        btnVirar.classList.remove('oculto');
    } catch (erro) {
        const negada = erro && (erro.name === 'NotAllowedError' || erro.name === 'SecurityError');

        mostrarCortina(
            'bi-camera-video-off-fill',
            negada
                ? 'Permissão de câmera negada. Libere o acesso nas configurações do navegador e tente de novo.'
                : 'Não foi possível abrir a câmera deste aparelho. Use o botão da galeria.',
            true
        );
        console.log(erro);
    }
}

async function virarCamera() {
    cameraTraseira = !cameraTraseira;
    await ligarCamera();
}

/* ------------------------------------------------------------
   Fotos
   ------------------------------------------------------------ */

function renderizarFotos() {
    listaFotos.innerHTML = '';

    fotos.forEach((foto, indice) => {
        const item = document.createElement('div');
        item.className = 'foto';
        item.innerHTML = `
            <img src="${foto.imagem}" alt="Foto ${indice + 1} do apontamento">
            <button type="button" class="foto-remover" data-indice="${indice}"
                    title="Remover foto" aria-label="Remover foto ${indice + 1}">
                <i class="bi bi-x-lg"></i>
            </button>`;
        listaFotos.appendChild(item);
    });

    contador.textContent = fotos.length;
    contador.classList.toggle('oculto', fotos.length === 0);
}

// Reduz o quadro mantendo a proporção e devolve o JPEG em base64
function reduzirParaJpeg(fonte, larguraFonte, alturaFonte) {
    const escala = Math.min(1, LADO_MAXIMO / Math.max(larguraFonte, alturaFonte));

    canvas.width = Math.round(larguraFonte * escala);
    canvas.height = Math.round(alturaFonte * escala);

    canvas.getContext('2d').drawImage(fonte, 0, 0, canvas.width, canvas.height);

    return canvas.toDataURL('image/jpeg', QUALIDADE_JPEG);
}

function capturar() {
    if (!stream || !video.videoWidth) {
        Mensagem('Ative a câmera antes de capturar.', 'warning');
        return;
    }

    fotos.push({
        imagem: reduzirParaJpeg(video, video.videoWidth, video.videoHeight),
        origem: 'camera'
    });

    renderizarFotos();

    // Piscada de confirmação, como o obturador da câmera do celular
    palco.classList.add('piscou');
    setTimeout(() => palco.classList.remove('piscou'), 300);
}

// Caminho alternativo: foto tirada pelo aplicativo do celular ou da galeria
function carregarArquivos(arquivos) {
    Array.from(arquivos).forEach(arquivo => {
        if (!arquivo.type.startsWith('image/')) return;

        const leitor = new FileReader();

        leitor.onload = () => {
            const img = new Image();

            img.onload = () => {
                fotos.push({
                    imagem: reduzirParaJpeg(img, img.naturalWidth, img.naturalHeight),
                    origem: 'galeria'
                });
                renderizarFotos();
            };

            img.src = leitor.result;
        };

        leitor.readAsDataURL(arquivo);
    });
}

async function limparFotos() {
    if (fotos.length === 0) return;

    const confirmacao = await Swal.fire({
        title: 'Descartar as fotos?',
        text: `${fotos.length} foto(s) serão removidas deste apontamento.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Descartar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#b02a37'
    });

    if (!confirmacao.isConfirmed) return;

    fotos = [];
    renderizarFotos();
}

/* ------------------------------------------------------------
   Apontamento
   ------------------------------------------------------------ */

function marcarInvalido(campo) {
    campo.classList.add('invalido');
    campo.focus();
    campo.addEventListener('input', () => campo.classList.remove('invalido'), { once: true });
}

async function apontar() {
    const op = campoOp.value.trim();
    const data = campoData.value;

    if (!op) {
        Mensagem('Informe a OP para apontar.', 'warning');
        marcarInvalido(campoOp);
        return;
    }

    if (!data) {
        Mensagem('Informe a data do apontamento.', 'warning');
        marcarInvalido(campoData);
        return;
    }

    // Perguntado só na primeira vez: depois o nome já está na tela
    const responsavel = await garantirResponsavel();

    if (!responsavel) {
        Mensagem('Informe o responsável para apontar.', 'warning');
        return;
    }

    // Apontar sem foto é permitido, mas nunca por descuido
    if (fotos.length === 0) {
        const confirmacao = await Swal.fire({
            title: 'Nenhuma foto capturada',
            text: 'Deseja apontar a OP sem registro fotográfico?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Apontar assim',
            cancelButtonText: 'Voltar e capturar'
        });

        if (!confirmacao.isConfirmed) return;
    }

    btnApontar.disabled = true;
    $('#loadingModal').modal('show');

    try {
        const resposta = await fetch('requests.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                acao: 'Apontar_Qualidade',
                dados: {
                    op: op,
                    dataApontamento: data,
                    responsavel: responsavel,
                    fotos: fotos.map(foto => foto.imagem)
                }
            })
        });

        const retorno = await resposta.json();

        if (retorno.status) {
            Mensagem(retorno.message || 'Apontamento registrado.', 'success');

            // A tela fica pronta para a próxima OP; a data é mantida porque
            // o apontamento costuma ser feito em série no mesmo dia
            fotos = [];
            renderizarFotos();
            campoOp.value = '';
            campoOp.focus();
        } else {
            Mensagem(retorno.message || 'Não foi possível apontar.', 'error');
        }
    } catch (erro) {
        console.log(erro);
        Mensagem('Falha de comunicação ao apontar.', 'error');
    } finally {
        $('#loadingModal').modal('hide');
        btnApontar.disabled = false;
    }
}

/* ------------------------------------------------------------
   Eventos
   ------------------------------------------------------------ */

btnLigar.addEventListener('click', ligarCamera);
btnVirar.addEventListener('click', virarCamera);
btnCapturar.addEventListener('click', capturar);
btnLimpar.addEventListener('click', limparFotos);
btnApontar.addEventListener('click', apontar);

campoArquivo.addEventListener('change', evento => {
    carregarArquivos(evento.target.files);
    // Zera para permitir escolher o mesmo arquivo outra vez
    evento.target.value = '';
});

// O text-transform do CSS é só visual; aqui o valor em si vira caixa alta.
// A posição do cursor é preservada porque o comprimento não muda.
campoResponsavel.addEventListener('input', () => {
    const inicio = campoResponsavel.selectionStart;
    const fim = campoResponsavel.selectionEnd;
    const emCaixaAlta = campoResponsavel.value.toUpperCase();

    if (campoResponsavel.value !== emCaixaAlta) {
        campoResponsavel.value = emCaixaAlta;
        campoResponsavel.setSelectionRange(inicio, fim);
    }

    responsavelAviso.textContent = '';
});

formResponsavel.addEventListener('submit', evento => {
    evento.preventDefault();

    const nome = normalizarNome(campoResponsavel.value);

    if (nome.length < MINIMO_NOME) {
        responsavelAviso.textContent = `Informe o nome do responsável com pelo menos ${MINIMO_NOME} letras.`;
        campoResponsavel.focus();
        return;
    }

    responsavelConfirmado = nome;
    gravarResponsavel(nome);
    modalResponsavel.hide();
});

btnTrocarResponsavel.addEventListener('click', async () => {
    await pedirResponsavel();
});

// Um único ponto de saída: vale tanto para confirmar quanto para
// cancelar, Esc ou toque fora do modal
elementoModal.addEventListener('hidden.bs.modal', () => {
    if (!resolverResponsavel) return;

    const resolver = resolverResponsavel;
    resolverResponsavel = null;
    resolver(responsavelConfirmado);
});

elementoModal.addEventListener('shown.bs.modal', () => campoResponsavel.focus());

campoOp.addEventListener('keydown', evento => {
    if (evento.key === 'Enter') {
        evento.preventDefault();
        campoOp.blur();
    }
});

window.addEventListener('resize', ajustarTituloFixo);

// Sair da aba com a câmera ligada mantém a luz acesa e consome bateria
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        pararCamera();
        if (cortina.classList.contains('oculto')) {
            mostrarCortina('bi-camera-fill', 'Câmera pausada. Toque para reativar.', true);
        }
    }
});

window.addEventListener('pagehide', pararCamera);

document.addEventListener('DOMContentLoaded', () => {
    ajustarTituloFixo();
    campoData.value = dataDeHoje();
    btnCapturar.disabled = true;

    modalResponsavel = new bootstrap.Modal(elementoModal);
    responsavelGravado = lerResponsavelGravado();
    renderizarResponsavel();

    if (!cameraDisponivel()) {
        // Já avisa antes do toque: em http o botão de ativar nunca funcionaria
        ligarCamera();
    }
});
