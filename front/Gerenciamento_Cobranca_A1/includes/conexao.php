<?php
$servername = "localhost"; // Geralmente é localhost
$username = "root";      // Seu nome de usuário do MySQL (padrão no XAMPP é root)
$password = "";          // Sua senha do MySQL (padrão no XAMPP é vazia)
$database = "cobranca_bd"; // O nome do banco de dados que você criou no phpMyAdmin

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Define o charset para UTF-8
$conn->set_charset("utf8");
?>