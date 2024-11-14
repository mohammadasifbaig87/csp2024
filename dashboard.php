<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo $_SESSION['user']; ?>!</h1>
        <nav>
            <ul class="header-buttons">
                <li><a href="index.php" class="nav-button">Home</a></li>
                <li><a href="logout.php" class="nav-button">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section>
        <h2>Options</h2>
        <div class="button-container">
            <button onclick="window.location.href='donate_item.php'">Donate Items</button>
            <button onclick="window.location.href='view_donated_items.php'">Take Donated Items</button>
            <button onclick="window.location.href='create_community_drive.php'">Create Community Drive</button>
            <button onclick="window.location.href='ongoing_community_drives.php'">Ongoing Community Drives</button>
            <button onclick="window.location.href='collected_donated_items.php'">Collected Items</button>
            <button onclick="window.location.href='my_donated_items.php'">Items Donated by Me</button>
        </div>
    </section>
</body>
</html>
