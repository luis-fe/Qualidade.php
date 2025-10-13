<?php
session_start();

function fazerLogin($username, $password, $empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/UsuarioSenha?codigo={$username}&senha={$password}";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a40016aabcx9",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

function rotinasUsuarios($codigo, $empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.190:5000' : 'http://10.162.0.191:5000';
    $apiUrl = "{$baseUrl}/api/rotasAutorizadasPORUsuario?codigo={$codigo}";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a40016aabcx9",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
    }

    curl_close($ch);

    return json_decode($apiResponse, true);
}

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        if (isset($_GET["acao"])) {
            $acao = $_GET["acao"];

            switch ($acao) {
                case 'Fazer_Login':
                    $username = $_GET['username'];
                    $password = $_GET['password'];
                    $empresa = $_GET['empresa'];

                    $loginResponse = fazerLogin($username, $password, $empresa);

                    if ($loginResponse && isset($loginResponse['status']) && $loginResponse['status'] === true) {
                        $_SESSION['username'] = $username;
                        $_SESSION['empresa'] = $empresa;
                        if ($_SESSION['empresa'] == 1) {
                                $_SESSION['nomeEmpresa'] = 'Matriz';}else{
                                            $_SESSION['nomeEmpresa'] = 'Cianorte';
                                }


                        $_SESSION['situacao'] = $loginResponse['situacao'];
                        $_SESSION['funcao'] = $loginResponse['funcao'];

                        header('Content-Type: application/json');
                        echo json_encode(['status' => true, 'data' => $loginResponse]);
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => false, 'message' => 'Login falhou.']);
                    }
                    break;

                case 'Rotinas_Usuarios':
                    $codigo = $_GET['codigo'];

                    $rotinasResponse = rotinasUsuarios($codigo, '1');

                    if ($rotinasResponse) {
                        // Acessar o array retornado corretamente
                        if (isset($rotinasResponse[0]['urlTela'])) {
                            $_SESSION['urlTela'] = $rotinasResponse[0]['urlTela']; // Corrigindo a atribuição
                        }
                        header('Content-Type: application/json');
                        echo json_encode(['status' => true, 'data' => $rotinasResponse]);
                    } else {
                        header('Content-Type: application/json');
                        echo json_encode(['status' => false, 'message' => 'Falha ao obter rotinas.']);
                    }
                    break;

                default:
                    header('Content-Type: application/json');
                    echo json_encode(['status' => false, 'message' => 'Ação não reconhecida.']);
                    break;
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['status' => false, 'message' => 'Nenhuma ação especificada.']);
        }
        break;

    default:
        error_log("Método de ação não especificado no método POST", 0);
        header('Content-Type: application/json');
        echo json_encode(['status' => false, 'message' => 'Erro: Método de requisição não suportado.']);
        break;
}
