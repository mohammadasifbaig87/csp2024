<?php
include 'database.php';

// Initialize an empty error message
$error = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $door_no = trim($_POST['door_no']);
    $area = trim($_POST['area']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $pincode = trim($_POST['pincode']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);

    // Validate phone number
    if (!preg_match("/^\d{10}$/", $phone)) {
        $error = "Phone number must be 10 digits.";
    } 
    // Validate address format (you can customize this validation based on your needs)
    elseif (empty($door_no) || empty($area) || empty($city) || empty($state) || !preg_match("/^\d{6}$/", $pincode)) {
        $error = "Please fill out all address fields and ensure the pincode is 5 digits.";
    } 
    else {
        // Check for duplicate user
        $checkQuery = "SELECT * FROM users WHERE phone='$phone'";
        $result = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($result) > 0) {
            $error = "This phone number is already registered.";
        } else {
            // Prepare the insert query with separate address fields
            $query = "INSERT INTO users (name, phone, door_no, area, city, state, pincode, password) 
                      VALUES ('$name', '$phone', '$door_no', '$area', '$city', '$state', '$pincode', '$password')";

            // After successful registration
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Registration successful!'); window.location.href='login.html?registered=true';</script>";
                exit();
            } else {
                echo "<script>alert('Error: Could not register. Please try again.'); window.location.href='register.html';</script>";
            }
        }
    }

    // If there is an error, display it
    if ($error) {
        echo "<script>alert('$error');</script>";
    }
}
?>
