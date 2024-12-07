<?php
session_start();
require_once("../config/db.php"); // Include header for database connection

// Disable error reporting and buffer output
error_reporting(E_ERROR | E_PARSE);
ob_start();

// Decode JSON data from the request
$data = json_decode(file_get_contents("php://input"), true);
$betAmount = $data['betAmount'];
$user_id = $_SESSION['user_login'];

try {
    // Fetch user's current balance
    $stmt = $conn->prepare("SELECT balance FROM user WHERE userID = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    ob_end_clean(); // Clear output buffer before sending JSON response

    if ($user) {
        $currentBalance = $user['balance'];

        // Check if balance is enough for the bet amount
        if ($betAmount <= $currentBalance) {
            // Deduct the bet amount and update balance
            $newBalance = $currentBalance - $betAmount;
            $updateStmt = $conn->prepare("UPDATE user SET balance = :newBalance WHERE userID = :user_id");
            $updateStmt->bindParam(':newBalance', $newBalance);
            $updateStmt->bindParam(':user_id', $user_id);

            if ($updateStmt->execute()) {
                echo json_encode(["success" => true, "message" => "Bet placed and balance updated successfully."]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update balance."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Insufficient balance."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found."]);
    }
} catch (PDOException $e) {
    ob_end_clean();
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
