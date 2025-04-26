<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Clientes</title>
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        .busca-container {
            margin-top: 20px;
            margin-bottom: 20px;
            width: 80%;
        }

        .busca-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .busca-container input[type="text"] {
            width: calc(100% - 12px);
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .cliente-lista {
            width: 80%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .cliente-lista h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .cliente-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cliente-item:last-child {
            border-bottom: none;
        }

        .acoes {
            display: flex;
            gap: 10px;
        }

        .acoes a {
            text-decoration: none;
            color: #007bff;
            padding: 5px 10px;
            border: 1px solid #007bff;
            border-radius: 5px;
        }

        .acoes a:hover {
            text-decoration: underline;
            background-color: #f0f8ff;
        }

        .oculto {
            display: none;
        }

        .export-btn {
            display: block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            width: 200px;
            border: none;
        }

        .export-btn:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <h1>Gerenciar Clientes</h1>
    <a href="index.php">Voltar para a Página Inicial</a>

    <div class="busca-container">
        <label for="busca">Buscar Cliente para Fatura:</label>
        <input type="text" id="busca" name="busca" onkeyup="buscarClientes(this.value)">
    </div>

    <div class="cliente-lista">
        <h2>Selecione o Cliente para Gerar a Fatura</h2>
        <div id="lista-clientes">
            <?php
            require_once '../includes/conexao.php';

            $sql = "SELECT id, nome, email FROM clientes ORDER BY nome";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="cliente-item" data-nome="' . htmlspecialchars(strtolower($row["nome"])) . '" data-email="' . htmlspecialchars(strtolower($row["email"])) . '">';
                    echo '<span>' . htmlspecialchars($row["nome"]) . ' (' . htmlspecialchars($row["email"]) . ')</span>';
                    echo '<div class="acoes">';
                    echo '<a href="emitir_fatura.php?cliente_id=' . $row["id"] . '">Gerar Fatura</a>';
                    echo '<a href="consulta_faturas.php?cliente_id=' . $row["id"] . '">Consultar Faturas</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Nenhum cliente cadastrado.</p>';
            }

            $conn->close();
            ?>
        </div>
    </div>

    <button class="export-btn" onclick="exportarFaturasParaExcel()">Exportar Faturas para Excel</button>

    <script>
        function buscarClientes(textoBusca) {
            var filtro = textoBusca.toLowerCase();
            var listaClientes = document.getElementById("lista-clientes");
            var clienteItem = listaClientes.getElementsByClassName("cliente-item");

            for (var i = 0; i < clienteItem.length; i++) {
                var nome = clienteItem[i].dataset.nome;
                var email = clienteItem[i].dataset.email;
                if (nome.includes(filtro) || email.includes(filtro)) {
                    clienteItem[i].classList.remove("oculto");
                } else {
                    clienteItem[i].classList.add("oculto");
                }
            }
        }

        function exportarFaturasParaExcel() {
            window.location.href = 'exportar_faturas_excel.php'; // Mudança aqui
        }
    </script>
</body>

</html>