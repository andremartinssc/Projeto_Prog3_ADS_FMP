<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <h1>Editar Cliente</h1>
    <?php
    require_once '../includes/conexao.php';

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT id, nome, email, telefone FROM clientes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cliente = $result->fetch_assoc();

        if ($cliente) {
            ?>
            <form action="atualizar_cliente.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
                <div>
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                </div>
                <div>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                </div>
                <div>
                    <label for="telefone">Telefone:</label>
                    <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($cliente['telefone']); ?>">
                </div>
                <button type="submit">Salvar Alterações</button>
            </form>
            <?php
        } else {
            echo '<p>Cliente não encontrado.</p>';
        }
        $stmt->close();
    } else {
        echo '<p>ID do cliente inválido.</p>';
    }
    $conn->close();
    ?>
    <p><a href="listar_clientes.php">Voltar para a Lista de Clientes</a></p>
    <p><a href="index.php">Voltar para a Página Inicial</a></p>
</body>
</html>