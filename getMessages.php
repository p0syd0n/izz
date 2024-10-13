<?php

session_start();
if (!isset($_SESSION["username"])) {
    header("Location: /login.php");
}

// A secure secret key (should be stored securely, not hardcoded like this)
$secret_key = 'my_super_secret_key';
$iv = '1234567890123456'; 
// Cipher method to use (AES-256-CBC is a common, strong choice)
$cipher_method = 'AES-256-CBC';

// Open a connection to the SQLite database
$db = new SQLite3('database.db');

// Query to select all messages
$query = "SELECT * FROM messages ORDER BY created_time ASC";
$results = $db->query($query);

// Check if query returns rows
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

echo implode(",", $messages);

?>
