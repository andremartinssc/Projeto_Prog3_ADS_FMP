<?php

// Inclui o arquivo de conexão com o banco de dados
require_once '../includes/conexao.php';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <h1>Cadastro de Novo Cliente</h1>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recupera os dados do formulário
        $nome = $_POST["nome"];
        $email = $_POST["email"];
        $telefone = $_POST["telefone"];

        // Validação básica (melhorias são necessárias)
        if (empty($nome) || empty($email)) {
            echo "<div class='mensagem erro'>Por favor, preencha todos os campos obrigatórios.</div>";
        } else {
            // Sanitização básica para evitar SQL Injection (usando mysqli_real_escape_string)
            $nome = mysqli_real_escape_string($conn, $nome);
            $email = mysqli_real_escape_string($conn, $email);
            $telefone = mysqli_real_escape_string($conn, $telefone);

            // Prepara a consulta SQL
            $sql = "INSERT INTO clientes (nome, email, telefone) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                // Vincula os parâmetros
                mysqli_stmt_bind_param($stmt, "sss", $nome, $email, $telefone);

                // Executa a consulta
                if (mysqli_stmt_execute($stmt)) {
                    echo "<div class='mensagem sucesso'>Cliente cadastrado com sucesso!</div>";
                    echo "<div class='opcoes-cadastro'>";
                    echo "<p><a href='cadastro_cliente.php' class='botao'>Cadastrar Outro Cliente</a></p>";
                    echo "<p><a href='listar_clientes.php' class='botao'>Listar Clientes</a></p>";
                    echo "<p><a href='index.php' class='botao'>Retornar à Página Inicial</a></p>";
                    echo "</div>";
                } else {
                    echo "<div class='mensagem erro'>Erro ao cadastrar cliente: " . mysqli_error($conn) . "</div>";
                }

                // Fecha a declaração
                mysqli_stmt_close($stmt);
            } else {
                echo "<div class='mensagem erro'>Erro na preparação da consulta: " . mysqli_error($conn) . "</div>";
            }
        }

        // Fecha a conexão com o banco de dados
        mysqli_close($conn);
    } else {
        ?>
        <form action="" method="POST">
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
        <?php
    }
    ?>
    <script src="js/script.js"></script>
</body>
</html>