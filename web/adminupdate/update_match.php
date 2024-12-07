<?php
session_start();
include_once("../config/db.php");
require_once("../header/header.php");

if (!isset($_SESSION['admin_login'])) {
    // Redirect to homepage if not logged in
    header("Location: ../homepage/homepage.php");
    exit; // Stop further execution of the script
}

// Handle match update when a specific update button is clicked
if (isset($_POST['update_match'])) {
    $match_id = $_POST['update_match']; // Get the match ID for the updated match
    $odd_team1 = $_POST['odd_team1'][$match_id];
    $odd_team2 = $_POST['odd_team2'][$match_id];
    $odd_draw = $_POST['odd_draw'][$match_id];
    $start_time = $_POST['start_time'][$match_id];
    $end_time = $_POST['end_time'][$match_id];

    // Update the match in the database
    $stmt = $conn->prepare("UPDATE matches SET 
        odd_team1 = :odd_team1,
        odd_team2 = :odd_team2,
        odd_draw = :odd_draw,
        start_time = :start_time,
        end_time = :end_time
        WHERE match_id = :match_id");

    // Bind parameters
    $stmt->bindParam(':odd_team1', $odd_team1);
    $stmt->bindParam(':odd_team2', $odd_team2);
    $stmt->bindParam(':odd_draw', $odd_draw);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':match_id', $match_id);

    // Execute the query
    if ($stmt->execute()) {
        $_SESSION['score_success'] = "Match $match_id updated successfully!";
    } else {
        $_SESSION['score_error'] = "Error updating match $match_id.";
    }

    // Redirect back to the page to see the changes
    header("Location: adminupdate.php");
    exit;
}
?>