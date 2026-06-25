<?php
require_once 'config.php';

$erro = '';

// Processa o formulário de Login via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if (!empty($email) && !empty($senha)) {
        // Busca o usuário na tabela de funcionários
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        // Se você usou senhas limpas (admin123), a comparação é direta.
        // Se usou password_hash, use: if ($usuario && password_verify($senha, $usuario['senha']))
        if ($usuario && $senha === $usuario['senha']) {
            $_SESSION['usuario_id']    = $usuario['id'];
            $_SESSION['usuario_nome']  = $usuario['nome'];
            $_SESSION['usuario_nivel'] = $usuario['nivel_acesso'];
            // Redirecionamento correto pós-login
            header("Location: dashboard.php");
            exit;
        } else {
            $erro = "E-mail ou senha incorretos!";
        }
    } else {
        $erro = "Por favor, preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema Oficina de Motos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin-top: 10%; }
    </style>
</head>
<body>

<div class="container login-container">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white text-center py-3">
            <h4 class="mb-0">Oficina MotoSport</h4>
        </div>
        <div class="card-body p-4">
            <h5 class="text-center mb-3 text-muted">Acesso ao Sistema</h5>
            
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger p-2 text-center"><?= $erro ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail Corporativo</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="exemplo@oficina.com" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" id="senha" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-dark w-100 mt-2">Entrar no Sistema</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>