<?php
/**
 * データベース初期化スクリプト
 * 設計書 v1.0 に基づく
 */

// --- 接続設定（環境に合わせて変更してください） ---
$host = 'localhost';
$dbname = 'cheers_yse_pos';
$user = 'root';
$pass = ''; // Laragon等のデフォルトは空
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 1. データベースの作成
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname` text");

    echo "データベース '$dbname' を選択しました。\n";

    // 2. テーブル作成SQL
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
        price INT NOT NULL, -- 円単位の整数
        is_takeout BOOLEAN DEFAULT FALSE -- TINYINT(1)として処理
    ) ENGINE=InnoDB;

    -- 商品×カテゴリ（多対多）
    CREATE TABLE IF NOT EXISTS product_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        category_id INT NOT NULL,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;

    -- 売上ヘッダー
    CREATE TABLE IF NOT EXISTS sales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_id INT NOT NULL, -- users.id を想定
        receipt_no VARCHAR(50) NOT NULL, -- 文字列形式に対応
        total_amount INT NOT NULL,
        tax_rate DECIMAL(4, 2) NOT NULL, -- 8%・10%を正確に保持
        sold_at DATETIME NOT NULL,
        FOREIGN KEY (store_id) REFERENCES users(id)
    ) ENGINE=InnoDB;

    -- 売上明細
    CREATE TABLE IF NOT EXISTS sale_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sale_id INT NOT NULL,
        product_id INT NOT NULL,
        unit_price INT NOT NULL, -- 売上時点の単価を記録
        quantity INT NOT NULL,
        FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    ) ENGINE=InnoDB;
    ";

    $pdo->exec($sql);
    echo "全てのテーブルが正常に作成されました。";

} catch (PDOException $e) {
    echo "エラーが発生しました: " . $e->getMessage();
}