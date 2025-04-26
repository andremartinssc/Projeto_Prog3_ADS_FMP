<?php
require_once '../includes/conexao.php';

// Verificar se o ID da fatura foi passado pela URL
if (isset($_GET['fatura_id']) && is_numeric($_GET['fatura_id'])) {
    $fatura_id = $_GET['fatura_id'];

    // Preparar a consulta SQL para excluir a fatura
    $sql_excluir = "DELETE FROM faturas WHERE id = ?";

    // Preparar a declaração
    $stmt = $conn->prepare($sql_excluir);

    if ($stmt) {
        // Vincular o parâmetro
        $stmt->bind_param("i", $fatura_id);

        // Executar a declaração
        if ($stmt->execute()) {
            // Exclusão bem-sucedida
            $mensagem = "Fatura excluída com sucesso.";
            $tipo_mensagem = "sucesso";
        } else {
            // Erro ao excluir
            $mensagem = "Erro ao excluir a fatura: " . $stmt->error;
            $tipo_mensagem = "erro";
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        // Erro na preparação da consulta
        $mensagem = "Erro na preparação da consulta: " . $conn->error;
        $tipo_mensagem = "erro";
    }
} else {
    // ID da fatura não foi passado ou não é válido
    $mensagem = "ID da fatura inválido.";
    $tipo_mensagem = "alerta";
}

// Fechar a conexão com o banco de dados
$conn->close();

// Redirecionar de volta para a página de consulta de faturas com a mensagem
header("Location: consulta_faturas.php?mensagem=" . urlencode($mensagem) . "&tipo=" . $tipo_mensagem);
exit();
?>