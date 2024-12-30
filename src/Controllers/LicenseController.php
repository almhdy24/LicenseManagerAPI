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
  }

  public function generate($email)
  {
    
    try {
      $licenseCode = bin2hex(random_bytes(16));
      if ($this->license->create($licenseCode)) {
        return json_encode([
          "license" => $licenseCode,
          "message" => "License generated successfully.",
        ]);
      } else {
        return json_encode(["message" => "Failed to generate license."]);
      }
    } catch (Exception $e) {
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
