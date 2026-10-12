<?php
require_once 'settings.php';

$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);

$username = trim($_POST['username']);
$password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

$query = "INSERT INTO user (username, password) VALUES ('$username', '$password')";
$result = mysqli_query($conn, $query);

if ($result) {
    echo "Sign up successful, you can now <a href='login.php'>login</a>.";
}
else {
    echo "Signup failed: ". mysqli_error($conn);
}
?>