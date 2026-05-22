<?php
// セッション開始と認証チェック
session_start();

// 未認証ならトップへリダイレクト
if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: /index.php');
    exit;
}

require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Product.php';

$db      = new Database();
$product = new Product($db);

// 絞り込み条件をGETパラメータから取得
$categoryId = isset($_GET['category_id']) && $_GET['category_id'] !== ''
    ? (int)$_GET['category_id']
    : null;

$keyword = isset($_GET['keyword']) && $_GET['keyword'] !== ''
    ? $_GET['keyword']
    : null;

// カテゴリ一覧を取得（プルダウン用）
$categories = $product->getAllCategories();

// 商品一覧を取得（絞り込み条件があれば絞り込む）
$products = $product->getAll($categoryId, $keyword);

// ビューの呼び出し
require_once __DIR__ . '/../../../views/admin/products.php';