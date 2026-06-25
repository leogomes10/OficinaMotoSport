<?php
require_once '../config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

$erro = '';
$sucesso = '';
$vendaEdit = null;

// DELETE (Cancelar Venda e Repor Stock)
if (isset($_GET['excluir'])) {
    $id_excluir = intval($_GET['excluir']);
    $pdo->beginTransaction();
    try {
        $stmtBusca = $pdo->prepare("SELECT peca_id, quantidade FROM vendas WHERE id = ?");
        $stmtBusca->execute([$id_excluir]);
        $vendaCancelar = $stmtBusca->fetch();
        
        if ($vendaCancelar) {
            // Repõe o stock antes de apagar
            $pdo->prepare("UPDATE pecas SET estoque = estoque + ? WHERE id = ?")->execute([$vendaCancelar['quantidade'], $vendaCancelar['peca_id']]);
            $pdo->prepare("DELETE FROM vendas WHERE id = ?")->execute([$id_excluir]);
            $pdo->commit();
            $sucesso = "Venda cancelada e stock reposto com sucesso.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $erro = "Erro ao cancelar venda.";
    }
}

// READ PARA UPDATE
if (isset($_GET['editar'])) {
    $id = intval($_GET['editar']);
    $stmt = $pdo->prepare("SELECT * FROM vendas WHERE id = ?");
    $stmt->execute([$id]);
    $vendaEdit = $stmt->fetch();
}

// CREATE e UPDATE (Validações rigorosas de Stock)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id = intval($_POST['cliente_id']);
    $peca_id = intval($_POST['peca_id']);
    $quantidade = intval($_POST['quantidade']);
    $id_edit = isset($_POST['id_edit']) ? intval($_POST['id_edit']) : 0;
    
    $pdo->beginTransaction();
    try {
        // Se for Edição, primeiro devolvemos a quantidade antiga ao stock para poder recalcular
        if ($id_edit > 0) {
            $stmtOld = $pdo->prepare("SELECT peca_id, quantidade FROM vendas WHERE id = ?");
            $stmtOld->execute([$id_edit]);
            $oldSale = $stmtOld->fetch();
            $pdo->prepare("UPDATE pecas SET estoque = estoque + ? WHERE id = ?")->execute([$oldSale['quantidade'], $oldSale['peca_id']]);
        }

        // Verifica o stock atual da peça escolhida
        $stmtPeca = $pdo->prepare("SELECT preco_venda, estoque FROM pecas WHERE id = ?");
        $stmtPeca->execute([$peca_id]);
        $peca = $stmtPeca->fetch();

        // Validação PHP Exigida: Bloquear se pedir mais do que tem no estoque [cite: 10]
        if ($peca && $quantidade > $peca['estoque']) {
            $erro = "Validação Falhou: O stock é insuficiente! Só restam {$peca['estoque']} unidades.";
            $pdo->rollBack(); // Desfaz a devolução se falhar
        } else if ($peca) {
            $valor_total = $peca['preco_venda'] * $quantidade;
            
            // Retira a nova quantidade do stock 
            $pdo->prepare("UPDATE pecas SET estoque = estoque - ? WHERE id = ?")->execute([$quantidade, $peca_id]);
            
            if ($id_edit > 0) {
                // Atualiza a venda [cite: 4]
                $pdo->prepare("UPDATE vendas SET cliente_id=?, peca_id=?, quantidade=?, valor_total=? WHERE id=?")
                    ->execute([$cliente_id, $peca_id, $quantidade, $valor_total, $id_edit]);
                $sucesso = "Venda atualizada com sucesso!";
            } else {
                // Nova venda [cite: 4]
                $pdo->prepare("INSERT INTO vendas (cliente_id, peca_id, quantidade, valor_total) VALUES (?, ?, ?, ?)")
                    ->execute([$cliente_id, $peca_id, $quantidade, $valor_total]);
                $sucesso = "Venda realizada e stock atualizado!";
            }
            $pdo->commit();
            $vendaEdit = null;
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $erro = "Erro ao processar transação: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Vendas</title>
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
                <li class="nav-item"><a class="nav-link active" href="vendas_lista.php">Vendas</a></li>
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
    <h2 class="text-dark">Gestão de Vendas</h2>
    <?php if($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="alert alert-success"><?= $sucesso ?></div><?php endif; ?>

    <div class="card mb-4">
        <div class="card-header <?= $vendaEdit ? 'bg-warning text-dark' : 'bg-success text-white' ?>">
            <?= $vendaEdit ? 'Editar Venda #'.$vendaEdit['id'] : 'Registrar Nova Venda' ?>
        </div>
        <div class="card-body">
            <form method="POST" action="vendas_lista.php">
                <?php if($vendaEdit): ?>
                    <input type="hidden" name="id_edit" value="<?= $vendaEdit['id'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-5">
                        <select name="cliente_id" class="form-select" required>
                            <option value="">Selecione o Cliente...</option>
                            <?php 
                            foreach($pdo->query("SELECT id, nome FROM clientes")->fetchAll() as $c) {
                                $selected = ($vendaEdit && $vendaEdit['cliente_id'] == $c['id']) ? 'selected' : '';
                                echo "<option value='{$c['id']}' $selected>{$c['nome']}</option>"; 
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <select name="peca_id" class="form-select" required>
                            <option value="">Selecione a Peça...</option>
                            <?php 
                            foreach($pdo->query("SELECT id, nome_peca, preco_venda, estoque FROM pecas")->fetchAll() as $p) {
                                $selected = ($vendaEdit && $vendaEdit['peca_id'] == $p['id']) ? 'selected' : '';
                                echo "<option value='{$p['id']}' $selected>{$p['nome_peca']} - R$ {$p['preco_venda']} (Estoque: {$p['estoque']})</option>"; 
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="quantidade" class="form-control" placeholder="Qtd" min="1" required value="<?= $vendaEdit ? $vendaEdit['quantidade'] : '' ?>">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn <?= $vendaEdit ? 'btn-warning' : 'btn-primary' ?>">
                        <?= $vendaEdit ? 'Salvar Alterações' : 'Finalizar Venda' ?>
                    </button>
                    <?php if($vendaEdit): ?><a href="vendas_lista.php" class="btn btn-secondary">Cancelar</a><?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-secondary text-white">Histórico de Vendas</div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover m-0">
                <thead class="table-dark"><tr><th>Cod</th><th>Cliente</th><th>Peça</th><th>Qtd</th><th>Data</th><th>Total Pago</th><th>Ações</th></tr></thead>
                <tbody>
                    <?php 
                    $sql = "SELECT v.*, c.nome as cliente, p.nome_peca as peca FROM vendas v JOIN clientes c ON v.cliente_id = c.id JOIN pecas p ON v.peca_id = p.id ORDER BY v.data_venda DESC";
                    foreach($pdo->query($sql)->fetchAll() as $v): 
                    ?>
                    <tr>
                        <td>#<?= $v['id'] ?></td>
                        <td><?= htmlspecialchars($v['cliente']) ?></td>
                        <td><?= htmlspecialchars($v['peca']) ?></td>
                        <td><?= $v['quantidade'] ?></td>
                        <td><?= date('d/m/Y', strtotime($v['data_venda'])) ?></td>
                        <td><strong>R$ <?= number_format($v['valor_total'], 2, ',', '.') ?></strong></td>
                        <td>
                            <a href="vendas_lista.php?editar=<?= $v['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="vendas_lista.php?excluir=<?= $v['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Cancelar venda e repor stock?')">Excluir</a>
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