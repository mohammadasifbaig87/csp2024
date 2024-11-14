<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$item_name = $quantity = $description = $door_no = $area = $city = $state = $pincode = $image_path = "";
$item_name_err = $quantity_err = $description_err = $address_err = $image_err = "";

// Set the donation date to the current date upon item donation
$donation_date = date('Y-m-d');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = trim($_POST["item_name"]);
    $quantity = intval(trim($_POST["quantity"]));
    $description = trim($_POST["description"]);
    
    // Separate address fields
    $door_no = trim($_POST["door_no"]);
    $area = trim($_POST["area"]);
    $city = trim($_POST["city"]);
    $state = trim($_POST["state"]);
    $pincode = trim($_POST["pincode"]);

    // Image upload validation and processing
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            $new_file_name = uniqid("IMG_", true) . '.' . $file_ext;
            $upload_dir = "uploads/";
            $image_path = $upload_dir . $new_file_name;

            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
                $image_err = "Failed to upload image.";
            }
        } else {
            $image_err = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        $image_err = "Please upload an image.";
    }

    // Validate other fields
    if (empty($item_name)) $item_name_err = "Please enter an item name.";
    if (empty($quantity) || $quantity <= 0) $quantity_err = "Please enter a valid quantity greater than zero.";
    if (empty($description)) $description_err = "Please enter a description.";
    if (empty($door_no) || empty($area) || empty($city) || empty($state) || empty($pincode)) {
        $address_err = "Please fill out all address fields.";
    }

    // Insert item into database if no errors
    if (empty($item_name_err) && empty($quantity_err) && empty($description_err) && empty($address_err) && empty($image_err)) {
        $user_name = $_SESSION['user'];
        $query = "SELECT id FROM users WHERE name='$user_name'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['id'];

        // Insert query with image_path and address fields
        $query = "INSERT INTO donated_items (user_id, item_name, quantity, description, donation_date, door_no, area, city, state, pincode, image_path) 
                  VALUES ('$user_id', '$item_name', '$quantity', '$description', '$donation_date', '$door_no', '$area', '$city', '$state', '$pincode', '$image_path')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Item donated successfully!');
                    window.location.href = 'dashboard.php';
                  </script>";
            exit();
        } else {
            echo "Error: " . $query . "<br>" . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donate Item</title>
    <link rel="stylesheet" href="donate.css">
    <script>
        function validateForm() {
            const itemName = document.forms["donationForm"]["item_name"].value.trim();
            const quantity = parseInt(document.forms["donationForm"]["quantity"].value.trim());
            const doorNo = document.forms["donationForm"]["door_no"].value.trim();
            const area = document.forms["donationForm"]["area"].value.trim();
            const city = document.forms["donationForm"]["city"].value.trim();
            const state = document.forms["donationForm"]["state"].value.trim();
            const pincode = document.forms["donationForm"]["pincode"].value.trim();
            let isValid = true;

            // Validate item name
            if (itemName === "") {
                alert("Please enter an item name.");
                isValid = false;
            }

            // Validate quantity (should be greater than zero)
            if (isNaN(quantity) || quantity <= 0) {
                alert("Please enter a valid quantity greater than zero.");
                isValid = false;
            }

            // Validate address fields
            if (doorNo === "" || area === "" || city === "" || state === "" || pincode === "") {
                alert("Please fill out all address fields.");
                isValid = false;
            }

            return isValid;
        }
    </script>
</head>
<body>
    <header>
        <h1><center>Donate Item</center></h1>
    <div class="header-buttons">
        <button class="home-button" onclick="location.href='index.php'">Home</button>
        <button class="dashboard-button" onclick="location.href='dashboard.php'">Dashboard</button>
        <button class="logout-button" onclick="location.href='logout.php'">Logout</button>
    </div>
    </header>
    <section>
        <form name="donationForm" action="donate_item.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label for="item_name">Item Name:</label>
            <input type="text" name="item_name" required>
            <span class="error"><?php echo $item_name_err; ?></span>

            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" min="1" required>
            <span class="error"><?php echo $quantity_err; ?></span><br>

            <label for="description">Description:</label><br>
            <textarea name="description" required></textarea>
            <span class="error"><?php echo $description_err; ?></span><br>

            <label><center>Address</center></label><br>

            <label for="door_no">Door No:</label>
            <input type="text" name="door_no" required>

            <label for="area">Area:</label>
            <input type="text" name="area" required>

            <label for="city">City:</label>
            <input type="text" name="city" required>

            <label for="state">State:</label>
            <input type="text" name="state" required>

            <label for="pincode">Pincode:</label>
            <input type="text" name="pincode" required>

            <label for="image">Upload Image:</label>
            <input type="file" name="image" accept="image/*" required>
            <span class="error"><?php echo $image_err; ?></span><br>

            <button type="submit">Donate Item</button>
        </form>
    </section>
</body>
</html>
