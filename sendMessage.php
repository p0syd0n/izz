<?php
require 'vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();
if (!isset($_SESSION["username"])) {
    header("Location: /login.php");
    exit;
}
error_log(":     ::::");
error_log($_SESSION['username']);
$message = isset($_POST['message']) ? $_SESSION['username'].": ".$_POST['message'] : '';
if ($message == "") {
    header("Location: /");
    exit;
}

$secret_key = $_ENV['secret_key'];
$iv = $_ENV['iv'];
$cipher_method = $_ENV['cipher_method'];

$encrypted_message = openssl_encrypt($message, $cipher_method, $secret_key, 0, $iv);
$db = new SQLite3('database.db');
$query = $db->prepare("INSERT INTO messages (message) VALUES (:message)");
$query->bindValue(':message', $encrypted_message, SQLITE3_TEXT);
$query->execute();

$db->close();
header("Location: /");
exit;
?>
// $ciphertext = openssl_encrypt($plaintext, $cipher_method, $secret_key, 0, $iv);

// Close the connection
