<?php
/**
 * Product Model
 * Handle product database operations
 */

class Product {
    private $conn;
    private $table_name = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all products (or only active products)
     * @param int $limit
     * @param int $offset
     * @param bool $active_only - If true, only return active products (default true)
     * @return array
     */
    public function getAll($limit = null, $offset = 0, $active_only = true) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id";
        
        if ($active_only) {
            $query .= " WHERE p.status = 1";
        }
        
        $query .= " ORDER BY p.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($limit) {
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get product by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get products by category
     * @param int $category_id
     * @param int $limit
     * @return array
     */
    public function getByCategory($category_id, $limit = null) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.category_id = :category_id AND p.status = 1 
                  ORDER BY p.created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category_id", $category_id);
        
        if ($limit) {
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search products
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE (p.name LIKE :keyword OR p.description LIKE :keyword) 
                  AND p.status = 1 
                  ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $search_term = "%" . $keyword . "%";
        $stmt->bindParam(":keyword", $search_term);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create new product (admin only)
     * @param array $data
     * @return int|false Last insert ID or false
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, description, price, discount_percentage, stock, category_id, sku, image, images, status) 
                  VALUES (:name, :description, :price, :discount_percentage, :stock, :category_id, :sku, :image, :images, :status)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":price", $data['price']);
        $discount = $data['discount_percentage'] ?? 0.00;
        $stmt->bindParam(":discount_percentage", $discount);
        $stmt->bindParam(":stock", $data['stock']);
        $stmt->bindParam(":category_id", $data['category_id']);
        $stmt->bindParam(":sku", $data['sku']);
        $image = $data['image'] ?? null;
        $stmt->bindParam(":image", $image);
        $images = $data['images'] ?? null;
        $stmt->bindParam(":images", $images);
        $stmt->bindParam(":status", $data['status']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Update product (admin only)
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description, price = :price, 
                      discount_percentage = :discount_percentage,
                      stock = :stock, category_id = :category_id, sku = :sku, 
                      image = :image, images = :images, status = :status 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":price", $data['price']);
        $discount = $data['discount_percentage'] ?? 0.00;
        $stmt->bindParam(":discount_percentage", $discount);
        $stmt->bindParam(":stock", $data['stock']);
        $stmt->bindParam(":category_id", $data['category_id']);
        $stmt->bindParam(":sku", $data['sku']);
        $image = $data['image'] ?? null;
        $stmt->bindParam(":image", $image);
        $images = $data['images'] ?? null;
        $stmt->bindParam(":images", $images);
        $stmt->bindParam(":status", $data['status']);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    /**
     * Delete product (admin only)
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    /**
     * Update stock
     * @param int $id
     * @param int $quantity
     * @return bool
     */
    public function updateStock($id, $quantity) {
        $query = "UPDATE " . $this->table_name . " 
                  SET stock = stock - :quantity 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":quantity", $quantity);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    /**
     * Get total products count
     * @return int
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Update product status (activate/deactivate)
     * @param int $id
     * @param int $status (1 = active, 0 = inactive)
     * @return bool
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
?>
