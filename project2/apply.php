<?php 
$page_title = "Apply";
include 'includes/header.inc';
include 'includes/navigation.inc';

// ALIS - added the following code to get values of user_profile
require_once 'settings.php';
require_once 'includes/session.inc';

$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);

if (!$conn) {
    die("Unable to connect to database.");
}

$profile_id = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
$profile = null;

if ($profile_id !== false && $profile_id !== null && $profile_id !== 1) {
    $sql = "SELECT * FROM user_profile WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $profile_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $profile = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if ($profile) {
            $selected_skills = array_filter(array_map('trim', explode(',', $profile['skills'])));
        }
    }
}

function escape_html($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// ALIS - end of profile fetch

?>

<main>

    <h1 class="text-center">Job Application Form</h1>
    <p class="text-center">Please fill in all required fields marked with an asterisk <span class="asterisk">*</span> to submit your application</p>

    <form method="post" action="process_eoi.php" id="job-form" novalidate="novalidate">

        <!-- Job reference number -->
        <fieldset>
            <legend>Position</legend>
            <label for="jobreference" class="required">Job Reference Number</label>
            <input type="text" id="jobreference" name="jobreference" required="required" placeholder="e.g. ABC123"
            pattern="^[a-zA-Z0-9]{6}$" maxlength="6"  value="<?= htmlspecialchars($_GET['ref'] ?? '') ?>"> <!-- Exactly 6 alphanumeric characters -->
        </fieldset>

        <!-- Personal information - first name, last name, birthday, gender -->
        <fieldset>
            <legend>Personal Information</legend>

            <!-- First Name -->
            <div class="form-row">
                <div class="form-field">
                    <label for="firstname" class="required">First Name</label>
                    <input type="text" id="firstname" name="firstname" required="required"
                    pattern="^[a-zA-Z]{1,20}$" maxlength="20" value="<?php if($profile !== null){ echo escape_html($profile['first_name']); } ?>"> <!-- Maximum 20 alphabetic characters -->
                </div>

                <!-- Last Name -->
                <div class="form-field">
                    <label for="lastname" class="required">Last Name</label>
                    <input type="text" id="lastname" name="lastname" required="required"
                    pattern="^[a-zA-Z]{1,20}$" maxlength="20" value="<?php  if($profile !== null){ echo escape_html($profile['last_name']); } ?>"> <!-- Maximum 20 alphabetic characters -->
                </div>
            </div>

                <!-- Date of Birth -->
                <label for="dob" class="required">Date of Birth</label>
                <input type="text" id="dob" name="dob" required="required" placeholder="DD/MM/YYYY"
                pattern="^(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[0-2])/[0-9]{4}$" value="<?php echo (!empty($profile['dob']) && $profile['dob'] !== '0000-00-00') ? escape_html(date('d/m/Y', strtotime($profile['dob']))) : ''; ?>"> <!-- dd/mm/yyyy -->
        </fieldset>


            <!-- Gender -->
            <fieldset>
                <legend class="required">Gender</legend>
                    <div class="radio-checkbox">
                        <label>
                            <input type="radio" name="gender" id="female" value="female" required="required" <?php if($profile !== null){ echo $profile['gender'] === 'female' ? 'checked' : ''; } ?>>
                            Female
                        </label>

                        <label>
                            <input type="radio" name="gender" id="male" value="male" <?php  if($profile !== null){ echo $profile['gender'] === 'male' ? 'checked' : ''; } ?>>
                            Male
                        </label>

                        <label>
                            <input type="radio" name="gender" id="nonbinary" value="nonbinary" <?php  if($profile !== null){ echo $profile['gender'] === 'nonbinary' ? 'checked' : ''; } ?>>
                            Non-binary
                        </label>

                        <label>
                            <input type="radio" name="gender" id="prefernot" value="prefernot" <?php  if($profile !== null){ echo $profile['gender'] === 'prefernot' ? 'checked' : ''; } ?>>
                            Prefer not to say
                        </label>
                    </div>
            </fieldset>

        <!-- Address - street, suburb, state, postcode -->
            <fieldset>
            <legend>Address</legend>

            <!-- Street Address -->
            <label for="streetaddress" class="required">Street Address</label>
            <input type="text" id="streetaddress" name="streetaddress" required="required"
            maxlength="40" value="<?php if($profile !== null){ echo escape_html($profile['street']); } ?>"> <!-- Maximum 40 characters -->

            <!-- Suburb / Town -->
            <div class="form-row-three">
                <div class="form-field">
                    <label for="suburb" class="required">Suburb / Town</label>
                    <input type="text" id="suburb" name="suburb"  required="required"
                    maxlength="40" value="<?php if($profile !== null){ echo escape_html($profile['suburb']); } ?>"> <!-- Maximum 40 characters -->
                </div>


                <!-- State -->
                <div class="form-field">
                    <label for="state" class="required">State</label>
                    <select name="state" id="state" required="required">
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
                            foreach ($states as $value => $label) 
                                { $selected = ($profile !== null && isset($profile['state']) && $profile['state'] === $value) ? 'selected' : '';
                                echo '<option value="' . escape_html($value) . '" ' .$selected . '>' . escape_html($label) . '</option>';
                            }
                        ?>
                    </select>
                </div>

                <!-- Postcode -->
                <div class="form-field">
                    <label for="postcode" class="required">Postcode</label>
                    <input type="text" id="postcode" name="postcode" required="required" 
                    pattern="^[0-9]{4}$" maxlength="4" value="<?php if($profile !== null){ echo escape_html($profile['postcode']); } ?>"> <!-- Exactly 4 digits -->
                </div>
            </div>
            </fieldset>

            <!-- Contact details - phone number, email -->
            <fieldset>
            <legend>Contact Details</legend>

            <!-- Email -->
            <label for="email" class="required">Email</label>
            <input type="email" name="email" id="email" required="required" placeholder="example@email.com" value="<?php if($profile !== null){ echo escape_html($profile['email']); } ?>"> <!-- Valid email format -->

            <!-- Phone number -->
            <label for="phone" class="required">Phone Number</label>
            <input type="text" name="phone" id="phone" required="required"
            pattern="^[0-9]{8,12}$" maxlength="12" value="<?php if($profile !== null){ echo escape_html($profile['phone']); } ?>"> <!-- 8–12 digits -->
            </fieldset>

            <!-- Skills -->
            <fieldset>
            <legend class="required">Professional Skills</legend>

                <!-- Skills list -->
            <div class="radio-checkbox">
                <label>
                    <input type="checkbox" name="skills[]" id="cybersecurity" value="cybersecurity" <?php if($profile !== null){ echo in_array('cybersecurity', $selected_skills, true) ? 'checked' : ''; } ?>>
                    Cybersecurity
                </label>

                <label>
                    <input type="checkbox" name="skills[]" id="webdevelopment" value="web_development" <?php if($profile !== null){ echo in_array('web_development', $selected_skills, true) ? 'checked' : ''; } ?>>
                    Web Development
                </label>

                <label>
                    <input type="checkbox" name="skills[]" id="sqldatabase" value="sql_database" <?php if($profile !== null){ echo in_array('sql_database', $selected_skills, true) ? 'checked' : ''; } ?>>
                    SQL & Databases
                </label>

                <label>
                    <input type="checkbox" name="skills[]" id="cloudcicd" value="cloud_cicd" <?php if($profile !== null){ echo in_array('cloud_cicd', $selected_skills, true) ? 'checked' : ''; } ?>>
                    Cloud & CI/CD
                </label>

                <label>
                    <input type="checkbox" name="skills[]" id="problemsolving" value="problem_solving" <?php if($profile !== null){ echo in_array('problem_solving', $selected_skills, true) ? 'checked' : ''; } ?>>
                    Problem Solving
                </label>
            </div>

            <!-- Other skills -->
            <label for="otherskills">Other Skills</label>
            <textarea name="otherskills" id="otherskills" rows="5" cols="40" placeholder="List any other relevant skills (optional)"><?php if($profile !== null){ echo escape_html($profile['other_skills']); } ?></textarea>
        </fieldset>

        <input type="submit" value="Submit">
        <input type="reset" value="Reset">
    </form>
</main>

<?php include 'includes/footer.inc'; ?>