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
    redirect_with_message('index.php', '不正なリクエストです。もう一度お試しください。');
}

if (empty($loginId) || empty($password)) {
    $_SESSION['last_login_id'] = $loginId;
    redirect_with_message('index.php', 'IDとパスワードを入力してください。');
}

try {
    require_once __DIR__ . '/../src/Database.php';
    $db = new Database();
    
    if (Auth::login($db, $loginId, $password)) {
        Auth::handleLoginRedirect();
    } else {
        $_SESSION['last_login_id'] = $loginId;
        redirect_with_message('index.php', 'IDまたはパスワードが正しくありません。');
    }
} catch (Exception $e) {
    error_log("Login Exception: " . $e->getMessage());
    redirect_with_message('index.php', 'システムエラーが発生しました。しばらく経ってから再度お試しください。');
}
