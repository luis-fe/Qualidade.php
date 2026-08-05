/* ============================================================
   Consulta Apontamento — fotos dos defeitos apontados por OP
   O usuário digita a OP; a tela lista cada foto gravada com o
   motivo do defeito logo abaixo, mais data, responsável e a
   observação quando houver.
   ============================================================ */

const campoOP = document.getElementById('campoOP');
const btnConsultar = document.getElementById('btnConsultar');
const resultado = document.getElementById('resultado');
const resultadoOP = document.getElementById('resultadoOP');
const resultadoTotal = document.getElementById('resultadoTotal');
const galeria = document.getElementById('galeria');
const estadoVazio = document.getElementById('estadoVazio');
const opSemFoto = document.getElementById('opSemFoto');

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

ajustarTituloFixo();
window.addEventListener('resize', ajustarTituloFixo);

/* ------------------------------------------------------------
   Consulta
   ------------------------------------------------------------ */

async function consultar() {
    const op = campoOP.value.trim().toUpperCase();

    if (!op) {
        Mensagem('Informe a OP para consultar.', 'warning');
        campoOP.focus();
        return;
    }

    btnConsultar.disabled = true;
    $('#loadingModal').modal('show');

    try {
        const resposta = await fetch(`requests.php?acao=Consultar_Apontamentos_OP&op=${encodeURIComponent(op)}`);
        const retorno = await resposta.json();

        if (!retorno.status) {
            Mensagem(retorno.message || 'Não foi possível consultar os apontamentos.', 'error');
            return;
        }

        renderizarResultado(retorno.op || op, Array.isArray(retorno.dados) ? retorno.dados : []);
    } catch (erro) {
        console.log(erro);
        Mensagem('Falha de comunicação ao consultar os apontamentos.', 'error');
    } finally {
        $('#loadingModal').modal('hide');
        btnConsultar.disabled = false;
    }
}

/* ------------------------------------------------------------
   Galeria — um card por apontamento: foto + motivo abaixo
   ------------------------------------------------------------ */

function renderizarResultado(op, apontamentos) {
    galeria.innerHTML = '';

    // Só entram os apontamentos que têm foto para mostrar
    const comFoto = apontamentos.filter(item => item.caminhoImg && item.caminhoImg !== '-');

    if (comFoto.length === 0) {
        resultado.classList.add('oculto');
        opSemFoto.textContent = op;
        estadoVazio.classList.remove('oculto');
        return;
    }

    estadoVazio.classList.add('oculto');
    resultadoOP.textContent = op;
    resultadoTotal.textContent = comFoto.length;

    comFoto.forEach(item => galeria.appendChild(montarCard(item)));

    resultado.classList.remove('oculto');
}

function montarCard(item) {
    const card = document.createElement('article');
    card.className = 'card-foto';

    // Foto — servida pelo proxy da tela; se o arquivo sumiu do volume,
    // o onerror troca a imagem pelo aviso
    const quadro = document.createElement('div');
    quadro.className = 'card-foto-quadro';

    const imagem = document.createElement('img');
    imagem.alt = 'Foto do defeito apontado';
    imagem.loading = 'lazy';
    imagem.src = `requests.php?acao=Ver_Imagem&caminho=${encodeURIComponent(item.caminhoImg)}`;
    imagem.addEventListener('error', () => {
        quadro.innerHTML = '<p class="card-foto-erro"><i class="bi bi-exclamation-triangle"></i> Foto indisponível</p>';
    });

    quadro.appendChild(imagem);
    card.appendChild(quadro);

    // Motivo do defeito logo abaixo da foto
    const motivo = document.createElement('p');
    motivo.className = 'card-foto-motivo';
    motivo.textContent = texto(item.motivoDefeito, 'Motivo não informado');
    card.appendChild(motivo);

    // Observação digitada na captura, quando houver
    const detalhamento = texto(item.detalhamento, '');
    if (detalhamento) {
        const observacao = document.createElement('p');
        observacao.className = 'card-foto-observacao';
        observacao.textContent = detalhamento;
        card.appendChild(observacao);
    }

    // Data do apontamento e responsável
    const meta = document.createElement('p');
    meta.className = 'card-foto-meta';
    meta.innerHTML = `<span><i class="bi bi-calendar-event"></i> ${dataBr(item.dataApontamento)}</span>`
        + `<span><i class="bi bi-person"></i> ${texto(item.usuario, '—')}</span>`;
    card.appendChild(meta);

    return card;
}

// Campos opcionais chegam com o default '-' da API
function texto(valor, vazio) {
    const limpo = String(valor === undefined || valor === null ? '' : valor).trim();

    return limpo && limpo !== '-' ? limpo : vazio;
}

function dataBr(valor) {
    const limpo = texto(valor, '');

    if (!/^\d{4}-\d{2}-\d{2}$/.test(limpo)) return '—';

    const [ano, mes, dia] = limpo.split('-');

    return `${dia}/${mes}/${ano}`;
}

/* ------------------------------------------------------------
   Eventos
   ------------------------------------------------------------ */

btnConsultar.addEventListener('click', consultar);

campoOP.addEventListener('keydown', (evento) => {
    if (evento.key === 'Enter') {
        evento.preventDefault();
        consultar();
    }
});
