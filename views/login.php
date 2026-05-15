<?php
/**
 * views/login.php
 * 
 * --- 処理部 (Processing) ---
 */

// 1. ページメタ情報の設定
$pageTitle = 'ログイン';

// 2. エラーメッセージの取得
// ログイン処理（public/login.php）からリダイレクト時にセッションやクエリパラメータで渡される想定
$errorMessage = $_SESSION['error'] ?? null;
unset($_SESSION['error']); // 一度表示したら消去

// 3. 入力値の保持（エラー時に再入力させないため）
$lastLoginId = $_SESSION['last_login_id'] ?? '';
unset($_SESSION['last_login_id']);
?>

<!-- --- 描画部 (Rendering) --- -->
<?php include 'layout/header.php'; ?>

<div class="login-page">
    <div class="login-card">
        <h2 class="login-title">ログイン</h2>
        
        <?php if ($errorMessage): ?>
            <div class="error-alert">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <form action="/login.php" method="POST" class="login-form">
            <div class="form-group">
                <label for="login_id">店舗ID または 管理者ID</label>
                <input type="text" id="login_id" name="login_id" 
                       value="<?php echo htmlspecialchars($lastLoginId); ?>" 
                       required autofocus placeholder="IDを入力してください">
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" 
                       required placeholder="パスワードを入力してください">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-login">ログイン</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* ログイン画面固有のスタイル */
    .login-page {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 60vh;
    }
    .login-card {
        width: 100%;
        max-width: 400px;
        padding: 30px;
        border: 1px solid #eee;
        border-radius: 8px;
        background: #fff;
    }
    .login-title {
        text-align: center;
        margin-bottom: 30px;
        color: var(--primary-color);
    }
    .error-alert {
        background-color: #fce4e4;
        border: 1px solid #fcc2c2;
        color: #cc0000;
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 0.9rem;
    }
    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .btn-login {
        width: 100%;
        padding: 14px;
        background-color: var(--accent-color);
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: opacity 0.3s;
    }
    .btn-login:hover {
        opacity: 0.9;
    }
</style>

<?php include 'layout/footer.php'; ?>
