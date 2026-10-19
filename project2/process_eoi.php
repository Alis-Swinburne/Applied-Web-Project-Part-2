<?php
function sanitise_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
}

// Block direct url access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: apply.php');
    exit();
}

$page_title = "Process EOI";
// Connect to database
require_once 'settings.php';
$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);
if (!$conn) {
    die("Unable to connect to database: " . mysqli_connect_error());
}
include 'includes/header.inc';
include 'includes/navigation.inc';

// Create table if it does not exist
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

// Clean and collect inputs
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $job_ref = sanitise_input($_POST["jobreference"] ?? '');
    $first_name = sanitise_input($_POST["firstname"] ?? '');
    $last_name = sanitise_input($_POST["lastname"] ?? '');
    $dob = sanitise_input($_POST['dob'] ?? '');
    $gender = sanitise_input($_POST['gender'] ?? '');
    $street = sanitise_input($_POST["streetaddress"] ?? '');
    $suburb = sanitise_input($_POST['suburb'] ?? '');
    $state = sanitise_input($_POST['state'] ?? '');
    $postcode = sanitise_input($_POST['postcode'] ?? '');
    $email = sanitise_input($_POST['email'] ?? "");
    $phone = sanitise_input($_POST['phone'] ?? '');
    $skills = isset($_POST["skills"]) ? implode(", ", array_map('sanitise_input', $_POST["skills"])) : "";
    $other_skills = sanitise_input($_POST['otherskills'] ?? "");
}

// Server side validation
$errors = [];

// Job reference - Exactly 6 alphanumeric characters
if (!preg_match("/^[a-zA-Z0-9]{6}$/", $job_ref)) $errors[] = "Job Reference must be 6 alphanumerical characters";

// First name - Maximum 20 alphabetic characters
if (!preg_match("/^[a-zA-Z]{1,20}$/", $first_name)) $errors[] = "First Name must be 1-20 alphabetical characters";

// Last name - Maximum 20 alphabetic characters
if (!preg_match("/^[a-zA-Z]{1,20}$/", $last_name)) $errors[] = "Last Name must be 1-20 alphabetical characters";

// Date of birth - dd/mm/yyyy
if (!preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/[0-9]{4}$/", $dob)) $errors[] = "Date of Birth must be in DD/MM/YYYY format";

// Gender - radio buttons
$valid_genders = ['female', 'male', 'nonbinary', 'prefernot'];
if (!in_array($gender, $valid_genders, true)) $errors[] = "Gender must be selected";

// Street address - Maximum 40 characters
if (strlen($street) <= 0 || strlen($street) > 40) $errors[] = "Street addresss must be 1-40 characters";

// Suburb - Maximum 40 characters
if (strlen($suburb) <= 0 || strlen($suburb) > 40) $errors[] = "Suburb must be 1-40 characters";

// State - dropdown box
$valid_states = ['vic', 'nsw', 'qld', 'nt', 'wa', 'sa', 'tas', 'act'];
if (!in_array($state, $valid_states, true)) $errors[] = "State must be selected";

// Postcode - Exactly 4 digits
if (!preg_match("/^[0-9]{4}$/", $postcode)) $errors[] = "Postcode must be 4 numerical characters";

// Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";

// Phone number - 8–12 digits
if (!preg_match("/^[0-9]{8,12}$/", $phone)) $errors[] = "Phone number must be 8-12 numerical characters";

// Skills - at least 1 checkbox selected
if(empty($skills)) $errors[] = "At least 1 skill must be selected";

if (!empty($errors)) {
    echo "<h1>We couldn't submit your application</h1>";
    echo "<h2>Please fix the following and try again: </h2>";
    foreach ($errors as $error) {
        echo "<p>$error</p>";
        }
} else {
    $stmt = mysqli_prepare($conn, "INSERT INTO eoi (`job_ref`, `first_name`, `last_name`, `dob`, `gender`, `street`, `suburb`, `state`, `postcode`, `email`, `phone`, `skills`, `other_skills`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        mysqli_stmt_bind_param(
        $stmt, "sssssssssssss", $job_ref, $first_name, $last_name, $dob, $gender, $street, $suburb, $state, $postcode, $email, $phone, $skills, $other_skills);
    

        if (mysqli_stmt_execute($stmt)) {
            $eoi_number = mysqli_insert_id($conn);
            echo "<h1>Your application has been submitted</h1>";
            echo "<p>Thank you, " . htmlspecialchars($first_name) . " " . htmlspecialchars($last_name) . ", for your application.</p>";;
            echo "<p> We have successfully received your interest for $job_ref and will contact you soon!</p>";
            echo "<p>Your EOI number is $eoi_number</p>";
        }else{
            echo "<p>Unable to submit your application. Please try again later</p>";
        }
        mysqli_stmt_close($stmt);

    } else {
        echo "<p> Unable to process your application. Please try again later.</p>";
    }   
}

include 'includes/footer.inc';
mysqli_close($conn);
