<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['subwithdraw'])) {
    $amount= $_POST['amount'];
    $trans_type="withdraw";
   $userid = $_SESSION['user_login'];
   $method=$_POST['payment_method'];
   $status='pending';
    if (empty( $amount) || $amount<=0) {
        $_SESSION['withdraw_error'] = "ERROR: Please fill valid amount";
        header("location: withdraw.php");
        exit();
    } else if (empty( $userid )) {
        $_SESSION['withdraw_error'] = "ERROR: Login First!!";
        header("location: withdraw.php");
        exit();
    } 
     else {
        try {
            $check_amount = $conn->prepare("SELECT balance from user where userID=:userid");
            $check_amount->bindParam(":userid", $userid);
            $check_amount->execute();
            $valid_withdraw = $check_amount->fetch(PDO::FETCH_ASSOC);
            if($valid_withdraw['balance'] < $amount){
                $_SESSION['withdraw_error'] = "Withdrawal amount more than balance!!!";
                header("location: withdraw.php");
                exit();
            }
                $stmt = $conn->prepare("INSERT INTO transaction (user_id, amount, transaction_datetime, transaction_type, transaction_status, method) 
                                        VALUES (:userid, :amount, NOW(), :transaction_type, :transaction_status,:method)");
                $stmt->bindParam(":userid", $userid);
                $stmt->bindParam(":amount", $amount);
                $stmt->bindParam(":transaction_type", $trans_type);
                $stmt->bindParam(":transaction_status", $status);
                $stmt->bindParam(":method", $method);


                $stmt1 = $conn->prepare("UPDATE user SET balance = balance - :amount where userID = :userid");
                $stmt1->bindParam(":amount",$amount);
                $stmt1->bindParam(":userid",$userid );

            if($stmt->execute() && $stmt1->execute()){
                $_SESSION['withdraw_success'] = "Withdraw DONE (wait for approval)";
                header("location: withdraw.php");
                exit();
            }
            else {
                $_SESSION['withdraw_error'] = "ERROR: Unexpected issue occurred.";
                header("location: withdraw.php");
                exit();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>
