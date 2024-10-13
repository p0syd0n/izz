<?php
// Require .env library
require 'vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Initialize session and check if user is logged in
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: /login.php");
    exit;
}

// Add username to message
$message = isset($_POST['message']) ? $_SESSION['username'].": ".$_POST['message'] : '';

// If its blank, go away
if ($message == "") {
    header("Location: /");
    exit;
}
// Get encryption info from .env
$secret_key = $_ENV['secret_key'];
$iv = $_ENV['iv'];
$cipher_method = $_ENV['cipher_method'];

// Encrypt message
$encrypted_message = openssl_encrypt($message, $cipher_method, $secret_key, 0, $iv);

// Open database connection and prepare+execute query, close cb
$db = new SQLite3('database.db');
$query = $db->prepare("INSERT INTO messages (message) VALUES (:message)");
$query->bindValue(':message', $encrypted_message, SQLITE3_TEXT);
$query->execute();

$db->close();
// Go away
header("Location: /");
exit;
?>