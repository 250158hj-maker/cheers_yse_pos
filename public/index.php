<?php
/**
 * public/index.php
 * SCR-01 ログイン画面 エントリポイント
 */

require_once __DIR__ . '/../src/Auth.php';

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. 既にログインしている場合は、権限に応じてリダイレクト
if (Auth::isLoggedIn()) {
    $user = Auth::user();
    if (isset($user['is_admin']) && $user['is_admin']) {
        header('Location: /admin/index.php');
    } else {
        header('Location: /register/index.php');
    }
    exit;
}

// 2. ログインしていない場合はログインViewを表示
// 処理と描画の分離方針に基づき、Viewファイルを読み込む
require_once __DIR__ . '/../views/login.php';
