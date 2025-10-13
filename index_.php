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
                header("Location: ./Wms/src/Inicio/index.php");
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
    <link rel="website icon" type="png" href="./templates/imagens/ImagemMplSemFundo.png">
    <link rel="stylesheet" href="./css/styleLogin.css"> 
</head>

<body>
    <main class="container-fluid h-100 main-container">
        <div class="row justify-content-center align-items-centes" style="min-width: 100%;">
        <div class="row fixed-top" style="min-width: 100%; margin-bottom: 100px; margin-top: 10px">
            <div class="col-12 text-center">
                <div class="logo-small">
                    <img src="./templates/imagens/ImagemMplSemFundo.png" alt="" class="img" style="width: 200px">
                </div>
            </div>
        </div>
        <div class="row" style="min-width: 100%; align-items: center; justify-content: center">
            <div class="col-10 col-sm-8 col-md-6 col-lg-4">
                <h2 class="text-white text-center" style="margin-bottom: 40px; font-weight: 700">Login</h2>
                <div class="card">
                        
                    <div class="card-body">
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
                            <button type="submit" class="btn btn-primary" style="height: 5vh">ENTRAR</button>
                        </form>
                    </div>
                    <div class="card-footer">
                        <?php if (isset($mensagemErro)) : ?>
                            <p style="color: red;"><?php echo $mensagemErro; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>
</body>

</html>
