<?php
require_once '../config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

$erro = '';
$sucesso = '';
$usuarioEdit = null;

// DELETE (Excluir)
if (isset($_GET['excluir'])) {
    $pdo->prepare("DELETE FROM usuarios WHERE id = ?")->execute([$_GET['excluir']]);
    $sucesso = "Usuário removido com sucesso.";
}

// READ PARA UPDATE
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $usuarioEdit = $stmt->fetch();
}

// CREATE e UPDATE via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $nivel = $_POST['nivel_acesso'];
    $id_edit = isset($_POST['id_edit']) ? intval($_POST['id_edit']) : 0;

    if ($id_edit > 0) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, senha=?, nivel_acesso=? WHERE id=?");
        $stmt->execute([$nome, $email, $senha, $nivel, $id_edit]);
        $sucesso = "Utilizador atualizado com sucesso!";
        $usuarioEdit = null;
    } else {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, nivel_acesso) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $senha, $nivel]);
        $sucesso = "Funcionário cadastrado e pronto para acessar o sistema!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <title>Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand font-weight-bold" href="../dashboard.php">🏍️ Oficina MotoSport</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="../dashboard.php">Início</a></li>
                <li class="nav-item"><a class="nav-link" href="clientes_lista.php">Clientes</a></li>
                <li class="nav-item"><a class="nav-link" href="pecas_lista.php">Estoque (Peças)</a></li>
                <li class="nav-item"><a class="nav-link" href="vendas_lista.php">Vendas</a></li>
                <li class="nav-item"><a class="nav-link active" href="usuarios_lista.php">Funcionários</a></li>
            </ul>
            <div class="d-flex align-items-center text-white">
                <span class="me-3">Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong></span>
                <a href="../dashboard.php?acao=logout" class="btn btn-outline-danger btn-sm">Sair</a>
=======
    <title>Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand font-weight-bold" href="dashboard.php">🏍️ Oficina MotoSport</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php">Início</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="views/clientes_lista.php">Clientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="views/pecas_lista.php">Estoque (Peças)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="views/vendas_lista.php">Vendas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="views/usuarios_lista.php">Funcionários</a>
                </li>
            </ul>
            <div class="d-flex align-items-center text-white">
                <span class="me-3">Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong> (<?= $_SESSION['usuario_nivel'] ?>)</span>
                <a href="dashboard.php?acao=logout" class="btn btn-outline-danger btn-sm">Sair</a>
>>>>>>> 41f9e814c6178675f175f4926b15771644e299fa
            </div>
        </div>
    </div>
</nav>

<div class="container">
<<<<<<< HEAD
    <h2 class="text-dark">Gestão de Funcionários (Usuários)</h2>
    <?php if($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="alert alert-success"><?= $sucesso ?></div><?php endif; ?>

    <div class="card mb-4">
        <div class="card-header <?= $usuarioEdit ? 'bg-warning text-dark' : 'bg-dark text-white' ?>">
            <?= $usuarioEdit ? 'Editar Utilizador: ' . htmlspecialchars($usuarioEdit['nome']) : 'Cadastrar Login' ?>
        </div>
        <div class="card-body">
            <form method="POST" action="usuarios_lista.php">
                <?php if($usuarioEdit): ?>
                    <input type="hidden" name="id_edit" value="<?= $usuarioEdit['id'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-3"><input type="text" name="nome" class="form-control" placeholder="Nome Completo" required value="<?= $usuarioEdit ? htmlspecialchars($usuarioEdit['nome']) : '' ?>"></div>
                    <div class="col-md-4"><input type="email" name="email" class="form-control" placeholder="E-mail (Login)" required value="<?= $usuarioEdit ? htmlspecialchars($usuarioEdit['email']) : '' ?>"></div>
                    <div class="col-md-2"><input type="text" name="senha" class="form-control" placeholder="Senha" required value="<?= $usuarioEdit ? htmlspecialchars($usuarioEdit['senha']) : '' ?>"></div>
                    <div class="col-md-3">
                        <select name="nivel_acesso" class="form-select">
                            <option value="Administrador" <?= ($usuarioEdit && $usuarioEdit['nivel_acesso'] == 'Administrador') ? 'selected' : '' ?>>Administrador</option>
                            <option value="Vendedor" <?= ($usuarioEdit && $usuarioEdit['nivel_acesso'] == 'Vendedor') ? 'selected' : '' ?>>Vendedor</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn <?= $usuarioEdit ? 'btn-warning' : 'btn-success' ?>">
                        <?= $usuarioEdit ? 'Salvar Alterações' : 'Criar Usuário' ?>
                    </button>
                    <?php if($usuarioEdit): ?><a href="usuarios_lista.php" class="btn btn-secondary">Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-secondary text-white">Equipa Registada</div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover m-0">
                <thead class="table-dark"><tr><th>ID</th><th>Nome</th><th>E-mail (Login)</th><th>Nível</th><th>Ações</th></tr></thead>
                <tbody>
                    <?php foreach($pdo->query("SELECT * FROM usuarios ORDER BY id DESC")->fetchAll() as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['nome']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($u['nivel_acesso']) ?></span></td>
                        <td>
                            <a href="usuarios_lista.php?editar=<?= $u['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="usuarios_lista.php?excluir=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir acesso?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
=======
    <div class="row mb-4">
        <div class="col">
            <h2 class="text-dark">Painel de Controle da Usuarios</h2>
            <p class="text-muted">Gerencie os usuarios do site da oficina mecânica.</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
>>>>>>> 41f9e814c6178675f175f4926b15771644e299fa
</body>
</html>