<?php
session_start();
require_once '../config/db.php';
require_once '../header/header.php';





// Check if the user is an admin (assuming you store user role in session)
if ( !isset($_SESSION['admin_login'])) {
    // Redirect to login page if not admin
    header("Location: ../homepage/homepage.php");
    exit();
}

if (isset($_POST['update_status'])) {
    // Update transaction status
    $transaction_id = $_POST['transaction_id'];
    $new_status = $_POST['transaction_status'];
    
    // Validate new status
    if ($new_status != 'complete' && $new_status != 'cancel') {
        $_SESSION['error'] = "Invalid status.";
        header("Location: transac_update.php");
        exit();
    }

    try {
        $update_status = $conn->prepare("UPDATE transaction SET transaction_status = :status WHERE transactionID = :transaction_id AND transaction_status = 'pending'");
        $update_status->bindParam(":status", $new_status);
        $update_status->bindParam(":transaction_id", $transaction_id);
        $update_status->execute();
        
        if ($update_status->rowCount() > 0) {
            $_SESSION['success'] = "Transaction status updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update the status (may be already updated).";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    header("Location: transac_update.php");
    exit();
}

// Fetch all pending transactions
try {
    $stmt = $conn->prepare("SELECT * FROM transaction WHERE transaction_status = 'pending' ORDER BY transaction_datetime ASC");
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching transactions: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <style>
        /* Add some basic styling */
        body{
            background-color: #fff;
            font-family: "Rubik";
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .alert {
            padding: 10px;
            margin: 10px 0;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .btn {
            padding: 5px 15px;
            border: none;
            cursor: pointer;
        }

        .btn-complete {
            background-color: #28a745;
            color: white;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: white;
        }

        .alert-container {
    margin-top: 20px;
}

/* Success message styling */
.alert.success {
    padding: 15px;
    background-color: #28a745; /* Green */
    color: white;
    border-radius: 5px;
    margin-bottom: 15px;
    font-size: 16px;
    border: 1px solid #218838;
}

/* Error message styling */
.alert.error {
    padding: 15px;
    background-color: #dc3545; /* Red */
    color: white;
    border-radius: 5px;
    margin-bottom: 15px;
    font-size: 16px;
    border: 1px solid #c82333;
}

/* Optional: You can add an animation for fading out the messages */
.alert {
    animation: fadeIn 1s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
    </style>
</head>
<body>
    <h2>Manage Pending Transactions</h2>

    <?php
if (isset($_SESSION['status_error']) || isset($_SESSION['status_success'])) {
    echo '<div class="alert-container">';
    
    // Display error message if available
    if (isset($_SESSION['status_error'])) {
        echo '<div class="alert error">'.$_SESSION['status_error'].'</div>';
        unset($_SESSION['status_error']);
    }
    
    // Display success message if available
    if (isset($_SESSION['status_success'])) {
        echo '<div class="alert success">'.$_SESSION['status_success'].'</div>';
        unset($_SESSION['status_success']);
    }
    
    echo '</div>';
}
?>





    <!-- Show success or error messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Transaction Table -->
    <table>
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>User ID</th>
                <th>Amount</th>
                <th>Transaction Type</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($transactions) > 0): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo $transaction['transactionID']; ?></td>
                        <td><?php echo $transaction['user_id']; ?></td>
                        <td><?php echo $transaction['amount']; ?></td>
                        <td><?php echo ucfirst($transaction['transaction_type']); ?></td>
                        <td><?php echo ucfirst($transaction['transaction_status']); ?></td>
                        <td><?php echo $transaction['method']; ?></td>
                        <td>
                            <!-- Form to update transaction status -->
                            <form action="manage_transactions.php" method="POST" style="display:inline;">
                                <input type="hidden" name="transaction_id" value="<?php echo $transaction['transactionID']; ?>">
                                <select name="transaction_status" required>
                                    <option value="complete">Complete</option>
                                    <option value="cancel">Cancel</option>
                                    
                                </select>
                                <button type="submit" name="update_status" class="btn <?php echo ($transaction['transaction_status'] == 'pending') ? 'btn-complete' : 'btn-cancel'; ?>">
                                    Update Status
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No pending transactions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
