<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            margin-top: 50px;
        }

        .opcao {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .opcao:hover {
            background-color: #eee;
        }

        .opcao a {
            text-decoration: none;
            color: #333;
            font-size: 18px;
            display: block;
        }
    </style>
</head>
<body>
    <h1>Sistema de Cobrança</h1>
    <div class="container">
        <div class="opcao">
            <a href="cadastro_cliente.php">Cadastro de Clientes</a>
        </div>

        <div class="opcao">
        <a href="listar_clientes.php">Gerenciamento de Clientes</a>
        </div>

        <div class="opcao">
            <a href="listar_clientes_fatura.php">Gerenciamento de Faturas</a>
        </div>
        
    </div>
</body>
</html>