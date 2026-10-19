<?php
function sanitise_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
}
    
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

$query = "CREATE TABLE IF NOT EXISTS `eoi` (
            `eoi_number` int(11) NOT NULL AUTO_INCREMENT,
            `job_ref` char(6) NOT NULL,
            `first_name` varchar(20) NOT NULL,
            `last_name` varchar(20) NOT NULL,
            `dob` char(10) NOT NULL,
            `gender` enum('female','male','nonbinary','prefernot') NOT NULL,
            `street` varchar(40) NOT NULL,
            `suburb` varchar(40) NOT NULL,
            `state` enum('vic','nsw','qld','nt','wa','sa','tas','act') NOT NULL,
            `postcode` char(4) NOT NULL,
            `email` varchar(254) NOT NULL,
            `phone` varchar(12) NOT NULL,
            `skills` text NOT NULL,
            `other_skills` text DEFAULT NULL,
            `status` enum('new','current','final') NOT NULL DEFAULT 'new',
            PRIMARY KEY (`eoi_number`)
            )";

$result = mysqli_query($conn, $query);

include 'includes/footer.inc';

mysqli_close($conn);
