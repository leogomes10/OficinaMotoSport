<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Peças</title>
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
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h2 class="text-dark">Painel de Controle de Peças</h2>
            <p class="text-muted">Gerencie o estoque da oficina mecânica.</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>