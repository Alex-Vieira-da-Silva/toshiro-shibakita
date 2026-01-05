<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Exemplo PHP</title>
</head>
<body>

<?php
ini_set("display_errors", 1);
header('Content-Type: text/html; charset=UTF-8');

// Exibe versão do PHP
echo 'Versão Atual do PHP: ' . phpversion() . '<br>';

// Variáveis de conexão (ideal: usar variáveis de ambiente)
$servername = getenv('DB_HOST') ?: '54.234.153.24';
$username   = getenv('DB_USER') ?: 'root';
$password   = getenv('DB_PASS') ?: 'Senha123';
$database   = getenv('DB_NAME') ?: 'meubanco';

// Criar conexão com tratamento de erro
$link = new mysqli($servername, $username, $password, $database);

if ($link->connect_errno) {
    die("Falha na conexão: " . $link->connect_error);
}

// Geração de dados aleatórios
$valor_rand1 = rand(1, 999);
$valor_rand2 = strtoupper(bin2hex(random_bytes(4)));
$host_name   = gethostname();

// Usando prepared statements (evita SQL Injection)
$stmt = $link->prepare(
    "INSERT INTO dados (AlunoID, Nome, Sobrenome, Endereco, Cidade, Host)
     VALUES (?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "isssss",
    $valor_rand1,
    $valor_rand2,
    $valor_rand2,
    $valor_rand2,
    $valor_rand2,
    $host_name
);

if ($stmt->execute()) {
    echo "Registro inserido com sucesso";
} else {
    echo "Erro ao inserir: " . $stmt->error;
}

$stmt->close();
$link->close();
?>

</body>
</html>