<?php
namespace App\Models;

use PDO;

class License
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Generate a new license
    public function generate($email, $applicationId)
    {
        $licenseCode = bin2hex(random_bytes(16));
        $stmt = $this->conn->prepare(
            "INSERT INTO licenses (email, license_code, application_id) VALUES (:email, :license_code, :application_id)"
        );
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':license_code', $licenseCode);
        $stmt->bindParam(':application_id', $applicationId);
        $stmt->execute();

        return $licenseCode;
    }

    // Validate a license
    public function validate($licenseCode, $applicationId)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM licenses WHERE license_code = :license_code AND application_id = :application_id"
        );
        $stmt->bindParam(':license_code', $licenseCode);
        $stmt->bindParam(':application_id', $applicationId);
        $stmt->execute();

        $license = $stmt->fetch(PDO::FETCH_ASSOC);
        return $license ? true : false;
    }
}
?>