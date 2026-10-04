<?php
session_start();
require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll();
$suppliers = $pdo->query("SELECT id, name FROM suppliers ORDER BY name")->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $supplierId = (int)($_POST['supplier_id'] ?? 0);
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    if ($name === '') $errors[] = 'Nama produk wajib diisi.';
    if ($categoryId <= 0) $errors[] = 'Kategori wajib dipilih.';
    if ($supplierId <= 0) $errors[] = 'Supplier wajib dipilih.';
    if ($price < 0) $errors[] = 'Harga tidak boleh negatif.';
    if ($stock < 0) $errors[] = 'Stok tidak boleh negatif.';

    if (!$errors) {
        $stmt = $pdo->prepare(
            "INSERT INTO products (name, category_id, supplier_id, price, stock)
             VALUES (:name, :category_id, :supplier_id, :price, :stock)"
        );
        $stmt->execute([
            'name' => $name,
            'category_id' => $categoryId,
            'supplier_id' => $supplierId,
            'price' => $price,
            'stock' => $stock
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Produk berhasil ditambahkan.'];
        header('Location: index.php');
        exit;
    }
}

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
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container narrow">
    <header class="header">
        <div>
            <p class="eyebrow">CREATE</p>
            <h1>Tambah Produk</h1>
        </div>
        <a class="btn ghost" href="index.php">← Kembali</a>
    </header>

    <?php if ($errors): ?>
        <div class="flash error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form class="card form-grid" method="post">
        <label>Nama Produk
            <input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
        </label>

        <label>Kategori
            <select name="category_id" required>
                <option value="">-- Pilih kategori --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['id'] ?>"
                        <?= (int)($_POST['category_id'] ?? 0) === (int)$category['id'] ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Supplier
            <select name="supplier_id" required>
                <option value="">-- Pilih supplier --</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= (int)$supplier['id'] ?>"
                        <?= (int)($_POST['supplier_id'] ?? 0) === (int)$supplier['id'] ? 'selected' : '' ?>>
                        <?= e($supplier['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Harga
            <input type="number" name="price" min="0" step="0.01" required value="<?= e($_POST['price'] ?? '') ?>">
        </label>

        <label>Stok
            <input type="number" name="stock" min="0" required value="<?= e($_POST['stock'] ?? '') ?>">
        </label>

        <button class="btn primary" type="submit">Simpan Produk</button>
    </form>
</div>
</body>
</html>
