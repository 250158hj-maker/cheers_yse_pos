<?php

/**
 * db/seeds.php
 * カフェ（喫茶店）を想定したダミーデータ投入スクリプト
 * 他のファイルを変更せずに単体で動作するように構築
 */
require_once __DIR__ . '/../src/Database.php';


try {
    $db = new Database();

    // FK依存で連鎖するため全件を1トランザクションに束ねる。
    // 途中失敗で中途半端に投入されたDBが残ると再実行時に重複・FK不整合を招くため、
    // 失敗時は rollBack で「何も投入していない状態」へ戻す
    $db->beginTransaction();

    // 再実行を可能にするため既存データを全削除してから入れ直す。
    // FK制約に違反しないよう子テーブルから親テーブルの順で消す。
    // 削除〜再投入を同一トランザクションに含めることで、失敗時は
    // rollBack で元データごと保全される
    $db->execute("DELETE FROM sale_items");
    $db->execute("DELETE FROM sales");
    $db->execute("DELETE FROM products");
    $db->execute("DELETE FROM categories");
    $db->execute("DELETE FROM users");

    // ユーザーデータの投入
    $userSql = "INSERT INTO users (name, login_id, password, is_admin) VALUES (?, ?, ?, ?)";
    $db->execute($userSql, ['管理者太郎', '0000', password_hash('0000', PASSWORD_DEFAULT), 1]);
    $db->execute($userSql, ['カフェ1号店', '1111', password_hash('1111', PASSWORD_DEFAULT), 0]);
    $storeId = $db->lastInsertId(); // 最後に登録した店舗ID

    // カテゴリデータの投入
    $categoryNames = ['コーヒー', 'ティー', 'デザート', '軽食'];
    $categoryIds = [];
    foreach ($categoryNames as $name) {
        $db->execute("INSERT INTO categories (name) VALUES (?)", [$name]);
        $categoryIds[$name] = $db->lastInsertId();
    }

    // 商品データの投入
    $products = [
        ['name' => 'ブレンドコーヒー', 'price' => 450, 'cat' => 'コーヒー'],
        ['name' => 'アイスコーヒー', 'price' => 480, 'cat' => 'コーヒー'],
        ['name' => 'カフェラテ', 'price' => 550, 'cat' => 'コーヒー'],
        ['name' => 'ダージリンティー', 'price' => 500, 'cat' => 'ティー'],
        ['name' => 'ベイクドチーズケーキ', 'price' => 600, 'cat' => 'デザート'],
        ['name' => 'ガトーショコラ', 'price' => 620, 'cat' => 'デザート'],
        ['name' => 'ミックスサンド', 'price' => 750, 'cat' => '軽食'],
    ];

    $sampleProductIds = [];
    foreach ($products as $p) {
        $db->execute("INSERT INTO products (name, price, category_id, is_takeout) VALUES (?, ?, ?, ?)", [$p['name'], $p['price'], $categoryIds[$p['cat']], 1]);
        $prodId = $db->lastInsertId();
        $sampleProductIds[] = ['id' => $prodId, 'price' => $p['price']];
    }

    // 売上データの投入（サンプル）
    $saleSql = "INSERT INTO sales (store_id, receipt_no, total_amount, tax_rate, sold_at) VALUES (?, ?, ?, ?, ?)";
    $itemSql = "INSERT INTO sale_items (sale_id, product_id, product_name, unit_price, quantity) VALUES (?, ?, ?, ?, ?)";

    // 会計1: コーヒー2つ
    $total1 = 450 * 2;
    $db->execute($saleSql, [$storeId, 'REC-0001', $total1, TAX_RATE_NORMAL, date('Y-m-d H:i:s', strtotime('-1 hour'))]);
    $saleId1 = $db->lastInsertId();
    $db->execute($itemSql, [$saleId1, $sampleProductIds[0]['id'],'ブレンドコーヒー', 450, 2]);

    // 会計2: ケーキ1つ + ティー1つ
    $total2 = 600 + 500;
    $db->execute($saleSql, [$storeId, 'REC-0002', $total2, TAX_RATE_NORMAL, date('Y-m-d H:i:s')]);
    $saleId2 = $db->lastInsertId();
    $db->execute($itemSql, [$saleId2, $sampleProductIds[4]['id'], 'ベイクドチーズケーキ', 600, 1]);
    $db->execute($itemSql, [$saleId2, $sampleProductIds[3]['id'], 'ダージリンティー', 500, 1]);

    $db->commit();
    echo "Seeding completed successfully!\n";
} catch (PDOException $e) {
    $db->rollBack();
    echo "Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    $db->rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
