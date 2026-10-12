<?php 
$page_title = "Signup";
include 'includes/header.inc';
include 'includes/navigation.inc';
?>
    
<main id="index-main">
    <h2>Signup</h2>

    <form action="process_signup.php" method="POST" novalidate>
        <div>
            <label for="username">Username</label>
            <input type="text" id="username" name="username">
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" id="passsword" name="password">
        </div>

        <div>
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation">
        </div>

        <button>Sign up</button>
    </form>
</main>

<?php include 'includes/footer.inc'; ?>
