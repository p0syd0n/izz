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
error_log(implode(",", $messages));
// $ciphertext = openssl_encrypt($plaintext, $cipher_method, $secret_key, 0, $iv);

// Close the connection
$db->close();
?>
<html>
    <head>
        <link rel="stylesheet" href="index.css">
    <body>
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
            <input type="text" name="message" placeholder="message goes here!">
            <button type="submit">send (or press enter)</button>
        </form>
    </body>
    <script>
    setTimeout(async () => {
        const result = await fetch("/getMessages.php");
        const resultText = await result.text(); // Make sure to read the response as text
        if (resultText !== document.getElementById("data").textContent) {
            location.reload(); // Reload the page if the messages are different
        }
    }, 500);
    </script>
</html>