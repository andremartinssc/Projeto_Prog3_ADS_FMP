<?php
require_once '../includes/conexao.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $cliente_id = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt->bind_param("i", $cliente_id);

        if ($stmt->execute()) {
            header("Location: listar_clientes.php?excluido=sucesso");
            exit();
        } else {
            header("Location: listar_clientes.php?excluido=erro");
            exit();
        }

        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        // Lida com erros de SQL, como chave estrangeira violada
        header("Location: listar_clientes.php?excluido=erro&mensagem=" . urlencode($e->getMessage()));
        exit();
    }

} else {
    header("Location: listar_clientes.php?excluido=invalido");
    exit();
}

$conn->close();
?>