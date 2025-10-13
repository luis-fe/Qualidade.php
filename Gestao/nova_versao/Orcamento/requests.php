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
                case 'Cosulta_Resumos':
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    $fase = $_GET['fase'];
                    $area = $_GET['area'];
                    $empresa = $_GET['empresa'];
                    $grupo = $_GET['grupoContas'];
                    jsonResponse(Cosulta_Resumos($empresa, $dataInicial, $dataFinal, $area, $fase, $grupo));
                    break;
                case 'Consulta_Detalhamento':
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    $fase = $_GET['fase'];
                    $area = $_GET['area'];
                    $empresa = $_GET['empresa'];
                    $grupo = $_GET['grupoContas'];
                    jsonResponse(Consulta_Detalhamento($empresa, $dataInicial, $dataFinal, $area, $fase, $grupo));
                    break;
                case 'Consulta_Orcado':
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    $fase = $_GET['fase'];
                    $area = $_GET['area'];
                    $empresa = $_GET['empresa'];
                    $grupo = $_GET['grupoContas'];
                    jsonResponse(Consulta_Orcado($empresa, $dataInicial, $dataFinal, $area, $fase, $grupo));
                    break;
                case 'Consulta_Empresas':
                    jsonResponse(Consulta_Empresas());
                    break;
                case 'Consulta_Area':
                    jsonResponse(Consulta_Area());
                    break;
                case 'Consulta_Centro_Custos':
                    jsonResponse(Consulta_Centro_Custos());
                    break;
                case 'Consulta_Grupo_Gastos':
                    jsonResponse(Consulta_Grupo_Gastos());
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

function Cosulta_Resumos($empresa, $dataInicial, $dataFinal, $area, $fase, $grupo)
{
    $fase_encode = urlencode($fase);
    $grupo_encode = urlencode($grupo);
    $baseUrl = 'http://10.162.0.53:7070/pcp';
    $apiUrl = "{$baseUrl}/api/ResumooGastosCentroCusto?codEmpresa={$empresa}&dataCompentenciaInicial={$dataInicial}&dataCompentenciaFinal={$dataFinal}&nomeArea={$area}&GRUPO={$grupo_encode}&nomeCentroCusto={$fase_encode}";
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




function Consulta_Detalhamento($empresa, $dataInicial, $dataFinal, $area, $fase, $grupo)
{
    $fase_encode = urlencode($fase);
    $grupo_encode = urlencode($grupo);
    $baseUrl = 'http://10.162.0.53:7070/pcp';
    $apiUrl = "{$baseUrl}/api/GastosCentroCusto?codEmpresa={$empresa}&dataCompentenciaInicial={$dataInicial}&dataCompentenciaFinal={$dataFinal}&nomeArea={$area}&nomeCentroCusto={$fase_encode}&GRUPO={$grupo_encode}";
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

function Consulta_Orcado($empresa, $dataInicial, $dataFinal, $area, $fase, $grupo)
{
    $fase_encode = urlencode($fase);
    $grupo_encode = urlencode($grupo);
    $baseUrl = 'http://10.162.0.53:7070/pcp';
    $apiUrl = "{$baseUrl}/api/ResumooGastosCentroCustoConta?codEmpresa={$empresa}&dataCompentenciaInicial={$dataInicial}&dataCompentenciaFinal={$dataFinal}&nomeArea={$area}&GRUPO={$grupo_encode}&nomeCentroCusto={$fase_encode}";
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

function Consulta_Empresas()
{
    $baseUrl = 'http://10.162.0.53:7070/pcp';
    $apiUrl = "{$baseUrl}/api/EmpresasGrupoMPL";
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

function Consulta_Area()
{
    $baseUrl = 'http://10.162.0.53:7070/pcp';
    $apiUrl = "{$baseUrl}/api/AreaCusto";
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

function Consulta_Centro_Custos()
{
    $baseUrl = 'http://10.162.0.53:7070/pcp';
    $apiUrl = "{$baseUrl}/api/CentroCustos";
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

function Consulta_Grupo_Gastos()
{
    $baseUrl = 'http://10.162.0.53:7070/pcp';
    $apiUrl = "{$baseUrl}/api/GrupoGastos";
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
