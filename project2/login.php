<?php 
require_once 'includes/session.inc';
$is_invalid = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'settings.php';

    $conn = mysqli_connect($host, $db_user, $pwd, $sql_db);

    $username = trim($_POST['username']);

    $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM user WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $account = mysqli_fetch_assoc($result);

    if ($account && password_verify($_POST['password'], $account['password'])) {
        $_SESSION['user_id'] = (int) $account['id'];
        header("Location: index.php");

        exit();
    }

    $is_invalid = true;
}

$page_title = "Login";
include 'includes/header.inc';
include 'includes/navigation.inc';
?>
    
<main id="index-main">
    <h2>Login</h2>

    <?php
        if ($is_invalid) {
            echo "<h3>Incorrect username or password.</h3>";
        }
    ?>

    <form method="POST" novalidate>
        <div>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? "")?>">
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" id="passsword" name="password">
        </div>

        <button>Login</button>
    </form>
</main>

<?php include 'includes/footer.inc'; ?>
