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
        header('Location: ' . BASE_URL . 'admin/index.php');
    } else {
        header('Location: ' . BASE_URL . 'register/index.php');
    }
    exit;
}

// 2. ログインしていない場合はログインViewを表示
require_once __DIR__ . '/../views/login.php';
