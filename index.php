<?php
// Requiring .env library
require 'vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Loading the session
session_start();

// Checking if the user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: /login.php");
}
// Getting encryption data from .env
$secret_key = $_ENV['secret_key'];
$iv = $_ENV['iv'];
$cipher_method = $_ENV['cipher_method'];

// Open a connection to the SQLite database
$db = new SQLite3('database.db');

// Query to select all messages
$query = "SELECT * FROM messages ORDER BY created_time ASC";
$results = $db->query($query);

// Initialize array for storing plaintext messages
$messages = [];
// Populate the array
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
error_log(implode(",", $messages));
// $ciphertext = openssl_encrypt($plaintext, $cipher_method, $secret_key, 0, $iv);

// Close the connection
$db->close();
?>
<html>
    <head>
        <link rel="stylesheet" href="index.css">
    <body>
        <!-- Hidden element here is used for checking if new messages have arrived !-->
        <p id="data" hidden><?php echo implode(",", $messages); ?></p>
        <?php foreach ($messages as $message): ?>
            <?php 
                // Split the message into username and content
                $parts = explode(':', $message, 2); // Limit to 2 parts
                $message_user = trim($parts[0]); // Username (first part)
                $message_content = isset($parts[1]) ? trim($parts[1]) : ''; // Content (second part)
            ?>
            <p class="<?php echo $message_user === $_SESSION['username'] ? '' : 'other-user-message'; ?>">
                <?php echo htmlspecialchars($message_content); // Display the message content ?>
            </p>
        <?php endforeach; ?>

        <form action="sendMessage.php" method="POST">
            <input type="text" name="message" placeholder="message goes here!" autofocus>
            <button type="submit">send (or press enter)</button>
        </form>
    </body>
    <script>
    setTimeout(async () => {
        const result = await fetch("/getMessages.php");
        const resultText = await result.text();
        if (resultText !== document.getElementById("data").textContent) {
            location.reload(); // Reload the page if the messages are different
        }
    }, 500);
    </script>
</html>