<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = [];

// If you want to destroy the session completely, use the following line
session_destroy();

// Redirect to the login page
header("Location: index.php");
exit; // Ensure no further code is executed after the redirect
?>
