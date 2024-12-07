<?php
session_start();
require '../config/db.php'; // Include database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login']) ) {
    die("User is not logged in.");
}


if (isset($_SESSION['user_login'])) {
    $userID = $_SESSION['user_login'];
}
else{
    $userID = $_SESSION['admin_login'];
}

// Sanitize and retrieve data from POST request
$fname = isset($_POST['fname']) ? $_POST['fname'] : null;
$lname = isset($_POST['lname']) ? $_POST['lname'] : null;
$doB = isset($_POST['doB']) ? $_POST['doB'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$phonenumber = isset($_POST['phonenumber']) ? $_POST['phonenumber'] : null;

// Debugging: Print the retrieved values
echo "Updating user ID $userID with the following values:<br>";
echo "First Name: $fname<br>";
echo "Last Name: $lname<br>";
echo "Date of Birth: $doB<br>";
echo "Email: $email<br>";
echo "Phone Number: $phonenumber<br>";

// Prepare the update query
$sql = "UPDATE user SET fname = :fname, lname = :lname, doB = :doB, email = :email, phonenumber = :phone WHERE userID = :userid";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Preparation failed: " . $conn->error);
}


$stmt->bindParam(":fname", $fname );
$stmt->bindParam(":lname", $lname );
$stmt->bindParam(":doB", $doB);
$stmt->bindParam(":email", $email);
$stmt->bindParam(":phone", $phonenumber);
$stmt->bindParam(":userid", $userID);

if ($stmt->execute()) {
    echo "Profile updated successfully.";
    $_SESSION['success_update'] = "Update successfully";
} else {
    echo "Error updating profile: " . $stmt->error;
    $_SESSION['error_update']="Update failed";
}
header("location: account.php");
exit();


?>
