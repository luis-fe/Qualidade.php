<?php
session_start();
if (isset($_SESSION['username']) && isset($_SESSION['empresa'])) {
    $username = $_SESSION['username'];
    $empresa = $_SESSION['empresa'];
} else {
    header("Location: ../../indexPcp.php");
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
                case 'Consulta_Metas':
                    $plano = $_GET['plano'];
                    jsonResponse(ConsultarMetas('1', $plano));
                    break;
                case 'Consulta_Planos':
                    jsonResponse(ConsultarPlanos('1'));
                    break;
                case 'Consulta_Marcas':
                    jsonResponse(ConsultarMarcas('1'));
                    break;
                case 'Consulta_Metas_Categoria':
                    $plano = $_GET['plano'];
                    $marca = $_GET['marca'];
                    jsonResponse(ConsultarMetasCategoria('1', $plano, $marca));
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
                case 'Salvar_Metas':
                    $dadosObjeto = (object) $dados;
                    header('Content-Type: application/json');
                    echo json_encode(SalvarMetas('1', $dadosObjeto));
                    break;
                case 'Salvar_Metas_Categoria':
                    $dadosObjeto = (object) $dados;
                    header('Content-Type: application/json');
                    echo json_encode(SalvarMetasCategoria('1', $dadosObjeto));
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

function ConsultarMetas($empresa, $plano)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/MetaGeralPlano?codPlano={$plano}";
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

function ConsultarMetasCategoria($empresa, $plano, $marca)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/consultarMetaCategoriaPlano?codPlano={$plano}&marca={$marca}";
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

function ConsultarMarcas($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/MarcasDisponiveis";
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

function SalvarMetas($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/inserirOuAtualizarMetaPlano";

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

function SalvarMetasCategoria($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://192.168.0.183:8000' : 'http://192.168.0.184:8000';
    $apiUrl = "{$baseUrl}/pcp/api/atualizaOuInserirMetaCategoria";

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
