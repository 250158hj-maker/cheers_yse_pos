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
        try {
            require_once __DIR__ . '/../../../src/Database.php';
            $db = new Database();
            $productModel = new Product($db);
            $productModel->delete((int)$id);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            // 外部キー制約（売上実績がある場合）のエラーをキャッチ
            if ($e->getCode() === '23000') {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['error_message'] = "この商品は売上実績があるため削除できません。";
                header('Location: index.php');
                exit;
            }
            throw $e;
        }
    }
}

// 通常のリダイレクト
header('Location: index.php');
exit;
