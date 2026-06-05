<?php
/**
 * public/admin/index.php
 * SCR-03 売上管理画面 エントリポイント
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Sale.php';
require_once __DIR__ . '/../../src/Database.php';

Auth::requireAdmin();

$db = new Database();
$saleModel = new Sale($db);

$filters = [
    'store_id'    => $_GET['store_id'] ?? '',
    'category_id' => $_GET['category_id'] ?? '',
    'date_from'   => $_GET['date_from'] ?? '',
    'date_to'     => $_GET['date_to'] ?? '',
    'receipt_no'  => $_GET['receipt_no'] ?? '',
];

view('admin/sales', [
    'pageTitle'  => '売上管理',
    'sales'      => $saleModel->findFiltered($filters),
    'summary'    => $saleModel->getSummary($filters),
    'filters'    => $filters,
    'stores'     => $db->fetchAll("SELECT id, name FROM users WHERE is_admin = 0 ORDER BY name ASC"),
    'categories' => $db->fetchAll("SELECT id, name FROM categories ORDER BY id ASC")
]);
