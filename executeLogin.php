<?php
require 'vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
session_start();
if (isset($_SESSION['username'])) {
    header("Location: /");
    exit;
}
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

$my_username = $_ENV['my_username'];
$my_password = $_ENV['my_password'];
$her_username = $_ENV['her_username'];
$her_password = $_ENV['her_password'];


if ($username == $my_username && $password == $my_password) {
    $_SESSION["username"] = "arthur";
    header("Location: /");
    exit;
} else if ($username == $her_username && $password == $her_password) {
    $_SESSION["username"] = "princess";
    header("Location: /");
    exit;
} else {
    header("Location: /login.php?message= uhhh gtfo");
}
?>
