<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: lightgray;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: right;
            height: 100vh;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        .login-box {
            background: #001f3f;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: fadeIn 1.5s ease;
            height: 100vh;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-box input {
            background: #ffffff;
            color: #333;
            border: none;
            border-radius: 10px;
            padding: 10px;
            margin: 5px 0;
            transition: 0.3s;
            min-width: 100%;
        }

        .login-box label {
            color: black;
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            cursor: pointer;
            background: none;
            border: none;
            color: #00509e;
            position: absolute;
            right: 0;
            top: 21px;
            z-index: 10;
        }

        .login-box .btn-primary {
            background: #00509e;
            border: none;
            width: 100%;
            padding: 10px;
            font-weight: bold;
            transition: background 0.3s ease;
            border-radius: 10px;
        }

        .login-box .btn-primary:hover {
            background: #0066cc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .login-box .forgot {
            color: #66b2ff;
            font-size: 0.9em;
            margin-top: 10px;
            display: inline-block;
            transition: color 0.3s ease;
        }

        .login-box .forgot:hover {
            color: #ffffff;
        }


        /* Animação de rotação */
    @keyframes giro360 {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }

    /* Aplicar animação ao carregar */
    .girar-ao-carregar {
      animation: giro360 1s ease-in-out forwards;
    }


    </style>
</head>

<body>
<div class="container text-center">
  <div class="d-flex flex-column align-items-center">
    
    <img src="iconeModuloPCP6.png"
         alt="Tela de Login"
         class="img-fluid mb-4 girar-ao-carregar"
         style="width: 600px; height: auto;">


</div>
         
  </div>

    <div class="login-box">
        <h2 class="mb-4">Login</h2>
        <form onsubmit="enviarDados(event)">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="usuario" placeholder="Usuário" required>
                <label for="usuario">Usuário</label>
            </div>
            <div class="mb-3 input-group">
                <div class="form-floating flex-grow-1">
                    <input type="password" class="form-control" id="senha" placeholder="Senha" required>
                    <label for="senha">Senha</label>
                </div>
                <span class="input-group-text" onclick="IconePassword()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </span>
            </div>
            <div class="form-floating mb-3">
                <select class="form-select" id="empresa" name="empresa" required>
                    <option value="" disabled selected>Selecione a Empresa</option>
                    <option value="1">Matriz</option>
                    <option value="4">Cianorte</option>
                </select>
                <label for="empresa">Empresa</label>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.7/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(() => {
            $('#usuario').focus()
        })

        function IconePassword() {
            const inputPassword = document.getElementById('senha');
            const iconePassword = document.getElementById('toggleIcon');
            if (inputPassword.type === 'password') {
                inputPassword.type = 'text';
                iconePassword.classList.remove('fa-eye');
                iconePassword.classList.add('fa-eye-slash');
            } else {
                inputPassword.type = 'password';
                iconePassword.classList.remove('fa-eye-slash');
                iconePassword.classList.add('fa-eye');
            }
        }

        async function enviarDados(event) {
            event.preventDefault();
            try {
                const response = await $.ajax({
                    type: 'GET',
                    url: 'requests.php',
                    dataType: 'json',
                    data: {
                        acao: 'Fazer_Login',
                        username: document.getElementById('usuario').value,
                        password: document.getElementById('senha').value,
                        empresa: document.getElementById('empresa').value
                    }
                });
                console.log(response);
                if (response['status'] === true) {
                    await Swal.fire({
                        title: 'Login Realizado',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 3000,
                        backdrop: false, // Desativa o backdrop para evitar scroll
                    });
                    Rotinas_Usuarios()
                    window.location.href = "Pcp/PlanoDeProducao/";
                    
                } else {
                    await Swal.fire({
                        title: 'Login Inválido',
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 3000,
                        backdrop: false, // Desativa o backdrop para evitar scroll
                    });
                    $('#usuario').val('');
                    $('#senha').val('');
                    $('#empresa').val('');

                }
            } catch (error) {
                console.error('Erro:', error);
            }
        }
        const Rotinas_Usuarios = async () => {
            // $('#loadingModal').modal('show');
            try {
                const data = await $.ajax({
                    type: 'GET',
                    url: 'requests.php',
                    dataType: 'json',
                    data: {
                        acao: 'Rotinas_Usuarios',
                        codigo: document.getElementById('usuario').value,
                    }
                });
            } catch (error) {
                console.error('Erro ao consultar chamados:', error);
            } finally {
                // $('#loadingModal').modal('hide');
            }
        }


        
    </script>
</body>

</html>
