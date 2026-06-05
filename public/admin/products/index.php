<?php
/**
 * public/admin/products/index.php
 */
require_once __DIR__ . '/../../../src/Auth.php';
Auth::requireAdmin();

require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Product.php';

$db      = new Database();
$productModel = new Product($db);

// 絞り込み条件
$categoryId = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null;
$keyword    = isset($_GET['keyword']) && $_GET['keyword'] !== '' ? $_GET['keyword'] : null;

// ビューへ渡すデータ
view('admin/products', [
    'categories' => $productModel->getAllCategories(),
    'products'   => $productModel->getAll($categoryId, $keyword)
]);
