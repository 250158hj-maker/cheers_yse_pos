<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">

        <!-- 右メインエリア  -->
        <main class="col-md-12 p-4">

            <!-- 新規商品登録フォーム -->
            <section class="card mb-4">
                <div class="card-header fw-bold">新規商品登録</div>
                <div class="card-body">
                    <form action="<?= url('admin/products/store.php') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="row g-3 align-items-end">

                            <div class="col-md-3">
                                <label class="form-label">商品名</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">価格（円）</label>
                                <input type="number" name="price" class="form-control" min="0" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">カテゴリ</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">選択してください</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= h($category['id']) ?>">
                                            <?= h($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">TO区分</label>
                                <select name="is_takeout" class="form-select">
                                    <option value="0">店内</option>
                                    <option value="1">テイクアウト</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    商品を登録する
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </section>

            <!-- 登録済み商品一覧 -->
            <section class="card">
                <div class="card-header fw-bold">登録済み商品一覧</div>
                <div class="card-body">

                    <!-- 絞り込みフォーム -->
                    <form method="get" action="<?= url('admin/products/index.php') ?>" class="row g-2 mb-3">
                        <div class="col-md-3">
                            <select name="category_id" class="form-select">
                                <option value="">すべてのカテゴリ</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= h($category['id']) ?>"
                                        <?= (isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                        <?= h($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="keyword" class="form-control"
                                placeholder="商品名を入力..."
                                value="<?= h($_GET['keyword'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">検索</button>
                        </div>
                    </form>

                    <!-- 商品テーブル -->
                    <?php if (empty($products)): ?>
                        <p class="text-muted">該当する商品がありません。</p>
                    <?php else: ?>
                        <table class="table table-striped table-hover">
                            <thead class="table-secondary">
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
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= h($product['id']) ?></td>
                                        <td><?= h($product['name']) ?></td>
                                        <td>¥<?= number_format($product['price']) ?></td>
                                        <td><?= h($product['category_name']) ?></td>
                                        <td><?= $product['is_takeout'] ? 'テイクアウト' : '店内' ?></td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="<?= url('admin/products/edit.php?id=' . $product['id']) ?>" 
                                                   class="btn btn-outline-primary btn-sm">編集</a>
                                                <form action="<?= url('admin/products/delete.php') ?>" method="post"
                                                    onsubmit="return confirm('本当に削除しますか？')">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= h($product['id']) ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">削除</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                </div>
            </section>

        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

<!-- エラー通知モーダル -->
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">エラー</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="mb-0"><?= h($_SESSION['error_message']) ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
