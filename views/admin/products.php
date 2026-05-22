<?php include __DIR__ . '/../layout/header.php'; ?>

<style>
    /* 王道スマート・テーマ（Airレジ風）の定義 */
    :root {
        --base-bg: #F4F6F8;
        --content-bg: #FFFFFF;
        --accent-blue: #00A4E5;
        --text-dark: #333333;
        --alert-red: #DC3545;
        --border-color: #E0E0E0;
    }

    body {
        background-color: var(--base-bg);
        color: var(--text-dark);
        font-family: "Helvetica Neue", Arial, sans-serif;
        margin: 0;
    }

    .admin-layout {
        display: flex;
        min-height: 100vh;
    }

    /* 左サイドバー */
    .sidebar {
        width: 220px;
        background-color: var(--content-bg);
        border-right: 1px solid var(--border-color);
        padding: 20px 0;
    }
    .sidebar-menu { list-style: none; padding: 0; margin: 0; }
    .sidebar-item a {
        display: block;
        padding: 15px 25px;
        text-decoration: none;
        color: var(--text-dark);
        font-weight: bold;
    }
    .sidebar-item.active a {
        background-color: var(--base-bg);
        color: var(--accent-blue);
        border-right: 4px solid var(--accent-blue);
    }

    /* メインコンテンツ */
    .main-content { flex: 1; padding: 30px; }
    .page-title { font-size: 1.25rem; margin-bottom: 25px; font-weight: bold; }

    /* パネル */
    .panel {
        background-color: var(--content-bg);
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        padding: 25px;
        margin-bottom: 30px;
    }
    .panel-header {
        font-size: 1rem;
        font-weight: bold;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--base-bg);
    }

    /* フォーム */
    .form-row { display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-end; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-label { font-size: 0.85rem; font-weight: bold; }
    .form-control {
        height: 40px;
        padding: 0 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
    }

    /* ボタン */
    .btn { height: 40px; padding: 0 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
    .btn-primary { background-color: var(--accent-blue); color: white; }
    .btn-outline-danger {
        background-color: transparent;
        border: 1px solid var(--alert-red);
        color: var(--alert-red);
        height: 32px;
        padding: 0 12px;
    }

    /* テーブル */
    .product-table { width: 100%; border-collapse: collapse; }
    .product-table th { background-color: #F8F9FA; text-align: left; padding: 12px 15px; border-bottom: 2px solid var(--base-bg); }
    .product-table td { padding: 15px; border-bottom: 1px solid var(--base-bg); }
    
    .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
    .badge-instore { background-color: #E3F2FD; color: #1976D2; }
    .badge-takeout { background-color: #F3E5F5; color: #7B1FA2; }
</style>

<div class="admin-layout">
    <nav class="sidebar">
        <ul class="sidebar-menu">
            <li class="sidebar-item"><a href="../index.php">売上管理</a></li>
            <li class="sidebar-item active"><a href="index.php">商品設定</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <h1 class="page-title">商品設定</h1>

        <section class="panel">
            <div class="panel-header">【新規商品登録】</div>
            <form action="store.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">商品名</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">価格 (税抜)</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">カテゴリ</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">選択してください</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['id']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">税率/区分設定</label>
                        <div style="height: 40px; display: flex; align-items: center; gap: 15px;">
                            <label><input type="radio" name="is_takeout" value="0" checked> 店内のみ (10%)</label>
                            <label><input type="radio" name="is_takeout" value="1"> TOのみ (8%)</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">+ 登録</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">【登録済み商品一覧】</div>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>商品名</th>
                        <th>価格</th>
                        <th>カテゴリ</th>
                        <th>区分</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['id']) ?></td>
                        <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                        <td>¥<?= htmlspecialchars(number_format($p['price'])) ?></td>
                        <td><?= htmlspecialchars($p['category']) ?></td>
                        <td>
                            <?php if($p['is_takeout']): ?>
                                <span class="badge badge-takeout">TOのみ</span>
                            <?php else: ?>
                                <span class="badge badge-instore">店内のみ</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="delete.php" method="POST" onsubmit="return confirm('削除しますか？');">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>">
                                <button type="submit" class="btn btn-outline-danger">削除</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>