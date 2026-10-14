<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Juliette Grove, Ada Yong, Andrea McPadden, Alisla Foley">
    <meta name="keywords" content="FinTech, financial technology, digital payments, online payments, personal finance, 
                budgeting tools, money management, account management, financial education, financial security">
    <meta name="description" content="Secure and user-friendly financial technology solutions for digital payments, 
                budgeting, account management, and financial education.">
    <link rel="stylesheet" href="styles/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&family=Unica+One&display=swap">
    <script src="https://kit.fontawesome.com/8b43896124.js" crossorigin="anonymous"></script>
    <title>FinTech</title>

    <!-- Embedded CSS -->
    <style>
            body {
            background-color: #e1e6ed;
            }
    </style>
</head>

<body>
    <header>
        <!-- Contains the company logo, name, and a navigation menu. -->
        <nav>
            <a href="index.html" class="brand">
                <img src="images/FinTech_logo.png"
                alt="FinTech Logo"
                title="FinTech Logo"
                loading="lazy">
                
                <div class="brand-text">
                    <h1>FinTech</h1>
                    <p>Secure your money, secure your future.</p>
                </div>
            </a>

            <input type="checkbox" id="menu-toggle">
            <label for="menu-toggle" class="hamburger">
                <span class="sr-only">Toggle navigation menu</span>
                <i class="fa-solid fa-bars"></i>
            </label>

            <ul class="nav-links">
                <li><a href="index.html"><i class="fa-solid fa-house"></i> HOME</a></li>
                <li><a href="jobs.html"><i class="fa-solid fa-suitcase"></i> JOBS</a></li>
                <li><a href="apply.html"><i class="fa-solid fa-pen-to-square"></i> APPLY</a></li>
                <li><a href="about.html"><i class="fa-solid fa-circle-info"></i> ABOUT</a></li>
            </ul>
        </nav>

    </header>

    <main>

        <!-- Inline CSS -->
        <h2 style="text-align: center; font-size: 3rem; margin-bottom: 0">Job Application Form</h2>
        <p style="text-align: center;">Please fill all required fields to submit your application</p>

        <form method="post" action="https://mercury.swin.edu.au/it000000/formtest.php" id="job-form">

            <!-- Job reference number -->
            <fieldset>
                <legend>Position</legend>
                <label for="jobreference">Job Reference Number</label>
                <input type="text" id="jobreference" name="jobreference" required="required" placeholder="e.g. ABC123"
                pattern="^[a-zA-Z0-9]{6}$" maxlength="6"> <!-- Exactly 6 alphanumeric characters -->
            </fieldset>

            <!-- Personal information - first name, last name, birthday, gender -->
            <fieldset>
                <legend>Personal Information</legend>

                <!-- First Name -->
                <div class="form-row">
                    <div class="form-field">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" required="required"
                        pattern="^[a-zA-Z]{1,20}$" maxlength="20"> <!-- Maximum 20 alphabetic characters -->
                    </div>

                    <!-- Last Name -->
                    <div class="form-field">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" required="required"
                        pattern="^[a-zA-Z]{1,20}$" maxlength="20"> <!-- Maximum 20 alphabetic characters -->
                    </div>
                </div>

                    <!-- Date of Birth -->
                    <label for="dob">Date of Birth</label>
                    <input type="text" id="dob" name="dob" required="required" placeholder="DD/MM/YYYY"
                    pattern="^(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[0-2])/[0-9]{4}$"> <!-- dd/mm/yyyy -->
            </fieldset>


                <!-- Gender -->
                <fieldset>
                    <legend>Gender</legend>
                        <div class="radio-checkbox">
                            <label>
                                <input type="radio" name="gender" id="female" value="female" required="required">
                                Female
                            </label>

                            <label>
                                <input type="radio" name="gender" id="male" value="male">
                                Male
                            </label>

                            <label>
                                <input type="radio" name="gender" id="nonbinary" value="non-binary">
                                Non-binary
                            </label>

                            <label>
                                <input type="radio" name="gender" id="prefernot" value="prefernot">
                                Prefer not to say
                            </label>
                        </div>
                </fieldset>

            <!-- Address - street, suburb, state, postcode -->
             <fieldset>
                <legend>Address</legend>

                <!-- Street Address -->
                <label for="streetaddress">Street Address</label>
                <input type="text" id="streetaddress" name="streetaddress" required="required"
                maxlength="40"> <!-- Maximum 40 characters -->

                <!-- Suburb / Town -->
                <div class="form-row-three">
                    <div class="form-field">
                        <label for="suburb">Suburb / Town</label>
                        <input type="text" id="suburb" name="suburb"  required="required"
                        maxlength="40"> <!-- Maximum 40 characters -->
                    </div>


                    <!-- State -->
                    <div class="form-field">
                        <label for="state">State</label>
                            <select name="state" id="state" required="required">
                                <option value="">Please select</option>
                                <option value="vic">VIC</option>
                                <option value="nsw">NSW</option>
                                <option value="qld">QLD</option>
                                <option value="nt">NT</option>
                                <option value="wa">WA</option>
                                <option value="sa">SA</option>
                                <option value="tas">TAS</option>
                                <option value="act">ACT</option>
                        </select>
                    </div>

                    <!-- Postcode -->
                    <div class="form-field">
                        <label for="postcode">Postcode</label>
                        <input type="text" id="postcode" name="postcode" required="required" 
                        pattern="^[0-9]{4}$" maxlength="4"> <!-- Exactly 4 digits -->
                    </div>
                </div>
             </fieldset>

             <!-- Contact details - phone number, email -->
             <fieldset>
                <legend>Contact Details</legend>

                <!-- Email -->
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required="required" placeholder="example@email.com"> <!-- Valid email format -->

                <!-- Phone number -->
                <label for="phone">Phone Number</label>
                <input type="text" name="phone" id="phone" required="required"
                pattern="^[0-9]{8,12}$" maxlength="12"> <!-- 8–12 digits -->
             </fieldset>

             <!-- Skills -->
              <fieldset>
                <legend>Professional Skills</legend>

                 <!-- Skills list -->
                <div class="radio-checkbox">
                    <label>
                        <input type="checkbox" name="skills[]" id="cybersecurity" value="cybersecurity">
                        Cybersecurity
                    </label>

                    <label>
                        <input type="checkbox" name="skills[]" id="webdevelopment" value="web_development">
                        Web Development
                    </label>

                    <label>
                        <input type="checkbox" name="skills[]" id="sqldatabase" value="sql_database">
                        SQL & Databases
                    </label>

                    <label>
                        <input type="checkbox" name="skills[]" id="cloudcicd" value="cloud_cicd">
                        Cloud & CI/CD
                    </label>

                    <label>
                        <input type="checkbox" name="skills[]" id="problemsolving" value="problem_solving">
                        Problem Solving
                    </label>
                </div>

                <!-- Other skills -->
                <label for="otherskills">Other Skills</label>
                <textarea name="otherskills" id="otherskills" rows="5" cols="40" placeholder="List any other relevant skills (optional)"></textarea>
            </fieldset>

            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
        </form>
    </main>

    <footer>
        <!-- Contains the Jira project link, GitHub repository link, and a fake email link for the company. -->
        <nav id="navFooter">
            <ul>
                <li><a href="https://alisla-foley-cos10026.atlassian.net/jira/software/projects/SCRUM/summary">
                    <i class="fa-brands fa-jira"></i> JIRA PROJECT</a></li>
                <li><a href="https://github.com/Alis-Swinburne/Applied-Web-Project"><i class="fa-brands fa-github"></i> GITHUB</a></li>
                <li><a href="mailto:fintech_customer_support@fintech.com.au"><i class="fa-solid fa-envelope-open"></i> CONTACT US</a></li>
            </ul>
        </nav>
    </footer>
</body>
</html>