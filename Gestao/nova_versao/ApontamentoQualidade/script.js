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
const btnIniciar = document.getElementById('btnIniciarApontamento');
const btnGravar = document.getElementById('btnGravarApontamento');
const btnEncerrar = document.getElementById('btnEncerrarSessao');
const blocoSessao = document.getElementById('sessao');
const sessaoOp = document.getElementById('sessaoOp');
const sessaoData = document.getElementById('sessaoData');
const sessaoGravados = document.getElementById('sessaoGravados');
const campoOp = document.getElementById('campoOp');
const campoData = document.getElementById('campoData');
const campoArquivo = document.getElementById('campoArquivo');
const contador = document.getElementById('cameraContador');
const listaFotos = document.getElementById('fotos');

// Área de usuário do headerGestao.php, reaproveitada para o responsável
const infoUsuario = document.getElementById('info-usuario-logado');
const headerNome = document.getElementById('header-nome-usuario');
const headerRotulo = document.getElementById('header-matricula-usuario');
const elementoModal = document.getElementById('modalResponsavel');
const formResponsavel = document.getElementById('formResponsavel');
const campoResponsavel = document.getElementById('campoResponsavel');
const responsavelAviso = document.getElementById('responsavelAviso');

let stream = null;
let cameraTraseira = true;
let fotos = [];

// Sessão aberta pelo "Iniciar Apontamento". Enquanto existir, a OP, a data
// e o responsável ficam travados e cada "Gravar apontamento" registra mais
// um apontamento na mesma OP.
let sessao = null;

let modalResponsavel = null;
let responsavelGravado = '';
let usuarioIdentificado = '';
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
// Lido uma única vez no início: depois disso o cabeçalho passa a exibir o
// responsável, e reler dali devolveria o nome que a própria tela escreveu.
function detectarUsuarioDaPagina() {
    if (window.usuarioAtivo && window.usuarioAtivo.nome) {
        return normalizarNome(window.usuarioAtivo.nome);
    }

    if (infoUsuario && !infoUsuario.classList.contains('d-none') && headerNome) {
        return normalizarNome(headerNome.textContent);
    }

    return '';
}

function responsavelAtual() {
    return usuarioIdentificado || responsavelGravado;
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

// O nome vai para a área de usuário do cabeçalho, ao lado do ícone: é
// informação de contexto, não campo de formulário, e no cabeçalho não
// disputa altura com a câmera
function renderizarResponsavel() {
    const atual = responsavelAtual();
    const podeTrocar = usuarioIdentificado === '' && sessao === null;

    headerNome.textContent = atual;
    // O rótulo avisa que o nome é tocável, sem depender de fonte de ícone
    headerRotulo.textContent = atual ? (podeTrocar ? 'Responsável · trocar' : 'Responsável') : '';
    infoUsuario.classList.toggle('d-none', atual === '');

    // Quem veio do login da página, ou está no meio de uma sessão, não troca
    infoUsuario.classList.toggle('trocavel', podeTrocar);
    infoUsuario.title = podeTrocar ? 'Trocar responsável' : '';
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
   Sessão da OP
   ------------------------------------------------------------ */

function marcarInvalido(campo) {
    campo.classList.add('invalido');
    campo.focus();
    campo.addEventListener('input', () => campo.classList.remove('invalido'), { once: true });
}

function formatarData(data) {
    const [ano, mes, dia] = data.split('-');

    return `${dia}/${mes}/${ano}`;
}

// Campos travados enquanto a sessão está aberta: trocar a OP no meio da
// série gravaria o apontamento seguinte na OP errada
function travarCampos(travado) {
    campoOp.disabled = travado;
    campoData.disabled = travado;
    btnIniciar.disabled = travado;
    // Fecha também a troca de responsável pelo cabeçalho
    renderizarResponsavel();
}

async function iniciarApontamento() {
    const op = campoOp.value.trim();
    const data = campoData.value;

    if (!op) {
        Mensagem('Informe a OP para iniciar.', 'warning');
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
        Mensagem('Informe o responsável para iniciar.', 'warning');
        return;
    }

    sessao = { op: op, data: data, responsavel: responsavel, gravados: 0 };

    sessaoOp.textContent = op;
    sessaoData.textContent = formatarData(data);
    sessaoGravados.textContent = '0';

    travarCampos(true);
    blocoSessao.classList.remove('oculto');

    fotos = [];
    renderizarFotos();

    // A câmera só existe a partir daqui, e este clique é o gesto do usuário
    // que o navegador exige para liberar o pedido de permissão
    await ligarCamera();

    blocoSessao.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

async function encerrarSessao() {
    if (fotos.length > 0) {
        const confirmacao = await Swal.fire({
            title: 'Encerrar sem gravar?',
            text: `${fotos.length} foto(s) capturada(s) ainda não foram gravadas e serão perdidas.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Encerrar assim',
            cancelButtonText: 'Voltar',
            confirmButtonColor: '#b02a37'
        });

        if (!confirmacao.isConfirmed) return;
    }

    pararCamera();

    sessao = null;
    fotos = [];
    renderizarFotos();

    blocoSessao.classList.add('oculto');
    mostrarCortina('bi-camera-fill', 'Ative a câmera para registrar a peça.', true);
    travarCampos(false);

    // Data mantida: a série seguinte costuma ser no mesmo dia
    campoOp.value = '';
    campoOp.focus();
}

async function gravarApontamento() {
    if (!sessao) return;

    if (fotos.length === 0) {
        Mensagem('Capture ao menos uma foto para gravar o apontamento.', 'warning');
        return;
    }

    btnGravar.disabled = true;
    $('#loadingModal').modal('show');

    try {
        const resposta = await fetch('requests.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                acao: 'Apontar_Qualidade',
                dados: {
                    op: sessao.op,
                    dataApontamento: sessao.data,
                    responsavel: sessao.responsavel,
                    fotos: fotos.map(foto => foto.imagem)
                }
            })
        });

        const retorno = await resposta.json();

        if (retorno.status) {
            sessao.gravados += 1;
            sessaoGravados.textContent = sessao.gravados;

            // A sessão continua aberta e a câmera ligada: a mesma OP recebe
            // o próximo apontamento sem redigitar nada
            fotos = [];
            renderizarFotos();

            Mensagem_Canto(
                retorno.message || `Apontamento ${sessao.gravados} gravado na OP ${sessao.op}.`,
                'success'
            );
        } else {
            Mensagem(retorno.message || 'Não foi possível gravar o apontamento.', 'error');
        }
    } catch (erro) {
        console.log(erro);
        Mensagem('Falha de comunicação ao gravar o apontamento.', 'error');
    } finally {
        $('#loadingModal').modal('hide');
        btnGravar.disabled = false;
    }
}

/* ------------------------------------------------------------
   Eventos
   ------------------------------------------------------------ */

btnLigar.addEventListener('click', ligarCamera);
btnVirar.addEventListener('click', virarCamera);
btnCapturar.addEventListener('click', capturar);
btnLimpar.addEventListener('click', limparFotos);
btnIniciar.addEventListener('click', iniciarApontamento);
btnGravar.addEventListener('click', gravarApontamento);
btnEncerrar.addEventListener('click', encerrarSessao);

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

// Trocar o responsável pelo próprio cabeçalho, onde o nome é exibido
infoUsuario.addEventListener('click', async () => {
    if (!infoUsuario.classList.contains('trocavel')) return;

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
    usuarioIdentificado = detectarUsuarioDaPagina();
    responsavelGravado = lerResponsavelGravado();
    renderizarResponsavel();

    if (!cameraDisponivel()) {
        // Já avisa antes do toque: em http o botão de ativar nunca funcionaria
        ligarCamera();
    }
});
