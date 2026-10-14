<?php 
require_once 'includes/session.inc';

$signup_error = $_SESSION['signup_error'] ?? null;
unset($_SESSION['signup_error']);
// Won't appear again after refreshing page

$page_title = "Signup";
include 'includes/header.inc';
include 'includes/navigation.inc';
?>
    
<main class="authentication-main">
    <section class="authentication-popup">
        <h2>Create an Account</h2>
        <p class="authentication-description">Sign up to get started</p>

        <?php if ($signup_error): ?>
            <p class="error-message"><em><?= htmlspecialchars($signup_error, ENT_QUOTES, 'UTF-8') ?></em><p>
        <?php endif; ?>

        <form class="authentication-form" action="process_signup.php" method="POST">
            <div class="form-field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required minlength="3" maxlength="50">
                <p class="authentication-hint"><em>Username must be between 3-50 characters and contain only letters, numbers, and underscores.</em></p>
            </div>

            <div class="form-field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="5">
                <p class="authentication-hint"><em>Password must be at least 5 characters.</em></p>
            </div>

            <div class="form-field">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="5">
            </div>

            <button class="authentication-button" type="submit">Sign up</button>
        </form>

        <p class="authentication-link">Already have an account? <a href="login.php">Log in</a></p>
    </section>
</main>

<?php include 'includes/footer.inc'; ?>
