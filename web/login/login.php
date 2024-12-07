<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $passwd = $_POST['passwd'];

    if (empty($email)) {
        $_SESSION['error'] = "ERROR: Email is required.";
        header("location: ../homepage/homepage.php");
        exit();
    } elseif (empty($passwd)) {
        $_SESSION['error'] = "Please fill password";
        header("location: ../homepage/homepage.php");
        exit();
    } else {
        try {
            $check_data = $conn->prepare("SELECT * FROM user WHERE email = :email");
            $check_data->bindParam(":email", $email);
            $check_data->execute();
            $row = $check_data->fetch(PDO::FETCH_ASSOC);

            if ($check_data->rowCount() > 0) {
                if ($email == $row['email']) {
                    if (password_verify($passwd, $row['hashpassword'])) {
                        if ($row['role'] == 'admin') {
                            $_SESSION['admin_login'] = $row['userID'];
                            header("location: ../homepage/homepage.php");
                            exit();
                        } else {
                            $_SESSION['user_login'] = $row['userID'];
                            $_SESSION['success'] = "Login successfully";
                            header("location: ../homepage/homepage.php");
                            exit();
                        }
                    } else {
                        $_SESSION['error'] = "Wrong Password";
                        header("location: ../homepage/homepage.php");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = "Wrong email";
                    header("location: ../homepage/homepage.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "No user found";
                header("location: ../homepage/homepage.php");
                exit();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
?>
