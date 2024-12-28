<?php
namespace App\Controllers;

use App\Models\License;
use Exception;

class LicenseController
{
    private $license;
    private $dbConnection; // Add this property to hold the database connection

    public function __construct($db)
    {
        $this->license = new License($db);
        $this->dbConnection = $db; // Store the database connection
    }

    public function generate()
    {
        try {
            $licenseCode = bin2hex(random_bytes(16));
            $this->dbConnection->beginTransaction(); // Use $this->dbConnection

            if ($this->license->create($licenseCode)) {
                $this->dbConnection->commit(); // Use $this->dbConnection
                return json_encode([
                    "license" => $licenseCode,
                    "message" => "License generated successfully.",
                ]);
            } else {
                $this->dbConnection->rollBack(); // Use $this->dbConnection
                return json_encode(["message" => "Failed to generate license."]);
            }
        } catch (Exception $e) {
            $this->dbConnection->rollBack(); // Use $this->dbConnection
            error_log($e->getMessage());
            return json_encode(["message" => "An error occurred."]);
        }
    }

    public function validate($licenseCode)
    {
        $result = $this->license->validate($licenseCode);

        if ($result) {
            return json_encode(["valid" => $result["is_valid"] ? true : false]);
        }
        return json_encode(["valid" => false, "message" => "License not found."]);
    }
}
?>
