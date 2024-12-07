<?php require_once("../header/header.php");

if (!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])) {
    // Redirect to homepage if not logged in
    header("Location: ../homepage/homepage.php");
    exit; // Stop further execution of the script
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #111;
            font-family: "Rubik";
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #FFD700;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-size: 16px;
            display: block;
            margin-bottom: 8px;
        }
        input[type="number"], input[type="text"], select, button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 14px;
        }

        /* General alert container */
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
    <div class="container">
        <h2>Deposit Funds</h2>

<!-- Display Success or Error Message -->
<?php
if (isset($_SESSION['depo_error']) || isset($_SESSION['depo_success'])) {
    echo '<div class="alert-container">';
    
    // Display error message if available
    if (isset($_SESSION['depo_error'])) {
        echo '<div class="alert error">'.$_SESSION['depo_error'].'</div>';
        unset($_SESSION['depo_error']);
    }
    
    // Display success message if available
    if (isset($_SESSION['depo_success'])) {
        echo '<div class="alert success">'.$_SESSION['depo_success'].'</div>';
        unset($_SESSION['depo_success']);
    }
    
    echo '</div>';
}
?>




        <form id="depositForm" action="depositdb.php" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="amount">Amount to Deposit</label>
                <input 
    type="number" 
    id="amount" 
    name="amount" 
    placeholder="Enter amount" 
    min="1" 
    step="0.01" 
    required 
    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
>
                <div id="amountError" class="error"></div>
            </div>

            <div class="form-group">
                <label for="paymentMethod">Payment Method</label>
                <select id="paymentMethod" name="payment_method" required onchange="toggleFields()">
                    <option value="">Select Payment Method</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cryptocurrency">Cryptocurrency</option>
                </select>
                <div id="paymentMethodError" class="error"></div>
            </div>

            <!-- Conditional fields for each payment method -->
            <div class="form-group" id="creditCardField" style="display: none;">
                <label for="cardNumber">Credit Card Number</label>
                <input 
    type="text" 
    id="cardNumber" 
    name="card_number" 
    maxlength="16" 
    placeholder="Enter card number" 
    oninput="this.value = this.value.replace(/[^0-9]/g, '');"
>

                <div id="cardNumberError" class="error"></div>
            </div>

            <div class="form-group" id="paypalField" style="display: none;">
                <label for="paypalEmail">PayPal Email</label>
                <input type="email" id="paypalEmail" name="paypal_email" placeholder="Enter PayPal email">
                <div id="paypalEmailError" class="error"></div>
            </div>

            <div class="form-group" id="bankTransferField" style="display: none;">
                <label for="bankAccount">Bank Account Number</label>
                <input type="text" id="bankAccount" name="bank_account" placeholder="Enter bank account number">
                <div id="bankAccountError" class="error"></div>
            </div>

            <div class="form-group" id="cryptoWalletField" style="display: none;">
                <label for="cryptoWallet">Cryptocurrency Wallet Address</label>
                <input type="text" id="cryptoWallet" name="crypto_wallet" placeholder="Enter wallet address">
                <div id="cryptoWalletError" class="error"></div>
            </div>

            <button type="submit" name="subdeposit">Deposit Now</button>
        </form>
    </div>

    <script>
        function toggleFields() {
            // Hide all conditional fields
            document.getElementById('creditCardField').style.display = 'none';
            document.getElementById('paypalField').style.display = 'none';
            document.getElementById('bankTransferField').style.display = 'none';
            document.getElementById('cryptoWalletField').style.display = 'none';

            // Get the selected payment method
            const paymentMethod = document.getElementById('paymentMethod').value;

            // Show the relevant field based on the selected payment method
            if (paymentMethod === 'credit_card') {
                document.getElementById('creditCardField').style.display = 'block';
            } else if (paymentMethod === 'paypal') {
                document.getElementById('paypalField').style.display = 'block';
            } else if (paymentMethod === 'bank_transfer') {
                document.getElementById('bankTransferField').style.display = 'block';
            } else if (paymentMethod === 'cryptocurrency') {
                document.getElementById('cryptoWalletField').style.display = 'block';
            }
        }

        function validateForm() {
            let amount = document.getElementById('amount').value;
            let paymentMethod = document.getElementById('paymentMethod').value;
            let valid = true;

            // Clear previous error messages
            document.getElementById('amountError').innerText = '';
            document.getElementById('paymentMethodError').innerText = '';

            // Validate Amount
            if (amount <= 0 || isNaN(amount)) {
                document.getElementById('amountError').innerText = 'Please enter a valid amount.';
                valid = false;
            }

            // Validate Payment Method
            if (paymentMethod === '') {
                document.getElementById('paymentMethodError').innerText = 'Please select a payment method.';
                valid = false;
            }

            // Additional validation based on selected payment method
            if (paymentMethod === 'credit_card') {
                let cardNumber = document.getElementById('cardNumber').value;
                if (!cardNumber.match(/^\d{16}$/)) {
                    document.getElementById('cardNumberError').innerText = 'Please enter a valid 16-digit card number.';
                    valid = false;
                }
            } else if (paymentMethod === 'paypal') {
                let paypalEmail = document.getElementById('paypalEmail').value;
                if (!paypalEmail.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                    document.getElementById('paypalEmailError').innerText = 'Please enter a valid email address.';
                    valid = false;
                }
            } else if (paymentMethod === 'bank_transfer') {
                let bankAccount = document.getElementById('bankAccount').value;
                if (bankAccount === '') {
                    document.getElementById('bankAccountError').innerText = 'Please enter a bank account number.';
                    valid = false;
                }
            } else if (paymentMethod === 'cryptocurrency') {
                let cryptoWallet = document.getElementById('cryptoWallet').value;
                if (cryptoWallet === '') {
                    document.getElementById('cryptoWalletError').innerText = 'Please enter a wallet address.';
                    valid = false;
                }
            }

            return valid;  // If false, form will not be submitted
        }
    </script>
</body>
</html>
