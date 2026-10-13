<?php
require_once 'includes/session.inc';
require_once 'settings.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit();
}

$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);

if (!$conn) {
    $_SESSION['signup_error'] = "Database connection failed, please try again later.";
    header('Location: signup.php');
    exit();
}

require_once 'includes/database_setup.inc';

// Helps with preventing SQL injection through character encoding
mysqli_set_charset($conn, 'utf8mb4');

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirmation = $_POST['password_confirmation'] ?? '';

// Username validation
if (!preg_match('/\A[a-zA-Z0-9_]{3,50}\z/', $username)) {
    $_SESSION['signup_error'] = "Username must be between 3-50 characters and contain only letters, numbers, and underscores.";
    header('Location: signup.php');
    exit();
}

// Password length validation
if (strlen($password) < 5 || strlen($password) > 4096) {
    $_SESSION['signup_error'] = "Password must be between 5 and 4096 characters.";
    header('Location: signup.php');
    exit();
}

// Password confirmation validation
if ($password !== $password_confirmation) {
    $_SESSION['signup_error'] = "Passwords do not match.";
    header('Location: signup.php');
    exit();
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, 'INSERT INTO user (username, password) VALUES (?, ?)');

if (!$stmt) {
    $_SESSION['signup_error'] = "Unable to signup, please try again later.";
    header('Location: signup.php');
    exit();
}

mysqli_stmt_bind_param($stmt, 'ss', $username, $password_hash);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $_SESSION['signup_success'] = true;

    header('Location: login.php');
    exit();
}

$error_code = mysqli_stmt_errno($stmt);

mysqli_stmt_close($stmt);
mysqli_close($conn);

if ($error_code === 1062) {
    $_SESSION['signup_error'] = "That username is already taken, please choose another.";
    header('Location: signup.php');
    exit();
}
else {
    $_SESSION['signup_error'] = "Signup failed, please try again later.";
}

header('Location: signup.php');
exit();
?>