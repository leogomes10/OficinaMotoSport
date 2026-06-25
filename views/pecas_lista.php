<?php
require_once '../config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

$erro = '';
$sucesso = '';
$pecaEdit = null;

// DELETE (Excluir)
if (isset($_GET['excluir'])) {
    $pdo->prepare("DELETE FROM pecas WHERE id = ?")->execute([$_GET['excluir']]);
    $sucesso = "Peça excluída do estoque.";
}

// READ PARA UPDATE (Preencher formulário)
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $pdo->prepare("SELECT * FROM pecas WHERE id = ?");
    $stmt->execute([$id]);
    $pecaEdit = $stmt->fetch();
}

// CREATE e UPDATE (Inserir e Atualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome_peca']);
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo_moto']);
    $preco = floatval($_POST['preco_venda']);
    $estoque = intval($_POST['estoque']);
    $id_edit = isset($_POST['id_edit']) ? intval($_POST['id_edit']) : 0;
    
    // Validação PHP Exigida: Preço negativo
    if ($preco < 0) {
        $erro = "Validação Falhou: O preço de venda não pode ser negativo!";
    } else {
        if ($id_edit > 0) {
            $stmt = $pdo->prepare("UPDATE pecas SET nome_peca=?, marca=?, modelo_moto=?, preco_venda=?, estoque=? WHERE id=?");
            $stmt->execute([$nome, $marca, $modelo, $preco, $estoque, $id_edit]);
            $sucesso = "Peça atualizada com sucesso!";
            $pecaEdit = null; // Limpa após editar
        } else {
            $stmt = $pdo->prepare("INSERT INTO pecas (nome_peca, marca, modelo_moto, preco_venda, estoque) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $marca, $modelo, $preco, $estoque]);
            $sucesso = "Peça adicionada ao estoque!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
<<<<<<< HEAD
    <title>Estoque de Peças</title>
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
                <li class="nav-item"><a class="nav-link active" href="pecas_lista.php">Estoque (Peças)</a></li>
                <li class="nav-item"><a class="nav-link" href="vendas_lista.php">Vendas</a></li>
                <li class="nav-item"><a class="nav-link" href="usuarios_lista.php">Funcionários</a></li>
            </ul>
            <div class="d-flex align-items-center text-white">
                <span class="me-3">Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong></span>
                <a href="../dashboard.php?acao=logout" class="btn btn-outline-danger btn-sm">Sair</a>
=======
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
>>>>>>> 41f9e814c6178675f175f4926b15771644e299fa
            </div>
        </div>
    </div>
</nav>

<div class="container">
<<<<<<< HEAD
    <h2 class="text-dark">Estoque de Peças</h2>
    <?php if($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="alert alert-success"><?= $sucesso ?></div><?php endif; ?>

    <div class="card mb-4">
        <div class="card-header <?= $pecaEdit ? 'bg-warning text-dark' : 'bg-primary text-white' ?>">
            <?= $pecaEdit ? 'Editar Peça: ' . htmlspecialchars($pecaEdit['nome_peca']) : 'Cadastrar Nova Peça' ?>
        </div>
        <div class="card-body">
            <form method="POST" action="pecas_lista.php">
                <?php if($pecaEdit): ?>
                    <input type="hidden" name="id_edit" value="<?= $pecaEdit['id'] ?>">
                <?php endif; ?>
                
                <div class="row g-3">
                    <div class="col-md-3"><input type="text" name="nome_peca" class="form-control" placeholder="Nome da Peça" required value="<?= $pecaEdit ? htmlspecialchars($pecaEdit['nome_peca']) : '' ?>"></div>
                    <div class="col-md-2"><input type="text" name="marca" class="form-control" placeholder="Marca" value="<?= $pecaEdit ? htmlspecialchars($pecaEdit['marca']) : '' ?>"></div>
                    <div class="col-md-3"><input type="text" name="modelo_moto" class="form-control" placeholder="Moto Compatível" value="<?= $pecaEdit ? htmlspecialchars($pecaEdit['modelo_moto']) : '' ?>"></div>
                    <div class="col-md-2"><input type="number" step="0.01" name="preco_venda" class="form-control" placeholder="Preço (R$)" required value="<?= $pecaEdit ? $pecaEdit['preco_venda'] : '' ?>"></div>
                    <div class="col-md-2"><input type="number" name="estoque" class="form-control" placeholder="Qtd. Estoque" required value="<?= $pecaEdit ? $pecaEdit['estoque'] : '' ?>"></div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn <?= $pecaEdit ? 'btn-warning' : 'btn-success' ?>">
                        <?= $pecaEdit ? 'Salvar Alterações' : 'Salvar no Estoque' ?>
                    </button>
                    <?php if($pecaEdit): ?><a href="pecas_lista.php" class="btn btn-secondary">Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-secondary text-white">Peças em Estoque</div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover m-0">
                <thead class="table-dark"><tr><th>ID</th><th>Peça</th><th>Marca</th><th>Moto</th><th>Preço</th><th>Estoque</th><th>Ações</th></tr></thead>
                <tbody>
                    <?php foreach($pdo->query("SELECT * FROM pecas ORDER BY id DESC")->fetchAll() as $p): ?>
                    <tr class="<?= $p['estoque'] < 5 ? 'table-danger' : '' ?>">
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['nome_peca']) ?></td>
                        <td><?= htmlspecialchars($p['marca']) ?></td>
                        <td><?= htmlspecialchars($p['modelo_moto']) ?></td>
                        <td>R$ <?= number_format($p['preco_venda'], 2, ',', '.') ?></td>
                        <td><strong><?= $p['estoque'] ?> un</strong></td>
                        <td>
                            <a href="pecas_lista.php?editar=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="pecas_lista.php?excluir=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir peça?')">Excluir</a>
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
            <h2 class="text-dark">Painel de Controle de Peças</h2>
            <p class="text-muted">Gerencie o estoque da oficina mecânica.</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
>>>>>>> 41f9e814c6178675f175f4926b15771644e299fa
</body>
</html>