<?php
require_once __DIR__ . '/../src/Database.php';

try {
    $db = new Database();
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        login_id VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        is_admin BOOLEAN DEFAULT FALSE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        price INT NOT NULL,
        category_id INT NOT NULL,
        FOREIGN KEY (category_id) REFERENCES categories(id),
        is_takeout BOOLEAN DEFAULT FALSE
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS sales (
        id INT AUTO_INCREMENT PRIMARY KEY,
        store_id INT NOT NULL,
        receipt_no VARCHAR(50) NOT NULL,
        total_amount INT NOT NULL,
        tax_rate DECIMAL(4, 2) NOT NULL,
        sold_at DATETIME NOT NULL,
        FOREIGN KEY (store_id) REFERENCES users(id)
    ) ENGINE=InnoDB;

    CREATE TABLE IF NOT EXISTS sale_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sale_id INT NOT NULL,
        product_id INT NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        unit_price INT NOT NULL,
        quantity INT NOT NULL,
        FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    ) ENGINE=InnoDB;
    ";

    // スキーマ定義の一括実行。DDL専用メソッドを通すことで接続経路を
    // src/Database.php に一本化する
    $db->runSchema($sql);
    echo "テーブルの作成が完了しました。\n";
} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage() . "\n";
}
