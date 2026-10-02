<?php
// Allasgo 10/02/26 Database connection for customer management system using MySQL Workbench. The connection details are loaded from a .env file for security reasons, it will not be pushed to the repository and is included in the .gitignore file. 

// Load environment variables from .env file if it exists
if (file_exists(__DIR__ . '/.env')) {
    $envFile = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        // Parse KEY=VALUE format
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Remove quotes if present
            if (($value[0] === '"' && $value[strlen($value) - 1] === '"') ||
                ($value[0] === "'" && $value[strlen($value) - 1] === "'")
            ) {
                $value = substr($value, 1, -1);
            }
            // Set environment variable if not already set
            if (getenv($key) === false) {
                putenv($key . '=' . $value);
            }
        }
    }
}

// Get configuration with fallback defaults
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';  // No default password for security
$database_name = getenv('DB_NAME') ?: 'user_project';

// Create connection
$connection = new mysqli($host, $username, $password, $database_name);
if ($connection->connect_error) {
    // Allasgo 10/02/26 Log connection error internally and show user-friendly message
    error_log("Connection failed: " . $connection->connect_error);
    die("Database connection failed. Please try again later.");
}
