<?php
require_once __DIR__ . '/Database.php';

/**
 * 商品（Product）データ操作クラス。
 * データベースへのアクセスは Database クラスを介して行う。
 * 商品情報の取得、登録、更新、削除のビジネスロジックを担う。
 */
class Product
{
    private Database $db;

    /**
     * コンストラクタ。
     * Database インスタンスが渡されない場合は内部で新規生成する。
     * これにより、他のクラス（Sale等）と整合性を取りつつ、既存の依存注入にも対応する。
     *
     * @param Database|null $db
     */
    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? new Database();
    }

    /**
     * カテゴリ一覧を取得する。
     * フォームのプルダウンや絞り込みUIで使用する。
     *
     * @return array
     */
    public function getAllCategories(): array
    {
        return $this->db->fetchAll("SELECT id, name FROM categories ORDER BY id");
    }

    /**
     * 商品一覧を取得する。
     * 引数によりカテゴリIDやキーワード（商品名の部分一致）で絞り込みが可能。
     *
     * @param int|null    $categoryId カテゴリID（nullなら絞り込みなし）
     * @param string|null $keyword    キーワード（nullなら絞り込みなし）
     * @return array
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
            $conditions[] = "p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        if ($keyword !== null && $keyword !== '') {
            $conditions[] = "p.name LIKE :keyword";
            $params['keyword'] = "%{$keyword}%";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY c.id, p.id";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * ID指定で商品情報を1件取得する。
     *
     * @param int $id 商品ID
     * @return array|null 商品情報（見つからない場合はnull）
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
     *
     * @param array $data ['name', 'price', 'category_id', 'is_takeout']
     * @return int 新規登録された商品のID
     */
    public function store(array $data): int
    {
        $sql = "
            INSERT INTO products (name, price, category_id, is_takeout)
            VALUES (:name, :price, :category_id, :is_takeout)
        ";
        $this->db->execute($sql, [
            'name'        => $data['name'],
            'price'       => $data['price'],
            'category_id' => $data['category_id'],
            'is_takeout'  => ($data['is_takeout'] ?? false) ? 1 : 0
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * 商品情報を更新する。
     *
     * @param int   $id   商品ID
     * @param array $data ['name', 'price', 'category_id', 'is_takeout']
     * @return bool 成功したかどうか
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
        // 1行以上更新されたか、あるいはエラーがなければ成功とする
        // PDOStatement::rowCount() は変更がない場合に0を返すことがあるため注意
        return $rowCount >= 0;
    }

    /**
     * 商品を削除する。
     *
     * @param int $id 商品ID
     * @return bool 削除に成功したか（影響行数が1以上か）
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM products WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]) > 0;
    }
}
