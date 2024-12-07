<!-- <?php 
include_once("../config/db.php");
session_start(); 
?> -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS888</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #000;
            padding: 15px 30px;
            color: #fff; 
            width: 100%;
        }
        
        .balance-info {
            font-size: 16px;
            color: #ccc;
            display: flex;
            align-items: center;
        }
        .balance-info div {
            margin: 0 15px;
            border-right: 1px solid #fff;
            padding-right: 15px;
        }
        .balance-info div:last-child {
            border-right: none;
        }
        .header-buttons {
            display: flex;
            align-items: center;
        }
        .header-buttons a {
            padding: 0 20px;
        }
        .header-buttons .button {
            background-color: #FFD700;
            color: #000;
            padding: 8px 15px;
            margin-right: 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .header-buttons .info {
            padding: 8px 15px;
            margin-right: 15px;
        }
        .header-buttons .account {
            cursor: pointer;
            color: #fff;
        }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }
        /* .modal-content {
            background: #e0e0e0;
            padding: 20px;
            width: 300px;
            position: relative;
            text-align: center;
        } */
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
        }
        .close-button {
            cursor: pointer;
            font-size: 20px;
        }
        .login-button,
        .signup-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            width: 100%;
            margin-top: 10px;
            cursor: pointer;
        }
        .signup-switch {
            background-color: #ffcc33;
            color: black;
            padding: 10px;
            width: 100%;
            margin-top: 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <header class="header py-4">
        <!-- Logo Section -->
        <a href="../homepage/homepage.php" style="text-decoration:none;"> <div class="fs-2 text-warning fw-bold ps-3 my-auto">CSS888</div></a>

        <!-- Buttons and Account Section -->
        <div class="header-buttons">
            <?php echo isset($_SESSION['user_login']) ? '<a href="../showtransaction/showtransaction.php" style="text-decoration:none; color:white;"><div class="info">Transaction History</div></a>' : ''; ?>
            <?php echo isset($_SESSION['admin_login']) ? '<a href="../transac_update/transac_update.php" style="text-decoration:none; color:white;"><div class="info">Update Transaction</div></a>' : ''; ?>
            <?php echo isset($_SESSION['admin_login']) ? '<a href="../adminupdate/adminupdate.php" style="text-decoration:none; color:white;"><div class="info">Update Game</div></a>' : ''; ?>
            <?php echo isset($_SESSION['admin_login']) ? '<a href="../admin/admin.php" style="text-decoration:none; color:white;"><div class="info">Add Game</div></a>' : ''; ?>

            <div class="info">Your balance (THB) <strong>
                <?php  
                  $getbalance = $conn->prepare("SELECT balance FROM user WHERE userid = :userid");
                  $getbalance->bindParam(":userid", $_SESSION['user_login']);
                  $getbalance->execute();
                  $row = $getbalance->fetch(PDO::FETCH_ASSOC);
                  echo isset($row['balance']) ? $row['balance'] : "-";
                ?> 
            </strong></div>
            
            <a href="<?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? '../withdraw/withdraw.php' : '#'; ?>" class="button" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? '' : 'onclick="openModal(\'loginModal\')"'; ?>>Withdraw $</a>
            
            <a href="<?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? '../deposit/deposit.php' : '#'; ?>" class="button" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? '' : 'onclick="openModal(\'loginModal\')"'; ?>>Deposit $</a>
            
            <a href="<?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? '../account/account.php' : '#'; ?>" style="text-decoration:none;">
                <div class="account" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? '' : 'onclick="openModal(\'loginModal\')"'; ?>>
                    My account ▼
                </div>
            </a>
        </div>
    </header>

    <!-- Login Modal -->
    <div id="loginModal" class="modal-overlay " onclick="closeModal(event, 'loginModal')">
        <div class="px-3 pb-3" onclick="event.stopPropagation()" style="background-color: white;">
            <div class="row justify-content-end">
                <span class="close-button col-1 text-end" onclick="closeModal(event, 'loginModal')">&times;</span>
            </div>
            <h2 class="fs-3 text-center pb-3">LOG IN</h2>
            <form action="../login/login.php" method="POST">
                <?php if (isset($_SESSION['error'])) { ?>
                    <div style="color: red; padding: 10px; border: 1px solid red; background-color: #f8d7da; margin-bottom: 10px;">
                        <?php 
                        echo $_SESSION['error'];        
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php } ?>
                <?php if (isset($_SESSION['success'])) { ?>
                    <div style="color: black; padding: 10px; border: 1px solid green; background-color: #f8d7da; margin-bottom: 10px;">
                        <?php 
                        echo $_SESSION['success'];       
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php } ?>
                <div class="row pb-3">
                    <label class="col-4 fs-5">Email</label>
                    <input type="email" name="email" required class="col-7">
                </div>
                <div class="row">
                    <label class="col-4 fs-5">Password</label>
                    <input type="password" name="passwd" required class="col-7">
                </div>
                <button type="submit" name="login" class="login-button">LOGIN</button>
            </form>
            <button class="signup-switch" onclick="switchToSignUp()">SIGN UP</button>
        </div>
    </div>

    <!-- Sign-Up Modal -->
    <div id="signUpModal" class="modal-overlay" onclick="closeModal(event, 'signUpModal')">
        <div class="bg-white px-3 pb-3 w-25" onclick="event.stopPropagation()">
            <div class="bg-light row justify-content-end">
                <span class="close-button col-1 text-center" onclick="closeModal(event, 'signUpModal')">&times;</span>
            </div>
                <h2 class="fs-3 text-center pb-3">SIGN UP</h2>
            <form action="../signup/signup_db.php" method="POST">
                <?php if (isset($_SESSION['error'])) { ?>
                    <div style="color: red; padding: 10px; border: 1px solid red; background-color: #f8d7da; margin-bottom: 10px;">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php } ?>
                <div class="row pb-3">
                    <label class="col-4 fs-5">User Name</label>
                    <input type="text" name="username" required class="col-7">
                </div>
                <div class="row pb-3">
                <label class="col-4 fs-5">Email</label>
                <input type="email" name="email" required class="col-7">
                </div>
                <div class="row pb-3">
                <label class="col-4 fs-5">Phone Number</label>
                <input type="tel" name="phonenumber" required class="col-7">
                </div>
                <div class="row pb-3">
                <label class="col-4 fs-5">Password</label>
                <input type="password" name="passwd" required class="col-7">
                </div>
                <div class="row pb-3">
                <label class="col-4 fs-5">Confirm Password</label>
                <input type="password" name="cpasswd" required class="col-7">
                </div>
                
                <button type="submit" name="signup" class="signup-button">SIGN UP</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(event, modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function switchToSignUp() {
            closeModal(null, 'loginModal');
            openModal('signUpModal');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                closeModal(event, 'loginModal');
                closeModal(event, 'signUpModal');
            }
        };

        // Automatically open the login modal if user is not logged in
        <?php 
            if (!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])) {
                echo 'openModal("loginModal");';
            }
        ?>
    </script>

</body>
</html>
