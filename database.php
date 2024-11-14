<?php
// Database credentials
$servername = "localhost";  // Typically 'localhost' if running locally
$username = "root";         // Your MySQL username, 'root' by default for local setup
$password = "";             // Your MySQL password, leave blank if none for local setup
$dbname = "donation_website"; // The name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
