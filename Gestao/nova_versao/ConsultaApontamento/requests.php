<?php
// session_start();
// if (isset($_SESSION['username']) && isset($_SESSION['empresa'])) {
//     $username = $_SESSION['username'];
//     $empresa = $_SESSION['empresa'];
// } else {
//     header("Location: ../../index.php");
// }

function jsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        if (isset($_GET["acao"])) {
            $acao = $_GET["acao"];
            switch ($acao) {
                case 'Consultar_Apontamentos_OP':
                    jsonResponse(ConsultarApontamentosOP('1', $_GET['op'] ?? ''));
                    break;
                case 'Ver_Imagem':
                    VerImagem('1', $_GET['caminho'] ?? '');
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação GET não reconhecida.']);
                    break;
            }
        }
        break;
    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}

/**
 * Lista os apontamentos de defeito de uma OP (GET /api/ApontamentoDefeito).
 * A tela de Apontamento grava a identificação em caixa alta, então a OP
 * digitada é normalizada do mesmo jeito antes do filtro de igualdade.
 * Retorno: status (bool), message (string) e dados — lista com dataHora,
 * dataApontamento, usuario, motivoDefeito, detalhamento, caminhoImg e
 * imagemDisponivel, do apontamento mais recente para o mais antigo.
 */
function ConsultarApontamentosOP($empresa, $op)
{
    $op = mb_strtoupper(trim((string) $op), 'UTF-8');

    if ($op === '') {
        return ['status' => false, 'message' => 'Informe a OP para consultar.', 'dados' => []];
    }

    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/api/ApontamentoDefeito?op=" . urlencode($op);
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a44pcp22",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
        curl_close($ch);

        return ['status' => false, 'message' => 'Não foi possível consultar: falha de comunicação com a API.', 'dados' => []];
    }

    curl_close($ch);

    $lista = json_decode($apiResponse, true);

    if (!is_array($lista)) {
        error_log('Resposta inesperada da API de apontamentos: ' . $apiResponse, 0);

        return ['status' => false, 'message' => 'Resposta inesperada da API ao consultar.', 'dados' => []];
    }

    return ['status' => true, 'op' => $op, 'dados' => $lista];
}

/**
 * Devolve a foto de um apontamento de defeito (JPEG) a partir do caminho
 * gravado em pcp."ApntamentoDefeito". Faz o papel de proxy do
 * GET /api/ApontamentoDefeitoImagem, que valida o caminho (só aceita
 * arquivos dentro do /dados do servidor).
 */
function VerImagem($empresa, $caminho)
{
    $caminho = trim((string) $caminho);

    if ($caminho === '') {
        http_response_code(404);
        jsonResponse(['status' => false, 'message' => 'Caminho da imagem não informado.']);
    }

    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/api/ApontamentoDefeitoImagem?caminhoImg=" . urlencode($caminho);
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: a44pcp22",
    ]);

    $apiResponse = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
        curl_close($ch);

        http_response_code(502);
        jsonResponse(['status' => false, 'message' => 'Falha de comunicação com a API de imagens.']);
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        http_response_code(404);
        jsonResponse(['status' => false, 'message' => 'Imagem não encontrada.']);
    }

    header('Content-Type: image/jpeg');
    // A foto de um apontamento não muda (trocar = excluir e gravar outra),
    // então o navegador pode guardar em cache ao repetir a consulta
    header('Cache-Control: private, max-age=3600');
    echo $apiResponse;
    exit;
}
