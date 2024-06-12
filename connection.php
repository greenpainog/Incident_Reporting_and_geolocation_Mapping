<?php
$conn = mysqli_connect("localhost", "root", "", "tweetmapping");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character encoding
mysqli_set_charset($conn, "utf8");
?>
