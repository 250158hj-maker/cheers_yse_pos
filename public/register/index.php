<?php
/**
 * public/register/index.php
 * SCR-02 レジ画面 エントリポイント
 */

require_once __DIR__ . '/../../src/Auth.php';

// 1. 認証ガード: ログインしていない場合はログイン画面へ強制リダイレクト
Auth::requireLogin();

/**
 * --- 処理部 (Processing) ---
 */
$pageTitle = 'レジ操作';
$user = Auth::user();

// ※ ここで商品の取得ロジックなどを呼び出すことになります。

/**
 * --- 描画部 (Rendering) ---
 */
require_once __DIR__ . '/../../views/register.php';
