<?php
require_once 'includes/session.inc';
require_once 'settings.php';

function verify_access(mysqli $conn): bool {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        $stmt = mysqli_prepare($conn, "SELECT username FROM user WHERE id = ?");
        
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $account = mysqli_fetch_assoc($result);

        if($account && $account['username'] === 'admin') {
            return true;
        }
    }

    return false;
}