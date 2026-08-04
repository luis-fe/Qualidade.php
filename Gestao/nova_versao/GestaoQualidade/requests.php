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

// Em funções, e não em const: o switch do topo do arquivo é executado antes
// destas linhas e termina em exit, então uma const daqui nunca seria declarada.
function metaMeses()
{
    return [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];
}

// 1,50% — mesmo valor exibido no painel de índice
function metaPadrao()
{
    return 0.015;
}

// Enquanto não existe backend, o que o usuário salva fica neste arquivo,
// no formato { "2026": [0.015, 0.015, ...] }
function arquivoMetasProvisorias()
{
    return __DIR__ . '/metas_provisorias.json';
}

function lerMetasProvisorias()
{
    $arquivo = arquivoMetasProvisorias();
    if (!file_exists($arquivo)) return [];

    $conteudo = json_decode(file_get_contents($arquivo), true);
    return is_array($conteudo) ? $conteudo : [];
}

/**
 * PROVISÓRIO — meta mensal de 2ª Qualidade.
 * Ainda não existe endpoint no backend, então os valores saem do arquivo
 * local e, na falta dele, do padrão de 1,50%. Quando a API oficial subir,
 * trocar o corpo desta função pelo curl (mesmo padrão das demais)
 * mantendo o formato de retorno.
 */
function ConsultarMeta($empresa, $ano)
{
    $ano = (int) $ano;
    $meses = metaMeses();
    $salvas = lerMetasProvisorias();
    $doAno = $salvas[(string) $ano] ?? null;

    $metas = (is_array($doAno) && count($doAno) === count($meses))
        ? array_map('floatval', $doAno)
        : array_fill(0, count($meses), metaPadrao());

    return [
        'AnoMeta' => $ano,
        'Meses'   => $meses,
        'Meta'    => $metas
    ];
}

/**
 * PROVISÓRIO — grava as metas do ano no arquivo local.
 * Espera $dados no mesmo formato devolvido por ConsultarMeta:
 * AnoMeta (int), Meses (12 nomes) e Meta (12 frações, 0.015 = 1,50%).
 */
function SalvarMeta($empresa, $dados)
{
    $ano = (int) ($dados['AnoMeta'] ?? 0);
    $metas = $dados['Meta'] ?? null;

    if ($ano < 2000 || $ano > 2100) {
        return ['status' => false, 'message' => 'Ano inválido.'];
    }

    if (!is_array($metas) || count($metas) !== count(metaMeses())) {
        return ['status' => false, 'message' => 'É esperada uma meta para cada um dos 12 meses.'];
    }

    $normalizadas = [];
    foreach ($metas as $valor) {
        if (!is_numeric($valor)) {
            return ['status' => false, 'message' => 'Meta inválida: informe apenas números.'];
        }

        $valor = (float) $valor;
        if ($valor < 0 || $valor > 1) {
            return ['status' => false, 'message' => 'A meta deve ficar entre 0 e 1 (0,015 = 1,50%).'];
        }

        $normalizadas[] = round($valor, 6);
    }

    $todas = lerMetasProvisorias();
    $todas[(string) $ano] = $normalizadas;

    $gravou = file_put_contents(
        arquivoMetasProvisorias(),
        json_encode($todas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );

    if ($gravou === false) {
        error_log('Falha ao gravar as metas provisorias em ' . arquivoMetasProvisorias(), 0);
        return ['status' => false, 'message' => 'Não foi possível gravar as metas. Verifique a permissão de escrita da pasta.'];
    }

    return [
        'status'  => true,
        'message' => 'Metas salvas.',
        'dados'   => ConsultarMeta($empresa, $ano)
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