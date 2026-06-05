<?php
/**
 * public/register/checkout.php
 * 計上処理（POST受付・DB保存）
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Sale.php';

// 1. 認証ガード: ログインしていない場合はログイン画面へ
Auth::requireLogin();

/**
 * --- 処理部 (Processing) ---
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRFトークンの検証
    $token = $_POST['csrf_token'] ?? '';
    if (!Auth::validateCsrfToken($token)) {
        header('Location: index.php?error=csrf_error');
        exit;
    }

    // 注文データ（JSON）を取得
    $json = $_POST['order_data'] ?? '';
    $data = json_decode($json, true);

    // バリデーション
    if (!$data || empty($data['items'])) {
        header('Location: index.php?error=invalid_data');
        exit;
    }

    require_once __DIR__ . '/../../src/Database.php';
    $db = new Database();
    $sale = new Sale($db);
    $user = Auth::user();
    
    // DBに保存
    $success = $sale->create($user['id'], $data);

    if ($success) {
        // 成功時：お釣り額をセッションに保存してレジ画面へ戻る
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['checkout_success'] = true;
        $_SESSION['last_change'] = $data['change_amount'] ?? 0;
        
        header('Location: index.php');
        exit;
    } else {
        // 失敗時：エラーメッセージを持って戻る
        header('Location: index.php?error=checkout_failed');
        exit;
    }
} else {
    // POST以外での直接アクセスはレジ画面へリダイレクト
    header('Location: index.php');
    exit;
}
