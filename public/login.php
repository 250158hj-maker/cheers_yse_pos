<?php
/**
 * public/login.php
 * ログイン処理ハンドラー (POST)
 */

require_once __DIR__ . '/../src/Auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$loginId   = $_POST['login_id'] ?? '';
$password  = $_POST['password'] ?? '';
$csrfToken = $_POST['csrf_token'] ?? '';

if (!Auth::validateCsrfToken($csrfToken)) {
    $_SESSION['error'] = '不正なリクエストです。もう一度お試しください。';
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

if (empty($loginId) || empty($password)) {
    $_SESSION['error'] = 'IDとパスワードを入力してください。';
    $_SESSION['last_login_id'] = $loginId;
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

try {
    require_once __DIR__ . '/../src/Database.php';
    $db = new Database();
    $user = Auth::login($db, $loginId, $password);

    if ($user) {
        if (isset($user['is_admin']) && $user['is_admin'] == 1) {
            header('Location: ' . BASE_URL . 'admin/index.php');
        } else {
            header('Location: ' . BASE_URL . 'register/index.php');
        }
        exit;
    } else {
        $_SESSION['error'] = 'IDまたはパスワードが正しくありません。';
        $_SESSION['last_login_id'] = $loginId;
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
} catch (Exception $e) {
    error_log("Login Exception: " . $e->getMessage());
    $_SESSION['error'] = 'システムエラーが発生しました。しばらく経ってから再度お試しください。';
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}
