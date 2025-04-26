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

        .acoes a {
            margin-left: 10px;
            text-decoration: none;
            color: #007bff;
        }

        .acoes a:hover {
            text-decoration: underline;
        }

        .excluir-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            margin-left: 10px;
            text-decoration: none;
            font-size: inherit;
            padding: 0;
        }

        .excluir-btn:hover {
            text-decoration: underline;
        }

        .mensagem {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .mensagem-sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensagem-erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .mensagem-invalido {
            background-color: #fff3cd;
            color: #85640a;
            border: 1px solid #ffeeba;
        }

        .oculto {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Gerenciar Clientes</h1>

    <a href="index.php">Voltar para a Página Inicial</a>

    <?php
    if (isset($_GET['excluido'])) {
        if ($_GET['excluido'] === 'sucesso') {
            echo '<div class="mensagem mensagem-sucesso">Cliente excluído com sucesso!</div>';
        } elseif ($_GET['excluido'] === 'erro') {
            echo '<div class="mensagem mensagem-erro">';
            echo 'Erro ao excluir o cliente.';
            if (isset($_GET['mensagem'])) {
                echo ' Detalhes: ' . htmlspecialchars(urldecode($_GET['mensagem']));
            }
            echo '</div>';
        } elseif ($_GET['excluido'] === 'invalido') {
            echo '<div class="mensagem mensagem-invalido">ID de cliente inválido.</div>';
        }
    }
    ?>

    <div class="busca-container">
        <label for="busca">Buscar Cliente:</label>
        <input type="text" id="busca" name="busca" onkeyup="buscarClientes(this.value)">
    </div>

    <div class="cliente-lista">
        <h2>Clientes Cadastrados</h2>
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
                    echo '<a href="editar_cliente.php?id=' . $row["id"] . '">Editar</a>';
                    echo ' <button class="excluir-btn" onclick="confirmarExclusao(' . $row["id"] . ')">Excluir</button>';
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

        function confirmarExclusao(clienteId) {
            if (confirm("Tem certeza que deseja excluir este cliente?")) {
                window.location.href = "excluir_cliente.php?id=" + clienteId;
            }
        }
    </script>
</body>
</html>