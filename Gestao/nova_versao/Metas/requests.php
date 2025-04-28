<?php

function jsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Detectar método
$method = $_SERVER['REQUEST_METHOD'];

// Buscar ação
if ($method === 'GET') {
    $acao = $_GET['acao'] ?? null;
    $dados = $_GET;
} else {
    $input = file_get_contents('php://input');
    $requestData = json_decode($input, true);
    $acao = $requestData['acao'] ?? null;
    $dados = $requestData['dados'] ?? [];
}

// Se não tiver ação, erro
if (!$acao) {
    jsonResponse(['status' => false, 'message' => 'Ação não informada.']);
}

// Definir ações
switch ($acao) {
    case 'Consulta_Planos':
        jsonResponse(ConsultarPlanos('1'));
        break;

    case 'Consulta_Lotes':
        $plano = $dados['plano'] ?? null;
        jsonResponse(ConsultarLotes('1', $plano));
        break;

    case 'Consultar_Realizados':
        $Fase = $dados['Fase'] ?? null;
        $dataInicial = $dados['dataInicial'] ?? null;
        $dataFinal = $dados['dataFinal'] ?? null;
        jsonResponse(ConsultarRealizados('1', $Fase, $dataInicial, $dataFinal));
        break;

    case 'Consultar_Cronograma':
        $plano = $dados['plano'] ?? null;
        $fase = $dados['fase'] ?? null;
        jsonResponse(ConsultarCronograma('1', $plano, $fase));
        break;

    case 'Consultar_Tipo_Op':
        jsonResponse(ConsultarTipoOp('1'));
        break;

    case 'Consulta_Previsao_Categoria':
        $fase = $dados['fase'] ?? null;
        jsonResponse(ConsultaPrevisaoCategoria($fase));
        break;

    case 'Consulta_Metas':
        jsonResponse(ConsultarMetas('1', $dados));
        break;

    case 'ConsultaFaltaProduzirCategoria_Fase':
        jsonResponse(ConsultaFaltaProduzirCategoria_Fase($dados));
        break;

    default:
        jsonResponse(['status' => false, 'message' => 'Ação não reconhecida.']);
        break;
}


function ConsultarLotes($empresa, $plano)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.183:8000';
    $apiUrl = "{$baseUrl}/pcp/api/ConsultaLotesVinculados?plano={$plano}";
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

function ConsultaPrevisaoCategoria($Fase)
{
    $fase_encoded = urlencode($Fase);
    $baseUrl = 'http://192.168.0.183:8000/pcp';
    $apiUrl = "{$baseUrl}/api/previsaoCategoriaFase?nomeFase={$fase_encoded}";
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




function ConsultarTipoOp($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.183:8000';
    $apiUrl = "{$baseUrl}/pcp/api/filtroProdutivo";
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

function ConsultarPlanos($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.183:8000';
    $apiUrl = "{$baseUrl}/pcp/api/Plano";
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

function ConsultarRealizados($empresa, $Fase, $dataInicio, $dataFinal)
{
    $fase_encoded = urlencode($Fase);
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.183:8000';
    $apiUrl = "{$baseUrl}/pcp/api/RetornoPorFaseDiaria?nomeFase={$fase_encoded}&dataInicio={$dataInicio}&dataFinal={$dataFinal}&codEmpresa={$empresa}";
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

function ConsultarCronograma($empresa, $codPlano, $codFase)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.183:8000';
    $apiUrl = "{$baseUrl}/pcp/api/ConsultaCronogramaFasePlanoFase?codigoPlano={$codPlano}&codFase={$codFase}";
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

function ConsultarMetas($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:7070' : 'http://192.168.0.183:7070';
    $apiUrl = "{$baseUrl}/pcp/api/MetasFases";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: a44pcp22",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        error_log("Erro na solicitação cURL: {$error}");
        return false;
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function ConsultaFaltaProduzirCategoria_Fase($dados)
{
    $baseUrl = 'http://192.168.0.183:7070/pcp';
    $apiUrl = "{$baseUrl}/api/FaltaProduzircategoria_fase";
    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: a44pcp22",
        ],
    ];

    curl_setopt_array($ch, $options);


    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

