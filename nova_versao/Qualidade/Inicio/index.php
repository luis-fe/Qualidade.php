<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Alterado</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
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
        <a href="http://10.162.0.190:8081/nova_versao/Qualidade/Inicio/">Ir para o novo link</a>
    </div>
</body>
</html>