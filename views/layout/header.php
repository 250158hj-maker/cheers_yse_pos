<?php
/**
 * header.php
 * 
 * --- 処理部 (Processing) ---
 */

require_once __DIR__ . '/../../src/Auth.php';

// 1. ページタイトルの設定
$displayTitle = isset($pageTitle) ? htmlspecialchars($pageTitle) . " | Cheers YSE POS" : "Cheers YSE POS";

// 2. セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. ユーザー情報の取得
$user = $_SESSION['user'] ?? null;
$isLoggedIn = !empty($user);
$isAdmin = $user['is_admin'] ?? false;
$userName = $isLoggedIn ? htmlspecialchars($user['name'] ?? 'スタッフ') : '';

// 4. ベースURLの計算 (サブディレクトリ環境対応)
$scriptName = $_SERVER['SCRIPT_NAME'];
$publicPos = strpos($scriptName, '/public/');
if ($publicPos !== false) {
    $baseUrl = substr($scriptName, 0, $publicPos + 8);
} else {
    $baseUrl = '/';
}

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
    <title><?php echo $displayTitle; ?></title>
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --danger-color: #e74c3c;
            --text-color: #333;
            --bg-color: #f4f7f6;
            --header-bg: #ffffff;
        }
        body { margin: 0; font-family: "Helvetica Neue", Arial, sans-serif; background-color: var(--bg-color); color: var(--text-color); line-height: 1.6; }
        .main-header { background-color: var(--header-bg); border-bottom: 1px solid #ddd; padding: 0 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .header-container { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto; height: 60px; }
        .logo a { font-size: 1.5rem; font-weight: bold; color: var(--primary-color); text-decoration: none; }
        .nav-wrapper { display: flex; align-items: center; }
        .main-nav ul { display: flex; list-style: none; margin: 0; padding: 0; }
        .main-nav li { margin-left: 20px; }
        .main-nav a { text-decoration: none; color: var(--text-color); font-size: 0.9rem; transition: color 0.3s; }
        .main-nav a:hover { color: var(--accent-color); }
        .user-info { margin-left: 30px; padding-left: 20px; border-left: 1px solid #eee; display: flex; align-items: center; font-size: 0.9rem; }
        .logout-form { margin-left: 15px; }
        .logout-button { background-color: var(--danger-color); color: white; border: none; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-size: 0.8rem; }
        .logout-button:hover { background-color: #c0392b; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); min-height: 80vh; }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <a href="<?php echo $baseUrl; ?>index.php">Cheers YSE POS</a>
            </div>
            <div class="nav-wrapper">
                <nav class="main-nav">
                    <ul>
                        <?php foreach ($navItems as $item): ?>
                            <li><a href="<?php echo $item['url']; ?>"><?php echo $item['label']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <?php if ($isLoggedIn): ?>
                    <div class="user-info">
                        <span>ログイン中: <strong><?php echo $userName; ?></strong></span>
                        <form action="<?php echo $logoutUrl; ?>" method="POST" class="logout-form">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                            <button type="submit" class="logout-button">ログアウト</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="container">
