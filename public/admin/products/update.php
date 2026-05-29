<?php
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/Product.php';

// 管理者権限チェック
Auth::requireAdmin();

// POSTリクエストのみ受け付ける
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRFトークンの検証
    $token = $_POST['csrf_token'] ?? '';
    if (!Auth::validateCsrfToken($token)) {
        header('Location: index.php');
        exit;
    }

    // パラメータ取得
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $categoryId = $_POST['category_id'] ?? null;
    $isTakeout = $_POST['is_takeout'] ?? 0;

    if ($id && !empty($name) && $price >= 0 && !empty($categoryId)) {
        $product = new Product();
        $product->update((int)$id, [
            'name'        => $name,
            'price'       => (int)$price,
            'category_id' => (int)$categoryId,
            'is_takeout'  => (bool)$isTakeout
        ]);
    }
}

// 商品一覧へリダイレクト
header('Location: index.php');
exit;
