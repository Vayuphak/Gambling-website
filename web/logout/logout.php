<?php
include_once("../config/db.php");
session_start();
unset($_SESSION['user_login']);
unset($_SESSION['admin_login']);
header('location:../homepage/homepage.php');
?>