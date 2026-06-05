<?php
/**
 * src/Auth.php
 * 認証・セッション管理クラス
 */

require_once __DIR__ . '/Database.php';

class Auth {
    /**
     * ログイン処理
     */
    public static function login(Database $db, $loginId, $password) {
        try {
            $sql = "SELECT * FROM users WHERE login_id = :login_id";
            $user = $db->fetchOne($sql, ['login_id' => $loginId]);

            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']);
                $_SESSION['user'] = $user;
                return $user;
            }
        } catch (PDOException $e) {
            error_log("Auth::login error: " . $e->getMessage());
            throw $e;
        }

        return false;
    }

    /**
     * ログアウト処理
     */
    public static function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * ログイン状態に応じたトップページへのリダイレクト
     */
    public static function handleLoginRedirect() {
        if (self::isAdmin()) {
            self::redirect('admin/index.php');
        } else {
            self::redirect('register/index.php');
        }
    }

    /**
     * すでにログインしている場合はリダイレクトする
     */
    public static function redirectIfLoggedIn() {
        if (self::isLoggedIn()) {
            self::handleLoginRedirect();
        }
    }

    /**
     * ログインチェック（ガード）
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            self::redirect('index.php');
        }
    }

    /**
     * 管理者チェック（ガード）
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            self::redirect('index.php');
        }
    }

    /**
     * ログイン中か判定
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user']);
    }

    /**
     * 管理者か判定
     */
    public static function isAdmin() {
        $user = self::user();
        return (isset($user['is_admin']) && $user['is_admin']);
    }

    /**
     * 現在のログインユーザー情報を取得
     */
    public static function user() {
        return $_SESSION['user'] ?? null;
    }

    /**
     * CSRFトークンを生成しセッションに保存
     */
    public static function generateCsrfToken() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    /**
     * 送信されたCSRFトークンを検証
     */
    public static function validateCsrfToken($token) {
        $storedToken = $_SESSION['csrf_token'] ?? null;
        if ($token && $storedToken && hash_equals($storedToken, $token)) {
            return true;
        }
        return false;
    }

    /**
     * 内部リダイレクトヘルパー
     */
    public static function redirect(string $path) {
        header('Location: ' . url($path));
        exit;
    }
}
