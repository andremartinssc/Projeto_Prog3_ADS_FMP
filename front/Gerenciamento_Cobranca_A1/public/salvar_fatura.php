<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Fatura Salva</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<?php
require_once '../includes/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id = $_POST["cliente_id"];
    $valor = $_POST["valor"];
    $data_vencimento = $_POST["data_vencimento"];

    if (empty($cliente_id) || empty($valor) || empty($data_vencimento) || !is_numeric($valor) || $valor <= 0) {
        echo "<div class='erro'>Por favor, preencha todos os campos corretamente.</div>";
        echo '<p><a class="botao" href="emitir_fatura.php' . (isset($_GET['cliente_id']) ? '?cliente_id=' . $_GET['cliente_id'] : '') . '">Voltar para Emitir Fatura</a></p>';
        exit;
    }

    $cliente_id = mysqli_real_escape_string($conn, $cliente_id);
    $valor = mysqli_real_escape_string($conn, $valor);
    $data_vencimento = mysqli_real_escape_string($conn, $data_vencimento);

    $sql = "INSERT INTO faturas (cliente_id, valor, data_vencimento, status) VALUES (?, ?, ?, 'pendente')";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ids", $cliente_id, $valor, $data_vencimento);

        if (mysqli_stmt_execute($stmt)) {
            echo "<div class='sucesso'>Fatura emitida com sucesso!</div>";
            echo '<p><a class="botao" href="listar_clientes_fatura.php">Emitir outra fatura</a></p>';
           
            echo '<p><a class="botao" href="index.php">Voltar para Página Inicial</a></p>';
        } else {
            echo "<div class='erro'>Erro ao emitir fatura: " . mysqli_error($conn) . "</div>";
            echo '<p><a class="botao" href="emitir_fatura.php' . (isset($_GET['cliente_id']) ? '?cliente_id=' . $_GET['cliente_id'] : '') . '">Voltar para Emitir Fatura</a></p>';
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "<div class='erro'>Erro na preparação da consulta: " . mysqli_error($conn) . "</div>";
        echo '<p><a class="botao" href="emitir_fatura.php' . (isset($_GET['cliente_id']) ? '?cliente_id=' . $_GET['cliente_id'] : '') . '">Voltar para Emitir Fatura</a></p>';
    }

    mysqli_close($conn);
} else {
    echo "<div class='erro'>Acesso inválido.</div>";
    echo '<p><a class="botao" href="index.php">Voltar para Página Inicial</a></p>';
}
?>

</body>
</html>