
<?php
$caminho_css = "style.css";
session_start();

// Verificar se o usuário já está logado
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar ao banco de dados (substitua com suas credenciais)
    $conexao = new mysqli("localhost", "root", "", "banco");

    // Verificar a conexão
    if ($conexao->connect_error) {
        die("Erro na conexão: " . $conexao->connect_error);
    }

    // Recuperar dados do formulário
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    // Consulta SQL para verificar as credenciais
    $sql = "SELECT id, nome, senha FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($usuario_id, $nome, $senha_hash);
    $stmt->fetch();

    // Verificar a senha
    if (password_verify($senha, $senha_hash)) {
        // Autenticação bem-sucedida
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['nome'] = $nome;
        header("Location: dashboard.php");
        exit();
    } else {
        // Senha incorreta
        $erro = "Credenciais inválidas.";
    }

    // Fechar a conexão
    $stmt->close();
    $conexao->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <head>
<link rel="stylesheet" href="<?php echo $caminho_css; ?>
">
</head>
</head>
<body>
    <h2 style="position: absolute; top:30px;">Login</h2>
    <?php if (isset($erro)) { echo "<p>$erro</p>"; } ?>
    <form action="login.php" method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" required><br>

        <input type="submit" value="Entrar">
    </form>
</body>
</html>
