<?php 
require_once 'includes/session.inc';
require_once 'settings.php';
require_once 'verify_access.php';

$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);

$has_access = verify_access($conn);

$page_title = "Manage";
include 'includes/header.inc';
include 'includes/navigation.inc';
?>
    
<main>
    <?php if ($has_access): ?>
        <h2>Manage</h2>
        <p>Welcome admin!</>
 
        <!-- management tools code here! check Jira or assignment requirements for list -->


    <?php else: ?>
        <?php 
        // Immediately redirects back to index.php if user doesn't have admin access
        header("Location: index.php");
        exit();
        ?>
    <?php endif; ?>
</main>

<?php include 'includes/footer.inc'; ?>
