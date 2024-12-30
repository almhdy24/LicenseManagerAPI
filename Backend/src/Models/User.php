<?php
namespace App\Models;

use PDO;

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

        if ($this->emailExists($userData["email"])) {
            return ["errors" => ["Email already exists."]];
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

        if ($this->emailExists($userData["email"], $id)) {
            return ["errors" => ["Email already exists."]];
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

    // Delete a user and their associated licenses
    public function delete($id)
    {
        // Delete associated licenses first
        $this->deleteLicensesByUserId($id);

        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    // Delete licenses by user ID
    private function deleteLicensesByUserId($userId)
    {
        $stmt = $this->conn->prepare("DELETE FROM licenses WHERE email = (SELECT email FROM users WHERE id = :userId)");
        $stmt->bindParam(":userId", $userId);
        $stmt->execute();
    }

    // Find user by email
    public function findByEmail($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Check if email already exists
    private function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT 1 FROM users WHERE email = :email";
        if ($excludeId) {
            $sql .= " AND id != :excludeId";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":email", $email);
        if ($excludeId) {
            $stmt->bindParam(":excludeId", $excludeId);
        }
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    // Get all users with pagination and search
    public function getAll($page = 1, $perPage = 10, $search = '')
    {
        $offset = ($page - 1) * $perPage;
        $searchQuery = $search ? "WHERE name LIKE :search OR email LIKE :search OR fullName LIKE :search" : "";
        $stmt = $this->conn->prepare("SELECT * FROM users $searchQuery LIMIT :perPage OFFSET :offset");
        if ($search) {
            $searchTerm = "%$search%";
            $stmt->bindParam(":search", $searchTerm);
        }
        $stmt->bindParam(":perPage", $perPage, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>