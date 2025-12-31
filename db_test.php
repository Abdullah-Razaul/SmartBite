<?php
include "config/db.php";

// Test the connection with a simple query
$result = mysqli_query($conn, "SELECT 1");
if ($result) {
    echo "Database Connected Successfully!";
    mysqli_free_result($result);
} else {
    echo "Database Connection Failed: " . mysqli_error($conn);
}

// Close the connection
mysqli_close($conn);
?>
