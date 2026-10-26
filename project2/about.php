PHP
<?php
$page_title = "About";
include 'includes/header.inc';
include 'includes/navigation.inc';

// --- GET GROUP INFO ---
$group_sql = "SELECT group_name, tutorial_class, photo_path, photo_caption FROM about_group ORDER BY id ASC LIMIT 1";
$group_result = mysqli_query($conn, $group_sql);
$group = ($group_result && mysqli_num_rows($group_result) > 0) ? mysqli_fetch_assoc($group_result) : null;

// --- GET MEMBER INFO ---
$members_sql = "SELECT student_number, name, project1_contribution, quote_original, quote_english, fun_fact FROM about_members ORDER BY student_number ASC";
$members_result = mysqli_query($conn, $members_sql);
$members = ($members_result) ? mysqli_fetch_all($members_result, MYSQLI_ASSOC) : [];
?>

<main>
    <h2 style="color:darkblue">About Our Team</h2>
       
    <h3>Group Information</h3>
    <ul>
        <li>Group Name: <?php echo htmlspecialchars($group['group_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
            <ul>
                <li>Tutorial Class: <?php echo htmlspecialchars($group['tutorial_class'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></li>
            </ul>
        </li>
    </ul>
       
    <h3>Member Contributions</h3>
    <dl>
        <?php foreach ($members as $member): ?>
            <dt>
                <span class="student-number">
                    <?php echo htmlspecialchars($member['student_number'], ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <?php echo htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8'); ?>
            </dt>
            <dd>
                Shared responsibility: CSS File, About Page<br>
                Individual responsibility: <?php echo htmlspecialchars($member['project1_contribution'], ENT_QUOTES, 'UTF-8'); ?>
            </dd>
        <?php endforeach; ?>
    </dl>

    <h3>Team Quotes</h3>
    <?php foreach ($members as $member): ?>
        <p>
            <strong><?php echo htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8'); ?>:</strong>
            "<?php echo htmlspecialchars($member['quote_original'], ENT_QUOTES, 'UTF-8'); ?>"  
            <br>English: "<?php echo htmlspecialchars($member['quote_english'], ENT_QUOTES, 'UTF-8'); ?>"
        </p>
    <?php endforeach; ?>

    <?php if (!empty($group['photo_path'])): ?>
    <h3>Group Photo</h3>
    <figure>
        <img src="<?php echo htmlspecialchars($group['photo_path'], ENT_QUOTES, 'UTF-8'); ?>"
             alt="Group Photo" width="600" height="400">
        <figcaption><?php echo htmlspecialchars($group['photo_caption'] ?? '', ENT_QUOTES, 'UTF-8'); ?></figcaption>
    </figure>
    <?php endif; ?>
       
    <h3>Fun Facts About Our Team</h3>
    <table id="about-table">    
        <caption>Fun Facts About Team Members</caption>
        <thead>
            <tr>
                <th>Name</th>
                <th>Fact</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td><?php echo htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($member['fun_fact'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php require_once 'includes/footer.inc'; ?>
