<?php
// Initlialize session and check if user is logged in
session_start();
if (isset($_SESSION["username"])) {
    header("Location: /");
}
// redner message from failed sign in
$message = isset($_GET['message']) ? $_GET['message'] : '';

?>

<html>
    <head>
        <title>Welcome</title>
        <link rel="stylesheet" href="login.css">
    </head>
    <body>
        <h1>Welcome!</h1>
        <h3 style="color: red"><?php echo $message ?></h3>
        <form action="executeLogin.php" method="POST">
            <input type="text" name="username" placeholder="username here">
            <input type="password" name="password" placeholder="password here">
            <button type="submit">go</button>
        </form>
    </body>
</html>
