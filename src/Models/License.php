<?php
namespace App\Models;

use PDO;

class License {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($licenseCode) {
        $stmt = $this->conn->prepare("INSERT INTO licenses (code, is_valid) VALUES (:code, 1)");
        $stmt->bindParam(":code", $licenseCode);
        
        return $stmt->execute();
    }

    public function validate($licenseCode) {
        $stmt = $this->conn->prepare("SELECT is_valid FROM licenses WHERE code = :code");
        $stmt->bindParam(":code", $licenseCode);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>