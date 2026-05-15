<?php
/**
 * public/login.php
 * ログイン処理ハンドラー (POST)
 */

// 必要なクラスの読み込み
// ※実際のファイルパスやクラス名に合わせて適宜調整してください
require_once __DIR__ . '/../src/Auth.php';

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. POSTメソッド以外は受け付けない
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

// 2. 入力値の取得
$loginId   = $_POST['login_id'] ?? '';
$password  = $_POST['password'] ?? '';
$csrfToken = $_POST['csrf_token'] ?? '';

// 3. CSRFトークンの検証
if (!Auth::validateCsrfToken($csrfToken)) {
    $_SESSION['error'] = '不正なリクエストです。もう一度お試しください。';
    header('Location: /index.php');
    exit;
}

// 4. バリデーション
if (empty($loginId) || empty($password)) {
    $_SESSION['error'] = 'IDとパスワードを入力してください。';
    $_SESSION['last_login_id'] = $loginId;
    header('Location: /index.php');
    exit;
}

// 4. 認証処理の実行
try {
    // Authクラスに認証ロジックを委譲する
    // login() メソッドは、認証成功時にユーザー情報を返し、失敗時に false を返す想定
    $user = Auth::login($loginId, $password);

    if ($user) {
        // ログイン成功: ロールに応じて遷移先を振り分け
        if (isset($user['is_admin']) && $user['is_admin']) {
            header('Location: /admin/index.php');
        } else {
            header('Location: /register/index.php');
        }
        exit;
    } else {
        // ログイン失敗
        $_SESSION['error'] = 'IDまたはパスワードが正しくありません。';
        $_SESSION['last_login_id'] = $loginId;
        header('Location: /index.php');
        exit;
    }

} catch (Exception $e) {
    // データベースエラーなどの例外処理
    error_log("Login Error: " . $e->getMessage());
    $_SESSION['error'] = 'システムエラーが発生しました。しばらく経ってから再度お試しください。';
    header('Location: /index.php');
    exit;
}
