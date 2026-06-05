<?php
/**
 * public/admin/products/edit.php
 */
require_once __DIR__ . '/../../../src/Auth.php';
Auth::requireAdmin();

require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Product.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    Auth::redirect('admin/products/index.php');
}

$db = new Database();
$productModel = new Product($db);
$product = $productModel->getById($id);

if (!$product) {
    Auth::redirect('admin/products/index.php');
}

view('admin/product_edit', [
    'product'    => $product,
    'categories' => $productModel->getAllCategories()
]);
