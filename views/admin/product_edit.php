<?php require_once __DIR__ . '/../layout/header.php'; ?>

<div class="container-fluid">
    <div class="row">

        <!-- 右メインエリア  -->
        <main class="col-md-12 p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">商品編集</h2>
                <a href="<?= $baseUrl ?>admin/products/index.php" class="btn btn-outline-secondary">
                    一覧に戻る
                </a>
            </div>

            <!-- 商品編集フォーム -->
            <section class="card">
                <div class="card-header fw-bold">商品情報の変更</div>
                <div class="card-body">
                    <form action="<?= $baseUrl ?>admin/products/update.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">商品名</label>
                                <input type="text" name="name" class="form-control" 
                                    value="<?= htmlspecialchars($product['name']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">価格（円）</label>
                                <input type="number" name="price" class="form-control" 
                                    value="<?= htmlspecialchars($product['price']) ?>" min="0" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">カテゴリ</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">選択してください</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= htmlspecialchars($category['id']) ?>"
                                            <?= $product['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">TO区分</label>
                                <select name="is_takeout" class="form-select">
                                    <option value="0" <?= !$product['is_takeout'] ? 'selected' : '' ?>>店内</option>
                                    <option value="1" <?= $product['is_takeout'] ? 'selected' : '' ?>>テイクアウト</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-5">
                                変更を保存する
                            </button>
                        </div>
                    </form>
                </div>
            </section>

        </main>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
