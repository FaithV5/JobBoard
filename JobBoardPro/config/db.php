<?php
$host = "localhost";      // Use 'localhost' instead of '127.0.0.1'
$dbname = "jobboardpro";
$username = "root";
$password = "";           // Leave blank (no password)

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
