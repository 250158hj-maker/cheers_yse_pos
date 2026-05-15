<?php
/**
 * db/seeds.php
 * 初期データ投入スクリプト
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = get_db();

    echo "データ投入を開始します...\n";

    // 1. ユーザーマスタの登録
    $users = [
        [
            'name' => '管理者',
            'login_id' => '0000',
            'password' => password_hash('0000', PASSWORD_DEFAULT),
            'is_admin' => 1
        ],
        [
            'name' => '店舗スタッフ01',
            'login_id' => '1111',
            'password' => password_hash('1111', PASSWORD_DEFAULT),
            'is_admin' => 0
        ]
    ];

    $stmt = $db->prepare("INSERT IGNORE INTO users (name, login_id, password, is_admin) VALUES (?, ?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute([$user['name'], $user['login_id'], $user['password'], $user['is_admin']]);
        echo "ユーザー追加: {$user['name']} (ID: {$user['login_id']})\n";
    }

    // 2. カテゴリマスタの登録
    $categories = ['フード', 'ドリンク', 'デザート'];
    $cat_ids = [];
    $stmt = $db->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
    foreach ($categories as $cat) {
        $stmt->execute([$cat]);
        $cat_ids[$cat] = $db->lastInsertId();
        echo "カテゴリ追加: {$cat}\n";
    }

    // 3. 商品マスタの登録
    $products = [
        ['name' => '生ビール', 'price' => 500, 'category' => 'ドリンク'],
        ['name' => 'レモンサワー', 'price' => 450, 'category' => 'ドリンク'],
        ['name' => '枝豆', 'price' => 300, 'category' => 'フード'],
        ['name' => '唐揚げ', 'price' => 600, 'category' => 'フード'],
        ['name' => 'バニラアイス', 'price' => 350, 'category' => 'デザート'],
    ];

    $stmt_prod = $db->prepare("INSERT IGNORE INTO products (name, price, is_takeout) VALUES (?, ?, ?)");
    $stmt_rel = $db->prepare("INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)");

    foreach ($products as $prod) {
        $stmt_prod->execute([$prod['name'], $prod['price'], 0]);
        $prod_id = $db->lastInsertId();
        
        // カテゴリとの紐付け
        if ($prod_id && isset($cat_ids[$prod['category']])) {
            $stmt_rel->execute([$prod_id, $cat_ids[$prod['category']]]);
        }
        echo "商品追加: {$prod['name']} ({$prod['price']}円)\n";
    }

    echo "データ投入が完了しました。\n";

} catch (PDOException $e) {
    echo "エラーが発生しました: " . $e->getMessage() . "\n";
}
