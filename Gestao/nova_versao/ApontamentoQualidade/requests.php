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
                case 'Consultar_Apontamentos':
                    $dataInicial = $_GET['dataInicial'] ?? date('Y-m-d');
                    $dataFinal = $_GET['dataFinal'] ?? date('Y-m-d');
                    jsonResponse(ConsultarApontamentos('1', $dataInicial, $dataFinal));
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação GET não reconhecida.']);
                    break;
            }
        }
        break;
    case "POST":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;
        if ($acao) {
            switch ($acao) {
                case 'Apontar_Qualidade':
                    jsonResponse(ApontarQualidade('1', $dados));
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação POST não reconhecida.']);
                    break;
            }
        }
        break;
    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}

/**
 * Grava o apontamento de qualidade da OP.
 * Espera em $dados: op (string), dataApontamento (Y-m-d), responsavel
 * (string) e fotos (lista de data URLs "data:image/jpeg;base64,...",
 * pode vir vazia).
 * Retorno: status (bool) e message (string).
 */
function ApontarQualidade($empresa, $dados)
{
    $op = trim((string) ($dados['op'] ?? ''));
    $dataApontamento = (string) ($dados['dataApontamento'] ?? '');
    // A tela já envia em caixa alta; aqui é garantia de que o nome grave
    // sempre no mesmo formato, venha de onde vier
    $responsavel = mb_strtoupper(trim((string) ($dados['responsavel'] ?? '')), 'UTF-8');
    $fotos = $dados['fotos'] ?? [];

    if ($op === '') {
        return ['status' => false, 'message' => 'OP não informada.'];
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataApontamento)) {
        return ['status' => false, 'message' => 'Data do apontamento inválida.'];
    }

    if ($responsavel === '') {
        return ['status' => false, 'message' => 'Responsável não informado.'];
    }

    if (!is_array($fotos)) {
        $fotos = [];
    }

    // A tela já envia JPEG reduzido; aqui só o prefixo do data URL é retirado,
    // para a API receber base64 puro
    $imagens = [];
    foreach ($fotos as $foto) {
        if (!is_string($foto) || $foto === '') {
            continue;
        }

        $imagens[] = preg_replace('#^data:image/[a-z+.-]+;base64,#i', '', $foto);
    }

    $payload = [
        'empresa' => $empresa,
        'op' => $op,
        'data_apontamento' => $dataApontamento,
        'responsavel' => $responsavel,
        'fotos' => $imagens,
    ];

    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/api/ApontamentoQualidade";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a44pcp22",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
        curl_close($ch);

        return ['status' => false, 'message' => 'Não foi possível apontar: falha de comunicação com a API.'];
    }

    curl_close($ch);

    $resposta = json_decode($apiResponse, true);

    // A tela só limpa as fotos quando status === true, então uma resposta fora
    // do formato precisa virar erro explícito e não um "apontado" silencioso
    if (!is_array($resposta) || !isset($resposta['status'])) {
        error_log('Resposta inesperada da API de apontamento: ' . $apiResponse, 0);

        return ['status' => false, 'message' => 'Resposta inesperada da API ao apontar.'];
    }

    return $resposta;
}

function ConsultarApontamentos($empresa, $dataInicial, $dataFinal)
{
    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/api/ApontamentoQualidade?data_inicio={$dataInicial}&data_fim={$dataFinal}";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a44pcp22",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}
