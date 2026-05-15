<?php
/**
 * public/logout.php
 * ログアウト処理ハンドラー (POST)
 */

require_once __DIR__ . '/../src/Auth.php';

// 1. POSTメソッド以外は受け付けない（セキュリティ・意図しないログアウト防止）
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

// 2. ログアウト処理の実行
Auth::logout();

// 3. ログイン画面（トップページ）へリダイレクト
header('Location: /index.php');
exit;
