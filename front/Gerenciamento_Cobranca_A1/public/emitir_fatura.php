<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emitir Fatura</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <h1>Emitir Nova Fatura</h1>
    <?php
    require_once '../includes/conexao.php';

    if (isset($_GET['cliente_id']) && is_numeric($_GET['cliente_id'])) {
        $cliente_id = $_GET['cliente_id'];
        $sql = "SELECT id, nome, email FROM clientes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cliente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cliente = $result->fetch_assoc();
        $stmt->close();

        if ($cliente) {
            ?>
            <form action="salvar_fatura.php" method="POST">
                <div>
                    <label for="cliente_nome">Cliente:</label>
                    <input type="text" id="cliente_nome" name="cliente_nome" value="<?php echo htmlspecialchars($cliente['nome'] . ' (' . $cliente['email'] . ')'); ?>" readonly>
                    <input type="hidden" name="cliente_id" value="<?php echo $cliente['id']; ?>">
                </div>
                <div>
                    <label for="valor">Valor:</label>
                    <input type="number" step="0.01" id="valor" name="valor" required>
                </div>
                <div>
                    <label for="data_vencimento">Data de Vencimento:</label>
                    <input type="date" id="data_vencimento" name="data_vencimento" required>
                </div>
                <button type="submit">Emitir Fatura</button>
                <p><a href="listar_clientes.php">Voltar para Gerenciar Clientes</a></p>
                <p><a href="index.php">Voltar para Página Inicial</a></p>
            </form>
            <?php
        } else {
            echo "<div class='erro'>Cliente não encontrado.</div>";
            echo '<p><a href="listar_clientes.php">Voltar para Gerenciar Clientes</a></p>';
            echo '<p><a href="index.php">Voltar para Página Inicial</a></p>';
        }
    } else {
        ?>
        <div class='erro'>Nenhum cliente selecionado.</div>
        <p><a href="listar_clientes.php">Voltar para Gerenciar Clientes</a></p>
        <p><a href="index.php">Voltar para Página Inicial</a></p>
        <?php
    }

    $conn->close();
    ?>
</body>
</html>