<?php
/**
 * src/Sale.php
 * 売上データ操作クラス
 */

require_once __DIR__ . '/Database.php';

class Sale
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * フィルタリング条件に基づいて売上一覧を取得する
     * 
     * @param array $filters ['store_id', 'category_id', 'date_from', 'date_to', 'receipt_no']
     * @return array
     */
    public function findFiltered(array $filters = []): array
    {
        list($sql, $params) = $this->buildFilteredQuery("SELECT DISTINCT s.*, u.name as store_name", $filters);
        
        $sql .= " ORDER BY s.sold_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * フィルタリング条件に合致する売上の集計（合計金額・件数）を取得する
     * 
     * @param array $filters
     * @return array ['total_amount' => int, 'count' => int]
     */
    public function getSummary(array $filters = []): array
    {
        list($sql, $params) = $this->buildFilteredQuery("SELECT COUNT(DISTINCT s.id) as count, SUM(s.total_amount) as total_amount", $filters);

        $result = $this->db->fetchOne($sql, $params);
        return [
            'total_amount' => (int)($result['total_amount'] ?? 0),
            'count' => (int)($result['count'] ?? 0)
        ];
    }

    /**
     * 新規売上を登録する
     * 
     * @param int $storeId 店舗ID（スタッフのID）
     * @param array $data ['total_amount', 'items' => [['id', 'name', 'price', 'quantity'], ...]]
     * @return bool
     */
    public function create(int $storeId, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            // 1. sales テーブルへ挿入
            $receiptNo = date('Ymd-His-') . sprintf('%04d', rand(0, 9999));
            $taxRate = TAX_RATE_NORMAL; // 定数を使用（将来的に商品ごとに可変にする土台）

            $sqlSale = "INSERT INTO sales (store_id, receipt_no, total_amount, tax_rate, sold_at) 
                        VALUES (:store_id, :receipt_no, :total_amount, :tax_rate, NOW())";
            
            $this->db->execute($sqlSale, [
                'store_id' => $storeId,
                'receipt_no' => $receiptNo,
                'total_amount' => $data['total_amount'],
                'tax_rate' => $taxRate
            ]);

            $saleId = $this->db->lastInsertId();

            // 2. sale_items テーブルへ挿入
            $sqlItem = "INSERT INTO sale_items (sale_id, product_id, product_name, unit_price, quantity) 
                        VALUES (:sale_id, :product_id, :product_name, :unit_price, :quantity)";
            
            foreach ($data['items'] as $item) {
                $this->db->execute($sqlItem, [
                    'sale_id' => $saleId,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity']
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
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
        $sql = $select . " FROM sales s JOIN users u ON s.store_id = u.id";

        // カテゴリ絞り込みがある場合はJOINが必要
        if (!empty($filters['category_id'])) {
            $sql .= " JOIN sale_items si ON s.id = si.sale_id
                      JOIN products p ON si.product_id = p.id";
        }

        $whereClauses = [];

        if (!empty($filters['store_id'])) {
            $whereClauses[] = "s.store_id = :store_id";
            $params['store_id'] = $filters['store_id'];
        }

        if (!empty($filters['category_id'])) {
            $whereClauses[] = "p.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (!empty($filters['date_from'])) {
            $whereClauses[] = "s.sold_at >= :date_from";
            $params['date_from'] = $filters['date_from'] . ' 00:00:00';
        }

        if (!empty($filters['date_to'])) {
            $whereClauses[] = "s.sold_at <= :date_to";
            $params['date_to'] = $filters['date_to'] . ' 23:59:59';
        }

        if (!empty($filters['receipt_no'])) {
            $whereClauses[] = "s.receipt_no LIKE :receipt_no";
            $params['receipt_no'] = '%' . $filters['receipt_no'] . '%';
        }

        if ($whereClauses) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        return [$sql, $params];
    }
}
