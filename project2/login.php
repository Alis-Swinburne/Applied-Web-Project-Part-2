<?php 
require_once 'includes/session.inc';
$is_invalid = false;
$signup_success = $_SESSION['signup_success'] ?? null;
unset($_SESSION['signup_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'settings.php';

    $conn = mysqli_connect($host, $db_user, $pwd, $sql_db);

    if (!$conn) {
        exit();
    }

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

    mysqli_set_charset($conn, 'utf8mb4');

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM user WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $account = mysqli_fetch_assoc($result);

    if ($account && password_verify($password, $account['password'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $account['id'];

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        header("Location: index.php");
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    $is_invalid = true;
}

$page_title = "Login";
include 'includes/header.inc';
include 'includes/navigation.inc';
?>
    
<main class="authentication-main">
    <section class="authentication-popup">
        <h2>Welcome back!</h2>
        <p class="authentication-description">Log in to your account to continue</p>

        <?php if ($signup_success): ?>
            <p class="success-message"><em>You have successfully made an account, you can now log in.</em><p>
        <?php endif; ?>

        <?php if ($is_invalid): ?>
            <p class="error-message"><em>Incorrect username or password.</em></p>
        <?php endif; ?>

        <form class="authentication-form" method="POST" action="login.php">
            <div class="form-field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required maxlength="50" value="<?= htmlspecialchars($_POST['username'] ?? "")?>">
            </div>

            <div class="form-field">
                <label for="password">Password</label>
                <input type="password" id="passsword" name="password" required>
            </div>

            <button class="authentication-button" type="submit">Login</button>
        </form>

        <p class="authentication-link">Don't have an account? <a href="signup.php">Create an Account</a></p>
    </section>
</main>

<?php include 'includes/footer.inc'; ?>
