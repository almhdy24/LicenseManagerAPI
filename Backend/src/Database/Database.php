<?php
namespace App\Database;

use PDO;
use PDOException;

class Database
{
  private $conn;

  public function __construct($db_file)
  {
    try {
      $this->conn = new PDO("sqlite:" . $db_file);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      error_log($e->getMessage());
      throw new Exception("Database connection error.");
    }
  }

  public function getConnection()
  {
    return $this->conn;
  }
}
?>
