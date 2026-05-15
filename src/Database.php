<?php
require_once __DIR__ . '/../config/database.php';

/**
 * 汎用DB実行層。ドメイン固有のSQLは持たず、Product/Sale/Auth から委譲された
 * プリペアド実行のみを担う。接続生成は config/database.php に封じ込めるため、
 * このクラスは get_db() からPDOを受け取るだけで new PDO() を呼ばない。
 */
class Database
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = get_db();
    }

    /**
     * SELECT結果を全件返す。0件なら空配列。
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * SELECT結果を1件返す。呼び出し側の判定を一意にするため、
     * 0件時はPDOの false ではなく null に正規化する。
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * INSERT/UPDATE/DELETE を実行し、影響行数を返す。
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * 直近INSERTの自動採番ID。sales挿入後に sale_items を紐付ける用途。
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    // トランザクションのプリミティブのみ提供する。どこを1トランザクションに
    // するかの業務判断は呼び出し側（Sale の計上処理）が持つ。
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }

    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    /**
     * スキーマ定義（DDL）専用の生SQL一括実行。複数文のCREATE TABLE等を
     * プリペアド化せず実行する用途に限定する。ユーザー入力を渡してはならない
     * （データ操作は必ず fetch*/execute のプリペアドを使うこと）。
     */
    public function runSchema(string $sql): void
    {
        $this->pdo->exec($sql);
    }
}
