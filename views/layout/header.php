<?php
/**
 * header.php
 * 
 * --- 処理部 (Processing) ---
 */

require_once __DIR__ . '/../../src/Auth.php';

// 1. ページタイトルの設定
$displayTitle = isset($pageTitle) ? h($pageTitle) . " | Cheers YSE POS" : "Cheers YSE POS";

// 3. ユーザー情報の取得
$user = $_SESSION['user'] ?? null;
$isLoggedIn = !empty($user);
$isAdmin = $user['is_admin'] ?? false;
$userName = $isLoggedIn ? h($user['name'] ?? 'スタッフ') : '';

// 4. ベースURLの取得
$baseUrl = BASE_URL;

// 5. ナビゲーションメニューの生成
$navItems = [];
if ($isLoggedIn) {
    if ($isAdmin) {
        $navItems[] = ['label' => '売上管理', 'url' => $baseUrl . 'admin/index.php'];
        $navItems[] = ['label' => '商品設定', 'url' => $baseUrl . 'admin/products/index.php'];
    } else {
        $navItems[] = ['label' => 'レジ操作', 'url' => $baseUrl . 'register/index.php'];
    }
} else {
    $navItems[] = ['label' => 'ログイン', 'url' => $baseUrl . 'index.php'];
}

$logoutUrl = $baseUrl . 'logout.php';
$csrfToken = Auth::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $displayTitle ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container-main { min-height: 80vh; padding-top: 2rem; padding-bottom: 2rem; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= $baseUrl ?>index.php">Cheers YSE POS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php foreach ($navItems as $item): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $item['url'] ?>"><?= h($item['label']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($isLoggedIn): ?>
                    <div class="d-flex align-items-center">
                        <span class="navbar-text me-3 text-light">
                            ログイン中: <strong><?= $userName ?></strong>
                        </span>
                        <form action="<?= $logoutUrl ?>" method="POST" class="m-0">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger btn-sm">ログアウト</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="container container-main bg-white shadow-sm rounded">
