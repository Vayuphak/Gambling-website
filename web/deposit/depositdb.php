<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['subdeposit'])) {
    $amount= $_POST['amount'];
    $trans_type="deposit";
   $userid = $_SESSION['user_login'];
   $method=$_POST['payment_method'];
   $status='pending';
    if (empty( $amount) || $amount<=0) {
        $_SESSION['depo_error'] = "ERROR: Please fill valid amount";
        header("location: deposit.php");
        exit();
    } else if (empty( $userid )) {
        $_SESSION['depo_error'] = "ERROR: Login First!!";
        header("location: deposit.php");
        exit();
    } 
     else {
        try {
            
                $stmt = $conn->prepare("INSERT INTO transaction (user_id, amount, transaction_datetime, transaction_type, transaction_status, method) 
                                        VALUES (:userid, :amount, NOW(), :transaction_type, :transaction_status,:method)");
                $stmt->bindParam(":userid", $userid);
                $stmt->bindParam(":amount", $amount);
                $stmt->bindParam(":transaction_type", $trans_type);
                $stmt->bindParam(":transaction_status", $status);
                $stmt->bindParam(":method", $method);
            if($stmt->execute()){
                $_SESSION['depo_success'] = "Deposit DONE (wait for approval)";
                header("location: deposit.php");
                exit();
            }
            else {
                $_SESSION['depo_error'] = "ERROR: Unexpected issue occurred.";
                header("location: deposit.php");
                exit();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>
