<?php
/**
 * Order Model
 * Handle order database operations
 */

class Order {
    private $conn;
    private $table_name = "orders";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new order
     * @param array $data
     * @return int|false Last insert ID or false
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, total, payment_method, shipping_address) 
                  VALUES (:user_id, :total, :payment_method, :shipping_address)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":total", $data['total']);
        $stmt->bindParam(":payment_method", $data['payment_method']);
        $stmt->bindParam(":shipping_address", $data['shipping_address']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Add order items
     * @param int $order_id
     * @param array $items
     * @return bool
     */
    public function addItems($order_id, $items) {
        $query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                  VALUES (:order_id, :product_id, :quantity, :price)";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($items as $item) {
            $stmt->bindParam(":order_id", $order_id);
            $stmt->bindParam(":product_id", $item['product_id']);
            $stmt->bindParam(":quantity", $item['quantity']);
            $stmt->bindParam(":price", $item['price']);
            
            if (!$stmt->execute()) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get orders by user
     * @param int $user_id
     * @return array
     */
    public function getByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get order by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT o.*, u.name as user_name, u.email as user_email 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  WHERE o.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get order items
     * @param int $order_id
     * @return array
     */
    public function getItems($order_id) {
        $query = "SELECT oi.*, p.name as product_name 
                  FROM order_items oi 
                  LEFT JOIN products p ON oi.product_id = p.id 
                  WHERE oi.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":order_id", $order_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all orders (admin only)
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($limit = null, $offset = 0) {
        $query = "SELECT o.*, u.name as user_name, u.email as user_email 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  ORDER BY o.created_at DESC";
        
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
     * Update order status
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    /**
     * Get total orders count
     * @return int
     */
    public function getTotalCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>
