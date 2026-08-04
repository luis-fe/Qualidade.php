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
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    $textoAvancado = $_GET['campoBusca'];
                    jsonResponse(ConsultarMotivos('1', $dataInicial, $dataFinal,$textoAvancado));
                    break;
                case 'Cosultar_Qualidade':
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    jsonResponse(ConsultaQualidade('1', $dataInicial, $dataFinal));
                    break;
                case 'Cosultar_Origem':
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    $textoAvancado = $_GET['campoBusca'];
                    jsonResponse(ConsultaOrigem('1', $dataInicial, $dataFinal,$textoAvancado));
                    break;
                case 'Cosultar_Fornecedor':
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    $textoAvancado = $_GET['campoBusca'];
                    jsonResponse(Cosultar_Fornecedor('1', $dataInicial, $dataFinal,$textoAvancado));
                    break;
                case 'Cosultar_Fornecedor_base':
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    $textoAvancado = $_GET['campoBusca'];
                    jsonResponse(Cosultar_Fornecedor_base('1', $dataInicial, $dataFinal,$textoAvancado));
                    break;
                case 'detalha_defeitos':
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    $textoAvancado = $_GET['campoBusca'];
                    jsonResponse(detalha_defeitos('1', $dataInicial, $dataFinal,$textoAvancado));
                    break;
                case 'defeitos_porOrigem':
                    $dataInicial = $_GET['dataInicial'];
                    $dataFinal = $_GET['dataFinal'];
                    $textoAvancado = $_GET['campoBusca'];
                    jsonResponse(defeitos_porOrigem('1', $dataInicial, $dataFinal,$textoAvancado));
                    break;
                case 'Consultar_Meta':
                    $ano = $_GET['ano'] ?? date('Y');
                    jsonResponse(ConsultarMeta('1', $ano));
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
                case 'Cadastrar_Linha':
                    header('Content-Type: application/json');
                    echo CadastrarLinha('1',  $dados);
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
                case 'Editar_Linha':
                    header('Content-Type: application/json');
                    echo json_encode(EditarLinha('1',  $dados));
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Ação POST não reconhecida.']);
                    break;
            }
        }
        break;
    case "DELETE":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;

        switch ($acao) {
            default:
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Ação não reconhecida.']);
                break;
        }
        break;
    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}

function ConsultarMotivos($empresa, $dataInicial, $dataFinal,$textoAvancado)
{
    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/api/MotivosAgrupado?textoAvancado={$textoAvancado}&data_inicio={$dataInicial}&data_fim={$dataFinal}";
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

function ConsultaQualidade($empresa, $dataInicial, $dataFinal)
{
    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/api/Dashboard2Qualidade?data_inicio={$dataInicial}&data_fim={$dataFinal}";
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

function ConsultaOrigem($empresa, $dataInicial, $dataFinal,$textoAvancado)
{
    $baseUrl = 'http://10.162.0.53:9000';    
    $apiUrl = "{$baseUrl}/api/defeitos_faccionista_agrupo_periodo?textoAvancado={$textoAvancado}&data_inicio={$dataInicial}&data_fim={$dataFinal}";
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



function detalha_defeitos($empresa, $dataInicial, $dataFinal,$textoAvancado)
{
    $baseUrl = 'http://10.162.0.53:9000';    
    $apiUrl = "{$baseUrl}/api/defeitos_detalhado_periodo?textoAvancado={$textoAvancado}&data_inicio={$dataInicial}&data_fim={$dataFinal}";
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



function defeitos_porOrigem($empresa, $dataInicial, $dataFinal,$textoAvancado)
{
    $baseUrl = 'http://10.162.0.53:9000';    
    $apiUrl = "{$baseUrl}/api/defeitos_origem_periodo?textoAvancado={$textoAvancado}&data_inicio={$dataInicial}&data_fim={$dataFinal}";
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

/**
 * PROVISÓRIO — meta mensal de 2ª Qualidade.
 * Ainda não existe endpoint no backend, então os valores são gerados aqui.
 * Quando a API oficial subir, trocar o corpo desta função pelo curl
 * (mesmo padrão das demais) mantendo o formato de retorno.
 */
function ConsultarMeta($empresa, $ano)
{
    $meses = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    $metaPadrao = 0.015; // 1,50% — mesmo valor exibido no painel de índice

    return [
        'AnoMeta' => (int) $ano,
        'Meses'   => $meses,
        'Meta'    => array_fill(0, count($meses), $metaPadrao)
    ];
}

function Cosultar_Fornecedor($empresa, $dataInicial, $dataFinal,$textoAvancado)
{
    $baseUrl = 'http://10.162.0.53:9000';    
    $apiUrl = "{$baseUrl}/api/defeitos_fornecedor_agrupo_periodo?textoAvancado={$textoAvancado}&data_inicio={$dataInicial}&data_fim={$dataFinal}";
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

function Cosultar_Fornecedor_base($empresa, $dataInicial, $dataFinal,$textoAvancado)
{
    $baseUrl = 'http://10.162.0.53:9000';    
    $apiUrl = "{$baseUrl}/api/defeitos_fornecedor_base_agrupo_periodo?textoAvancado={$textoAvancado}&data_inicio={$dataInicial}&data_fim={$dataFinal}";
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