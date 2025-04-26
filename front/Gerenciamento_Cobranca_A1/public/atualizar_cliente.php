<?php

require_once '../includes/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $telefone = $_POST["telefone"];

    if (empty($nome) || empty($email) || !is_numeric($id)) {
        echo "Por favor, preencha todos os campos corretamente.";
        exit;
    }

    $nome = mysqli_real_escape_string($conn, $nome);
    $email = mysqli_real_escape_string($conn, $email);
    $telefone = mysqli_real_escape_string($conn, $telefone);
    $id = mysqli_real_escape_string($conn, $id);

    $sql = "UPDATE clientes SET nome = ?, email = ?, telefone = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $nome, $email, $telefone, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "Dados do cliente atualizados com sucesso!";
            echo '<p><a href="listar_clientes.php">Voltar para a Lista de Clientes</a></p>';
            echo '<p><a href="index.php">Voltar para a Página Inicial</a></p>';
        } else {
            echo "Erro ao atualizar os dados do cliente: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Erro na preparação da consulta: " . mysqli_error($conn);
    }

    $conn->close();
} else {
    echo "Acesso inválido.";
}

?>