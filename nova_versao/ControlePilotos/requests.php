<?php

// Função auxiliar para enviar resposta JSON
function jsonResponse($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// -------------------------------------------------------------------------------------------------
// Lógica de Roteamento Baseada no Método HTTP
// -------------------------------------------------------------------------------------------------

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        if (isset($_GET["acao"])) {
            $acao = $_GET["acao"];
            switch ($acao) {
                case 'getConsultaPilotos':
                    // Assume que a ConsultaPilotos('1') retorna os dados necessários ou false em caso de falha
                    $pilotos = ConsultaPilotos('1'); 
                    if ($pilotos !== null) {
                        jsonResponse(['status' => true, 'data' => $pilotos]);
                    } else {
                        // Resposta mais informativa se a API falhar
                        jsonResponse(['status' => false, 'message' => 'Falha ao consultar a API de pilotos.']);
                    }
                    break;
                case 'gerarDoc_controle_OP':
                    // Assume que a ConsultaPilotos('1') retorna os dados necessários ou false em caso de falha
                        $data = gerarDoc_controle_OP(); 
                        jsonResponse($data);
                    break;
                case 'tags_transferidas_documento_atual':
                        $codigoDoumento = $_GET['documento'];
                        $data = tags_transferidas_documento_atual($codigoDoumento); 
                        jsonResponse($data);
                    break;
                case 'get_pilotos_em_transito':
                        $data = get_pilotos_em_transito(); 
                        jsonResponse($data);
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação GET não reconhecida.']);
                    break;
            }
        } else {
            jsonResponse(['status' => false, 'message' => 'Ação não especificada na requisição GET.']);
        }
        break;

    case "POST":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;
        if ($acao) {
            // Este é o switch onde você deve adicionar suas ações POST, se houver
            switch ($acao) {
                case 'TransferirPilotos':
                    $dadosObjeto = (object) $dados;
                    header('Content-Type: application/json');
                    echo json_encode(TransferirPilotos($dadosObjeto));
                    break;

                default:
                    // Resposta padrão se a ação POST não for mapeada
                    jsonResponse(['status' => false, 'message' => "Ação POST '{$acao}' não reconhecida."]);
                    break;
            }
        } else {
             jsonResponse(['status' => false, 'message' => 'Ação POST não especificada.']);
        }
        break;
    
    default:
        // Lidar com outros métodos HTTP (PUT, DELETE, etc.)
        jsonResponse(['status' => false, 'message' => 'Método HTTP não suportado.']);
        break;
}

// -------------------------------------------------------------------------------------------------
// Funções de Negócio
// -------------------------------------------------------------------------------------------------

/**
 * Consulta a API para buscar dados dos pilotos.
 * @param string $codEmpresa Código da empresa para a consulta.
 * @return array|null Retorna um array associativo dos dados decodificados ou null em caso de falha.
 */
function ConsultaPilotos($codEmpresa)
{
    $baseUrl = 'http://10.162.0.53:7070/pcp';
    $apiUrl = "{$baseUrl}/api/Consula_tags_pilotos?codEmpresa={$codEmpresa}";
    
    $ch = curl_init($apiUrl);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a44pcp22", // A chave de autorização deve ser mantida em segredo!
    ]);

    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        // Loga o erro da requisição cURL
        error_log("Erro na requisição cURL para 'ConsultaPilotos': " . curl_error($ch), 0);
        curl_close($ch);
        return null; // Retorna null em caso de falha de conexão
    }
    
    curl_close($ch);
    
    if ($httpCode >= 400) {
        // Loga erro de HTTP (Ex: 404, 500)
        error_log("A API 'ConsultaPilotos' retornou o código HTTP: {$httpCode}. Resposta: " . $apiResponse, 0);
        return null; // Retorna null em caso de erro da API
    }

    $decodedData = json_decode($apiResponse, true);
    
    // Verifica se a decodificação JSON foi bem-sucedida
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("Erro ao decodificar JSON da API: " . json_last_error_msg(), 0);
        return null;
    }

    return $decodedData;
}


function gerarDoc_controle_OP()
{
    $baseUrl = 'http://10.162.0.53:7070/pcp';
    $apiUrl = "{$baseUrl}/api/gerarNovoDocumento";
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



function tags_transferidas_documento_atual($codDocumento)
{
    $baseUrl = 'http://10.162.0.53:7070';
    $apiUrl = "{$baseUrl}/pcp/api/tags_transferidas_documento_atual?documento={$codDocumento}";
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



function get_pilotos_em_transito()
{
    $baseUrl = 'http://10.162.0.53:7070';
    $apiUrl = "{$baseUrl}/pcp/api/get_pilotos_em_transito";
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




function TransferirPilotos($dados)
{
    $baseUrl = 'http://10.162.0.53:7070';
    $apiUrl = "{$baseUrl}/pcp/api/transferir_pilotos";

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