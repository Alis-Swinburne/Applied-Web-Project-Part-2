<?php 
$page_title = "About";
require_once 'settings.php';

$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);
if (!$conn) {
    die("Unable to connect to database: " . mysqli_connect_error());
}

include 'includes/header.inc';
include 'includes/navigation.inc';

$create_table = "CREATE TABLE IF NOT EXISTS `about` (
    `student_id` CHAR(9) NOT NULL,
    `name` VARCHAR(20) NOT NULL,
    `part1_contribution` TEXT NOT NULL,
    `part2_contribution` TEXT NOT NULL,
    `original_quote` TEXT NOT NULL,
    `translated_quote` TEXT NOT NULL,
    `fun_fact` TEXT NOT NULL,
    PRIMARY KEY (`student_id`)
);";

if (!mysqli_query($conn, $create_table)) {
    error_log("About table setup failed: " . mysqli_error($conn));
    die("Database setup failed: " . mysqli_error($conn));
}

$query = "SELECT student_id, name,
                 part1_contribution, part2_contribution,
                 original_quote, translated_quote, fun_fact
          FROM about";

$result = mysqli_query($conn, $query);

if (!$result) {
    error_log(mysqli_error($conn));
    die("Unable to load team information.");
}

$members = [];

while ($row = mysqli_fetch_assoc($result)) {
    $members[] = $row;
}
?>

<main>
    <h1>About Our Team</h1> 
    <h2>Group Information</h2>
    <ul>
         <li>Group Name: Courageous Coders
            <ul>
                 <li>Tutorial Class: Thursday 12:30-2:30 PM</li>
            </ul>
        </li>
    </ul>
<?php if (empty($members)): ?>
    <p><strong>Team information is currently unavailable.</strong></p>
<?php else: ?>
        
<h2>Member Contributions</h2>

<dl>
    <?php foreach ($members as $member): ?>
        <dt>
            <span class="student-number"><strong><?php echo htmlspecialchars($member['student_id']); ?></strong></span>
            <strong><?php echo htmlspecialchars($member['name']); ?></strong>
        </dt>
        <dd class="contributions">
            <div class="contribution">
                Part 1 Contribution:<br>
                <?php echo htmlspecialchars($member['part1_contribution']); ?>
            </div>
            <div class="contribution">
                Part 2 Contribution: <br>
                <?php echo htmlspecialchars($member['part2_contribution']); ?>
            </div>
        </dd>
    <?php endforeach; ?>
</dl>

<h2>Team Quotes</h2>
<?php foreach ($members as $member): ?>
    <p>
        <strong><?php echo htmlspecialchars($member['name']); ?>:</strong>
        <?php echo htmlspecialchars($member['original_quote']); ?>
        <br>
        English: <?php echo htmlspecialchars($member['translated_quote']); ?>
    </p>
<?php endforeach; ?>

<h2> Group Photo</h2>
<figure>
<img src="images/group_photo.jpeg" alt="Group Photo of Courageous Coders" width="600" height="400">
<figcaption>The Courageous Coders in our Thursday tutorial.</figcaption>
</figure>
        
<h2>Fun Facts About Our Team</h2>

<table id="about-table">
    <caption>Fun Facts About Team Members</caption>
    <tr>
        <th>Name</th>
        <th>Fact</th>
    </tr>
    <?php foreach ($members as $member): ?>
        <tr>
            <td><?php echo htmlspecialchars($member['name']); ?></td>
            <td><?php echo htmlspecialchars($member['fun_fact']); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
</main>

<?php
include 'includes/footer.inc';
mysqli_close($conn);
?>