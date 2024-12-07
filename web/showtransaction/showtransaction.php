<?php
session_start();
require '../header/header.php'; // Include database connection
if (!isset($_SESSION['user_login'])) {
    // Redirect to homepage if not logged in
    header("Location: ../homepage/homepage.php");
    exit; // Stop further execution of the script
}
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_login'])) {
    die("User is not logged in.");
}

$userID = $_SESSION['user_login'];

// Fetch transactions for the logged-in user, ordered by datetime DESC
$sql = "SELECT transactionID, amount, transaction_datetime, transaction_type, transaction_status, method 
        FROM transaction 
        WHERE user_id = :userid 
        ORDER BY transaction_datetime DESC
        LIMIT 15";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":userid", $userID, PDO::PARAM_INT);
$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <style>
        body { background-color: #111; color: white; font-family: Rubik; }
        .container { max-width: 800px; margin: 50px auto; padding: 20px; background-color: #333; border-radius: 10px; }
        h2 { text-align: center; color: yellow; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: center; border-bottom: 1px solid #555; }
        th { background-color: #555; color: white; }
        tr:hover { background-color: #444; }
    </style>
</head>
<body>
<div class="container">
    <h2>Transaction History</h2>
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Amount</th>
                <th>Date & Time</th>
                <th>Type</th>
                <th>Status</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)) : ?>
                <?php foreach ($transactions as $transaction) : ?>
                    <tr>
                        <td><?= htmlspecialchars($transaction['transactionID']) ?></td>
                        <td><?= htmlspecialchars($transaction['amount']) ?></td>
                        <td><?= htmlspecialchars($transaction['transaction_datetime']) ?></td>
                        <td><?= htmlspecialchars($transaction['transaction_type']) ?></td>
                        <td><?= htmlspecialchars($transaction['transaction_status']) ?></td>
                        <td><?= htmlspecialchars($transaction['method']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6">No transactions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
