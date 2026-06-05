<?php
/**
 * public/logout.php
 * ログアウト処理ハンドラー (POST)
 */

require_once __DIR__ . '/../src/Auth.php';

// 1. POSTメソッド以外は受け付けない
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Auth::redirect('index.php');
}

// 2. CSRFトークンの検証
$csrfToken = $_POST['csrf_token'] ?? '';
if (!Auth::validateCsrfToken($csrfToken)) {
    error_log("Logout Failed: Invalid CSRF Token");
    Auth::redirect('index.php');
}

// 3. ログアウト処理の実行
Auth::logout();

// 4. ログイン画面へリダイレクト
Auth::redirect('index.php');
