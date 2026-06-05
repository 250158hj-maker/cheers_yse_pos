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
    // "/public/" より前の部分を取得し、最後に "/" をつける
    $baseUrl = substr($scriptName, 0, $publicPos) . '/';
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

/**
 * URLを生成する
 */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return BASE_URL . $path;
}

/**
 * Viewをレンダリングする
 */
function view(string $path, array $data = []): void
{
    // 配列のキーを変数名として展開
    extract($data);
    
    // BASE_URL などの定数はそのまま使えるが、ヘルパー経由が望ましい
    $baseUrl = url();
    
    // viewパスの解決 (.php がなければ付与)
    $viewFile = __DIR__ . '/../views/' . ltrim($path, '/') . (str_ends_with($path, '.php') ? '' : '.php');
    
    if (file_exists($viewFile)) {
        require $viewFile;
    } else {
        die("View file not found: {$viewFile}");
    }
}

/**
 * メッセージ付きでリダイレクトする
 */
function redirect_with_message(string $path, string $message, string $type = 'error'): void
{
    $_SESSION[$type] = $message;
    header('Location: ' . url($path));
    exit;
}
