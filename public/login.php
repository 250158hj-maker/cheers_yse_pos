<?php
/**
 * public/login.php
 * ログイン処理ハンドラー (POST)
 */

require_once __DIR__ . '/../src/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Auth::redirect('index.php');
}

$loginId   = $_POST['login_id'] ?? '';
$password  = $_POST['password'] ?? '';
$csrfToken = $_POST['csrf_token'] ?? '';

if (!Auth::validateCsrfToken($csrfToken)) {
    $_SESSION['error'] = '不正なリクエストです。もう一度お試しください。';
    Auth::redirect('index.php');
}

if (empty($loginId) || empty($password)) {
    $_SESSION['error'] = 'IDとパスワードを入力してください。';
    $_SESSION['last_login_id'] = $loginId;
    Auth::redirect('index.php');
}

try {
    require_once __DIR__ . '/../src/Database.php';
    $db = new Database();
    
    if (Auth::login($db, $loginId, $password)) {
        Auth::handleLoginRedirect();
    } else {
        $_SESSION['error'] = 'IDまたはパスワードが正しくありません。';
        $_SESSION['last_login_id'] = $loginId;
        Auth::redirect('index.php');
    }
} catch (Exception $e) {
    error_log("Login Exception: " . $e->getMessage());
    $_SESSION['error'] = 'システムエラーが発生しました。しばらく経ってから再度お試しください。';
    Auth::redirect('index.php');
}
