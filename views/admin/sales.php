<?php
/**
 * views/admin/sales.php
 * 売上管理画面（一覧・フィルタリング）
 */

require_once __DIR__ . '/../layout/header.php';
?>

<div class="sales-page">
    <div class="page-header">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
    </div>

    <!-- 検索・フィルタエリア -->
    <section class="filter-section">
        <form action="" method="GET" class="filter-form">
            <div class="filter-grid">
                <div class="filter-item">
                    <label for="store_id">店舗</label>
                    <select name="store_id" id="store_id">
                        <option value="">すべての店舗</option>
                        <?php foreach ($stores as $s): ?>
                            <option value="<?php echo $s['id']; ?>" <?php echo $filters['store_id'] == $s['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-item">
                    <label for="category_id">カテゴリ</label>
                    <select name="category_id" id="category_id">
                        <option value="">すべてのカテゴリ</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo $filters['category_id'] == $c['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-item">
                    <label for="date_from">期間（開始）</label>
                    <input type="date" name="date_from" id="date_from" value="<?php echo htmlspecialchars($filters['date_from']); ?>">
                </div>

                <div class="filter-item">
                    <label for="date_to">期間（終了）</label>
                    <input type="date" name="date_to" id="date_to" value="<?php echo htmlspecialchars($filters['date_to']); ?>">
                </div>

                <div class="filter-item">
                    <label for="receipt_no">会計番号</label>
                    <input type="text" name="receipt_no" id="receipt_no" value="<?php echo htmlspecialchars($filters['receipt_no']); ?>" placeholder="REC-0000">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-search">絞り込む</button>
                <a href="index.php" class="btn-clear">クリア</a>
            </div>
        </form>
    </section>

    <!-- 集計サマリー -->
    <section class="summary-section">
        <div class="summary-card">
            <div class="summary-item">
                <span class="summary-label">合計金額</span>
                <span class="summary-value">¥<?php echo number_format($summary['total_amount']); ?></span>
            </div>
            <div class="summary-item">
                <span class="summary-label">合計件数</span>
                <span class="summary-value"><?php echo number_format($summary['count']); ?> 件</span>
            </div>
        </div>
    </section>

    <!-- 売上一覧テーブル -->
    <section class="list-section">
        <table class="sales-table">
            <thead>
                <tr>
                    <th>会計日時</th>
                    <th>店舗</th>
                    <th>会計番号</th>
                    <th>合計金額</th>
                    <th>税率</th>
                    <!-- 今後、詳細表示ボタンなどを追加可能 -->
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sales)): ?>
                    <tr>
                        <td colspan="5" class="no-data">該当する売上データが見つかりません。</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('Y/m/d H:i', strtotime($sale['sold_at']))); ?></td>
                            <td><?php echo htmlspecialchars($sale['store_name']); ?></td>
                            <td><code><?php echo htmlspecialchars($sale['receipt_no']); ?></code></td>
                            <td class="amount">¥<?php echo number_format($sale['total_amount']); ?></td>
                            <td><?php echo (float)$sale['tax_rate'] * 100; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</div>

<style>
    .sales-page { padding: 10px; }
    .page-header h1 { margin-bottom: 25px; font-size: 1.8rem; color: var(--primary-color); border-left: 5px solid var(--accent-color); padding-left: 15px; }

    /* フィルタセクション */
    .filter-section { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #eee; }
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
    .filter-item label { display: block; font-size: 0.85rem; font-weight: bold; margin-bottom: 5px; color: #666; }
    .filter-item select, .filter-item input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    .filter-actions { display: flex; gap: 10px; justify-content: flex-end; }
    .btn-search { background: var(--accent-color); color: white; border: none; padding: 10px 25px; border-radius: 4px; cursor: pointer; font-weight: bold; }
    .btn-clear { background: #eee; color: #333; text-decoration: none; padding: 10px 25px; border-radius: 4px; font-size: 0.9rem; display: inline-block; }
    .btn-search:hover { opacity: 0.9; }
    .btn-clear:hover { background: #ddd; }

    /* サマリーセクション */
    .summary-section { margin-bottom: 30px; }
    .summary-card { display: flex; gap: 30px; background: var(--primary-color); color: white; padding: 20px 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .summary-item { display: flex; flex-direction: column; }
    .summary-label { font-size: 0.9rem; opacity: 0.8; margin-bottom: 5px; }
    .summary-value { font-size: 1.8rem; font-weight: bold; }

    /* テーブルセクション */
    .sales-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .sales-table th, .sales-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
    .sales-table th { background: #f4f7f6; color: #555; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .sales-table tr:hover { background-color: #fafafa; }
    .sales-table .amount { font-weight: bold; color: var(--primary-color); }
    .sales-table .no-data { text-align: center; padding: 40px; color: #999; }
    code { background: #eee; padding: 2px 5px; border-radius: 3px; font-family: monospace; }
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
