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
                case 'Consulta_Planos':
                    jsonResponse(ConsultarPlanos('1'));
                    break;
                case 'Consulta_Imagem':
                    $codigoImagem = urldecode($_GET['codigoMP']);
                    jsonResponse(obterImagemMP($codigoImagem));
                    break;
                case 'consultarInformacoesPlano':
                    $plano = $_GET['plano'];
                    $empresa = $_GET['empresa'];
                    jsonResponse(consultarInformacoesPlano($empresa, "a44pcp22", $plano));
                    break;
                case 'Consulta_Abc_Plano':
                    $plano = $_GET['plano'];
                    jsonResponse(ConsultaAbcPlano('1', $plano));
                    break;
                case 'selecao_produtos_simulacao':
                    $nomeSimulacao = $_GET['nomeSimulacao'];
                    jsonResponse(selecao_produtos_simulacao('a44pcp22','1',$nomeSimulacao));
                    break;
                case 'Detalha_OrdemProducao':
                    $codSku = $_GET['codReduzido'];
                    jsonResponse(DetalhaOrdemProducao('a44pcp22', $codSku));
                    break;
                case 'Consulta_Simulacoes':
                    jsonResponse(ConsultaSimulacoes('1'));
                    break;
                case 'Consulta_Categorias':
                    jsonResponse(ConsultaCategorias('1'));
                    break;
                case 'Consulta_Abc':
                    jsonResponse(ConsultaAbc('1'));
                    break;
                case 'Consulta_Simulacao_Especifica':
                    $simulacao = urldecode($_GET['simulacao']);
                    jsonResponse(ConsultaSimulacaoEspecifica('1', $simulacao));
                    break;
                case 'Detalha_Pedidos':
                    $codPlano = $_GET['codPlano'];
                    $consideraPedidosBloqueado = $_GET['consideraPedidosBloqueado'];
                    $codReduzido = $_GET['codReduzido'];
                    jsonResponse(Detalha_PedidosSku($codReduzido, $codPlano, $consideraPedidosBloqueado));
                    break;
                case 'Detalha_PedidosSaldo':
                    $codPlano = $_GET['codPlano'];
                    $consideraPedidosBloqueado = $_GET['consideraPedidosBloqueado'];
                    $codReduzido = $_GET['codReduzido'];
                    jsonResponse(Detalha_PedidosSkuSaldo($codReduzido, $codPlano, $consideraPedidosBloqueado));
                    break;
                case 'Detalha_PedidosSaldoGeral':
                    $codPlano = $_GET['codPlano'];
                    $consideraPedidosBloqueado = $_GET['consideraPedidosBloqueado'];
                    $codReduzido = $_GET['codReduzido'];
                    jsonResponse(Detalha_PedidosGeralSaldo($codPlano, $consideraPedidosBloqueado));
                    break;
                case 'Consulta_Ultimo_CalculoTendencia':
                    $plano = isset($_GET['plano']) ? $_GET['plano'] : null;
                    jsonResponse(Consulta_Ultimo_CalculoTendencia('1', $plano));
                    break;
                case 'obter_produtos_tendencia':
                    $codPlano = isset($_GET['codPlano']) ? $_GET['codPlano'] : null;
                    $nomeSimulacao = isset($_GET['nomeSimulacao']) ? $_GET['nomeSimulacao'] : '';
                    jsonResponse(obter_produtos_tendencia($codPlano,$nomeSimulacao));
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
                case 'Consulta_Tendencias':
                    $dadosObjeto = (object) $dados;
                    header('Content-Type: application/json');
                    echo json_encode(ConsultaTendencias('1', $dadosObjeto));
                    break;
                case 'atualizaInserirSimulacaoProdutos':
                    $dadosObjeto = (object) $dados;
                    header('Content-Type: application/json');
                    echo json_encode(atualizaInserirSimulacaoProdutos($dadosObjeto));
                    break;
                case 'Simular_Programacao':
                    $dadosObjeto = (object) $dados;
                    header('Content-Type: application/json');
                    echo json_encode(Simular_Programacao('1', $dadosObjeto));
                    break;
                case 'Cadastro_Simulacao':
                    $dadosObjeto = (object) $dados;
                    header('Content-Type: application/json');
                    echo json_encode(CadastroSimulacao('1', $dadosObjeto));
                    break;

                case 'simulacaoDetalhadaPorSku':
                    $dadosObjeto = (object) $dados;
                    header('Content-Type: application/json');
                    echo json_encode(simulacaoDetalhadaPorSku('1', $dadosObjeto));
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
    case "DELETE":
        $requestData = json_decode(file_get_contents('php://input'), true);
        $acao = $requestData['acao'] ?? null;
        $dados = $requestData['dados'] ?? null;

        switch ($acao) {
            case 'Deletar_Simulacao':
                $dadosObjeto = (object)$dados;
                header('Content-Type: application/json');
                echo DeleteSimulacao("1", $dadosObjeto);
                break;
             case 'Deletar_SimulacaoProduto':
                $dadosObjeto = (object)$dados;
                header('Content-Type: application/json');
                echo DeleteSimulacaoProduto($dadosObjeto);
                break;
             case 'exluindo_simulacao_Produtos_zerados':
                $dadosObjeto = (object)$dados;
                header('Content-Type: application/json');
                echo exluindo_simulacao_Produtos_zerados($dadosObjeto);
                break;
        }
        break;
    default:
        jsonResponse(['status' => false, 'message' => 'Método de requisição não suportado.']);
        break;
}



function ConsultarPlanos($empresa)
{
    $baseUrl = ($empresa == "1") ? '10.162.0.53:9000' : '192.168.0.183:9000';
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

function ConsultaSimulacaoEspecifica($empresa, $simulacao)
{
    $simulacao = str_replace(' ', '%20', $simulacao);
    $baseUrl = '10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/consultaDetalhadaSimulacao?nomeSimulacao={$simulacao}";
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


function ConsultaCategorias($empresa)
{
    $baseUrl = ($empresa == "1") ? '10.162.0.53:9000' : '192.168.0.183:9000';
    $apiUrl = "{$baseUrl}/pcp/api/CategoriasDisponiveis";
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

function Detalha_PedidosSku($codReduzido, $plano, $consideraBloq, $empresa = '1')
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://10.162.0.191:9000';
    $apiUrl = "{$baseUrl}/pcp/api/DetalhaPedidosSKU?codPlano={$plano}&consideraPedidosBloqueado={$consideraBloq}&codReduzido={$codReduzido}";
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

function Detalha_PedidosSkuSaldo($codReduzido, $plano, $consideraBloq, $empresa = '1')
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://10.162.0.191:9000';
    $apiUrl = "{$baseUrl}/pcp/api/DetalhaPedidosSKUSaldo?codPlano={$plano}&consideraPedidosBloqueado={$consideraBloq}&codReduzido={$codReduzido}";
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


function Detalha_PedidosGeralSaldo($plano, $consideraBloq, $empresa = '1')
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://10.162.0.191:9000';
    $apiUrl = "{$baseUrl}/pcp/api/DetalhaPedidosGeralSaldo?codPlano={$plano}&consideraPedidosBloqueado={$consideraBloq}";
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


function obter_produtos_tendencia($plano, $nomeSimulacao)
{
    $baseUrl = 'http://10.162.0.53:9000';
    $nomeSimulacao = rawurlencode($nomeSimulacao); // trata espaços e acentos
    
    $apiUrl = "{$baseUrl}/pcp/api/obter_produtos_tendencia?codPlano={$plano}&nomeSimulacao={$nomeSimulacao}";
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




function ConsultaAbc($empresa)
{
    $baseUrl = '10.162.0.53:9000';
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

function ConsultaSimulacoes($empresa)
{
    $baseUrl = ($empresa == "1") ? '10.162.0.53:9000' : '192.168.0.183:9000';
    $apiUrl = "{$baseUrl}/pcp/api/ConsultaSimulacoes";
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
    $baseUrl = '10.162.0.53:9000';
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


function ConsultaTendencias($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? '10.162.0.53:9000' : '192.168.0.183:9000';
    $apiUrl = "{$baseUrl}/pcp/api/tendenciaSku";

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

function Simular_Programacao($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? '10.162.0.53:9000' : '192.168.0.183:9000';
    $apiUrl = "{$baseUrl}/pcp/api/simulacaoProgramacao";

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


function CadastroSimulacao($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? '10.162.0.53:9000' : '192.168.0.183:9000';
    $apiUrl = "{$baseUrl}/pcp/api/atualizaInserirSimulacao";

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

function atualizaInserirSimulacaoProdutos($dados)
{
    $baseUrl = '10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/atualizaInserirSimulacaoProdutos";

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

function DeleteSimulacao($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://10.162.0.190:8000';
    $apiUrl = "{$baseUrl}/pcp/api/deletarSimulacao";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "DELETE",
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


function DeleteSimulacaoProduto($dados)
{
    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/limpar_produtos_simulacao_Especifica";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "DELETE",
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


function simulacaoDetalhadaPorSku($empresa, $dados)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://10.162.0.191:9000';
    $apiUrl = "{$baseUrl}/pcp/api/simulacaoDetalhadaPorSku";

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


function Consulta_Ultimo_CalculoTendencia($empresa, $plano)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://10.162.0.191:9000';
    $apiUrl = "{$baseUrl}/pcp/api/obtendoUltimaTendencia_porPlano?codPlano={$plano}";
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

function obterImagemMP($codigoImagem)
{
    $baseUrl = 'http://10.162.0.53:9000';

    // Consulta ao backend para obter o total de imagens
    $quantidadeUrl = "{$baseUrl}/imagemEng/{$codigoImagem}/quantidade";

    $quantidade = 1;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $quantidadeUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if ($response !== false) {
        $json = json_decode($response, true);
        if (isset($json['total_imagens'])) {
            $quantidade = intval($json['total_imagens']);
        }
    }

    curl_close($ch);

    return [
        'imagem_url' => "{$baseUrl}/imagemEng/{$codigoImagem}/0", // começa com a primeira imagem
        'total_imagens' => $quantidade
    ];
}



function consultarInformacoesPlano($empresa, $token, $plano){
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://10.162.0.191:9000';
    $apiUrl = "{$baseUrl}/pcp/api/PlanoPorPlano?codigoPlano={$plano}";
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


function DetalhaOrdemProducao($token, $codSku){
    $empresa = "1";
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://10.162.0.191:9000';
    $apiUrl = "{$baseUrl}/pcp/api/OrdemProd_porSku?codSku={$codSku}";
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


function selecao_produtos_simulacao($token, $empresa, $nomeSimulacao){
    $nomeSimulacao = str_replace(' ', '%20', $nomeSimulacao);
    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/selecao_produtos_simulacao?codEmpresa={$empresa}&nomeSimulacao={$nomeSimulacao}";
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


function exluindo_simulacao_Produtos_zerados($dados)
{
    $baseUrl = 'http://10.162.0.53:9000';
    $apiUrl = "{$baseUrl}/pcp/api/exluindo_simulacao_Produtos_zerados";

    $ch = curl_init($apiUrl);

    $options = [
        CURLOPT_CUSTOMREQUEST => "DELETE",
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