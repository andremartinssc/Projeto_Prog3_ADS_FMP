<?php
require_once '../includes/conexao.php';

if (isset($_GET['fatura_id']) && is_numeric($_GET['fatura_id'])) {
    $fatura_id = $_GET['fatura_id'];

    $sql = "SELECT f.id, c.nome, f.valor, f.data_vencimento, f.status
            FROM faturas f
            JOIN clientes c ON f.cliente_id = c.id
            WHERE f.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $fatura_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $fatura = $result->fetch_assoc();

    if (!$fatura) {
        echo "Fatura não encontrada.";
        exit;
    }
} else {
    echo "ID de fatura inválido.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novo_status = $_POST['status'];
    $sql_update = "UPDATE faturas SET status = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $novo_status, $fatura_id);
    if ($stmt_update->execute()) {
        echo "<div class='sucesso'>Status atualizado com sucesso!</div>";
        // Recarrega os dados da fatura com o novo status
        $stmt->execute();
        $result = $stmt->get_result();
        $fatura = $result->fetch_assoc();
    } else {
        echo "<div class='erro'>Erro ao atualizar o status.</div>";
    }
    $stmt_update->close();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Fatura <?php echo $fatura['id']; ?></title>
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        .detalhes-fatura {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group select {
            width: 100%;
            padding: 8px;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container button {
            padding: 10px 20px;
            cursor: pointer;
        }

        .sucesso {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }

        .erro {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <h1>Detalhes da Fatura <?php echo $fatura['id']; ?></h1>

    <div class="detalhes-fatura">
        <p><strong>Número:</strong> <?php echo $fatura['id']; ?></p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($fatura['nome']); ?></p>
        <p><strong>Valor:</strong> R$ <?php echo number_format($fatura['valor'], 2, ',', '.'); ?></p>
        <p><strong>Data de Vencimento:</strong> <?php echo date('d/m/Y', strtotime($fatura['data_vencimento'])); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($fatura['status']); ?></p>

        <form method="POST">
            <div class="form-group">
                <label for="status">Alterar Status:</label>
                <select id="status" name="status">
                    <option value="pendente" <?php if ($fatura['status'] == 'pendente') echo 'selected'; ?>>Pendente</option>
                    <option value="enviada" <?php if ($fatura['status'] == 'enviada') echo 'selected'; ?>>Enviada</option>
                    <option value="paga" <?php if ($fatura['status'] == 'paga') echo 'selected'; ?>>Paga</option>
                    <option value="cancelada" <?php if ($fatura['status'] == 'cancelada') echo 'selected'; ?>>Cancelada</option>
                </select>
            </div>
            <div class="button-container">
                <button type="submit">Atualizar Status</button>
            </div>
        </form>
    </div>

    <p><a href="consulta_faturas.php">Voltar para Consulta de Faturas</a></p>

</body>

</html>