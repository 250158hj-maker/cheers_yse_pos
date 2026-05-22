<?php
// セッション開始と認証チェック
session_start();

// 本来はここでDB接続とデータ取得を行う
// require_once __DIR__ . '/../../../src/Database.php';
// $db = new Database();

// 11～22行目をSQLに置き換える。

// --- データの準備（ビューに渡すための変数） ---
$categories = [
    ['id' => 1, 'name' => 'フード'],
    ['id' => 2, 'name' => 'ドリンク'],
    ['id' => 3, 'name' => 'デザート']
];

$products = [
    ['id' => 1, 'name' => 'コーヒー', 'price' => 400, 'category' => 'ドリンク', 'is_takeout' => 0],
    ['id' => 2, 'name' => 'カレー', 'price' => 800, 'category' => 'フード', 'is_takeout' => 0],
    ['id' => 3, 'name' => 'サンドイッチ', 'price' => 500, 'category' => 'フード', 'is_takeout' => 1],
];

// --- ビュー（画面）の呼び出し ---
// public/admin/products から見て、views/admin/products/index.php を読み込む
// ここで include することで、上記で定義した $categories や $products がビュー側で使えるようになります
require_once __DIR__ . '/../../../views/admin/products.php';