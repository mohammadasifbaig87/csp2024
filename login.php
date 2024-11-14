<?php
include 'database.php';
session_start();

$phone = $_POST['phone'];
$password = $_POST['password'];

// Check if the user has been redirected from the registration page
$isRegistered = isset($_GET['registered']) && $_GET['registered'] === 'true';

$query = "SELECT * FROM users WHERE phone='$phone'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user['name'];
    header("Location: dashboard.php");
} else {
    // Only show the alert if the user wasn't redirected from the registration page
    if (!$isRegistered) {
        echo "<script>alert('Invalid login credentials.'); window.location.href='login.html';</script>";
    } else {
        header("Location: login.html");
    }
}
?>
