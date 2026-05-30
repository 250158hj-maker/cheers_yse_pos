<?php
/**
 * views/register.php
 * SCR-02 レジ画面 テンプレート
 */
?>
<?php require __DIR__ . '/layout/header.php'; ?>

<style>
    /* 画面全体を固定してスクロールを防止 */
    body {
        overflow: hidden;
    }
    .register-container {
        height: calc(100vh - 120px); /* ヘッダー・フッター分を引く */
    }
    /* 商品グリッドの高さ固定とスクロール */
    #product-grid-container {
        height: calc(100vh - 220px);
        overflow-y: auto;
    }
    /* 注文明細の高さ固定とスクロール */
    #order-list-container {
        height: calc(100vh - 580px); /* 下部のテンキー・合計エリア分を調整 */
        overflow-y: auto;
        border: 1px solid #dee2e6;
    }
    /* ボタンの余白調整 */
    .btn-py-custom {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
    .card-body {
        padding: 0.75rem;
    }
    .mb-3 {
        margin-bottom: 0.5rem !important;
    }
</style>

<div class="container-fluid mt-2 register-container">
    <div class="row h-100">
        <!-- 左側：商品選択・カテゴリ切替エリア -->
        <div class="col-md-7 d-flex flex-column h-100">
            <div class="card flex-grow-1 mb-0">
                <div class="card-header py-1">
                    <ul class="nav nav-pills card-header-pills" id="category-tabs">
                        <li class="nav-item">
                            <a class="nav-link active py-1" href="#" data-category-id="all">すべて</a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                            <li class="nav-item">
                                <a class="nav-link py-1" href="#" data-category-id="<?= h($category['id']) ?>">
                                    <?= h($category['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="card-body" id="product-grid-container">
                    <div class="row g-2" id="product-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="col-4 product-item" data-category-id="<?= h($product['category_id']) ?>">
                                <button class="btn btn-outline-primary w-100 d-flex flex-column align-items-center justify-content-center btn-py-custom" 
                                        data-product-id="<?= h($product['id']) ?>"
                                        data-product-name="<?= h($product['name']) ?>"
                                        data-product-price="<?= h($product['price']) ?>">
                                    <span class="fw-bold small"><?= h($product['name']) ?></span>
                                    <small class="text-muted">¥<?= number_format($product['price']) ?></small>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- 右側：注文明細・会計エリア -->
        <div class="col-md-5 d-flex flex-column h-100">
            <div class="card flex-grow-1 mb-0">
                <div class="card-body d-flex flex-column p-2">
                    <h6 class="fw-bold mb-1">注文明細</h6>
                    <div id="order-list-container" class="mb-2">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="small">商品名</th>
                                    <th class="small text-end">単価</th>
                                    <th class="small text-center">数</th>
                                    <th class="small text-end">小計</th>
                                </tr>
                            </thead>
                            <tbody id="order-list-body" class="small">
                                <!-- 明細行 -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="bg-dark text-end p-1 mb-2 rounded">
                        <small class="text-secondary d-block" style="font-size: 0.7rem;">入力中</small>
                        <span class="h4 text-white mb-0" id="calc-display">0</span>
                    </div>

                    <div id="numpad" class="mb-2">
                        <div class="row g-1 mb-1">
                            <div class="col-4"><button class="btn btn-danger btn-sm w-100 py-2 calc-btn" data-op="ac">AC</button></div>
                            <div class="col-4"><button class="btn btn-warning btn-sm w-100 py-2 calc-btn" data-op="tax8">8%</button></div>
                            <div class="col-4"><button class="btn btn-warning btn-sm w-100 py-2 calc-btn" data-op="tax10">10%</button></div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-4"><button class="btn btn-secondary btn-sm w-100 py-2 num-btn" data-value="7">7</button></div>
                            <div class="col-4"><button class="btn btn-secondary btn-sm w-100 py-2 num-btn" data-value="8">8</button></div>
                            <div class="col-4"><button class="btn btn-secondary btn-sm w-100 py-2 num-btn" data-value="9">9</button></div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-4"><button class="btn btn-secondary btn-sm w-100 py-2 num-btn" data-value="4">4</button></div>
                            <div class="col-4"><button class="btn btn-secondary btn-sm w-100 py-2 num-btn" data-value="5">5</button></div>
                            <div class="col-4"><button class="btn btn-secondary btn-sm w-100 py-2 num-btn" data-value="6">6</button></div>
                        </div>
                        <div class="row g-1 mb-1">
                            <div class="col-4"><button class="btn btn-secondary btn-sm w-100 py-2 num-btn" data-value="1">1</button></div>
                            <div class="col-4"><button class="btn btn-secondary btn-sm w-100 py-2 num-btn" data-value="2">2</button></div>
                            <div class="col-4"><button class="btn btn-secondary btn-sm w-100 py-2 num-btn" data-value="3">3</button></div>
                        </div>
                        <div class="row g-1 mb-2">
                            <div class="col-4"><button class="btn btn-secondary btn-sm w-100 py-2 num-btn" data-value="0">0</button></div>
                            <div class="col-4"><button class="btn btn-secondary btn-sm w-100 py-2 num-btn" data-value="00">00</button></div>
                            <div class="col-4"><button class="btn btn-danger btn-sm w-100 py-2 calc-btn" data-op="clear">C</button></div>
                        </div>
                    </div>

                    <div class="border-top pt-1 mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <span class="small fw-bold">合計金額</span>
                            <span class="h4 text-primary mb-0" id="total-amount">¥ 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <span class="small">預り金</span>
                            <span class="h6 mb-0" id="received-amount">¥ 0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-danger">お釣り</span>
                            <span class="h5 text-danger mb-0" id="change-amount">¥ 0</span>
                        </div>
                        <form id="checkout-form" action="checkout.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                            <input type="hidden" name="order_data" id="order-data-input">
                            <button type="button" class="btn btn-success btn-lg w-100" id="checkout-btn">計上 (F10)</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 会計完了モーダル -->
<?php if (isset($_SESSION['checkout_success'])): ?>
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-success border-4">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">会計完了</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <div class="mb-3 text-success">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>
                    <p class="h5 mb-2">計上が完了しました。</p>
                    <div class="bg-light p-3 rounded shadow-sm mb-3">
                        <p class="small text-muted mb-1">お釣り</p>
                        <p class="h1 mb-0 text-danger fw-bold">¥ <?= number_format($_SESSION['last_change'] ?? 0) ?></p>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg w-100" data-bs-dismiss="modal">次へ進む (閉じる)</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            // Enterキーでモーダルを閉じる
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    successModal.hide();
                }
            });
        });
    </script>
    <?php 
        unset($_SESSION['checkout_success']);
        unset($_SESSION['last_change']);
    ?>
<?php endif; ?>

<!-- エラー通知モーダル（計上失敗時など） -->
<?php if (isset($_GET['error'])): ?>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger border-4">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">エラー</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="h5 mb-0">計上に失敗しました。</p>
                    <p class="small text-muted mt-2">データを確認して再度お試しください。</p>
                    <button type="button" class="btn btn-secondary mt-3" data-bs-dismiss="modal">閉じる</button>
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
<?php endif; ?>

<!-- レジ画面専用JavaScript -->
<script>
    // PHPの定数をJavaScriptへ渡す
    window.TAX_CONFIG = {
        NORMAL: <?= TAX_RATE_NORMAL ?>,
        REDUCED: <?= TAX_RATE_REDUCED ?>
    };
</script>
<script src="../js/register.js"></script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
