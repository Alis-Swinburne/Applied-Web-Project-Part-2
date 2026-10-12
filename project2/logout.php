<?php
require_once 'includes/session.inc';
session_destroy();
header("Location: index.php");
exit();
?>