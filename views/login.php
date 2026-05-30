<?php
/**
 * views/login.php
 * 
 * --- 処理部 (Processing) ---
 */

// Authクラスを読み込む
require_once __DIR__ . '/../src/Auth.php';

// 1. ページメタ情報の設定
$pageTitle = 'ログイン';

// 2. エラーメッセージの取得
// ログイン処理（public/login.php）からリダイレクト時にセッションやクエリパラメータで渡される想定
$errorMessage = $_SESSION['error'] ?? null;
unset($_SESSION['error']); // 一度表示したら消去

// 3. 入力値の保持（エラー時に再入力させないため）
$lastLoginId = $_SESSION['last_login_id'] ?? '';
unset($_SESSION['last_login_id']);

// 4. CSRFトークンの生成
$csrfToken = Auth::generateCsrfToken();
?>

<!-- --- 描画部 (Rendering) --- -->
<?php include __DIR__ . '/layout/header.php'; ?>

<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center py-4">
                <h3 class="mb-0">Cheers YSE POS</h3>
                <p class="mb-0 small opacity-75">システムの利用にはログインが必要です</p>
            </div>
            <div class="card-body p-5">
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?= h($errorMessage) ?></div>
                    </div>
                <?php endif; ?>

                <form action="./login.php" method="POST">
                    <!-- CSRFトークン -->
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                    
                    <div class="mb-4">
                        <label for="login_id" class="form-label fw-bold">店舗ID または 管理者ID</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                            <input type="text" id="login_id" name="login_id" 
                                   class="form-control form-control-lg"
                                   value="<?= h($lastLoginId) ?>" 
                                   inputmode="numeric" pattern="\d{4}" maxlength="4"
                                   required autofocus placeholder="数字4桁">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">パスワード</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                            <input type="password" id="password" name="password" 
                                   class="form-control form-control-lg"
                                   inputmode="numeric" pattern="\d{4}" maxlength="4"
                                   required placeholder="数字4桁">
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-5">
                        <button type="submit" class="btn btn-primary btn-lg text-white fw-bold">ログイン</button>
                    </div>
                </form>
            </div>
            <div class="card-footer bg-light text-center py-3">
                <small class="text-muted">パスワードを忘れた場合は管理者へお問い合わせください</small>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<?php include __DIR__ . '/layout/footer.php'; ?>
