<?php
/**
 * src/Auth.php
 * 認証・セッション管理クラス
 */

require_once __DIR__ . '/../config/database.php';

class Auth {
    /**
     * ログイン処理
     * @param string $loginId
     * @param string $password
     * @return array|false 成功時はユーザー情報を、失敗時は false を返す
     */
    public static function login($loginId, $password) {
        try {
            $db = get_db();
            $stmt = $db->prepare("SELECT * FROM users WHERE login_id = ? LIMIT 1");
            $stmt->execute([$loginId]);
            $user = $stmt->fetch();

            // パスワード照合 (password_hash で保存されている前提)
            // もし開発初期で平文パスワードを使用する場合は $password === $user['password'] に調整
            if ($user && password_verify($password, $user['password'])) {
                // セッションに保存するために機密情報を除外
                unset($user['password']);
                
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // セッション変数を空にする
        $_SESSION = [];
        // セッションクッキーも削除
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        // セッションを破棄
        session_destroy();
    }

    /**
     * ログインチェック（ガード）
     * 未ログインの場合はログイン画面へリダイレクト
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: /index.php');
            exit;
        }
    }

    /**
     * 管理者チェック（ガード）
     * 管理者でない場合はトップへリダイレクト
     */
    public static function requireAdmin() {
        self::requireLogin();
        $user = self::user();
        if (!($user['is_admin'] ?? false)) {
            header('Location: /index.php');
            exit;
        }
    }

    /**
     * ログイン中か判定
     */
    public static function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user']);
    }

    /**
     * 現在のログインユーザー情報を取得
     */
    public static function user() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }

    /**
     * CSRFトークンを生成しセッションに保存
     */
    public static function generateCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    /**
     * 送信されたCSRFトークンを検証
     */
    public static function validateCsrfToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $storedToken = $_SESSION['csrf_token'] ?? null;
        if ($token && $storedToken && hash_equals($storedToken, $token)) {
            // 一度使用したトークンは無効化（必要に応じて）
            // unset($_SESSION['csrf_token']); 
            return true;
        }
        return false;
    }
}
