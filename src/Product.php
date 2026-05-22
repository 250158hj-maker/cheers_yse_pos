<?php
require_once __DIR__ . '/../src/Database.php';

class Product
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * カテゴリ一覧を取得する。
     * フォームのプルダウンと絞り込みUIで使用する。
     */
    public function getAllCategories(): array
    {
        return $this->db->fetchAll("SELECT id, name FROM categories ORDER BY id");
    }

    /**
     * 商品一覧を取得する。
     * 引数を省略すると全件取得。categoryId・keyword を渡すと絞り込む。
     * 両方渡した場合はAND条件で絞り込む。
     *
     * @param int|null    $categoryId カテゴリID（nullなら絞り込みなし）
     * @param string|null $keyword    商品名の部分一致キーワード（nullなら絞り込みなし）
     */
    public function getAll(?int $categoryId = null, ?string $keyword = null): array
    {
        $sql = "
            SELECT
                p.id,
                p.name,
                p.price,
                p.is_takeout,
                c.id   AS category_id,
                c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
        ";

        $params = [];
        $conditions = [];

        if ($categoryId !== null) {
            $conditions[] = "p.category_id = ?";
            $params[] = $categoryId;
        }

        if ($keyword !== null && $keyword !== '') {
            $conditions[] = "p.name LIKE ?";
            $params[] = "%{$keyword}%";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY c.id, p.id";

        return $this->db->fetchAll($sql, $params);
    }
}