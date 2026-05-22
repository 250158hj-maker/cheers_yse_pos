<?php
/**
 * public/admin/index.php
 * SCR-03 売上管理画面 エントリポイント
 */

require_once __DIR__ . '/../../src/Auth.php';

// 1. 認証ガード: 管理者権限がない場合はログイン画面（またはトップ）へ強制リダイレクト
// ※ Auth::requireAdmin() は内部で Auth::requireLogin() も実行します
Auth::requireAdmin();

/**
 * --- 処理部 (Processing) ---
 */
$pageTitle = '売上管理';
$user = Auth::user();

// ※ ここで売上データの取得ロジックなどを呼び出すことになります。

/**
 * --- 描画部 (Rendering) ---
 */
require_once __DIR__ . '/../../views/admin/sales.php';
