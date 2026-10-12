<?php

$current = $nav_current ?? basename($_SERVER['SCRIPT_NAME']); // Keeps a track of the current script/page
$brand = $site_name ?? "UPDATE COMPANY NAME";

$nav_items = ['index.php => ['HOME', 'fa-house'], 'jobs.php' => ['JOBS', 'fa-suitcase'], 'apply.php' => ['APPLY', 'fa-pen-to-square'], 'about.php' => ['ABOUT', 'fa-circle-info'], ];

// TODO: Add login nav items below when I get to it depending on if the user is logged in
>

<header>
            <!-- Contains the company logo, name, and a navigation menu. -->
            <nav aria-label="Main Navigation">
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