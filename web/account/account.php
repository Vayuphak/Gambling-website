<?php
session_start();



require '../header/header.php'; // Include database connection
if (!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])) {
    // Redirect to homepage if not logged in
    header("Location: ../homepage/homepage.php");
    exit; // Stop further execution of the script
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (isset($_SESSION['user_login'])) {
    $userID = $_SESSION['user_login'];
}
if (isset($_SESSION['admin_login'])) {
    $userID = $_SESSION['admin_login'];
}



// Fetch the user's current data
$sql = "SELECT username, email, phonenumber, fname, lname, doB FROM user WHERE userID = :userid";
$stmt = $conn->prepare($sql);
$stmt->bindParam("userid", $userID);
$stmt->execute();
$user =  $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("No user data found for user ID: $userID");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Profile</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <style>
         /* Styling similar to your screenshot  */
         body { background-color: #111;
            font-family: "Rubik"; }
        .container { max-width: unset; }
        /* .field { display: flex; justify-content: space-between; padding: 10px; }
        .field label { width: 30%; }
        .field input { width: 65%; padding: 5px; border: none; }
        .save-btn { background-color: green; color: white; padding: 10px; width: 100%; border: none; cursor: pointer; }
        .logout-btn { background-color: red; color: white; padding: 10px; width: 100%; border: none; cursor: pointer; margin-top: 10px; }  */

        .success-message {
    color: #155724; /* Dark green text */
    background-color: #d4edda; /* Light green background */
    border: 1px solid #c3e6cb; /* Green border */
    padding: 10px 15px; /* Add some spacing inside the box */
    margin: 10px 0; /* Add spacing around the box */
    border-radius: 5px; /* Rounded corners */
    font-family: Arial, sans-serif; /* Change font */
    font-size: 16px; /* Adjust text size */
    text-align: center; /* Center the text */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Add subtle shadow */
}

.error-message {
    color: #721c24; /* Dark red text */
    background-color: #f8d7da; /* Light red background */
    border: 1px solid #f5c6cb; /* Red border */
    padding: 10px 15px;
    margin: 10px 0;
    border-radius: 5px;
    font-family: Arial, sans-serif;
    font-size: 16px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}
    </style>
</head>
<body>
    <?php
if (isset($_SESSION['success_update'])) {
    echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_update']) . '</div>';
    unset($_SESSION['success_update']); // Clear the message after displaying
}

if (isset($_SESSION['error_update'])) {
    echo '<div class="error-message">' . htmlspecialchars($_SESSION['error_update']) . '</div>';
    unset($_SESSION['error_update']); // Clear the message after displaying
}
?>
<div class="row justify-content-center pt-5">
   
    <div class="container col-5 bg-warning mt-5 rounded"  >
        <h2 class="fs-2 fw-bold my-5 text-center">Personal Profile</h2>
        <form action="update_profile.php" method="POST" >
            <div class="row pb-3 justify-content-center">
                <label class="col-3 fs-3 justify-content-center">Username :</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" readonly class="col-6">
            </div>
            <div class="row pb-3 justify-content-center" >
                <label class="col-3 fs-3">First Name :</label>
                <?php $fname = $user['fname'] ?? ""; ?>
                <input 
    type="text" 
    name="fname" 
    value="<?= htmlspecialchars($fname) ?>" 
    class="col-6" 
    oninput="validateOnlyString(this)" 
    pattern="[A-Za-z\s]+" 
    title="Name can only contain letters and spaces." 
    required>
            </div>
            <div class="row pb-3 justify-content-center">
                <label class="col-3 fs-3">Last Name :</label>
                <?php $lname = $user['lname'] ?? ""; ?>
                <input 
    type="text" 
    name="lname" 
    value="<?= htmlspecialchars($lname) ?>" 
    class="col-6" 
    oninput="validateOnlyString(this)" 
    pattern="[A-Za-z\s]+" 
    title="Last name can only contain letters and spaces." 
    required>

            </div>
            <div class="row pb-3 justify-content-center">
                <label class="col-3 fs-3">Date of Birth :</label>
                <input type="date" name="doB" value="<?= htmlspecialchars($user['doB']) ?>" class="col-6" required>
            </div>
            <div class="row pb-3 justify-content-center">
                <label class="col-3 fs-3">Email :</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="col-6">
            </div>
            <div class="row pb-3 justify-content-center">
                <label class="col-3 fs-3">Phone Number :+66</label>
                <input 
    type="text" 
    name="phonenumber" 
    value="<?= htmlspecialchars($user['phonenumber']) ?>" 
    class="col-6" 
    maxlength="10" 
    oninput="validateExactNumber(this)" 
    pattern="\d{10}" 
    title="Phone number must be exactly 10 digits." 
    required>
    <script>

function validateExactNumber(input) {
    // Remove any non-numeric characters
    input.value = input.value.replace(/[^0-9]/g, '');

    // Truncate to 10 digits if exceeded
    if (input.value.length > 10) {
        input.value = input.value.slice(0, 10);
    }

    // Validate the length to exactly 10 digits
    if (input.value.length !== 10) {
        input.setCustomValidity("Phone number must be exactly 10 digits.");
    } else {
        input.setCustomValidity(""); // Clear error if valid
    }
}
    </script>
            </div>
            <div class="row justify-content-center pt-5">
                <button type="submit" class="col-4 btn btn-success" >Save</button>
            </div>
        </form>

        <!-- Logout Button -->

            <form action="../logout/logout.php" method="POST" class="row justify-content-center py-5">
                <button type="submit" class="col-4 btn btn-danger">Logout</button>
            </form>

    </div>
    
</div>
</body>
</html>
