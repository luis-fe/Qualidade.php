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
                case 'Consulta_Planos':
                    jsonResponse(ConsultarPlanos('1'));
                    break;
                case 'Consulta_Colecoes':
                    $plano = $_GET['plano'];
                    jsonResponse(ConsultarColecoes('1', $plano));
                    break;
                case 'Consulta_Lotes':
                    $plano = $_GET['plano'];
                    jsonResponse(ConsultaLotes('1', $plano));
                    break;
                case 'Consulta_Notas':
                    $plano = $_GET['plano'];
                    jsonResponse(ConsultaTipoNotas('1', $plano));
                    break;
                case 'Consulta_Colecoes_Csw':
                    jsonResponse(ConsultaColecoesCsw('1'));
                    break;
                case 'Consulta_Lotes_Csw':
                    jsonResponse(ConsultaLotesCsw('1'));
                    break;
                case 'Consulta_Notas_Csw':
                    jsonResponse(ConsultaNotasCsw('1'));
                    break;
                case 'Consulta_Abc_Plano':
                    $plano = $_GET['plano'];
                    jsonResponse(ConsultaAbcPlano('1', $plano));
                    break;
                case 'Consulta_Abc':
                    jsonResponse(ConsultarAbc('1'));
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
                    header('Content-Type: application/json');
                    echo (CadastrarPlano('1', $dados));
                    break;
                case 'Vincular_Colecoes':
                    header('Content-Type: application/json');
                    echo (VincularColecoes('1', $dados));
                    break;
                case 'Cadastrar_Abc':
                    header('Content-Type: application/json');
                    echo (CadastrarAbc('1', $dados));
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
        if ($acao) {
            switch ($acao) {
                case 'Alterar_Plano':
                    header('Content-Type: application/json');
                    echo json_encode(AlterarPlano('1',  $dados));
                    break;
                case 'Vincular_Lotes':
                    header('Content-Type: application/json');
                    echo json_encode(VincularLote('1', $dados));
                    break;
                case 'Vincular_Notas':
                    header('Content-Type: application/json');
                    echo json_encode(VincularNotas('1', $dados));
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

function ConsultarPlanos($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
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

function ConsultarColecoes($empresa, $codPlano)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/ConsultaColecaoVinculados?codPlano={$codPlano}";
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

function ConsultaLotes($empresa, $plano)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
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

function ConsultaTipoNotas($empresa, $plano)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
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

function ConsultarAbc($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/consultaParametrizacaoABC";
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

function ConsultaAbcPlano($empresa, $plano)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/consultaPlanejamentoABC_plano?codPlano={$plano}";
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

function ConsultaColecoesCsw($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/colecao_csw";
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
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
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

function ConsultaNotasCsw($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
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


function CadastrarPlano($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
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

function CadastrarAbc($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/InserirOuAlterPlanoABC";

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
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
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

function VincularColecoes($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/VincularColecoesPlano";

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

function VincularLote($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
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
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
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