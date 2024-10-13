<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: /");
    exit;
}
$username = isset($_POST['username']) ? $_POST['username'] : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($username == "a" && $password == "a") {
    $_SESSION["username"] = "arthur";
    header("Location: /");
    exit;
} else if ($username == "p" && $password == "b") {
    $_SESSION["username"] = "princess";
    header("Location: /");
    exit;
} else {
    header("Location: /login.php?message= uhhh gtfo");
}
?>
