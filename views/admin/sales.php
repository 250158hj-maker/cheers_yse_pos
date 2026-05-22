<?php
/**
 * views/admin/sales.php
 * 売上管理画面（一覧・フィルタリング）
 */

// 変数の未定義エラー防止用の初期化
$summary = $summary ?? ['total_amount' => 0, 'count' => 0];
$sales = $sales ?? [];
$filters = $filters ?? ['store_id' => '', 'category_id' => '', 'date_from' => '', 'date_to' => '', 'receipt_no' => ''];
$stores = $stores ?? [];
$categories = $categories ?? [];
$pageTitle = $pageTitle ?? '売上管理';

require_once __DIR__ . '/../layout/header.php';
?>

<div class="py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h1 class="h2 mb-0"><?php echo htmlspecialchars($pageTitle); ?></h1>
    </div>

    <!-- 検索・フィルタエリア -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">検索フィルター</h5>
        </div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="store_id" class="form-label small fw-bold">店舗</label>
                        <select name="store_id" id="store_id" class="form-select">
                            <option value="">すべての店舗</option>
                            <?php foreach ($stores as $s): ?>
                                <option value="<?php echo $s['id']; ?>" <?php echo $filters['store_id'] == $s['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="category_id" class="form-label small fw-bold">カテゴリ</label>
                        <select name="category_id" id="category_id" class="form-select">
                            <option value="">すべてのカテゴリ</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['id']; ?>" <?php echo $filters['category_id'] == $c['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="receipt_no" class="form-label small fw-bold">会計番号</label>
                        <input type="text" name="receipt_no" id="receipt_no" class="form-control" value="<?php echo htmlspecialchars($filters['receipt_no']); ?>" placeholder="REC-0000">
                    </div>

                    <div class="col-md-4">
                        <label for="date_from" class="form-label small fw-bold">期間（開始）</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo htmlspecialchars($filters['date_from']); ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="date_to" class="form-label small fw-bold">期間（終了）</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo htmlspecialchars($filters['date_to']); ?>">
                    </div>

                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100">絞り込む</button>
                        <a href="index.php" class="btn btn-outline-secondary w-100">クリア</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 集計サマリー -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">合計売上額</h6>
                    <h2 class="card-title mb-0">¥<?php echo number_format($summary['total_amount']); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 opacity-75">合計会計件数</h6>
                    <h2 class="card-title mb-0"><?php echo number_format($summary['count']); ?> <small>件</small></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- 売上一覧テーブル -->
    <div class="table-responsive">
        <table class="table table-hover align-middle border">
            <thead class="table-light">
                <tr>
                    <th>会計日時</th>
                    <th>店舗</th>
                    <th>会計番号</th>
                    <th class="text-end">合計金額</th>
                    <th class="text-center">税率</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sales)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">該当する売上データが見つかりません。</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('Y/m/d H:i', strtotime($sale['sold_at']))); ?></td>
                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($sale['store_name']); ?></span></td>
                            <td><code class="text-dark bg-light px-2 py-1 rounded"><?php echo htmlspecialchars($sale['receipt_no']); ?></code></td>
                            <td class="text-end fw-bold">¥<?php echo number_format($sale['total_amount']); ?></td>
                            <td class="text-center"><?php echo (float)$sale['tax_rate'] * 100; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>