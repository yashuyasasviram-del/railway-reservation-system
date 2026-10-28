<?php

$conn = new mysqli("localhost", "root", "", "railway_system");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>