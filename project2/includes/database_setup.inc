<?php

// This include is for creating tables if they do not exist, call it after setting and verifying $conn in the file you are using tables in



$sql = "CREATE TABLE IF NOT EXISTS `user` (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(128) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL)";

if (!mysqli_query($conn, $sql)) {
    error_log("User table setup failed: ". mysqli_error($conn));
    die("Database setup failed: ". mysqli_error($conn));
}
else {
    // insert admin user automatically as first entry in db
    $sql = "INSERT INTO user (username, password) VALUES ('admin', 'admin')";
    mysqli_query($conn, $sql);
}

?>