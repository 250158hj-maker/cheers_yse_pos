<?php
/**
 * public/register/index.php
 * SCR-02 レジ画面 エントリポイント
 */

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Product.php';

// 1. 認証ガード: ログインしていない場合はログイン画面へ強制リダイレクト
Auth::requireLogin();

/**
 * --- 処理部 (Processing) ---
 */
$pageTitle = 'レジ操作';
$user = Auth::user();

// 商品データ・カテゴリデータの取得
$productModel = new Product();
$categories = $productModel->getAllCategories();
$products = $productModel->getAll();

/**
 * --- 描画部 (Rendering) ---
 */
require_once __DIR__ . '/../../views/register.php';
