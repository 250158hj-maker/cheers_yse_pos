<?php
require_once __DIR__ . '/Database.php';

/**
 * 商品（Product）データ操作クラス。
 * Database ラッパー経由で DB 操作を行う。
 */
class Product
{
    private Database $db;

    /**
     * コンストラクタ。外部から Database インスタンスを注入する。
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * カテゴリ一覧を取得する。
     * @return array
     */
    public function getAllCategories(): array
    {
        return $this->db->fetchAll("SELECT id, name FROM categories ORDER BY id");
    }

    /**
     * 商品一覧を取得する。
     * カテゴリやキーワードでの絞り込みが可能。
     * @param int|null $categoryId
     * @param string|null $keyword
     * @return array
     */
    public function getAll(?int $categoryId = null, ?string $keyword = null): array
    {
        $filters = [
            'category_id' => $categoryId,
            'keyword' => $keyword
        ];

        list($sql, $params) = $this->buildFilteredQuery("
            SELECT
                p.id,
                p.name,
                p.price,
                p.is_takeout,
                c.id   AS category_id,
                c.name AS category_name
        ", $filters);

        $sql .= " ORDER BY c.id, p.id";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * カテゴリIDを指定して商品一覧を取得する（互換ラッパー）
     * @param int $categoryId
     * @return array
     */
    public function getByCategory(int $categoryId): array
    {
        return $this->getAll($categoryId, null);
    }

    /**
     * ID指定で商品情報を1件取得する。
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $sql = "
            SELECT
                p.*,
                c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
        ";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }

    /**
     * 商品を新規登録する。
     * @param array $data
     * @return int
     */
    public function store(array $data): int
    {
        $sql = "
            INSERT INTO products (name, price, category_id, is_takeout)
            VALUES (:name, :price, :category_id, :is_takeout)
        ";
        return (int)$this->db->insert($sql, [
            'name'        => $data['name'],
            'price'       => $data['price'],
            'category_id' => $data['category_id'],
            'is_takeout'  => ($data['is_takeout'] ?? false) ? 1 : 0
        ]);
    }

    /**
     * 商品情報を更新する。
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE products
            SET name = :name, price = :price, category_id = :category_id, is_takeout = :is_takeout
            WHERE id = :id
        ";
        $rowCount = $this->db->execute($sql, [
            'id'          => $id,
            'name'        => $data['name'],
            'price'       => $data['price'],
            'category_id' => $data['category_id'],
            'is_takeout'  => ($data['is_takeout'] ?? false) ? 1 : 0
        ]);
        return $rowCount >= 0;
    }

    /**
     * 商品を削除する。
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM products WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]) > 0;
    }

    /**
     * 共通のクエリビルドロジック
     * 
     * @param string $select
     * @param array $filters
     * @return array [string $sql, array $params]
     */
    private function buildFilteredQuery(string $select, array $filters): array
    {
        $params = [];
        $sql = $select . " FROM products p JOIN categories c ON p.category_id = c.id";

        $whereClauses = [];

        if (!empty($filters['category_id'])) {
            $whereClauses[] = "p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['keyword'])) {
            $whereClauses[] = "p.name LIKE :keyword";
            $params['keyword'] = "%{$filters['keyword']}%";
        }

        if ($whereClauses) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        return [$sql, $params];
    }
}
