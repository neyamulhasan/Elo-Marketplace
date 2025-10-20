<?php
/**
 * Category Model
 * Handle category database operations
 */

class Category {
    private $conn;
    private $table_name = "categories";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all categories
     * @return array
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get category by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new category (admin only)
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, description) 
                  VALUES (:name, :description)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        
        return $stmt->execute();
    }

    /**
     * Update category (admin only)
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    /**
     * Delete category (admin only)
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>
