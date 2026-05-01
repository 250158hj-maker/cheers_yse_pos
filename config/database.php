<?php
require_once __DIR__ . '/app.php';

/**
 * DB接続用関数
 * 環境変数を使用しているファイルを限定するため隔離
 * 接続処理はsrc/Database.php Database::getConnectionから行う。
 * {@return} PDOオブジェクト（接続が成功した場合のみ）
 */
function get_db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
        $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}
