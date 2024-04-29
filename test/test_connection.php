<?php
include '../config/config.php'; // Adjust the path as needed to include config.php

// Check if connected successfully
if ($con) {
    echo "Connection successful!";
} else {
    echo "Failed to connect: " . mysqli_connect_error();
}
?>
