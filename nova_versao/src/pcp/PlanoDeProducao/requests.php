<?php

set_time_limit(300);
session_start();
if (isset($_SESSION['usuario']) && isset($_SESSION['empresa'])) {
    $usuario = $_SESSION['usuario'];
    $empresa = $_SESSION['empresa'];
    $token = $_SESSION['token'];
} else {
    header("Location: ../../../indexPcp.php");
}

$usuario = $_SESSION['usuario'];
$empresa = $_SESSION['empresa'];
$token = $_SESSION['token'];

function ConsultarPlanos($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
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

function ConsultaTipoNotasVinculados($empresa, $plano)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/ConsultaTipoNotasVinculados?plano={$plano}";
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

function ConsultaNotasCsw($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/TipoNotasCsw";
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

function ConsultaLotesCsw($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/lotes_csw?empresa={$empresa}";
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

function ConsultaLotesVinculados($empresa, $plano)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
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


function CadastrarPlano($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/NovoPlano";

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
        $response = [
            'status' => false,
            'message' => "Erro na solicitação cURL: {$error}"
        ];
    } else {
        $response = [
            'status' => true,
            'resposta' => json_decode($apiResponse, true)
        ];
    }

    curl_close($ch);

    return json_encode($response);
}

function AlterarPlano($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/AlterPlano";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: a44pcp22",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        error_log("Erro na solicitação cURL: {$error}");
        return false;
    }

    curl_close($ch);

    if ($httpCode >= 400) {
        error_log("Erro na API: Código HTTP {$httpCode}");
        return false;
    }

    return json_decode($apiResponse, true);
}

function VincularLote($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/VincularLotesPlano";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: a44pcp22",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        error_log("Erro na solicitação cURL: {$error}");
        return false;
    }

    curl_close($ch);

    if ($httpCode >= 400) {
        error_log("Erro na API: Código HTTP {$httpCode}");
        return false;
    }

    return json_decode($apiResponse, true);
}

function VincularNotas($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://10.162.0.191:8000';
    $apiUrl = "{$baseUrl}/pcp/api/VincularNotasPlano";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: a44pcp22",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        error_log("Erro na solicitação cURL: {$error}");
        return false;
    }

    curl_close($ch);

    if ($httpCode >= 400) {
        error_log("Erro na API: Código HTTP {$httpCode}");
        return false;
    }

    return json_decode($apiResponse, true);
}

function DeleteNotas($dados)
{
    $baseUrl = 'https://easyjohnfield-production.up.railway.app/';
    $NomeProjeto = 'JonhField';
    $apiUrl = "{$baseUrl}/api/{$NomeProjeto}/ExluirColetaProducao";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "DELETE",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Easy445277888",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        $response = [
            'status' => false,
            'message' => "Erro na solicitação cURL: {$error}"
        ];
    } else {
        $response = [
            'status' => true,
            'resposta' => json_decode($apiResponse, true)
        ];
    }

    curl_close($ch);

    return json_encode($response);
}

function DeleteLotes($dados)
{
    $baseUrl = 'https://easyjohnfield-production.up.railway.app/';
    $NomeProjeto = 'JonhField';
    $apiUrl = "{$baseUrl}/api/{$NomeProjeto}/ExluirColetaProducao";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "DELETE",
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Authorization: Easy445277888",
        ],
    ];

    curl_setopt_array($ch, $options);

    $apiResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        $response = [
            'status' => false,
            'message' => "Erro na solicitação cURL: {$error}"
        ];
    } else {
        $response = [
            'status' => true,
            'resposta' => json_decode($apiResponse, true)
        ];
    }

    curl_close($ch);

    return json_encode($response);
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
                case 'Consultar_Planos':
                    jsonResponse(ConsultarPlanos('1'));
                    break;
                case 'Consultar_Notas_Vinculados':
                    $plano = $_GET['plano'];
                    jsonResponse(ConsultaTipoNotasVinculados('1', $plano));
                    break;
                case 'Consultar_Lotes_Vinculados':
                    $plano = $_GET['plano'];
                    jsonResponse(ConsultaLotesVinculados('1', $plano));
                    break;
                case 'Consultar_Notas_Csw':
                    jsonResponse(ConsultaNotasCsw('1'));
                    break;
                case 'Consultar_Lotes_Csw':
                    jsonResponse(ConsultaLotesCsw('1'));
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
                case 'Cadastrar_Plano':
                    $requestData = json_decode(file_get_contents('php://input'), true);
                    $dados = $requestData['dados'] ?? null;
                    $dadosObjeto = (object)$dados;
                    header('Content-Type: application/json');
                    echo (CadastrarPlano('1', $dadosObjeto));
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação POST não reconhecida.']);
                    break;
            }
        }
        break;

    case "PUT":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;

        switch ($acao) {
            case 'Alterar_Plano':
                $requestData = json_decode(file_get_contents('php://input'), true);
                $dados = $requestData['dados'] ?? null;
                $dadosObjeto = (object)$dados;
                header('Content-Type: application/json');
                echo json_encode(AlterarPlano('1',  $dadosObjeto));
                break;
            case 'Vincular_Lote':
                $requestData = json_decode(file_get_contents('php://input'), true);
                $dados = $requestData['dados'] ?? null;
                $dadosObjeto = (object)$dados;
                header('Content-Type: application/json');
                echo json_encode(VincularLote('1',  $dadosObjeto));
                break;
                case 'Vincular_Notas':
                    $requestData = json_decode(file_get_contents('php://input'), true);
                    $dados = $requestData['dados'] ?? null;
                    $dadosObjeto = (object)$dados;
                    header('Content-Type: application/json');
                    echo json_encode(VincularNotas('1',  $dadosObjeto));
                    break;
                
            default:
                jsonResponse(['status' => false, 'message' => 'Ação POST não reconhecida.']);
                break;
        }

        break;

    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}
