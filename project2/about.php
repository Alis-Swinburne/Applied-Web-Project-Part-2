<?php 
$page_title = "About";
include 'includes/header.inc';
include 'includes/navigation.inc';
include 'includes/db_connect.inc'; // make sure this connects to your database

// --- GET GROUP INFO ---
$group_sql = "SELECT * FROM about_group LIMIT 1";
$group_result = mysqli_query($conn, $group_sql);
$group = mysqli_fetch_assoc($group_result);

// --- GET MEMBER INFO ---
$members_sql = "SELECT * FROM about_members";
$members_result = mysqli_query($conn, $members_sql);
?>

<main>
    <h2 style="color:darkblue"> About Our Team</h2>
        
    <h3>Group Information</h3>
    <ul>
        <li>Group Name: <?php echo $group['group_name']; ?>
            <ul>
                <li>Tutorial Class: <?php echo $group['tutorial_class']; ?></li>
            </ul>
        </li>
    </ul>
        
    <h3>Member Contributions</h3>
    <dl>
        <?php while ($member = mysqli_fetch_assoc($members_result)) { ?>
            <dt>
                <span class="student-number">
                    <?php echo $member['student_number']; ?>
                </span>
                <?php echo $member['name']; ?>
            </dt>
            <dd>
                Shared responsibility: CSS File, About Page<br>
                Individual responsibility: <?php echo $member['project1_contribution']; ?>
            </dd>
        <?php } ?>
    </dl>

    <h3>Team Quotes</h3>

    <?php 
    // Reset pointer to loop again for quotes
    mysqli_data_seek($members_result, 0); 
    while ($member = mysqli_fetch_assoc($members_result)) { 
    ?>
        <p>
            <strong><?php echo $member['name']; ?>:</strong> 
            "<?php echo $member['quote_original']; ?>"  
            <br>English: "<?php echo $member['quote_english']; ?>"
        </p>
    <?php } ?>

    <h3> Group Photo</h3>
    <figure>
        <img src="<?php echo $group['photo_path']; ?>" 
             alt="Group Photo" width="600" height="400">
        <figcaption><?php echo $group['photo_caption']; ?></figcaption>
    </figure>
        
    <h3>Fun Facts About Our Team</h3>
        
    <table id="about-table">    
        <caption>Fun Facts About Team Members</caption>
        <tr>
            <th>Name</th>
            <th>Fact</th>
        </tr>

        <?php 
        // Reset pointer again for fun facts
        mysqli_data_seek($members_result, 0); 
        while ($member = mysqli_fetch_assoc($members_result)) { 
        ?>
            <tr>
                <td><?php echo $member['name']; ?></td>
                <td><?php echo $member['fun_fact']; ?></td>
            </tr>
        <?php } ?>
    </table>
</main>

<?php require_once 'includes/footer.inc'; ?>
>

