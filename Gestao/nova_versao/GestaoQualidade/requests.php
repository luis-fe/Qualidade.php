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
                case 'Ver_Imagem':
                    VerImagem('1', $_GET['caminho'] ?? '');
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
                case 'Salvar_Meta':
                    jsonResponse(SalvarMeta('1', $dados));
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
 * Meta mensal de 2ª Qualidade — tabela pcp."MetaQualide".
 * Retorno: AnoMeta (int), Meses (12 nomes) e Meta (12 frações, 0.015 = 1,50%).
 */
function ConsultarMeta($empresa, $ano)
{
    $ano = (int) $ano;
    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/api/MetaQualidade?ano={$ano}";
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
 * Grava as metas do ano. Espera $dados no mesmo formato devolvido por
 * ConsultarMeta e repassa o objeto tal como veio da tela.
 * Retorno: status (bool), message (string) e dados (formato do ConsultarMeta).
 */
function SalvarMeta($empresa, $dados)
{
    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/api/MetaQualidade";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a44pcp22",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
        curl_close($ch);

        return ['status' => false, 'message' => 'Não foi possível gravar as metas: falha de comunicação com a API.'];
    }

    curl_close($ch);

    $resposta = json_decode($apiResponse, true);

    // A tela só aceita o salvamento quando status === true, então uma resposta
    // fora do formato precisa virar erro explícito e não um "salvo" silencioso
    if (!is_array($resposta) || !isset($resposta['status'])) {
        error_log('Resposta inesperada da API de metas: ' . $apiResponse, 0);

        return ['status' => false, 'message' => 'Resposta inesperada da API ao gravar as metas.'];
    }

    return $resposta;
}

/**
 * Devolve a foto de um apontamento de defeito (JPEG) a partir do caminho
 * gravado em pcp."ApntamentoDefeito" — o mesmo que vem na coluna "imagens"
 * do detalhamento. Faz o papel de proxy do GET /api/ApontamentoDefeitoImagem,
 * que valida o caminho (só aceita arquivos dentro do /dados do servidor).
 */
function VerImagem($empresa, $caminho)
{
    $caminho = trim((string) $caminho);

    if ($caminho === '') {
        http_response_code(404);
        jsonResponse(['status' => false, 'message' => 'Caminho da imagem não informado.']);
    }

    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/api/ApontamentoDefeitoImagem?caminhoImg=" . urlencode($caminho);
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: a44pcp22",
    ]);

    $apiResponse = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
        curl_close($ch);

        http_response_code(502);
        jsonResponse(['status' => false, 'message' => 'Falha de comunicação com a API de imagens.']);
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        http_response_code(404);
        jsonResponse(['status' => false, 'message' => 'Imagem não encontrada.']);
    }

    header('Content-Type: image/jpeg');
    // A foto de um apontamento não muda (trocar = excluir e gravar outra),
    // então o navegador pode guardar em cache ao navegar entre as imagens
    header('Cache-Control: private, max-age=3600');
    echo $apiResponse;
    exit;
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