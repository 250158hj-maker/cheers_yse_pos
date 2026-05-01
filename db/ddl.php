<?php

// DB接続設定ファイル（GitHubには上げないファイル）を読み込む想定です
require_once __DIR__ . '/config/database.php';

try {
    // $pdo は config/database.php 内で接続済みの前提です
    
    $sql = "
    -- ユーザーマスタ
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        login_id VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin BOOLEAN DEFAULT FALSE
    ) ENGINE=InnoDB;

    -- カテゴリマスタ
    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL
    ) ENGINE=InnoDB;

    -- 商品マスタ
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price INT NOT NULL,
        is_takeout BOOLEAN DEFAULT FALSE
    ) ENGINE=InnoDB;

    -- 商品×カテゴリ（多対多の紐付け）
    CREATE TABLE IF NOT EXISTS product_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        category_id INT NOT NULL,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    -- 売上ヘッダー（1回の会計データ）
    CREATE TABLE IF NOT EXISTS sales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_id INT NOT NULL,
        receipt_no VARCHAR(50) NOT NULL,
        total_amount INT NOT NULL,
        tax_rate DECIMAL(4, 2) NOT NULL,
        sold_at DATETIME NOT NULL,
        FOREIGN KEY (store_id) REFERENCES users(id)
    ) ENGINE=InnoDB;

    -- 売上明細（会計ごとの購入商品リスト）
    CREATE TABLE IF NOT EXISTS sale_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sale_id INT NOT NULL,
        product_id INT NOT NULL,
        unit_price INT NOT NULL,
        quantity INT NOT NULL,
        FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    ) ENGINE=InnoDB;
    ";

    // データベース（MySQL）に対してSQLを一気に実行します
    $pdo->exec($sql);
    echo "テーブルの作成が完了しました。\n";

} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage() . "\n";
}