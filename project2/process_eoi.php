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
            `dob` DATE NOT NULL,
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
            PRIMARY KEY (`eoi_number`),
            FOREIGN KEY (`job_ref`) REFERENCES `Jobs`(`Job_Code`) ON UPDATE CASCADE
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

// Job reference - Exactly 6 alphanumeric characters and exists on jobs table
if (!preg_match("/^[a-zA-Z0-9]{6}$/", $job_ref)) $errors[] = "Job Reference must be 6 alphanumerical characters";
$stmt = mysqli_prepare($conn, "SELECT * FROM Jobs WHERE Job_Code = ?");
mysqli_stmt_bind_param($stmt, "s", $job_ref);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
 if (mysqli_num_rows($result) === 0) {
            $errors[] = "Job Reference does not currently exist";
        }

// First name - Maximum 20 alphabetic characters
if (!preg_match("/^[a-zA-Z]{1,20}$/", $first_name)) $errors[] = "First name must be 1-20 alphabetical characters";

// Last name - Maximum 20 alphabetic characters
if (!preg_match("/^[a-zA-Z]{1,20}$/", $last_name)) $errors[] = "Last name must be 1-20 alphabetical characters";

// Date of birth - dd/mm/yyyy, age between 18-80
if (!preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/[0-9]{4}$/", $dob)) {
    $errors[] = "Date of Birth must be in DD/MM/YYYY format";
} else {
    $date = DateTime::createFromFormat('!d/m/Y', $dob);
    $date_errors = DateTime::getLastErrors();

    if (
        !$date ||
        ($date_errors !== false &&
            ($date_errors['warning_count'] > 0 ||
             $date_errors['error_count'] > 0)) ||
        $date->format('d/m/Y') !== $dob
    ) {
        $errors[] = "Please enter a valid Date of Birth in DD/MM/YYYY format";
    } else {
        $today = new DateTime('today');
        $min_dob = (clone $today)->modify('-80 years');
        $max_dob = (clone $today)->modify('-18 years');

        if ($date < $min_dob || $date > $max_dob) {
            $errors[] = "Applicants must be between 18 and 80 years old";
        } else {
            // Convert to SQL date format
            $dob = $date->format('d/m/Y');
        }
    }
}

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

// Postcode - Exactly 4 digits and matches state
if (!preg_match("/^[0-9]{4}$/", $postcode)) {
    $errors[] = "Postcode must be 4 numerical characters";
} else {
    $postcode_int = $postcode;
    $valid_postcode = false;

    switch (($state)) {
        case 'nsw':
            $valid_postcode =
                ($postcode_int >= 2000 && $postcode_int <= 2599) ||
                ($postcode_int >= 2619 && $postcode_int <= 2899) ||
                ($postcode_int >= 2921 && $postcode_int <= 2999);
            break;

        case 'act':
            $valid_postcode =
                ($postcode_int >= 2600 && $postcode_int <= 2618) ||
                ($postcode_int >= 2900 && $postcode_int <= 2920);
            break;

        case 'vic':
            $valid_postcode =
                ($postcode_int >= 3000 && $postcode_int <= 3996);
            break;

        case 'qld':
            $valid_postcode =
                ($postcode_int >= 4000 && $postcode_int <= 4999);
            break;

        case 'sa':
            $valid_postcode =
                ($postcode_int >= 5000 && $postcode_int <= 5799);
            break;

        case 'wa':
            $valid_postcode =
                ($postcode_int >= 6000 && $postcode_int <= 6797);
            break;

        case 'tas':
            $valid_postcode =
                ($postcode_int >= 7000 && $postcode_int <= 7799);
            break;

        case 'nt':
            $valid_postcode =
                ($postcode_int >= 800 && $postcode_int <= 899);
            break;
    }

    if (in_array($state, $valid_states, true) && !$valid_postcode) {
        $errors[] = "Postcode does not match the selected state";
    }
}

// Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";

// Phone number - 8–12 digits
if (!preg_match("/^[0-9]{8,12}$/", $phone)) $errors[] = "Phone number must be 8-12 numerical characters";

// Skills - at least 1 checkbox selected
if(empty($skills)) $errors[] = "At least 1 skill must be selected";

// Only allow user to apply to a job once, checks via email
if (empty($errors)) {
    $check_stmt = mysqli_prepare($conn, "SELECT * FROM eoi WHERE job_ref = ? AND email = ?");
    mysqli_stmt_bind_param($check_stmt, "ss", $job_ref,$email);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) $errors[] = "You have already submitted an application for " . htmlspecialchars($job_ref);
    mysqli_stmt_close($check_stmt);
}

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
            echo "<p>Thank you, " . htmlspecialchars($first_name) . " " . htmlspecialchars($last_name) . ", for your application.</p>";
            echo "<p> We have successfully received your interest for " . htmlspecialchars($job_ref) . " and will contact you soon!</p>";
            echo "<p>Your EOI number is " . htmlspecialchars($eoi_number) . "</p>";
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
