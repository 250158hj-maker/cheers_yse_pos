<?php
/**
 * public/admin/index.php
 * SCR-03 売上管理画面 エントリポイント
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Sale.php';
require_once __DIR__ . '/../../src/Database.php';

// 1. 認証ガード: 管理者権限がない場合はログイン画面へ強制リダイレクト
Auth::requireAdmin();

/**
 * --- 処理部 (Processing) ---
 */
$pageTitle = '売上管理';
$user = Auth::user();

$saleModel = new Sale();
$db = new Database();

// 1. フィルタ条件の取得（GETパラメータから）
$filters = [
    'store_id'    => $_GET['store_id'] ?? '',
    'category_id' => $_GET['category_id'] ?? '',
    'date_from'   => $_GET['date_from'] ?? '',
    'date_to'     => $_GET['date_to'] ?? '',
    'receipt_no'  => $_GET['receipt_no'] ?? '',
];

// 2. 売上データと集計結果の取得
$sales = $saleModel->findFiltered($filters);
$summary = $saleModel->getSummary($filters);

// 3. フィルタフォーム用のマスタデータ取得
// ※ 本来は Store モデルや Category モデルを作るのが理想ですが、
//    今回はシンプルに Database クラスを直接使用して取得します。
$stores = $db->fetchAll("SELECT id, name FROM users WHERE is_admin = 0 ORDER BY name ASC");
$categories = $db->fetchAll("SELECT id, name FROM categories ORDER BY id ASC");

/**
 * --- 描画部 (Rendering) ---
 */
require_once __DIR__ . '/../../views/admin/sales.php';
