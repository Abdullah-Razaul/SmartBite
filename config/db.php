<?php
$host = "127.0.0.1";
$user = "root";
$password = ""; 
$dbname = "smartbite";

// Debug: check password type
var_dump($password);

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "DB Connected Successfully";
?>
