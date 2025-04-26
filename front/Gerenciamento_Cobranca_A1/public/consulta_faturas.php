<?php
require_once '../includes/conexao.php';

// Buscar clientes para o filtro
$sql_clientes = "SELECT id, nome FROM clientes";
$result_clientes = $conn->query($sql_clientes);

$clientes = [];
if ($result_clientes->num_rows > 0) {
    while ($row = $result_clientes->fetch_assoc()) {
        $clientes[$row['id']] = $row['nome'];
    }
}

// Buscar faturas (com filtro opcional)
$sql_faturas = "SELECT f.id, c.nome, f.valor, f.data_vencimento, f.status, c.id as cliente_id
                FROM faturas f
                JOIN clientes c ON f.cliente_id = c.id";

if (isset($_GET['cliente_id']) && is_numeric($_GET['cliente_id'])) {
    $cliente_id = $_GET['cliente_id'];
    $sql_faturas .= " WHERE f.cliente_id = $cliente_id";
}

$result_faturas = $conn->query($sql_faturas);

$faturas = [];
if ($result_faturas->num_rows > 0) {
    while ($row = $result_faturas->fetch_assoc()) {
        $faturas[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Faturas</title>
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            text-align: left;
        }

        th,
        td {
            padding: 10px;
        }

        th {
            background-color: #f2f2f2;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .button-container button {
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
        }

        #filtro-cliente {
            display: block;
            margin: 10px auto;
            width: 200px;
            padding: 8px;
        }

        .actions-container {
            display: flex;
            gap: 5px;
        }
    </style>
</head>

<body>
    <h1>Consulta de Faturas</h1>

    <label for="filtro-cliente">Filtrar por Cliente:</label>
    <select id="filtro-cliente" name="filtro-cliente">
        <option value="">Todos os Clientes</option>
        <?php foreach ($clientes as $id => $nome): ?>
            <option value="<?php echo $id; ?>" <?php if (isset($cliente_id) && $cliente_id == $id) echo 'selected'; ?>>
                <?php echo htmlspecialchars($nome); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <table id="tabela-faturas">
        <thead>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th>Valor</th>
                <th>Data de Vencimento</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($faturas as $fatura): ?>
                <tr>
                    <td><?php echo $fatura['id']; ?></td>
                    <td><?php echo htmlspecialchars($fatura['nome']); ?></td>
                    <td>R$ <?php echo number_format($fatura['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($fatura['data_vencimento'])); ?></td>
                    <td><?php echo htmlspecialchars($fatura['status']); ?></td>
                    <td>
                        <div class="actions-container">
                            <button onclick="visualizarFatura(<?php echo $fatura['id']; ?>)">Visualizar</button>
                            <button onclick="excluirFatura(<?php echo $fatura['id']; ?>)">Excluir</button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        function visualizarFatura(faturaId) {
            window.location.href = 'detalhes_fatura.php?fatura_id=' + faturaId;
        }

        function excluirFatura(faturaId) {
            if (confirm('Tem certeza que deseja excluir esta fatura?')) {
                window.location.href = 'excluir_fatura.php?fatura_id=' + faturaId;
            }
        }

        document.getElementById('filtro-cliente').addEventListener('change', function() {
            let clienteId = this.value;
            window.location.href = 'consulta_faturas.php' + (clienteId ? '?cliente_id=' + clienteId : '');
        });
    </script>

    <p><a href="listar_clientes_fatura.php">Voltar Faturas</a></p>

</body>

</html>