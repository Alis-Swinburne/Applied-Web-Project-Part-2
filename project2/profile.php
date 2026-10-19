<?php
$page_title = "Profile Management";

require_once 'settings.php';
require_once 'includes/session.inc';

$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);

if (!$conn) {
    die("Unable to connect to databse.");
}

include 'includes/header.inc';
include 'includes/navigation.inc';

$sql = "CREATE TABLE IF NOT EXISTS `user_profile` (
            `id` INT NOT NULL PRIMARY KEY,
            `first_name` varchar(20) DEFAULT NULL,
            `last_name` varchar(20) DEFAULT NULL,
            `dob` char(10) DEFAULT NULL,
            `gender` enum('female','male','nonbinary','prefernot') DEFAULT NULL,
            `street` varchar(40) DEFAULT NULL,
            `suburb` varchar(40) DEFAULT NULL,
            `state` enum('vic','nsw','qld','nt','wa','sa','tas','act') DEFAULT NULL,
            `postcode` char(4) DEFAULT NULL,
            `email` varchar(254) DEFAULT NULL,
            `phone` varchar(12) DEFAULT NULL,
            `skills` text DEFAULT NULL,
            `other_skills` text DEFAULT NULL,
            CONSTRAINT `fk_profile_user`
            FOREIGN KEY (`id`) REFERENCES `user`(`id`)) ENGINE=InnoDB";

if (!mysqli_query($conn, $sql)) {
    error_log("User table setup failed: ". mysqli_error($conn));
    die("Database setup failed: ". mysqli_error($conn));
}

$profile_id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);

if (!$profile_id) {
    die("Could not find profile id.");
}

$sql = "SELECT * FROM user_profile WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $profile_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$profile = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$profile) {
    die("Could not find profile.");
}

$selected_skills = array_filter(array_map('trim', explode(',', $profile['skills'])));

function escape_html($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

?>

<main>
    <h1 style="text-align: center; font-size: 3rem; margin-bottom: 0">Edit Profile</h1>
    <p style="text-align: center;">Update your personal information and professional skills below to make applying easier.</p>

    <form method="POST" action="process_profile_update.php" id="profile-form">

        <fieldset>
            <legend>Personal Information</legend>

            <div class="form-row">
                <div class="form-field">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" required maxlength="20" 
                    pattern="[a-zA-Z]{1,20}" value="<?php echo escape_html($profile['first_name']); ?>">
                </div>

                <div class="form-field">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" required maxlength="20" 
                    pattern="[a-zA-Z]{1,20}" value="<?php echo escape_html($profile['last_name']); ?>">
                </div>
            </div>

            <label for="dob">Date of Birth</label>
            <input type="text" id="dob" name="dob" required placeholder="DD/MM/YYYY" 
            pattern="(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[0-2])/[0-9]{4}"
            value="<?php echo escape_html($profile['dob']); ?>">
        </fieldset>

        <fieldset>
            <legend>Gender</legend>
            <div class="radio-checkbox">
                <label>
                    <input type="radio" name="gender" id="female" value="female" required
                    <?php echo $profile['gender'] === 'female' ? 'checked' : ''; ?>>
                    Female
                </label>

                <label>
                    <input type="radio" name="gender" id="male" value="male" required
                    <?php echo $profile['gender'] === 'male' ? 'checked' : ''; ?>>
                    Male
                </label>

                <label>
                    <input type="radio" name="gender" id="nonbinary" value="nonbinary" required
                    <?php echo $profile['gender'] === 'nonbinary' ? 'checked' : ''; ?>>
                    Non-binary
                </label>

                <label>
                    <input type="radio" name="gender" id="prefernot" value="prefernot" required
                    <?php echo $profile['gender'] === 'prefernot' ? 'checked' : ''; ?>>
                    Prefer not to say
                </label>
            </div>
        </fieldset>

        <fieldset>
            <legend>Address</legend>

            <label for="streetaddress">Street Address</label>
            <input type="text" id="streetaddress" name="streetaddress" required maxlength="40"
            value="<?php echo escape_html($profile['street']); ?>">

            <div class="form-row-three">
                <div class="form-field">
                    <label for="suburb">Suburb / Town</label>
                    <input type="text" id="suburb" name="suburb" required maxlength="40"
                    value="<?php echo escape_html($profile['suburb']); ?>">
                </div>

                <div class="form-field">
                    <label for="state">State</label>
                    <select name="state" id="state" required>
                        <option value="">Please select</option>
                        <?php
                        $states = [
                            'vic' => 'VIC',
                            'nsw' => 'NSW',
                            'qld' => 'QLD',
                            'nt' => 'NT',
                            'wa' => 'WA',
                            'sa' => 'SA',
                            'tas' => 'TAS',
                            'act' => 'ACT'
                        ];

                        foreach ($states as $value => $label) {
                            $selected = ($profile['state'] === $value) ? 'selected' : '';
                            echo '<option value="' . escape_html($value) . '" ' .$selected . '>' . escape_html($label) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-field">
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" name="postcode" required pattern="[0-9]{4}" maxlength="4" 
                    value="<?php echo escape_html($profile['postcode']); ?>">
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Contact Details</legend>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required maxlength="254" placeholder="example@email.com"
            value="<?php echo escape_html($profile['email']); ?>">

            <label for="phone">Phone Number</label>
            <input type="text" name="phone" id="phone" required pattern="[0-9]{8,12}" maxlength="12"
            value="<?php echo escape_html($profile['phone']); ?>">
        </fieldset>

        <fieldset>
            <legend>Professional Skills</legend>
            <div class="radio-checkbox">
                <label>
                    <input type="checkbox" name="skills[]" id="cybersecurity" value="cybersecurity"
                    <?php echo in_array('cybersecurity', $selected_skills, true) ? 'checked' : ''; ?>>
                    Cybersecurity
                </label>

                <label>
                    <input type="checkbox" name="skills[]" id="webdevelopment" value="web_development"
                    <?php echo in_array('web_development', $selected_skills, true) ? 'checked' : ''; ?>>
                    Web Development
                </label>

                <label>
                    <input type="checkbox" name="skills[]" id="sqldatabase" value="sql_database"
                    <?php echo in_array('sql_database', $selected_skills, true) ? 'checked' : ''; ?>>
                    SQL &amp; Databases
                </label>

                <label>
                    <input type="checkbox" name="skills[]" id="cloudcicd" value="cloud_cicd"
                    <?php echo in_array('cloud_cicd', $selected_skills, true) ? 'checked' : ''; ?>>
                    Cloud &amp; CI/CD
                </label>

                <label>
                    <input type="checkbox" name="skills[]" id="problemsolving" value="problem_solving"
                    <?php echo in_array('problem_solving', $selected_skills, true) ? 'checked' : ''; ?>>
                    Problem Solving
                </label>
            </div>

            <label for="otherskills">Other Skills</label>
            <textarea name="otherskills" id="otherskills" rows="5" cols="40" placeholder="List any other relevant skills (optional)">
            <?php echo escape_html($profile['other_skills']); ?></textarea>
        </fieldset>

        <input type="submit" value="Save Changes">
        <input type="reset" value="Reset">

    </form>
</main>

<?php include 'includes/footer.inc'; ?>