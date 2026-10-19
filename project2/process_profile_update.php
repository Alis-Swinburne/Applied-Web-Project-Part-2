<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profile.php');
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
if (!$id || $id < 1) {
    mysqli_close($conn);
    die("Please log in to update your profile.");
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

if (!$id || $id < 1 || 
    !preg_match('/\A[a-zA-Z]{1,20}\z/', $first_name) ||
    !preg_match('/\A[a-zA-Z]{1,20}\z/', $last_name) ||
    !in_array($gender, $allowed_genders, true) ||
    $street === '' || strlen($street) > 40 ||
    $suburb === '' || strlen($suburb) > 40 ||
    !in_array($state, $allowed_states, true) ||
    !preg_match('/\A[0-9]{4}\z/', $postcode) ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($email) > 254 ||
    !preg_match('/\A[0-9]{8,12}\z/', $phone) ||
    !is_array($skills) ||
    count(array_diff($skills, $allowed_skills)) > 0
) {
    mysqli_close($conn);
    die("Invalid input!");
}

$date = DateTime::createFromFormat('!d/m/Y', $dob);
$date_errors = DateTime::getLastErrors();

if (!$date || ($date_errors !== false &&
    ($date_errors['warning_count'] > 0 ||
    $date_errors['error_count'] > 0)) ||
    $date->format('d/m/Y') !== $dob
) {
    mysqli_close($conn);
    die("Invalid Date of Birth.");
}

$skills = array_unique($skills);
$skills_string = implode(', ', $skills);

$sql = "UPDATE user_profile SET first_name = ?, last_name = ?,
        dob = ?, gender = ?, street = ?, suburb = ?, state = ?,
        postcode = ?, email = ?, phone = ?, skills = ?, other_skills = ? WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    error_log("Failed to update profile: " . mysqli_error($conn));
    mysqli_close($conn);
    die("Unable to update profile.");
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
        mysqli_close($conn);
        header('Location: profile.php?id=' . $id . '&updated=1');
        exit();
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>

