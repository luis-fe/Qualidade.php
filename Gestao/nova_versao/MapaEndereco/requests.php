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
                case 'get_mapa_enderecos':
                    jsonResponse(get_mapa_enderecos('1'));
                    break;
               
                case 'ConsultaFaltaProduzirCategoria_Fase':
                    header('Content-Type: application/json');
                    echo json_encode(ConsultaFaltaProduzirCategoria_Fase($dados));
                    break;
                case 'get_consultar_endereco':
                    jsonResponse(get_consultar_endereco($_GET['endereco']));
                    break;
                default:
                    jsonResponse(['status' => false, 'message' => 'Acao GET não reconhecida.']);
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
               
                case 'ConsultaFilaResumoCategoria':
                        jsonResponse(ConsultaFilaResumoCategoria($dados));
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


function get_mapa_enderecos($empresa)
{
    $baseUrl ='http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/get_mapa_enderecos?codEmpresa={$empresa}";
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