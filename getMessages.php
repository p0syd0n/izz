<?php
// Require .env library
require 'vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: /login.php");
}

// Get encryption info
$secret_key = $_ENV['secret_key'];
$iv = $_ENV['iv'];
$cipher_method = $_ENV['cipher_method'];

// Open a connection to the SQLite database
$db = new SQLite3('database.db');

// Query to select all messages
$query = "SELECT * FROM messages ORDER BY created_time ASC";
$results = $db->query($query);

// Go thru and decrypt then all and add to array
$messages = [];
if ($results) {
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        // Log each row using error_log
        $log_message = "Message: " . $row['message'] . " | Created Time: " . $row['created_time'];
        $messages[] = openssl_decrypt($row['message'], $cipher_method, $secret_key, 0, $iv);
        error_log($log_message);
    }
} else {
    error_log("No data found or query failed: " . $db->lastErrorMsg());
}
// $ciphertext = openssl_encrypt($plaintext, $cipher_method, $secret_key, 0, $iv);

// Close the connection
$db->close();

// Return the messages as a string
echo implode(",", $messages);

?>
