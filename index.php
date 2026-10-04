<?php
session_start();
require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare(
        "SELECT p.id, p.name, p.price, p.stock,
                c.name AS category_name,
                s.name AS supplier_name
         FROM products p
         JOIN categories c ON p.category_id = c.id
         JOIN suppliers s ON p.supplier_id = s.id
         WHERE p.name LIKE :search
            OR c.name LIKE :search
            OR s.name LIKE :search
         ORDER BY p.id DESC"
    );
    $stmt->execute(['search' => "%{$search}%"]);
} else {
    $stmt = $pdo->prepare(
        "SELECT p.id, p.name, p.price, p.stock,
                c.name AS category_name,
                s.name AS supplier_name
         FROM products p
         JOIN categories c ON p.category_id = c.id
         JOIN suppliers s ON p.supplier_id = s.id
         ORDER BY p.id DESC"
    );
    $stmt->execute();
}

$products = $stmt->fetchAll();

$message = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Inventaris</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <header class="header">
        <div>
            <p class="eyebrow">Tugas Rutin 8</p>
            <h1>Inventaris Barang</h1>
            <p class="muted">PHP + PDO + MySQL</p>
        </div>
        <a class="btn primary" href="create.php">+ Tambah Produk</a>
    </header>

    <?php if ($message): ?>
        <div class="flash <?= e($message['type']) ?>">
            <?= e($message['text']) ?>
        </div>
    <?php endif; ?>

    <section class="card">
        <form class="search" method="get">
            <input type="text" name="search" placeholder="Cari produk, kategori, atau supplier..."
                   value="<?= e($search) ?>">
            <button class="btn" type="submit">Cari</button>
            <?php if ($search !== ''): ?>
                <a class="btn ghost" href="index.php">Reset</a>
            <?php endif; ?>
        </form>
    </section>

    <section class="card">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Supplier</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!$products): ?>
                    <tr>
                        <td colspan="7" class="empty">Data tidak ditemukan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $i => $product): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= e($product['name']) ?></strong></td>
                            <td><?= e($product['category_name']) ?></td>
                            <td><?= e($product['supplier_name']) ?></td>
                            <td>Rp <?= number_format((float)$product['price'], 0, ',', '.') ?></td>
                            <td><?= (int)$product['stock'] ?></td>
                            <td class="actions">
                                <a class="btn small" href="edit.php?id=<?= (int)$product['id'] ?>">Edit</a>
                                <a class="btn small danger" href="delete.php?id=<?= (int)$product['id'] ?>"
                                   onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
</body>
</html>
