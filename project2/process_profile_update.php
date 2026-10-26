<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

require_once 'settings.php';
require_once 'includes/session.inc';

$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);

if (!$conn) {
    die("Unable to connect to database.");
}

function sanitise_input($data) {
    return trim($data);
}

$id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id === null || $id < 1) {
    mysqli_close($conn);
    $_SESSION['profile_error'] = "Please login to update your profile.";
    header('Location: profile.php');
    exit();
}

$first_name = sanitise_input($_POST['firstname'] ?? '');
$last_name = sanitise_input($_POST['lastname'] ?? '');
$dob = sanitise_input($_POST['dob'] ?? '');
$gender = sanitise_input($_POST['gender'] ?? '');
$street = sanitise_input($_POST['streetaddress'] ?? '');
$suburb = sanitise_input($_POST['suburb'] ?? '');
$state = sanitise_input($_POST['state'] ?? '');
$postcode = sanitise_input($_POST['postcode'] ?? '');
$email = sanitise_input($_POST['email'] ?? '');
$phone = sanitise_input($_POST['phone'] ?? '');
$other_skills = sanitise_input($_POST['otherskills'] ?? '');

$skills = $_POST['skills'] ?? [];

$allowed_genders = ['female', 'male', 'nonbinary', 'prefernot'];
$allowed_states = ['vic', 'nsw', 'qld', 'nt', 'wa', 'sa', 'tas', 'act'];
$allowed_skills = ['cybersecurity', 'web_development', 'sql_database', 'cloud_cicd', 'problem_solving'];

// Server-side validation (more clean than before)
$valid = preg_match('/\A[a-zA-Z]{1,20}\z/', $first_name) && // First name matches criteria
        preg_match('/\A[a-zA-Z]{1,20}\z/', $last_name) && // Last name matches criteria
        in_array($gender, $allowed_genders, true) && // Gender is in allowed genders
        $street !== '' && strlen($street) <= 40 && // Street is not empty and is less than or equal to 40 characters
        $suburb !== '' && strlen($suburb) <= 40 && // Suburb is not empty and is less than or equal to 40 characters
        in_array($state, $allowed_states, true) && // State is in allowed states
        preg_match('/\A[0-9]{4}\z/', $postcode) && // Postcode matches criteria
        filter_var($email, FILTER_VALIDATE_EMAIL) !== false && // Email is valid 
        strlen($email) <= 254 && // Email length is less than or equal to 254
        preg_match('/\A[0-9]{8,12}\z/', $phone) && // Phone matches criteria
        is_array($skills) && // Skills is an array
        count(array_diff($skills, $allowed_skills)) === 0 && // Skills and allowed skills are equal
        is_string($other_skills) && // Submitted skills is string
        strlen($other_skills) <= 2000; // Skills length less than or equal to 2000

$valid_postcode = false;

// Took this from process_eoi.php!
if (preg_match('/\A[0-9]{4}\z/', $postcode)) {
    $postcode_number = (int) $postcode;

    switch ($state) {
        case 'nsw':
            $valid_postcode =
                ($postcode >= 2000 && $postcode <= 2599) ||
                ($postcode >= 2619 && $postcode <= 2899) ||
                ($postcode >= 2921 && $postcode <= 2999);
            break;

        case 'act':
            $valid_postcode =
                ($postcode >= 2600 && $postcode <= 2618) ||
                ($postcode >= 2900 && $postcode <= 2920);
            break;

        case 'vic':
            $valid_postcode =
                ($postcode >= 3000 && $postcode <= 3996);
            break;

        case 'qld':
            $valid_postcode =
                ($postcode >= 4000 && $postcode <= 4999);
            break;

        case 'sa':
            $valid_postcode =
                ($postcode >= 5000 && $postcode <= 5799);
            break;

        case 'wa':
            $valid_postcode =
                ($postcode >= 6000 && $postcode <= 6797);
            break;

        case 'tas':
            $valid_postcode =
                ($postcode >= 7000 && $postcode <= 7799);
            break;

        case 'nt':
            $valid_postcode =
                ($postcode >= 800 && $postcode <= 899);
            break;
    }
}

if (!$valid_postcode) {
    $_SESSION['profile_error'] = "Postcode does not match the selected state.";
    mysqli_close($conn);
    header('Location: profile.php');
    exit();
}

if (!$valid) {
    $_SESSION['profile_error'] = "Invalid input, please check the information you entered matches the listed criteria.";
    mysqli_close($conn);
    header('Location: profile.php');
    exit();
}

$date = DateTime::createFromFormat('!d/m/Y', $dob);
$date_errors = DateTime::getLastErrors();

if (!$date || ($date_errors !== false &&
    ($date_errors['warning_count'] > 0 ||
    $date_errors['error_count'] > 0)) ||
    $date->format('d/m/Y') !== $dob
) {
    $_SESSION['profile_error'] = "Date of Birth is invalid.";
    mysqli_close($conn);
    header('Location: profile.php');
    exit();
}

$today = new DateTime('today');
$min_dob = (clone $today)->modify('-80 years');
$max_dob = (clone $today)->modify('-18 years');

if ($date < $min_dob || $date > $max_dob) {
    $_SESSION['profile_error'] = "You must be between 18 and 80 years old.";
    mysqli_close($conn);
    header('Location: profile.php');
    exit();
}

$dob = $date->format('Y/m/d');

$skills = array_unique($skills);
$skills_string = implode(', ', $skills);

$sql = "UPDATE user_profile SET first_name = ?, last_name = ?,
        dob = ?, gender = ?, street = ?, suburb = ?, state = ?,
        postcode = ?, email = ?, phone = ?, skills = ?, other_skills = ? WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    error_log("Failed to update profile: " . mysqli_error($conn));
    $_SESSION['profile_error'] = "Could not update profile, try again.";
    mysqli_close($conn);
    header('Location: profile.php');
    exit();
}

mysqli_stmt_bind_param(
    $stmt,
    "ssssssssssssi",
    $first_name,
    $last_name,
    $dob,
    $gender,
    $street,
    $suburb,
    $state,
    $postcode,
    $email,
    $phone,
    $skills_string,
    $other_skills,
    $id
);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) >= 0) {
        mysqli_stmt_close($stmt);
        $_SESSION['profile_success'] = "Successfully updated your details.";
        mysqli_close($conn);
        header('Location: profile.php');
        exit();
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);


