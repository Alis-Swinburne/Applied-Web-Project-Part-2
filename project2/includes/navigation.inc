<?php

require_once './verify_access.php';

$current = $nav_current ?? basename($_SERVER['SCRIPT_NAME']); // Keeps a track of the current page
$brand = $site_name ?? "UPDATE COMPANY NAME";
$logged_in = isset($_SESSION['user_id']);
$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);

$nav_items = [
    'index.php' => ['HOME', 'fa-house'], 
    'jobs.php' => ['JOBS', 'fa-suitcase'], 
    'apply.php' => ['APPLY', 'fa-pen-to-square'], 
    'about.php' => ['ABOUT', 'fa-circle-info'], 
];

// For determing admin access

// If logged in add the manage nav button and the logout button
// Else add the login and signup buttons
if($logged_in) {
    if(verify_access($conn)) {
        $nav_items['manage.php'] = ['MANAGE', 'fa-table-list'];
    }

    $nav_items['logout.php'] = ['LOGOUT', 'fa-right-from-bracket'];
}
else { 
    $nav_items['signup.php'] = ['SIGNUP', 'fa-file-pen'];
    $nav_items['login.php'] = ['LOGIN', 'fa-right-to-bracket'];
}
?>

<header>
            <!-- Contains the company logo, name, and a navigation menu. -->
            <nav class="main-nav" aria-label="Main Navigation">
                <a href="index.php" class="brand">
                    <!-- Custom created Logo in Canva (free to use) -->
                    <img src="images/FinTech_logo.png" 
                    alt="UPDATE COMPANY NAME Logo"
                    loading="lazy">
                    
                    <div class="brand-text">
                        <span class="brand-name"><?php echo htmlspecialchars($brand); ?></span>
                    </div>
                </a>

                <input type="checkbox" id="menu-toggle">
                <label for="menu-toggle" class="hamburger" title="hamburger menu">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    <span class="hamburger-text">MENU</span>
                </label>

                <ul class="nav-links">
                    <?php foreach ($nav_items as $file => [$label, $icon]): ?>
                        <li><a href="<?php echo $file; ?>"
                        <?php echo ($file === $current) ? ' aria-current="page"' : ''; ?>>
                        <i class="fa-solid <?php echo $icon; ?>" aria-hidden="true"></i>
                        <?php echo $label; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </header>
    <body>