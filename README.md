# 📘 Projeto: Nginx Load Balancer + PHP + MySQL (Docker)

Este projeto demonstra uma arquitetura simples utilizando **Nginx como load balancer**, múltiplas instâncias PHP conectadas a um banco **MySQL**, tudo containerizado com Docker.  
A aplicação PHP insere registros aleatórios na tabela `dados` e o Nginx distribui as requisições entre os servidores.

---

## 🐳 Arquitetura do Projeto

            ┌──────────────────────────┐
            │        Cliente            │
            └──────────────┬───────────┘
                           │
                           ▼
                ┌──────────────────┐
                │     NGINX LB     │  Porta 4500
                └───────┬──────────┘
        ┌───────────────┼────────────────┬───────────────┐
        ▼               ▼                ▼
 PHP App #1       PHP App #2        PHP App #3
        │               │                │
        └──────────────┴────────────────┘
                       ▼
                ┌──────────────┐
                │    MySQL     │
                └──────────────┘


---

## 📂 Estrutura de Arquivos

├── Dockerfile
├── nginx.conf
├── index.php
└── README.md

---

⚙️ Configuração do Nginx (Load Balancer)

## 🧱 Dockerfile (Nginx)

```Dockerfile

FROM nginx:1.27-alpine

RUN rm -f /etc/nginx/conf.d/default.conf

COPY nginx.conf /etc/nginx/nginx.conf

RUN chmod 644 /etc/nginx/nginx.conf

EXPOSE 80

worker_processes auto;

events {
    worker_connections 1024;
}

http {

    sendfile on;
    keepalive_timeout 65;

    upstream all {
        server 172.31.0.37:80 max_fails=3 fail_timeout=10s;
        server 172.31.0.151:80 max_fails=3 fail_timeout=10s;
        server 172.31.0.149:80 max_fails=3 fail_timeout=10s;
    }

    server {
        listen 4500;

        location / {
            proxy_pass http://all;

            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;

            proxy_buffering off;
        }
    }
}

---

🐘 Código PHP (index.php)

<?php
ini_set("display_errors", 1);
header('Content-Type: text/html; charset=UTF-8');

echo 'Versão Atual do PHP: ' . phpversion() . '<br>';

$servername = getenv('DB_HOST') ?: '54.234.153.24';
$username   = getenv('DB_USER') ?: 'root';
$password   = getenv('DB_PASS') ?: 'Senha123';
$database   = getenv('DB_NAME') ?: 'meubanco';

$link = new mysqli($servername, $username, $password, $database);

if ($link->connect_errno) {
    die("Falha na conexão: " . $link->connect_error);
}

$valor_rand1 = rand(1, 999);
$valor_rand2 = strtoupper(bin2hex(random_bytes(4)));
$host_name   = gethostname();

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

---

🗄️ Estrutura da Tabela MySQL

CREATE TABLE dados (
    AlunoID INT PRIMARY KEY AUTO_INCREMENT,
    Nome VARCHAR(50) NOT NULL,
    Sobrenome VARCHAR(50) NOT NULL,
    Endereco VARCHAR(150),
    Cidade VARCHAR(50),
    Host VARCHAR(50),
    DataCriacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

---
