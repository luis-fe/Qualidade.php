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

// Modos de identificação da peça. Só o Tag usa a câmera para ler QR Code;
// em OP/Referência a câmera serve apenas para fotografar.
const MODOS = {
    TAG: {
        rotulo: 'Informe a Tag',
        curto: 'Tag',
        icone: 'bi-qr-code-scan',
        exemplo: 'Leia o QR Code ou digite',
        leQrCode: true
    },
    OP: {
        rotulo: 'Informe a OP / Referência',
        curto: 'OP / Ref.',
        icone: 'bi-upc-scan',
        exemplo: 'Ex.: 123456',
        leQrCode: false
    }
};

// Intervalo entre tentativas de leitura do QR. Abaixo disso o jsQR ocupa
// a thread e a prévia da câmera engasga nos aparelhos mais fracos.
const INTERVALO_LEITURA = 160;
// Largura em que o quadro é analisado: o QR é lido de sobra e o custo
// por tentativa cai muito em relação ao quadro cheio
const LARGURA_LEITURA = 480;

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
const btnRelerTag = document.getElementById('btnRelerTag');
const blocoSessao = document.getElementById('sessao');
const sessaoTipo = document.getElementById('sessaoTipo');
const sessaoIdentificacao = document.getElementById('sessaoIdentificacao');
const sessaoData = document.getElementById('sessaoData');
const sessaoGravados = document.getElementById('sessaoGravados');
const campoIdentificacao = document.getElementById('campoIdentificacao');
const rotuloIdentificacao = document.getElementById('rotuloIdentificacao');
const iconeIdentificacao = document.getElementById('iconeIdentificacao');
const mira = document.getElementById('mira');
const btnDigitarTag = document.getElementById('btnDigitarTag');
const elementoModalModo = document.getElementById('modalModo');
const elementoModalMotivo = document.getElementById('modalMotivo');
const elementoModalLiberar = document.getElementById('modalLiberarCamera');
const formMotivo = document.getElementById('formMotivo');
const previaFoto = document.getElementById('previaFoto');
const buscaMotivo = document.getElementById('buscaMotivo');
const listaMotivos = document.getElementById('listaMotivos');
const campoObservacao = document.getElementById('campoObservacao');
const motivoAviso = document.getElementById('motivoAviso');
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

let modalModo = null;
let resolverModo = null;
let modoConfirmado = '';

let modalLiberar = null;

let modalMotivo = null;
let resolverMotivo = null;
let motivoConfirmado = null;
let motivos = [];
let motivoEscolhido = '';

// Leitura de QR: canvas próprio para não brigar com o canvas da captura,
// que é redimensionado a cada foto
const canvasLeitura = document.createElement('canvas');
let temporizadorLeitura = null;

// Verdadeiro no modo Tag enquanto a peça ainda não foi identificada: segura a
// captura até a tag ser bipada (ou lida pela câmera, quando disponível)
let aguardandoTag = false;

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
   Modo de apontamento
   ------------------------------------------------------------ */

// Perguntado a cada início: a forma de identificar a peça muda de uma
// série para outra, então não faz sentido lembrar a escolha anterior
function perguntarModo() {
    return new Promise(resolve => {
        resolverModo = resolve;
        modoConfirmado = '';

        modalModo.show();
    });
}

function aplicarModo(modo) {
    const config = MODOS[modo];

    rotuloIdentificacao.textContent = config.rotulo;
    campoIdentificacao.placeholder = config.exemplo;
    iconeIdentificacao.className = `bi ${config.icone}`;
}

/* ------------------------------------------------------------
   Leitura de QR Code — só no modo Tag
   ------------------------------------------------------------ */

function pararLeitura() {
    if (temporizadorLeitura) {
        clearInterval(temporizadorLeitura);
        temporizadorLeitura = null;
    }

    mira.classList.add('oculto');
}

// Um quadro reduzido do vídeo é suficiente para o jsQR e sai muito mais
// barato que analisar a resolução cheia da câmera
function lerQrCodeDoQuadro() {
    if (!stream || !video.videoWidth) return null;

    const escala = Math.min(1, LARGURA_LEITURA / video.videoWidth);

    canvasLeitura.width = Math.round(video.videoWidth * escala);
    canvasLeitura.height = Math.round(video.videoHeight * escala);

    const contexto = canvasLeitura.getContext('2d', { willReadFrequently: true });
    contexto.drawImage(video, 0, 0, canvasLeitura.width, canvasLeitura.height);

    const quadro = contexto.getImageData(0, 0, canvasLeitura.width, canvasLeitura.height);
    const achado = jsQR(quadro.data, quadro.width, quadro.height, { inversionAttempts: 'dontInvert' });

    return achado && achado.data ? achado.data.trim() : null;
}

// Bipagem: em http a câmera não abre, então a tag é lida pelo leitor de
// código de barras/QR direto no campo, como no conferencia. O Enter do leitor
// confirma a tag (ver o keydown de campoIdentificacao). A câmera, quando
// disponível, roda em paralelo e também confirma pela leitura do vídeo.
function iniciarBipagem() {
    aguardandoTag = true;
    mira.classList.add('oculto');
    btnCapturar.disabled = true;

    campoIdentificacao.disabled = false;
    campoIdentificacao.value = '';
    campoIdentificacao.placeholder = 'Aguardando leitura…';
    campoIdentificacao.focus();
}

function iniciarLeitura() {
    pararLeitura();
    aguardandoTag = true;

    if (typeof jsQR !== 'function') {
        // Sem a biblioteca sobra digitar; a leitura é conveniência, não requisito
        Mensagem('Leitor de QR Code indisponível. Digite a tag.', 'warning');
        perguntarTagDigitada();
        return;
    }

    mira.classList.remove('oculto');
    // Fotografar antes de saber a tag gravaria a foto na peça errada
    btnCapturar.disabled = true;

    temporizadorLeitura = setInterval(() => {
        let lido = null;

        try {
            lido = lerQrCodeDoQuadro();
        } catch (erro) {
            console.log(erro);
        }

        if (lido) {
            confirmarIdentificacao(lido);
        }
    }, INTERVALO_LEITURA);
}

// Fecha a leitura e libera a captura das fotos daquela tag
function confirmarIdentificacao(valor) {
    if (!sessao) return;

    pararLeitura();
    aguardandoTag = false;

    sessao.identificacao = valor;
    sessaoIdentificacao.textContent = valor;
    campoIdentificacao.value = valor;   // deixa a tag lida visível no campo
    // Só há o que capturar com a câmera ligada; em http a foto vem da galeria
    btnCapturar.disabled = !stream;

    // Feedback tátil: no chão de fábrica nem sempre dá para olhar a tela
    if (navigator.vibrate) {
        navigator.vibrate(60);
    }

    Mensagem_Canto(`Tag ${valor} lida.`, 'success');
}

async function perguntarTagDigitada() {
    const resposta = await Swal.fire({
        title: 'Digitar a tag',
        input: 'text',
        inputPlaceholder: 'Tag da peça',
        inputAttributes: { autocapitalize: 'characters', autocomplete: 'off' },
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Voltar a ler',
        inputValidator: valor => (valor && valor.trim() ? undefined : 'Informe a tag.')
    });

    if (resposta.isConfirmed) {
        confirmarIdentificacao(resposta.value.trim().toUpperCase());
    }
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
    // Enquanto a tag não foi lida a captura continua bloqueada, senão a
    // foto entraria no apontamento sem peça identificada
    btnCapturar.disabled = aguardandoTag;
}

function cameraDisponivel() {
    return !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
}

// Verdadeiro quando a página está em http fora de localhost: é aqui que o
// navegador bloqueia a câmera ao vivo e não mostra o popup nativo de permissão
function contextoInseguro() {
    return location.protocol !== 'https:' &&
           location.hostname !== 'localhost' &&
           location.hostname !== '127.0.0.1';
}

// Copia texto para a área de transferência. navigator.clipboard só existe em
// contexto seguro; em http caímos no textarea + execCommand, que ainda funciona
async function copiarTexto(texto) {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(texto);
            return true;
        }
    } catch (_) { /* cai no fallback abaixo */ }

    try {
        const area = document.createElement('textarea');
        area.value = texto;
        area.setAttribute('readonly', '');
        area.style.position = 'fixed';
        area.style.opacity = '0';
        document.body.appendChild(area);
        area.select();
        const ok = document.execCommand('copy');
        document.body.removeChild(area);
        return ok;
    } catch (_) {
        return false;
    }
}

// Popup de liberação: abre sozinho ao carregar em http. Em https/localhost a
// câmera funciona direto e este popup nunca aparece.
function abrirLiberacaoCamera() {
    if (!elementoModalLiberar) return;

    const campoOrigem = document.getElementById('origemPagina');
    if (campoOrigem) campoOrigem.value = location.origin;

    if (!modalLiberar) modalLiberar = new bootstrap.Modal(elementoModalLiberar);
    modalLiberar.show();
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
   Tipo de defeito — perguntado a cada captura
   ------------------------------------------------------------ */

// A mesma lista que alimenta o painel "Defeitos por Motivo" da Gestão da
// Qualidade. Carregada uma vez: não muda no meio de um turno.
async function carregarMotivos() {
    try {
        const resposta = await fetch('requests.php?acao=Consultar_Motivos');
        const retorno = await resposta.json();

        motivos = retorno && retorno.status && Array.isArray(retorno.dados) ? retorno.dados : [];
    } catch (erro) {
        console.log(erro);
        motivos = [];
    }
}

function renderizarMotivos(filtro) {
    const termo = (filtro || '').trim().toUpperCase();
    const visiveis = termo
        ? motivos.filter(item => item.motivo.toUpperCase().includes(termo))
        : motivos;

    listaMotivos.innerHTML = '';

    if (visiveis.length === 0) {
        const vazio = document.createElement('p');
        vazio.className = 'sem-motivos';
        vazio.textContent = motivos.length === 0
            ? 'Não foi possível carregar os motivos. Descreva o defeito na observação.'
            : 'Nenhum motivo encontrado para o filtro.';
        listaMotivos.appendChild(vazio);
        return;
    }

    visiveis.forEach(item => {
        const botao = document.createElement('button');
        botao.type = 'button';
        botao.className = 'motivo' + (item.motivo === motivoEscolhido ? ' escolhido' : '');
        botao.dataset.motivo = item.motivo;
        botao.innerHTML = `
            <span class="motivo-nome"></span>
            <span class="motivo-qtd">${item.qtd}</span>`;
        // textContent para o nome não injetar marcação vinda da API
        botao.querySelector('.motivo-nome').textContent = item.motivo;
        listaMotivos.appendChild(botao);
    });
}

// Devolve {motivo, observacao} ou null quando o operador descarta a foto
function pedirMotivo(imagem) {
    return new Promise(resolve => {
        resolverMotivo = resolve;
        motivoConfirmado = null;
        motivoEscolhido = '';

        previaFoto.src = imagem;
        buscaMotivo.value = '';
        campoObservacao.value = '';
        motivoAviso.textContent = '';
        renderizarMotivos('');

        modalMotivo.show();
    });
}

/* ------------------------------------------------------------
   Fotos
   ------------------------------------------------------------ */

function renderizarFotos() {
    listaFotos.innerHTML = '';

    fotos.forEach((foto, indice) => {
        const item = document.createElement('div');
        item.className = 'foto';
        item.title = foto.observacao ? `${foto.motivo} — ${foto.observacao}` : foto.motivo;
        item.innerHTML = `
            <img src="${foto.imagem}" alt="Foto ${indice + 1} do apontamento">
            <span class="foto-motivo"></span>
            <button type="button" class="foto-remover" data-indice="${indice}"
                    title="Remover foto" aria-label="Remover foto ${indice + 1}">
                <i class="bi bi-x-lg"></i>
            </button>`;
        item.querySelector('.foto-motivo').textContent = foto.motivo;
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

// A foto só entra na lista depois de classificada: registro fotográfico
// sem tipo de defeito não serve para a análise depois
async function registrarFoto(imagem, origem) {
    const defeito = await pedirMotivo(imagem);

    if (!defeito) return;

    fotos.push({
        imagem: imagem,
        origem: origem,
        motivo: defeito.motivo,
        observacao: defeito.observacao
    });

    renderizarFotos();
}

async function capturar() {
    if (!stream || !video.videoWidth) {
        Mensagem('Ative a câmera antes de capturar.', 'warning');
        return;
    }

    // O quadro é extraído antes do modal abrir, senão a foto seria o que a
    // câmera estava vendo depois da classificação, não no clique
    const imagem = reduzirParaJpeg(video, video.videoWidth, video.videoHeight);

    // Piscada de confirmação, como o obturador da câmera do celular
    palco.classList.add('piscou');
    setTimeout(() => palco.classList.remove('piscou'), 300);

    await registrarFoto(imagem, 'camera');
}

// Caminho alternativo: foto tirada pelo aplicativo do celular ou da galeria
function carregarArquivos(arquivos) {
    Array.from(arquivos).forEach(arquivo => {
        if (!arquivo.type.startsWith('image/')) return;

        const leitor = new FileReader();

        leitor.onload = () => {
            const img = new Image();

            img.onload = () => registrarFoto(
                reduzirParaJpeg(img, img.naturalWidth, img.naturalHeight),
                'galeria'
            );

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
    // No modo Tag o campo recebe a bipagem de cada peça durante a sessão, por
    // isso não é travado; em OP/Referência a identificação é fixa na série
    const bipando = travado && sessao && MODOS[sessao.modo].leQrCode;
    campoIdentificacao.disabled = travado && !bipando;
    campoData.disabled = travado;
    btnIniciar.disabled = travado;
    // Fecha também a troca de responsável pelo cabeçalho
    renderizarResponsavel();
}

async function iniciarApontamento() {
    // A pergunta vem antes de tudo: é ela que define o que vai ser cobrado
    // no campo de identificação
    const modo = await perguntarModo();

    if (!modo) return;

    aplicarModo(modo);

    const config = MODOS[modo];
    const identificacao = campoIdentificacao.value.trim().toUpperCase();
    const data = campoData.value;

    // No modo Tag a identificação vem do QR Code, então o campo pode estar
    // vazio; nos demais ela é obrigatória para começar
    if (!config.leQrCode && !identificacao) {
        Mensagem(`${config.rotulo} para iniciar.`, 'warning');
        marcarInvalido(campoIdentificacao);
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

    sessao = {
        modo: modo,
        identificacao: identificacao,
        data: data,
        responsavel: responsavel,
        gravados: 0
    };

    sessaoTipo.textContent = config.curto;
    sessaoIdentificacao.textContent = identificacao || '—';
    sessaoData.textContent = formatarData(data);
    sessaoGravados.textContent = '0';
    btnRelerTag.classList.toggle('oculto', !config.leQrCode);

    travarCampos(true);
    blocoSessao.classList.remove('oculto');

    fotos = [];
    renderizarFotos();

    // Sem tag prévia no modo Tag, a captura fica bloqueada até a leitura
    aguardandoTag = config.leQrCode && !identificacao;

    // A câmera só existe a partir daqui, e este clique é o gesto do usuário
    // que o navegador exige para liberar o pedido de permissão
    await ligarCamera();

    // Modo Tag sem tag lida: com a câmera aberta, ela procura o QR direto
    // (sem focar o campo, que abriria o teclado sobre a imagem). Sem câmera
    // (http), cai na bipagem por campo focado. O botão "Digitar tag" continua
    // como saída manual nos dois casos.
    if (aguardandoTag) {
        if (stream) {
            iniciarLeitura();
        } else {
            iniciarBipagem();
        }
    }

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

    pararLeitura();
    pararCamera();
    aguardandoTag = false;

    sessao = null;
    fotos = [];
    renderizarFotos();

    blocoSessao.classList.add('oculto');
    mostrarCortina('bi-camera-fill', 'Ative a câmera para registrar a peça.', true);
    travarCampos(false);

    // Data mantida: a série seguinte costuma ser no mesmo dia
    campoIdentificacao.value = '';
    campoIdentificacao.focus();
}

// Volta a ler: a próxima peça da série tem outra tag
function relerTag() {
    if (!sessao || !MODOS[sessao.modo].leQrCode) return;

    if (fotos.length > 0) {
        Mensagem('Grave ou descarte as fotos antes de ler outra tag.', 'warning');
        return;
    }

    sessao.identificacao = '';
    sessaoIdentificacao.textContent = '—';

    // Com câmera, volta a procurar o QR; sem câmera (http), volta à bipagem
    if (stream) {
        iniciarLeitura();
    } else {
        iniciarBipagem();
    }
}

async function gravarApontamento() {
    if (!sessao) return;

    if (!sessao.identificacao) {
        Mensagem('Leia a tag antes de gravar o apontamento.', 'warning');
        return;
    }

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
                    tipo: sessao.modo,
                    identificacao: sessao.identificacao,
                    dataApontamento: sessao.data,
                    responsavel: sessao.responsavel,
                    fotos: fotos.map(foto => ({ imagem: foto.imagem, motivo: foto.motivo, observacao: foto.observacao }))
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
                retorno.message || `Apontamento ${sessao.gravados} gravado em ${sessao.identificacao}.`,
                'success'
            );
        } else {
            // Falha parcial: as fotos que a API já aceitou saem da lista, senão
            // o próximo "Gravar" as reenviaria e duplicaria o apontamento
            if (Array.isArray(retorno.gravadas) && retorno.gravadas.length > 0) {
                fotos = fotos.filter((foto, indice) => !retorno.gravadas.includes(indice));
                renderizarFotos();
            }

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

// Botões "copiar" do popup de liberação (endereço da flag e origem da página)
document.querySelectorAll('#modalLiberarCamera [data-copiar]').forEach((botao) => {
    botao.addEventListener('click', async () => {
        const alvo = document.querySelector(botao.getAttribute('data-copiar'));
        if (!alvo) return;

        const ok = await copiarTexto(alvo.value);
        const icone = botao.querySelector('i');
        if (icone && ok) {
            const antes = icone.className;
            icone.className = 'bi bi-check-lg';
            setTimeout(() => { icone.className = antes; }, 1500);
        }
    });
});

// Botão principal do popup: abre a câmera nativa do celular (funciona em
// http) e fecha o popup, deixando o usuário prosseguir com a foto
const btnUsarCameraCelular = document.getElementById('btnUsarCameraCelular');
if (btnUsarCameraCelular) btnUsarCameraCelular.addEventListener('click', () => {
    if (modalLiberar) modalLiberar.hide();
    campoArquivo.click();
});

// "Já liberei, recarregar": após habilitar a flag e reabrir, recarrega para
// o navegador reavaliar o contexto como seguro
const btnJaLiberei = document.getElementById('btnJaLiberei');
if (btnJaLiberei) btnJaLiberei.addEventListener('click', () => location.reload());
btnCapturar.addEventListener('click', capturar);
btnLimpar.addEventListener('click', limparFotos);
btnIniciar.addEventListener('click', iniciarApontamento);
btnGravar.addEventListener('click', gravarApontamento);
btnEncerrar.addEventListener('click', encerrarSessao);
btnRelerTag.addEventListener('click', relerTag);
btnDigitarTag.addEventListener('click', perguntarTagDigitada);

/* --- Modo de apontamento --- */

elementoModalModo.querySelectorAll('.opcao-modo').forEach(opcao => {
    opcao.addEventListener('click', () => {
        modoConfirmado = opcao.dataset.modo;
        modalModo.hide();
    });
});

elementoModalModo.addEventListener('hidden.bs.modal', () => {
    if (!resolverModo) return;

    const resolver = resolverModo;
    resolverModo = null;
    resolver(modoConfirmado);
});

/* --- Tipo de defeito --- */

buscaMotivo.addEventListener('input', () => renderizarMotivos(buscaMotivo.value));

// Delegação: a lista é redesenhada a cada filtro
listaMotivos.addEventListener('click', evento => {
    const botao = evento.target.closest('.motivo');
    if (!botao) return;

    motivoEscolhido = botao.dataset.motivo;
    motivoAviso.textContent = '';

    listaMotivos.querySelectorAll('.motivo').forEach(item => {
        item.classList.toggle('escolhido', item === botao);
    });
});

formMotivo.addEventListener('submit', evento => {
    evento.preventDefault();

    const observacao = campoObservacao.value.trim();

    // Sem motivos carregados a observação vira o registro do defeito, senão
    // a falha da API travaria o apontamento inteiro
    if (!motivoEscolhido && motivos.length > 0) {
        motivoAviso.textContent = 'Escolha o tipo de defeito.';
        return;
    }

    if (!motivoEscolhido && !observacao) {
        motivoAviso.textContent = 'Descreva o defeito na observação.';
        campoObservacao.focus();
        return;
    }

    motivoConfirmado = {
        motivo: motivoEscolhido || 'NÃO INFORMADO',
        observacao: observacao
    };

    modalMotivo.hide();
});

// Um único ponto de saída: confirmar, descartar, Esc ou toque fora
elementoModalMotivo.addEventListener('hidden.bs.modal', () => {
    if (!resolverMotivo) return;

    const resolver = resolverMotivo;
    resolverMotivo = null;
    resolver(motivoConfirmado);
});

// Delegação: as miniaturas são redesenhadas a cada foto
listaFotos.addEventListener('click', evento => {
    const botao = evento.target.closest('.foto-remover');
    if (!botao) return;

    fotos.splice(Number(botao.dataset.indice), 1);
    renderizarFotos();
});

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

// Leitor de código de barras manda Enter no fim da leitura. Numa sessão Tag
// ativa, cada Enter é uma bipagem: confirma a tag lida. Fora disso, apenas
// fecha o teclado sem submeter nada.
campoIdentificacao.addEventListener('keydown', evento => {
    if (evento.key !== 'Enter') return;
    evento.preventDefault();

    if (sessao && MODOS[sessao.modo].leQrCode) {
        // Só confirma quando está de fato aguardando a peça; depois de lida, a
        // próxima tag passa pelo "Reler tag", que trava se houver foto pendente
        if (aguardandoTag) {
            const valor = campoIdentificacao.value.trim().toUpperCase();
            if (valor) confirmarIdentificacao(valor);
        }
        return;
    }

    campoIdentificacao.blur();
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
    modalModo = new bootstrap.Modal(elementoModalModo);
    modalMotivo = new bootstrap.Modal(elementoModalMotivo);

    usuarioIdentificado = detectarUsuarioDaPagina();
    responsavelGravado = lerResponsavelGravado();
    renderizarResponsavel();

    if (!cameraDisponivel()) {
        // Já avisa antes do toque: em http o botão de ativar nunca funcionaria
        ligarCamera();

        // Em http o popup nativo de permissão nunca aparece; abrimos o nosso
        // já na carga, com o passo a passo para liberar a origem no Chrome
        if (contextoInseguro()) abrirLiberacaoCamera();
    }

    // Em segundo plano: a lista só é necessária na primeira captura
    carregarMotivos();
});
