<?php
/**
 * User Model
 * Handle user database operations
 */

class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Register new user
     * @param array $data
     * @return bool
     */
    public function register($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, email, password, phone, address) 
                  VALUES (:name, :email, :password, :phone, :address)";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":address", $data['address']);
        
        return $stmt->execute();
    }

    /**
     * Login user
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function login($email, $password) {
        $query = "SELECT id, name, email, password, role, status 
                  FROM " . $this->table_name . " 
                  WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if account is active
            if ($user['status'] != 1) {
                return false;
            }
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }
        }
        
        return false;
    }

    /**
     * Get user by ID
     * @param int $id
     * @return array|false
     */
    public function getUserById($id) {
        $query = "SELECT id, name, email, phone, address, role, status, created_at 
                  FROM " . $this->table_name . " 
                  WHERE id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if email exists
     * @param string $email
     * @return bool
     */
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE email = :email LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Update user profile
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProfile($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, phone = :phone, address = :address 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":address", $data['address']);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    /**
     * Get all users (admin only)
     * @return array
     */
    public function getAllUsers() {
        $query = "SELECT id, name, email, phone, role, status, created_at 
                  FROM " . $this->table_name . " 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update user status (admin only)
     * @param int $id
     * @param int $status
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
}
?>
