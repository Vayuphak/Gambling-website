<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['delete_match'])) {
    $match_id =$_POST['delete_match'];


    try {
        $stmt = $conn->prepare("
            DELETE  FROM matches
            WHERE match_id = :match_id
        ");
        $stmt->bindParam(':match_id', $match_id);

        if ($stmt->execute()) {
            $_SESSION['score_success'] = "Delete Match successfully!";
        } else {
            $_SESSION['score_error'] = "Error ing Match.";
        }
    } catch (PDOException $e) {
        $_SESSION['score_error'] = "Error: " . $e->getMessage();
    }

    header("location: ../adminupdate/adminupdate.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("location: ../adminupdate/adminupdate.php");
    exit();
}
?>
