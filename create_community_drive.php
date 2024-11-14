<?php
session_start();
include 'database.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

// Retrieve user information including user_id
$user_name = $_SESSION['user'];
$query = "SELECT id, phone, door_no, area, city, state, pincode FROM users WHERE name='$user_name'";
$result = mysqli_query($conn, $query);
$user_info = mysqli_fetch_assoc($result);

// Check if user info is available
if (!$user_info) {
    echo "<script>alert('Error fetching user information.'); window.location.href='login.html';</script>";
    exit();
}

// Initialize variables
$user_id = $user_info['id']; // Get user ID
$phone = $user_info['phone'];
$address = $user_info['door_no'] . ", " . $user_info['area'] . ", " . $user_info['city'] . ", " . $user_info['state'] . ", " . $user_info['pincode'];
$donation_date = date('Y-m-d'); // Current date for community drive creation

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $drive_date = $_POST['drive_date'];
    $drive_type = $_POST['drive_type'];

    // Insert into community drive table, including user_id
    $query = "INSERT INTO community_drives (user_id, user_name, phone, address, drive_date, drive_type) 
              VALUES ('$user_id', '$user_name', '$phone', '$address', '$drive_date', '$drive_type')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Community drive created successfully!'); window.location.href='create_community_drive.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error creating community drive. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Community Drive</title>
    <link rel="stylesheet" href="drive.css"> <!-- Link to the new CSS file -->
</head>
<body>
    <header>
        <h1>Create Community Drive</h1>
        <nav>
            <ul class="header-buttons">
                <li><a class="nav-button" href="index.php">Home</a></li>
                <li><a class="nav-button" href="dashboard.php">Dashboard</a></li>
                <li><a class="nav-button" href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <form action="create_community_drive.php" method="POST">
            <label for="drive_date">Date of Organization:</label>
            <input type="date" name="drive_date" required>

            <label for="drive_type">Type of Drive:</label>
            <select name="drive_type" required>
                <option value="swap">Swap/Exchange</option>
                <option value="low_cost">Low Cost</option>
                <option value="both">Both</option>
            </select>

            <button type="submit">Create Drive</button>
        </form>
    </section>
</body>
</html>
