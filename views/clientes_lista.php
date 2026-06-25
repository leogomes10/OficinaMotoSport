<?php
require_once '../config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

$erro = '';
$sucesso = '';
$clienteEdit = null; // Variável para guardar os dados do cliente que será editado

// DELETE (D - Excluir)
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $pdo->prepare("DELETE FROM clientes WHERE id = ?")->execute([$id]);
    $sucesso = "Cliente excluído com sucesso!";
}

// READ PARA UPDATE (Ler os dados para preencher o formulário)
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
    $stmt->execute([$id]);
    $clienteEdit = $stmt->fetch();
}

// CREATE (C - Inserir) e UPDATE (U - Atualizar) via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $endereco = trim($_POST['endereco']);
    $id_edit = isset($_POST['id_edit']) ? intval($_POST['id_edit']) : 0; // Se vier um ID, é edição
    
    // Validação PHP Exigida: CPF em branco ou menor que 11 dígitos
    $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);
    if (empty($cpf_limpo) || strlen($cpf_limpo) < 11) {
        $erro = "Validação Falhou: O CPF não pode estar em branco e deve conter no mínimo 11 números.";
    } else {
        if ($id_edit > 0) {
            // Se tem ID, dispara o UPDATE (Atualizar)
            $stmt = $pdo->prepare("UPDATE clientes SET nome=?, cpf=?, telefone=?, endereco=? WHERE id=?");
            $stmt->execute([$nome, $cpf, $telefone, $endereco, $id_edit]);
            $sucesso = "Cliente atualizado com sucesso!";
            $clienteEdit = null; // Limpa o formulário após editar
        } else {
            // Se não tem ID, dispara o INSERT (Criar)
            $stmt = $pdo->prepare("INSERT INTO clientes (nome, cpf, telefone, endereco) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nome, $cpf, $telefone, $endereco]);
            $sucesso = "Cliente cadastrado com sucesso!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand font-weight-bold" href="../dashboard.php">🏍️ Oficina MotoSport</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="../dashboard.php">Início</a></li>
                <li class="nav-item"><a class="nav-link active" href="clientes_lista.php">Clientes</a></li>
                <li class="nav-item"><a class="nav-link" href="pecas_lista.php">Estoque (Peças)</a></li>
                <li class="nav-item"><a class="nav-link" href="vendas_lista.php">Vendas</a></li>
                <li class="nav-item"><a class="nav-link" href="usuarios_lista.php">Funcionários</a></li>
            </ul>
            <div class="d-flex align-items-center text-white">
                <span class="me-3">Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong></span>
                <a href="../dashboard.php?acao=logout" class="btn btn-outline-danger btn-sm">Sair</a>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="text-dark">Gestão de Clientes</h2>
    
    <?php if($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="alert alert-success"><?= $sucesso ?></div><?php endif; ?>

    <div class="card mb-4">
        <div class="card-header <?= $clienteEdit ? 'bg-warning text-dark' : 'bg-primary text-white' ?>">
            <?= $clienteEdit ? 'Editando Cliente: ' . htmlspecialchars($clienteEdit['nome']) : 'Cadastrar Novo Cliente' ?>
        </div>
        <div class="card-body">
            <form method="POST" action="clientes_lista.php">
                <?php if($clienteEdit): ?>
                    <input type="hidden" name="id_edit" value="<?= $clienteEdit['id'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="nome" class="form-control" placeholder="Nome Completo" required value="<?= $clienteEdit ? htmlspecialchars($clienteEdit['nome']) : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="cpf" class="form-control" placeholder="CPF (Apenas Números)" required value="<?= $clienteEdit ? htmlspecialchars($clienteEdit['cpf']) : '' ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="telefone" class="form-control" placeholder="Telefone" value="<?= $clienteEdit ? htmlspecialchars($clienteEdit['telefone']) : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="endereco" class="form-control" placeholder="Endereço" value="<?= $clienteEdit ? htmlspecialchars($clienteEdit['endereco']) : '' ?>">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn <?= $clienteEdit ? 'btn-warning' : 'btn-success' ?>">
                        <?= $clienteEdit ? 'Salvar Alterações' : 'Salvar Cliente' ?>
                    </button>
                    <?php if($clienteEdit): ?>
                        <a href="clientes_lista.php" class="btn btn-secondary">Cancelar Edição</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-secondary text-white">Clientes Cadastrados</div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover m-0">
                <thead class="table-dark"><tr><th>ID</th><th>Nome</th><th>CPF</th><th>Telefone</th><th>Endereço</th><th>Ações</th></tr></thead>
                <tbody>
                    <?php foreach($pdo->query("SELECT * FROM clientes ORDER BY id DESC")->fetchAll() as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['nome']) ?></td>
                        <td><?= htmlspecialchars($c['cpf']) ?></td>
                        <td><?= htmlspecialchars($c['telefone']) ?></td>
                        <td><?= htmlspecialchars($c['endereco']) ?></td>
                        <td>
                            <a href="clientes_lista.php?editar=<?= $c['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="clientes_lista.php?excluir=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir cliente?')">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>