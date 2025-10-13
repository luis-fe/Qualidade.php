<?php
session_start();
if (isset($_SESSION['usuario']) && isset($_SESSION['empresa'])) {
    $username = $_SESSION['usuario'];
    $empresa = $_SESSION['empresa'];
    $token = $_SESSION['token'];
}

function ConsultaLotes($plano)
{
    $baseUrl = 'http://192.168.0.183:8000/pcp';
    $apiUrl = "{$baseUrl}/api/ConsultaLotesVinculados?plano={$plano}";
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

function ConsultaPlanosDisponiveis($empresa, $token)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/Plano";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: {$token}",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function ConsultaFaccionistasCsw()
{
    $baseUrl = 'http://192.168.0.183:8000/pcp';
    $apiUrl = "{$baseUrl}/api/ConsultaFaccionistasCsw";
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

function ConsultaCategorias()
{
    $baseUrl = 'http://192.168.0.183:8000/pcp';
    $apiUrl = "{$baseUrl}/api/ObterCategorias";
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


function ConsultaMetas($dados)
{
    $baseUrl = 'http://192.168.0.183:8000/pcp';
    $apiUrl = "{$baseUrl}/api/MetasFaccionista";

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


function ConsultaMetasCategorias($dados)
{
    $baseUrl = 'http://192.168.0.183:8000/pcp';
    $apiUrl = "{$baseUrl}/api/MetasFasesCosturaCategorias";

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

function ConsultaRealizadoGeral($dados)
{
    $baseUrl = 'http://192.168.0.183:8000/pcp';
    $apiUrl = "{$baseUrl}/api/RealizadoGeralCostura";

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



function CadastrarFaccionista($dados)
{
    $baseUrl = 'http://192.168.0.183:8000/pcp';
    $apiUrl = "{$baseUrl}/api/CadastrarCapacidadeDiariaFac";

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
                case 'Consultar_Lotes':
                    $plano = $_GET['plano'];
                    jsonResponse(ConsultaLotes($plano));
                    break;
                case 'Consulta_Planos_Disponiveis':
                    jsonResponse(ConsultaPlanosDisponiveis('1', 'a44pcp22'));
                    break;
                case 'Consulta_Categorias':
                    jsonResponse(ConsultaCategorias('1', 'a44pcp22'));
                    break;
                case 'Consulta_Faccionistas':
                    jsonResponse(ConsultaFaccionistasCsw('1', 'a44pcp22'));
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

                case 'Consultar_Metas':
                    $dadosObjeto = (object)$dados;
                    header('Content-Type: application/json');
                    echo json_encode(ConsultaMetas($dadosObjeto));
                    break;
                case 'Consultar_Metas_Categorias':
                    $dadosObjeto = (object)$dados;
                    header('Content-Type: application/json');
                    echo json_encode(ConsultaMetasCategorias($dadosObjeto));
                    break;
                case 'Cadastrar_Faccionista':
                    $dadosObjeto = (object)$dados;
                    header('Content-Type: application/json');
                    echo json_encode(CadastrarFaccionista($dadosObjeto));
                    break;
                case 'Realizado_Geral':
                    $dadosObjeto = (object)$dados;
                    header('Content-Type: application/json');
                    echo json_encode(ConsultaRealizadoGeral($dadosObjeto));
                    break;

                default:
                    jsonResponse(['status' => false, 'message' => 'Ação POST não reconhecida.']);
                    break;
            }
        }
        break;

        // case "PUT":
        //     $requestData = json_decode(file_get_contents('php://input'), true);
        //     $acao = $requestData['acao'] ?? null;
        //     $dados = $requestData['dados'] ?? null;
        //     if ($acao === 'Cadastrar_Justificativa') {
        //         $requestData = json_decode(file_get_contents('php://input'), true);
        //         $dados = $requestData['dados'] ?? null;
        //         $dadosObjeto = (object)$dados;
        //         header('Content-Type: application/json');
        //         echo json_encode(CadastrarJustificativa($dadosObjeto));
        //     } else {
        //         jsonResponse(['status' => false, 'message' => 'Ação PUT não reconhecida.']);
        //     }
        //     break;

    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}
