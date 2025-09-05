<?php
$servername = "fdb1031.runhosting.com";
$username   = "4679849_studentdb";
$password   = "Sydlik@2005";
$dbname     = "4679849_studentdb";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}
?>
