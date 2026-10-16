<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: apply.php');
    exit();
}
$page_title = "Process EOI";
require_once 'settings.php';
$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);
if (!$conn) {
    die("Unable to connect to database: " . mysqli_connect_error());
}
include 'includes/header.inc';
include 'includes/navigation.inc';



require_once 'includes/footer.inc';