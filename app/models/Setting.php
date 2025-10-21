<?php
class Setting {
    private $conn;
    private $table = 'site_settings';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function get($key) {
        $query = "SELECT setting_value FROM " . $this->table . " WHERE setting_key = :key";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':key', $key);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['setting_value'] : null;
    }

    public function set($key, $value) {
        $query = "INSERT INTO " . $this->table . " (setting_key, setting_value) 
                  VALUES (:key, :value) 
                  ON DUPLICATE KEY UPDATE setting_value = :value";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':key', $key);
        $stmt->bindParam(':value', $value);
        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY setting_key";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAsArray() {
        $settings = $this->getAll();
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }
        return $result;
    }

    /**
     * Delete a setting by key
     * @param string $key
     * @return bool
     */
    public function delete($key) {
        $query = "DELETE FROM " . $this->table . " WHERE setting_key = :key";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':key', $key);
        return $stmt->execute();
    }
}
