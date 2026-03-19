<?php

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
                case 'ConsultarEnderecos':
                    jsonResponse(ConsultarEnderecos('1'));
                    break;
                case 'Consulta_Lotes':
                    $plano = $_GET['plano'];
                    jsonResponse(ConsultarLotes('1', $plano));
                    break;
                case 'get_consultar_endereco':
                    $endereco = $_GET['endereco'];
                    jsonResponse(get_consultar_endereco($endereco));
                    break;
                case 'Consultar_Realizados':
                    $Fase = $_GET['Fase'];
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    jsonResponse(ConsultarRealizados('1', $Fase, $dataInicial, $dataFinal));
                    break;
                case 'Consultar_RealizadosDia':
                    $Fase = $_GET['Fase'];
                    $dataInicial = $_GET['dataInicial'];
                    jsonResponse(ConsultarRealizadosDia('1', $Fase, $dataInicial));
                    break;
                case 'Consultar_Cronograma':
                    $plano = $_GET['plano'];
                    $fase = $_GET['fase'];
                    jsonResponse(ConsultarCronograma('1', $plano, $fase));
                    break;
                case 'Consultar_Tipo_Op':
                    jsonResponse(ConsultarTipoOp('1'));
                    break;
                case 'ConsultaFaltaProduzirCategoria_Fase':
                    // Usa a função existente no código (verifique de onde vem $dados no GET original, adaptei para evitar erro)
                    $dados = $_GET['dados'] ?? null; 
                    header('Content-Type: application/json');
                    echo json_encode(ConsultaFaltaProduzirCategoria_Fase($dados));
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Acao GET não reconhecida.']);
                    break;
            }
        }
        break;

    case "POST":
        $requestData = json_decode(file_get_contents('php://input'), true);
        
        // CORREÇÃO: Tenta pegar a ação da URL (?acao=...) primeiro, depois do JSON
        $acao = $_GET['acao'] ?? ($requestData['acao'] ?? null);
        
        // CORREÇÃO: Se existir a chave 'dados', usa ela. Se não, os próprios dados enviados são o payload.
        $dados = $requestData['dados'] ?? $requestData;

        if ($acao) {
            switch ($acao) {
                case 'inserir_endereco_item_reposto_kit':
                    // Padronizando para usar o jsonResponse
                    jsonResponse(inserir_endereco_item_reposto_kit($dados));
                    break;
                case 'inserir_endereco_item_reposto_unitario':
                    // Padronizando para usar o jsonResponse
                    jsonResponse(inserir_endereco_item_reposto_unitario($dados));
                    break;
                case 'update_endereco_item_reposto_unitario':
                    // Padronizando para usar o jsonResponse
                    jsonResponse(update_endereco_item_reposto_unitario($dados));
                    break;
                case 'inserir_endereco':
                    jsonResponse(inserir_endereco($dados));
                    break;
                case 'inserir_endereco_massa':
                    jsonResponse(inserir_endereco_massa($dados));
                    break;
                case 'Consulta_fila_fase':
                    jsonResponse(ConsultaFilaResumo($dados));
                    break;
                case 'ConsultaFilaResumoCategoria':
                    jsonResponse(ConsultaFilaResumoCategoria($dados));
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação POST não reconhecida.']);
                    break;
            }
        } else {
            jsonResponse(['status' => false, 'message' => 'Ação não informada no POST.']);
        }
        break;

    case "PUT":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;
        if ($acao) {
            switch ($acao) {
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação PUT não reconhecida.']);
                    break;
            }
        }
        break;

    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}


// ==========================================
// FUNÇÕES DE COMUNICAÇÃO COM A API DO PCP
// ==========================================

function inserir_endereco($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/inserir_endereco_aviamento";
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

function inserir_endereco_item_reposto_kit($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/inserir_endereco_item_reposto_kit";
    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($dados), // Agora converte corretamente o payload limpo que enviamos
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
        return ['status' => false, 'message' => 'Falha de comunicação com a API interna.'];
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}


function inserir_endereco_item_reposto_unitario($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/inserir_endereco_item_reposto_unitario";
    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($dados), // Agora converte corretamente o payload limpo que enviamos
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
        return ['status' => false, 'message' => 'Falha de comunicação com a API interna.'];
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}


function update_endereco_item_reposto_unitario($dados)
{
    $baseUrl = 'http://10.162.0.53:9000/pcp';
    $apiUrl = "{$baseUrl}/api/update_endereco_item_reposto_unitario";
    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($dados), // Agora converte corretamente o payload limpo que enviamos
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
        return ['status' => false, 'message' => 'Falha de comunicação com a API interna.'];
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}


function get_consultar_endereco($endereco)
{
    $baseUrl ='http://10.162.0.53:9000';
    // Use o urlencode para garantir que espaços e traços não quebrem a requisição interna
$enderecoCodificado = urlencode($endereco);
$apiUrl = "{$baseUrl}/pcp/api/get_consultar_endereco?endereco={$enderecoCodificado}";
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


// ... Coloque aqui embaixo as outras funções que você chamou no Switch (ConsultarEnderecos, etc) caso eu não tenha listado todas