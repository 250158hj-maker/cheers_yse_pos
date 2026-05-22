<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = get_db();
    $stmt = $db->query("SELECT id, name, login_id, is_admin FROM users");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "ユーザーが見つかりません。db/seeds.php を実行してください。\n";
    } else {
        echo "登録されているユーザー一覧:\n";
        foreach ($users as $user) {
            echo "ID: {$user['login_id']}, Name: {$user['name']}, Admin: {$user['is_admin']}\n";
        }
    }
} catch (Exception $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
