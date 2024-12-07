<?php
session_start();
require_once("../header/header.php");

// Ensure the user is logged in
if (!isset($_SESSION['user_login'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if bet_id is provided
if (isset($_GET['bet_id'])) {
    $bet_id = (int)$_GET['bet_id'];

    try {
        // Start a transaction to ensure data consistency
        $conn->beginTransaction();

        // Fetch the bet details for the pending bet
        $stmt = $conn->prepare("SELECT bet_amount, user_id FROM bet WHERE bet_id = :bet_id AND bet_outcome IS NULL");
        $stmt->bindValue(':bet_id', $bet_id, PDO::PARAM_INT);
        $stmt->execute();
        $bet = $stmt->fetch(PDO::FETCH_ASSOC);

        // If a pending bet is found
        if ($bet) {
            $bet_amount = $bet['bet_amount'];
            $user_id = $bet['user_id'];

            // Update the user's balance by adding the bet amount back
            $stmt = $conn->prepare("UPDATE user SET balance = balance + :bet_amount WHERE userID = :user_id");
            $stmt->bindValue(':bet_amount', $bet_amount, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Delete the bet from the 'bet' table
            $stmt = $conn->prepare("DELETE FROM bet WHERE bet_id = :bet_id");
            $stmt->bindValue(':bet_id', $bet_id, PDO::PARAM_INT);
            $stmt->execute();

            // Commit the transaction
            $conn->commit();

            // Redirect back to the betting page
            header("Location: yourbet.php"); // Replace with actual betting page
            exit();
        } else {
            // If no matching pending bet found
            header("Location: yourbet.php"); // Replace with actual betting page
            exit();
        }

    } catch (PDOException $e) {
        // If an error occurs, rollback the transaction and show the error
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
} else {
    // If no bet_id is provided, redirect back to the betting page
    header("Location: yourbet.php"); // Replace with actual betting page
    exit();
}
?>
