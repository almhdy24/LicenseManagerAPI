<?php
require_once "../vendor/autoload.php";

use App\Database\Database;
use App\Controllers\LicenseController;

header("Content-Type: application/json");
$database = new Database("licenses.db");
$conn = $database->getConnection();
$licenseController = new LicenseController($conn);

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
  case "POST":
    if (isset($_POST["email"])) {
      echo $licenseController->generate($_POST["email"]);
    }else {
      echo json_encode(["message" => "Email is required."]);
    }
    break;

  case "GET":
    if (isset($_GET["code"])) {
      echo $licenseController->validate($_GET["code"]);
    } else {
      echo json_encode(["message" => "License code is required."]);
    }
    break;

  default:
    echo json_encode(["message" => "Invalid request method."]);
    break;
}

// Close connection
$conn = null;
?>
