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

$result = mysqli_query($conn, "SHOW TABLES LIKE 'user'");
$table_exists = mysqli_num_rows($result) > 0;

if (!$table_exists) {
    $sql = "CREATE TABLE IF NOT EXISTS `user` (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(128) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL)";

    if (!mysqli_query($conn, $sql)) {
        error_log("User table setup failed: ". mysqli_error($conn));
        die("Database setup failed: ". mysqli_error($conn));
    }

    // insert admin user automatically as first entry in db
    $admin_username = 'admin';
    $admin_password = password_hash('admin', PASSWORD_DEFAULT); // Fixed: now correctly hashed before being inserted
    $stmt = mysqli_prepare($conn, "INSERT INTO `user` (username, password) values (?, ?)");
    mysqli_stmt_bind_param($stmt, "ss", $admin_username, $admin_password);
    
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Could not insert admin: ". mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
}

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
    $user_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    $sql = "CREATE TABLE IF NOT EXISTS `user_profile` (
            `id` INT UNSIGNED NOT NULL PRIMARY KEY,
            `first_name` varchar(20) DEFAULT NULL,
            `last_name` varchar(20) DEFAULT NULL,
            `dob` DATE DEFAULT NULL,
            `gender` enum('female','male','nonbinary','prefernot') DEFAULT NULL,
            `street` varchar(40) DEFAULT NULL,
            `suburb` varchar(40) DEFAULT NULL,
            `state` enum('vic','nsw','qld','nt','wa','sa','tas','act') DEFAULT NULL,
            `postcode` char(4) DEFAULT NULL,
            `email` varchar(254) DEFAULT NULL,
            `phone` varchar(12) DEFAULT NULL,
            `skills` text DEFAULT NULL,
            `other_skills` text DEFAULT NULL,
            CONSTRAINT `fk_profile_user`
            FOREIGN KEY (`id`) REFERENCES `user`(`id`)) ENGINE=InnoDB";

    if (!mysqli_query($conn, $sql)) {
        error_log("User table setup failed: ". mysqli_error($conn));
        die("Database setup failed: ". mysqli_error($conn));
        mysqli_close($conn);
        header('Location: signup.php');
        exit();
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO user_profile (id) VALUES (?)");
    mysqli_stmt_bind_param($stmt, 'i', $user_id);

    if (!mysqli_stmt_execute($stmt)) {
        error_log("Profile creation failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        $_SESSION['signup_error'] = "Signup failed, please try again later.";
        header('Location: signup.php');
        exit();
    }

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