<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $passwd = $_POST['passwd'];
    $cpasswd = $_POST['cpasswd'];
    $urole = 'user';

    if (empty($username)) {
        $_SESSION['error'] = "ERROR: Username is required.";
        header("location: ../homepage/homepage.php");
        exit();
    } else if (empty($email)) {
        $_SESSION['error'] = "ERROR: Email is required.";
        header("location: ../homepage/homepage.php");
        exit();
    } else if (strlen($passwd) > 20 || strlen($passwd) < 5) {
        $_SESSION['error'] = "ERROR: Password must be between 5 and 20 characters.";
        header("location: ../homepage/homepage.php");
        exit();
    } else if ($passwd != $cpasswd) {
        $_SESSION['error'] = "ERROR: Passwords do not match.";
        header("location: ../homepage/homepage.php");
        exit();
    } else {
        try {
            $check_email = $conn->prepare("SELECT email FROM user WHERE email = :email");
            $check_email->bindParam(":email", $email);
            $check_email->execute();
            $row = $check_email->fetch(PDO::FETCH_ASSOC);

            if ($row && $row['email'] == $email) {
                $_SESSION['warning'] = "Email is already in use.";
                header("location: ../homepage/homepage.php");
                exit();
            } else if (!isset($_SESSION['error'])) {
                $passwordHash = password_hash($passwd, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO user (username, email, phonenumber, hashpassword, role) 
                                        VALUES (:username, :email, :phonenumber, :hashpassword, :role)");
                $stmt->bindParam(":username", $username);
                $stmt->bindParam(":email", $email);
                $stmt->bindParam(":phonenumber", $phonenumber);
                $stmt->bindParam(":hashpassword", $passwordHash);
                $stmt->bindParam(":role", $urole);
                $stmt->execute();

                $_SESSION['success'] = "Sign Up DONE. <a href='../homepage/homepage.php'>BACK</a>";
                header("location: ../homepage/homepage.php");
                exit();
            } else {
                $_SESSION['error'] = "ERROR: Unexpected issue occurred.";
                header("location: ../homepage/homepage.php");
                exit();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>
