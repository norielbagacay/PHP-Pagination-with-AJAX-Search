<?php

$dbHost = 'localhost'; 
$dbUsername = 'root'; 
$dbPassword = ''; 
$dbName = 'sample'; 

// Create database connection
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>
