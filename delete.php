<?php
session_start();
require_once 'config/database.php';

$pdo = Database::getInstance()->getConnection();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash'] = ['type' => 'error', 'text' => 'ID produk tidak valid.'];
    header('Location: index.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() === 0) {
        throw new RuntimeException('Produk tidak ditemukan.');
    }

    $pdo->commit();
    $_SESSION['flash'] = ['type' => 'success', 'text' => 'Produk berhasil dihapus.'];
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['flash'] = ['type' => 'error', 'text' => 'Produk gagal dihapus.'];
}

header('Location: index.php');
exit;
