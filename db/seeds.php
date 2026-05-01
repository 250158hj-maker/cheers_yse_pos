<?php
/**
 * db/seeds.php
 * カフェ（喫茶店）を想定したダミーデータ投入スクリプト
 * 他のファイルを変更せずに単体で動作するように構築
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// .envファイルを読み込む
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// データベース接続設定（.envから取得）
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'cheers_yse_pos';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected to the database successfully.\n";

    // 1. テーブルの作成（存在しない場合）
    echo "Creating tables if not exists...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    $tables = [
        "CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `login_id` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `is_admin` BOOLEAN DEFAULT FALSE,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;",

        "CREATE TABLE IF NOT EXISTS `products` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `price` INT NOT NULL,
            `is_takeout` BOOLEAN DEFAULT FALSE,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;",

        "CREATE TABLE IF NOT EXISTS `categories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL UNIQUE,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;",

        "CREATE TABLE IF NOT EXISTS `product_categories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `product_id` INT NOT NULL,
            `category_id` INT NOT NULL
        ) ENGINE=InnoDB;",

        "CREATE TABLE IF NOT EXISTS `sales` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `store_id` INT NOT NULL,
            `receipt_no` VARCHAR(50),
            `total_amount` INT NOT NULL,
            `tax_rate` DECIMAL(4, 2) NOT NULL,
            `sold_at` DATETIME NOT NULL
        ) ENGINE=InnoDB;",

        "CREATE TABLE IF NOT EXISTS `sale_items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `sale_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `unit_price` INT NOT NULL,
            `quantity` INT NOT NULL
        ) ENGINE=InnoDB;"
    ];

    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 2. データの全削除（リセットしたい場合のみ。ここでは重複を避けるために一度クリア）
    echo "Clearing existing data...\n";
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE sale_items;");
    $pdo->exec("TRUNCATE TABLE sales;");
    $pdo->exec("TRUNCATE TABLE product_categories;");
    $pdo->exec("TRUNCATE TABLE categories;");
    $pdo->exec("TRUNCATE TABLE products;");
    $pdo->exec("TRUNCATE TABLE users;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // 3. ユーザーデータの投入
    echo "Seeding users...\n";
    $userStmt = $pdo->prepare("INSERT INTO users (name, login_id, password, is_admin) VALUES (?, ?, ?, ?)");
    $userStmt->execute(['管理者太郎', '0000', password_hash('0000', PASSWORD_DEFAULT), 1]);
    $userStmt->execute(['カフェ1号店', '1111', password_hash('1111', PASSWORD_DEFAULT), 0]);
    $storeId = $pdo->lastInsertId(); // 最後に登録した店舗ID

    // 4. カテゴリデータの投入
    echo "Seeding categories...\n";
    $catStmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $categoryNames = ['コーヒー', 'ティー', 'デザート', '軽食'];
    $categoryIds = [];
    foreach ($categoryNames as $name) {
        $catStmt->execute([$name]);
        $categoryIds[$name] = $pdo->lastInsertId();
    }

    // 5. 商品データの投入
    echo "Seeding products...\n";
    $products = [
        ['name' => 'ブレンドコーヒー', 'price' => 450, 'cat' => 'コーヒー'],
        ['name' => 'アイスコーヒー', 'price' => 480, 'cat' => 'コーヒー'],
        ['name' => 'カフェラテ', 'price' => 550, 'cat' => 'コーヒー'],
        ['name' => 'ダージリンティー', 'price' => 500, 'cat' => 'ティー'],
        ['name' => 'ベイクドチーズケーキ', 'price' => 600, 'cat' => 'デザート'],
        ['name' => 'ガトーショコラ', 'price' => 620, 'cat' => 'デザート'],
        ['name' => 'ミックスサンド', 'price' => 750, 'cat' => '軽食'],
    ];

    $prodStmt = $pdo->prepare("INSERT INTO products (name, price, is_takeout) VALUES (?, ?, ?)");
    $linkStmt = $pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");

    $sampleProductIds = [];
    foreach ($products as $p) {
        $prodStmt->execute([$p['name'], $p['price'], 1]);
        $prodId = $pdo->lastInsertId();
        $linkStmt->execute([$prodId, $categoryIds[$p['cat']]]);
        $sampleProductIds[] = ['id' => $prodId, 'price' => $p['price']];
    }

    // 6. 売上データの投入（サンプル）
    echo "Seeding sales samples...\n";
    $saleStmt = $pdo->prepare("INSERT INTO sales (store_id, receipt_no, total_amount, tax_rate, sold_at) VALUES (?, ?, ?, ?, ?)");
    $itemStmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, unit_price, quantity) VALUES (?, ?, ?, ?)");

    // 会計1: コーヒー2つ
    $total1 = 450 * 2;
    $saleStmt->execute([$storeId, 'REC-0001', $total1, 0.10, date('Y-m-d H:i:s', strtotime('-1 hour'))]);
    $saleId1 = $pdo->lastInsertId();
    $itemStmt->execute([$saleId1, $sampleProductIds[0]['id'], 450, 2]);

    // 会計2: ケーキ1つ + ティー1つ
    $total2 = 600 + 500;
    $saleStmt->execute([$storeId, 'REC-0002', $total2, 0.10, date('Y-m-d H:i:s')]);
    $saleId2 = $pdo->lastInsertId();
    $itemStmt->execute([$saleId2, $sampleProductIds[4]['id'], 600, 1]);
    $itemStmt->execute([$saleId2, $sampleProductIds[3]['id'], 500, 1]);

    echo "Seeding completed successfully!\n";

} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
