<?php
/**
 * header.php
 * 
 * --- 処理部 (Processing) ---
 * 描画に必要なロジックや変数の準備を行います。
 */

// ページタイトルの設定（各ページで $pageTitle が定義されている想定）
$displayTitle = isset($pageTitle) ? htmlspecialchars($pageTitle) . " | Cheers YSE POS" : "Cheers YSE POS";

// セッションが開始されていない場合は開始（共通ヘッダーで行うのが一般的）
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ユーザー情報の取得（仮の実装：Authクラスの実装に合わせて調整が必要）
$user = $_SESSION['user'] ?? null;
$isLoggedIn = !empty($user);
$isAdmin = $user['is_admin'] ?? false;

// ナビゲーションメニューの動的生成
$navItems = [];

if ($isLoggedIn) {
    if ($isAdmin) {
        // 管理者用メニュー
        $navItems[] = ['label' => '売上管理', 'url' => '/admin/index.php'];
        $navItems[] = ['label' => '商品設定', 'url' => '/admin/products/index.php'];
    } else {
        // スタッフ用メニュー
        $navItems[] = ['label' => 'レジ操作', 'url' => '/register/index.php'];
    }
    // ユーザー名表示とログアウト
    $userName = htmlspecialchars($user['name'] ?? 'スタッフ');
    $navItems[] = ['label' => "ログアウト ({$userName})", 'url' => '/logout.php', 'method' => 'POST'];
} else {
    $navItems[] = ['label' => 'ログイン', 'url' => '/index.php'];
}

?>
<!-- 
  --- 描画部 (Rendering) ---
  HTMLの出力を行います。
-->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $displayTitle; ?></title>
    
    <!-- 共通スタイル（将来的に assets/css/style.css を作成することを想定） -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --text-color: #333;
            --bg-color: #f4f7f6;
            --header-bg: #ffffff;
        }
        body {
            margin: 0;
            font-family: "Helvetica Neue", Arial, "Hiragino Kaku Gothic ProN", "Hiragino Sans", Meiryo, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
        }
        .main-header {
            background-color: var(--header-bg);
            border-bottom: 1px solid #ddd;
            padding: 0 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            height: 60px;
        }
        .logo a {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            text-decoration: none;
        }
        .main-nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .main-nav li {
            margin-left: 20px;
        }
        .main-nav a, .nav-button-link {
            text-decoration: none;
            color: var(--text-color);
            font-size: 0.9rem;
            transition: color 0.3s;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            font-family: inherit;
        }
        .main-nav a:hover, .nav-button-link:hover {
            color: var(--accent-color);
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            min-height: 80vh;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <a href="/">Cheers YSE POS</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <?php foreach ($navItems as $item): ?>
                        <li>
                            <?php if (isset($item['method']) && $item['method'] === 'POST'): ?>
                                <form action="<?php echo $item['url']; ?>" method="POST" style="display:inline;">
                                    <button type="submit" class="nav-button-link"><?php echo $item['label']; ?></button>
                                </form>
                            <?php else: ?>
                                <a href="<?php echo $item['url']; ?>"><?php echo $item['label']; ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
