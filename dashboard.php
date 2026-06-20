<?php
require_once 'config.php';

// Segurança: Bloqueia o acesso se não houver sessão ativa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Lógica de Logout
if (isset($_GET['acao']) && $_GET['acao'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Alerta: Busca Peças com estoque crítico (abaixo de 5 unidades)
$stmtCritico = $pdo->query("SELECT * FROM pecas WHERE estoque < 5 ORDER BY estoque ASC");
$pecasCriticas = $stmtCritico->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema Oficina</title>
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
            <h2 class="text-dark">Painel de Controle Principal</h2>
            <p class="text-muted">Gerencie o estoque, clientes e vendas da sua oficina mecânica.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card border-danger shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">⚠️ ALERTA: Peças com estoque baixo (Menos de 5 unidades)</h5>
                </div>
                <div class="card-body">
                    <?php if (count($pecasCriticas) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Peça</th>
                                        <th>Marca</th>
                                        <th>Moto Compatível</th>
                                        <th class="text-center">Quantidade Atual</th>
                                        <th>Preço Venda</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pecasCriticas as $peca): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($peca['nome_peca']) ?></strong></td>
                                            <td><?= htmlspecialchars($peca['marca']) ?></td>
                                            <td><?= htmlspecialchars($peca['modelo_moto']) ?></td>
                                            <td class="text-center font-weight-bold text-danger">
                                            <span class="badge bg-danger fs-6"><?= $peca['estoque'] ?> un</span>
                                            </td>
                                            <td>R$ <?= number_format($peca['preco_venda'], 2, ',', '.') ?></td>
                                            <td class="text-center">
                                                <span class="text-danger font-weight-bold">Repor Urgente!</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success mb-0 py-3 text-center">
                            🎉 Excelente! Nenhuma peça está com estoque abaixo do limite de 5 unidades.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>