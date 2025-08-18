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



         body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea, #1d0ac4);
            color: #333;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 50px 30px;
            border-radius: 20px;
            text-align: center;
            max-width: 450px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            animation: fadeIn 1.5s ease-in-out;
        }

        .container img {
            width: 100px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        h1 {
            margin: 15px 0;
            font-size: 28px;
            color: #333;
        }

        p {
            font-size: 18px;
            margin-bottom: 30px;
            color: #555;
        }

        a {
            display: inline-block;
            padding: 12px 25px;
            background-color: #0f36e2;
            color: #fff;
            font-weight: bold;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        a:hover {
            background-color: #101fa3;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }



    </style>
</head>

<body>
 <div class="container">
        <img src="https://cdn-icons-png.flaticon.com/512/1828/1828843.png" alt="Aviso">
        <h1>O link mudou!</h1>
        <p>Você pode acessar o novo endereço clicando no botão abaixo:</p>
        <a href="http://10.162.0.53:8081/nova_versao/indexPcp.php">Ir para o novo link</a>
    </div>

</body>

</html>
