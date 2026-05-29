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

    $id = $_POST['id'] ?? null;

    if ($id) {
        $product = new Product();
        $product->delete((int)$id);
    }
}

// 商品一覧へリダイレクト
header('Location: index.php');
exit;
