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
                case 'Consultar_Motivos':
                    jsonResponse(ConsultarMotivos('1'));
                    break;
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
 * Grava um apontamento de qualidade.
 * Espera em $dados: tipo ('TAG' ou 'OP'), identificacao (a tag lida ou a
 * OP/referência digitada), dataApontamento (Y-m-d), responsavel (string)
 * e fotos — cada uma com imagem (data URL), motivo e observacao.
 * Retorno: status (bool) e message (string).
 */
function ApontarQualidade($empresa, $dados)
{
    $tipo = mb_strtoupper(trim((string) ($dados['tipo'] ?? '')), 'UTF-8');
    $identificacao = mb_strtoupper(trim((string) ($dados['identificacao'] ?? '')), 'UTF-8');
    $dataApontamento = (string) ($dados['dataApontamento'] ?? '');
    // A tela já envia em caixa alta; aqui é garantia de que o nome grave
    // sempre no mesmo formato, venha de onde vier
    $responsavel = mb_strtoupper(trim((string) ($dados['responsavel'] ?? '')), 'UTF-8');
    $fotos = $dados['fotos'] ?? [];

    if (!in_array($tipo, ['TAG', 'OP'], true)) {
        return ['status' => false, 'message' => 'Tipo de apontamento inválido.'];
    }

    if ($identificacao === '') {
        return ['status' => false, 'message' => $tipo === 'TAG' ? 'Tag não informada.' : 'OP / Referência não informada.'];
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

    // A tela já envia JPEG reduzido; aqui só o prefixo do data URL é
    // retirado, para a API receber base64 puro
    $imagens = [];
    foreach ($fotos as $foto) {
        $imagem = is_array($foto) ? (string) ($foto['imagem'] ?? '') : '';

        if ($imagem === '') {
            continue;
        }

        $imagens[] = [
            'imagem' => preg_replace('#^data:image/[a-z+.-]+;base64,#i', '', $imagem),
            'motivo' => trim((string) ($foto['motivo'] ?? '')),
            'observacao' => trim((string) ($foto['observacao'] ?? '')),
        ];
    }

    if (count($imagens) === 0) {
        return ['status' => false, 'message' => 'Nenhuma foto no apontamento.'];
    }

    $payload = [
        'empresa' => $empresa,
        'tipo' => $tipo,
        'identificacao' => $identificacao,
        // Repetido no campo próprio de cada tipo para a API não precisar
        // interpretar o 'tipo' antes de gravar
        'tag' => $tipo === 'TAG' ? $identificacao : '',
        'op' => $tipo === 'OP' ? $identificacao : '',
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

/**
 * Motivos de 2ª qualidade oferecidos na hora de capturar a foto.
 * Vem da mesma API que alimenta o painel "Defeitos por Motivo" da Gestão
 * da Qualidade, que é agregada por período — por isso a janela larga de
 * 12 meses: motivo que não ocorreu nesse intervalo não aparece na lista.
 * Retorno: lista de {motivo, qtd}, do mais frequente para o menos.
 */
function ConsultarMotivos($empresa)
{
    $dataFinal = date('Y-m-d');
    $dataInicial = date('Y-m-d', strtotime('-12 months'));

    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/api/MotivosAgrupado?textoAvancado=&data_inicio={$dataInicial}&data_fim={$dataFinal}";
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

        return ['status' => false, 'message' => 'Não foi possível carregar os motivos.', 'dados' => []];
    }

    curl_close($ch);

    $lista = json_decode($apiResponse, true);

    if (!is_array($lista)) {
        return ['status' => false, 'message' => 'Resposta inesperada da API de motivos.', 'dados' => []];
    }

    // O mesmo motivo pode voltar repetido na agregação; aqui vira lista
    // única de nomes, somando as quantidades
    $motivos = [];
    foreach ($lista as $item) {
        $nome = trim((string) ($item['motivo2Qualidade'] ?? ''));

        if ($nome === '') {
            continue;
        }

        $chave = mb_strtoupper($nome, 'UTF-8');

        if (!isset($motivos[$chave])) {
            $motivos[$chave] = ['motivo' => $nome, 'qtd' => 0];
        }

        $motivos[$chave]['qtd'] += (int) ($item['qtd'] ?? 0);
    }

    $motivos = array_values($motivos);
    usort($motivos, function ($a, $b) {
        return $b['qtd'] <=> $a['qtd'];
    });

    return ['status' => true, 'dados' => $motivos];
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
