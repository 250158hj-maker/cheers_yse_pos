<?php
/**
 * public/logout.php
 * ログアウト処理ハンドラー (POST)
 */

require_once __DIR__ . '/../src/Auth.php';

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ベースURLの計算
$scriptName = $_SERVER['SCRIPT_NAME'];
$publicPos = strpos($scriptName, '/public/');
$baseUrl = ($publicPos !== false) ? substr($scriptName, 0, $publicPos + 8) : '/';

// 1. POSTメソッド以外は受け付けない
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $baseUrl . 'index.php');
    exit;
}

// 2. CSRFトークンの検証
$csrfToken = $_POST['csrf_token'] ?? '';
if (!Auth::validateCsrfToken($csrfToken)) {
    error_log("Logout Failed: Invalid CSRF Token");
    header('Location: ' . $baseUrl . 'index.php');
    exit;
}

// 3. ログアウト処理の実行
Auth::logout();
error_log("Logout Success: Session cleared. Redirecting to " . $baseUrl . "index.php");

// 4. ログイン画面へリダイレクト
header('Location: ' . $baseUrl . 'index.php');
exit;
