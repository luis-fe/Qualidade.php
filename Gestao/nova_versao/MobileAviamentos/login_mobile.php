<?php
session_start();

// Função que consome a sua API para buscar os usuários
function Consultar_Usuarios($empresa)
{
    $baseUrl = ($empresa == "1") ? 'http://10.162.0.53:9000' : 'http://192.168.0.183:8000';
    $apiUrl = "{$baseUrl}/pcp/api/UsuarioHabilitadoAviamento?codEmpresa={$empresa}";
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: a44pcp22",
    ]);

    $apiResponse = curl_exec($ch);

    if (!$apiResponse) {
        error_log("Erro na requisição: " . curl_error($ch), 0);
        return []; 
    }

    curl_close($ch);
    return json_decode($apiResponse, true);
}

// Se o usuário já estiver logado, redireciona para a Reposição
if (isset($_SESSION['matricula']) && !empty($_SESSION['matricula'])) {
    header("Location: modulos/reposicao/index.php");
    exit;
}

$erro_login = "";

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['matricula'])) {
    
    $matriculaDigitada = trim($_POST['matricula']);
    $empresa = "1"; 

    $usuariosHabilitados = Consultar_Usuarios($empresa);
    $usuarioValido = false;
    $nomeOperador = "";

    if ($usuariosHabilitados && is_array($usuariosHabilitados)) {
        foreach ($usuariosHabilitados as $user) {
            if (isset($user['codMatricula']) && trim($user['codMatricula']) == $matriculaDigitada) {
                $usuarioValido = true;
                $nomeOperador = isset($user['nomeUsuario']) ? $user['nomeUsuario'] : "Operador";
                break; 
            }
        }
    }

    if ($usuarioValido) {
        $_SESSION['matricula'] = $matriculaDigitada;
        $_SESSION['nomeUsuario'] = $nomeOperador;
        
        // Redireciona para o sistema principal de reposição
        header("Location: modulos/reposicao/index.php");
        exit;
    } else {
        $erro_login = "Matrícula não autorizada ou não encontrada.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Reposição Mobile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f3f4f6; }
    </style>
</head>
<body class="w-full p-4 flex flex-col items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="mb-6 text-center border-b border-gray-100 pb-4">
            <h2 class="text-2xl font-bold text-gray-800">Acesso ao Sistema</h2>
            <p class="text-sm text-gray-500 mt-1">Informe sua matrícula para iniciar a reposição</p>
        </div>

        <?php if (!empty($erro_login)): ?>
            <div class="mb-5 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg relative text-sm text-center font-bold">
                <?= $erro_login ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login_mobile.php">
            <div class="mb-6">
                <label for="matricula" class="block text-sm font-medium text-gray-700 mb-2">Matrícula</label>
                <input type="number" name="matricula" id="matricula" placeholder="Ex: 12345" required autofocus
                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-2xl px-4 py-3 border font-bold text-center bg-gray-50">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold text-lg py-3 px-4 rounded-lg shadow hover:bg-blue-700 transition">
                Entrar
            </button>
        </form>
    </div>

</body>
</html>