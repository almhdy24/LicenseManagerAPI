<?php
require_once 'vendor/autoload.php';
require_once 'src/Database/Database.php';

use App\Database\Database;

function setupDatabase($db) {
    // Create users table
    $createUsersTable = "
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            fullName TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            location TEXT NOT NULL,
            activation_status TEXT NOT NULL CHECK (activation_status IN ('active', 'inactive'))
        );
    ";

    // Create licenses table
    $createLicensesTable = "
        CREATE TABLE IF NOT EXISTS licenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            license_code TEXT NOT NULL UNIQUE,
            application_id INTEGER NOT NULL,
            FOREIGN KEY (email) REFERENCES users(email) ON DELETE CASCADE
        );
    ";

    try {
        // Execute table creation queries
        $db->exec($createUsersTable);
        $db->exec($createLicensesTable);

        echo "Database setup completed successfully.\n";
    } catch (PDOException $e) {
        echo "Error setting up database: " . $e->getMessage() . "\n";
    }
}

// Get database connection
$database = new Database(__DIR__ . '/licenses.db');
$conn = $database->getConnection();

// Setup database
setupDatabase($conn);

// Close connection
$conn = null;
?>