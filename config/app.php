<?php

require_once __DIR__ . '/../vendor/autoload.php';

// セッションの開始（全ページ共通）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// ベースURLの計算 (サブディレクトリ環境対応)
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$publicPos = strpos($scriptName, '/public/');
if ($publicPos !== false) {
    $baseUrl = substr($scriptName, 0, $publicPos + 8);
} else {
    $baseUrl = '/';
}
define('BASE_URL', $baseUrl);

// 消費税率の設定
define('TAX_RATE_NORMAL', 0.10);  // 10%
define('TAX_RATE_REDUCED', 0.08); // 8%

/**
 * HTMLエスケープの短縮関数
 */
function h(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * CSRFトークンの隠しフィールドを生成
 */
function csrf_field(): string
{
    $token = $_SESSION['csrf_token'] ?? '';
    return '<input type="hidden" name="csrf_token" value="' . h($token) . '">';
}

/**
 * JSONレスポンスを返却
 */
function json(array $data, int $status = 200): void
{
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code($status);
    echo json_encode($data);
    exit;
}
