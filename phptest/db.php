<?php
// CC 09/30/26 Database connection for customer management system
// Centralized database configuration to avoid duplication across phptest files

$host = 'localhost';
$username = 'root';
$password = 'Klooney0214$';
$dbname = 'user_project';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    // CC 09/29/26 Log connection error internally and show user-friendly message
    error_log("Connection failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}
