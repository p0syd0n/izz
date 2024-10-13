<?php
// REquire .env library
require 'vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Check if user is logged in
session_start();
if (isset($_SESSION['username'])) {
    header("Location: /");
    exit;
}
// Get username and password from request
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Get correct usernames and passwords from .env
$my_username = $_ENV['my_username'];
$my_password = $_ENV['my_password'];
$her_username = $_ENV['her_username'];
$her_password = $_ENV['her_password'];

// Compare (no, its not hashed. no, i dont care.)
if ($username == $my_username && $password == $my_password) {
    $_SESSION["username"] = "arthur";
    header("Location: /");
    exit;
} else if ($username == $her_username && $password == $her_password) {
    $_SESSION["username"] = "princess";
    header("Location: /");
    exit;
} else {
    // gtfo
    header("Location: /login.php?message= uhhh gtfo");
}
?>
