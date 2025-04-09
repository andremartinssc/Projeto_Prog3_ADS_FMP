<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Cadastro de Novo Cliente</h1>
    <form action="cadastrar_cliente.php" method="POST">
        <div>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="telefone">Telefone:</label>
            <input type="tel" id="telefone" name="telefone">
        </div>
        <button type="submit">Cadastrar</button>
    </form>
    <script src="js/script.js"></script>
</body>
</html>