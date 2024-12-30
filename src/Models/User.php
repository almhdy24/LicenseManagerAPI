<?php
namespace App\Models;

use PDO;
use Exception;

class User
{
  private $conn;

  public function __construct($db)
  {
    $this->conn = $db;
  }

  // Create a new user with validation
  public function create(array $userData)
  {
    $validationErrors = $this->validate($userData);

    if (!empty($validationErrors)) {
      return ["errors" => $validationErrors];
    }

    $stmt = $this->conn->prepare(
      "INSERT INTO users (name, fullName, email, location, activation_status) VALUES (:name, :fullName, :email, :location, :activation_status)"
    );
    $stmt->bindParam(":name", $userData["name"]);
    $stmt->bindParam(":fullName", $userData["fullName"]);
    $stmt->bindParam(":email", $userData["email"]);
    $stmt->bindParam(":location", $userData["location"]);
    $stmt->bindParam(":activation_status", $userData["activation_status"]);

    return $stmt->execute();
  }

  // Validate user data
  private function validate(array $userData)
  {
    $errors = [];

    if (empty($userData["name"])) {
      $errors[] = "Name is required.";
    }

    if (empty($userData["fullName"])) {
      $errors[] = "Full name is required.";
    }

    if (
      empty($userData["email"]) ||
      !filter_var($userData["email"], FILTER_VALIDATE_EMAIL)
    ) {
      $errors[] = "Valid email is required.";
    }

    if (empty($userData["location"])) {
      $errors[] = "Location is required.";
    }

    if (!in_array($userData["activation_status"], ["active", "inactive"])) {
      $errors[] = "Activation status must be either active or inactive.";
    }

    return $errors;
  }

  // Read a user by id
  public function read($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  // Update a user's details
  public function update($id, array $userData)
  {
    $validationErrors = $this->validate($userData);

    if (!empty($validationErrors)) {
      return ["errors" => $validationErrors];
    }

    $stmt = $this->conn->prepare(
      "UPDATE users SET name = :name, fullName = :fullName, email = :email, location = :location, activation_status = :activation_status WHERE id = :id"
    );
    $stmt->bindParam(":name", $userData["name"]);
    $stmt->bindParam(":fullName", $userData["fullName"]);
    $stmt->bindParam(":email", $userData["email"]);
    $stmt->bindParam(":location", $userData["location"]);
    $stmt->bindParam(":activation_status", $userData["activation_status"]);
    $stmt->bindParam(":id", $id);

    return $stmt->execute();
  }



  // Delete a user
  public function delete($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(":id", $id);

    return $stmt->execute();
  }
}
?>
