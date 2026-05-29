<?php
require_once __DIR__ . '/../../../src/Auth.php';
Auth::requireAdmin();

require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Product.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id) {
    header('Location: index.php');
    exit;
}

$productModel = new Product();
$product = $productModel->getById($id);

if (!$product) {
    header('Location: index.php');
    exit;
}

$categories = $productModel->getAllCategories();

// ビューの呼び出し
require_once __DIR__ . '/../../../views/admin/product_edit.php';
