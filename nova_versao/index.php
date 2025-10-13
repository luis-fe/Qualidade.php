<?php
    include_once("./requests.php");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $username = $_POST["usuario"];
        $password = $_POST["senha"];
        $empresa = $_POST["empresa"];
        
    
        $Resposta = fazerChamadaApi($username, $password, $empresa);

        if ($Resposta['status'] == true) {
            $nome = $Resposta['nome'];
            session_start();
            $_SESSION['usuario'] = $nome;
            $_SESSION['empresa'] = $empresa;
            $_SESSION['funcao'] = $Resposta['funcao'];
            $_SESSION['token'] = "a40016aabcx9";
            if($Resposta['funcao'] == "ADMINISTRADOR"){
                header("Location: ./src/Terceirizados/StatusOps/index.php");
            };            
            exit();
        } else {
            $mensagemErro = "Usuário inválido. Tente novamente.";
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo Mpl</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <link rel="shortcut icon" type="image/png" href="./templates/ImagemMplSemFundo.png">
    <link rel="stylesheet" href="./css/styleLogin.css">
    <style>
        /* Estilos adicionais podem ser colocados aqui */
        body {
            background-color: #f0f0f0; /* Cor de fundo */
        }
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        }
        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .logo-small {
            width: 150px; /* Ajuste conforme necessário */
        }
        .form-floating {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <main class="container-fluid main-container">
        <div class="card">
            <div class="card-body">
                <div class="logo-container">
                    <img src="./templates/ImagemMplSemFundo.png" alt="Logo" class="logo-small">
                </div>
                <h2 class="text-center mb-4">Login</h2>
                <form action="" method="POST" class="was-validated">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="usuario" id="usuario" placeholder=" " required>
                        <label for="usuario">Matrícula</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="senha" id="senha" placeholder=" " required>
                        <label for="senha">Senha</label>
                    </div>
                    <div class="form-floating">
                        <select class="form-select" id="empresa" name="empresa" required>
                            <option value="" disabled selected>Selecione a Empresa</option>
                            <option value="1">Matriz</option>
                            <option value="4">Cianorte</option>
                        </select>
                        <label for="empresa">Empresa</label>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg w-100">ENTRAR</button>
                    </div>
                </form>
                <?php if (isset($mensagemErro)) : ?>
                    <div class="alert alert-danger mt-3" role="alert">
                        <?php echo $mensagemErro; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-vw85IePz0yrakp6BtdLWCP4MUL1Lh7A5i+M4fzJN1XSSZfz7/QJ5sFEocLT1Gma5"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-KpQ1q3X98ndJb11TgbJ1DEtM8VmVGSyK5vjJ6m/YjEA=" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
</body>

</html>