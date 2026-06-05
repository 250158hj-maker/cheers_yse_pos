<?php
/**
 * public/register/index.php
 */
require_once __DIR__ . '/../../src/Auth.php';
Auth::requireStaff();

require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/Product.php';

$db = new Database();
$productModel = new Product($db);

view('register', [
    'categories' => $productModel->getAllCategories(),
    'products'   => $productModel->getAll()
]);
