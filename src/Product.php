<?php
/**
 * src/Product.php
 * 商品データ操作クラス
 */

require_once __DIR__ . '/Database.php';

class Product
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * 全カテゴリを取得する
     * レジ画面のカテゴリ切替ボタン生成に使用
     */
    public function getAllCategories(): array
    {
        $sql = "SELECT id, name FROM categories ORDER BY id ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * 全商品を取得する
     * レジ画面の「すべて」タブ用
     */
    public function getAll(): array
    {
        $sql = "SELECT id, category_id, name, price, is_takeout FROM products ORDER BY id ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * カテゴリIDを指定して商品一覧を取得する
     * レジ画面の商品ボタン生成に使用
     */
    public function getByCategory(int $categoryId): array
    {
        $sql = "SELECT id, category_id, name, price, is_takeout 
                FROM products 
                WHERE category_id = :category_id 
                ORDER BY id ASC";
        return $this->db->fetchAll($sql, ['category_id' => $categoryId]);
    }
}
