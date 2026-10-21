<?php
require_once 'settings.php';
require_once 'includes/session.inc';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Method not allowed.');
}

$profile_id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
if (!$profile_id) {
    exit('You must be logged in!');
}

$submitted_token = $_POST['csrf_token'] ?? '';
$session_token = $_SESSION['csrf_token'] ?? '';

if (!is_string($submitted_token) ||
    !is_string($session_token) ||
    $session_token === '' ||
    !hash_equals($session_token, $submitted_token)
) {
    exit('Invalid security token!');
}

$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);

if (!$conn) {
    exit('Unable to connect to the database.');
}

$sql = "DELETE FROM user_profile WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    exit('Unable to clear profile data.');
}

mysqli_stmt_bind_param($stmt, 'i', $profile_id);

if (!mysqli_stmt_execute($stmt)) {
    exit('Unable to clear profile data.');
}

$sql = "DELETE FROM user WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    exit('Unable to clear user data.');
}

mysqli_stmt_bind_param($stmt, 'i', $profile_id);

if (!mysqli_stmt_execute($stmt)) {
    exit('Unable to clear user data.');
}

header('Location: logout.php');
?>