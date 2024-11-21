<?php
// Database configuration
$host = 'localhost'; // usually localhost
$db_name = 'vendocu'; // replace with your database name
$username = 'root'; // usually 'root' for XAMPP
$password = ''; // default password for XAMPP MySQL is empty

// Create connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
?>
