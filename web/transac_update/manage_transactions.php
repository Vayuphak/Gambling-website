<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['update_status'])) {
    $status= $_POST['transaction_status'];
    $transactionid=$_POST['transaction_id'];
 
    if (empty( $status )) {
        $_SESSION['status_error'] = "ERROR: Invalid Status";
        header("location: transac_update.php");
        exit();
    } 
     else {
        try {
            
                $stmt = $conn->prepare("UPDATE transaction
SET transaction_status = :newstatus
WHERE transactionID = :transactionid;");
                $stmt->bindParam(":transactionid", $transactionid);
                $stmt->bindParam(":newstatus", $status);
              
            if($stmt->execute()){
                $_SESSION['status_success'] = "Update Status DONE";
                header("location: transac_update.php");
                exit();
            }
            else {
                $_SESSION['status_error'] = "ERROR: Unexpected issue occurred.";
                header("location: transac_update.php");
                exit();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>
