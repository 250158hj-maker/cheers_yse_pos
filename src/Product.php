<?php
// src/Product.php [cite: 455]
class Product {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // 商品一覧の取得（フィルタ・検索対応） [cite: 728]
    public function getAll($categoryId = null, $keyword = null) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE 1=1";
        
        $params = [];
        if ($categoryId) {
            $sql .= " AND c.id = :category_id";
            $params['category_id'] = $categoryId;
        }
        if ($keyword) {
            $sql .= " AND p.name LIKE :keyword";
            $params['keyword'] = "%$keyword%";
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // 新規登録 [cite: 726]
    public function create($name, $price, $isTakeout, $categoryId) {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO products (name, price, is_takeout) VALUES (?, ?, ?)");
            $stmt->execute([$name, $price, $isTakeout]);
            $productId = $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
            $stmt->execute([$productId, $categoryId]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    // 削除 [cite: 733]
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}