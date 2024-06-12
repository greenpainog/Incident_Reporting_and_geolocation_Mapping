<?php

$conn = mysqli_connect("sql211.infinityfree.com", "if0_36356385", "SGLeyOEMJWz56", "if0_36356385_policetweets");

// Check connection

if (!$conn) {

    die("Connection failed: " . mysqli_connect_error());

}



// Set character encoding

mysqli_set_charset($conn, "utf8");

?>