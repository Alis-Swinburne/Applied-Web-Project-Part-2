<?php 
$page_title = "Jobs";
include 'includes/header.inc';
include 'includes/navigation.inc';

// Database connection details. Copy these from the file that already connects
// to your database (e.g. settings.php or login.php) if they differ.
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "finova_db";   

mysqli_report(MYSQLI_REPORT_OFF);  // stops harmless errors (like the setup file's duplicate INSERTs) crashing the page
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("<p>Database connection failed: " . htmlspecialchars(mysqli_connect_error()) . "</p>");
}
mysqli_set_charset($conn, "utf8mb4");

// database_setup.inc must be included AFTER $conn exists (it creates the tables if missing)
require_once 'includes/database_setup.inc';

$table = "Jobs";   // matches the table created in database_setup.inc

// Show salary nicely if it's a number, otherwise print it as stored
function format_salary($value) {
    $clean = str_replace([',', '$'], '', (string)$value);
    if (is_numeric($clean)) {
        return '$' . number_format((float)$clean) . ' per annum + Super';
    }
    return htmlspecialchars($value);
}

// Turn a newline-separated column into an array of non-empty lines
function split_lines($text) {
    return array_filter(array_map('trim', explode("\n", (string)$text)), 'strlen');
}

// Read the search term from the URL (?q=...)
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($search !== '') {
    $like = '%' . $search . '%';
    $stmt = $conn->prepare(
        "SELECT * FROM `$table`
         WHERE Job_Title LIKE ? OR Job_Code LIKE ? OR Description LIKE ?
         ORDER BY Job_Title"
    );
    $stmt->bind_param("sss", $like, $like, $like);
} else {
    $stmt = $conn->prepare("SELECT * FROM `$table` ORDER BY Job_Title");
}
if (!$stmt) {
    die("<p>Query failed: " . htmlspecialchars($conn->error) . "</p>");
}
$stmt->execute();
$jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<main>
    <section class="intro-section">
        <h2 style="color: black; text-align:left">Current Career Opportunities</h2> 
        <p>Join the Finova team &amp; shape the future of digital banking and personal finance technology</p>
        
        <!-- Perks section moved here: inside the intro, above the directory -->
        <aside class="perks-sidebar">
            <h3>Why work at Finova?</h3>
            <p>We believe our employees do their best work when they feel supported, challenged and rewarded.</p>
            <ul>
                <li><strong>Flexible Work:</strong> Hybrid and fully remote work options.</li>
                <li><strong>Learning &amp; Development:</strong> $2000 annual budget per employee for courses and certifications.</li>
                <li><strong>Health &amp; Wellness:</strong> Comprehensive health insurance and complimentary mental health days.</li>
            </ul>
        </aside>

        <form class="job-search" action="jobs.php" method="get">
            <label for="q">Search jobs:</label>
            <input type="search" id="q" name="q" placeholder="Title, keyword or job code"
                   value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
            <?php if ($search !== ''): ?>
                <a href="jobs.php">Clear</a>
            <?php endif; ?>
        </form>

        <!-- Quick Navigation Directory -->
        <section class="job-directory">
            <h3>Open Positions Overview</h3>

            <?php if ($search !== ''): ?>
                <p><?php echo count($jobs); ?> result(s) for &ldquo;<?php echo htmlspecialchars($search); ?>&rdquo;</p>
            <?php endif; ?>

            <?php if (count($jobs) > 0): ?>
                <p>Click on a job title below to jump directly to the position details:</p>
                <ul>
                    <?php foreach ($jobs as $job): ?>
                        <li><a href="#<?php echo htmlspecialchars($job['Job_Code']); ?>">
                            <?php echo htmlspecialchars($job['Job_Title']); ?>
                            (Ref: <?php echo htmlspecialchars($job['Job_Code']); ?>)</a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </section>

    <?php if (count($jobs) === 0): ?>
        <p class="no-results">No positions matched your search. <a href="jobs.php">View all positions</a></p>
    <?php endif; ?>

    <?php foreach ($jobs as $job): 
        $code = htmlspecialchars($job['Job_Code']);
        // Optional columns; blank if they don't exist
        $reporting   = $job['Reporting_Line'] ?? '';
        $resp        = split_lines($job['Responsibilities'] ?? '');
        $essential   = split_lines($job['Essential_Requirements'] ?? '');
        $preferable  = split_lines($job['Preferable_Requirements'] ?? '');
    ?>
    <article class="job-card" id="<?php echo $code; ?>">
        <header>
            <h3><?php echo htmlspecialchars($job['Job_Title']); ?></h3>
            <p><strong>Reference Number:</strong> <?php echo $code; ?></p>
            <p><strong>Salary:</strong> <?php echo format_salary($job['Salary']); ?></p>
            <?php if ($reporting !== ''): ?>
                <p><strong>Reporting Line:</strong> <?php echo htmlspecialchars($reporting); ?></p>
            <?php endif; ?>
            <p><strong>Minimum Experience:</strong> <?php echo htmlspecialchars($job['Min_Experience']); ?><sup>+</sup> years</p>
        </header>

        <p><?php echo nl2br(htmlspecialchars($job['Description'])); ?></p>

        <?php if ($resp): ?>
        <section>
            <h4>Key Responsibilities</h4>
            <ul>
                <?php foreach ($resp as $item): ?>
                    <li><?php echo htmlspecialchars($item); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endif; ?>

        <?php if ($essential || $preferable): ?>
        <section>
            <h4>Requirements</h4>
            <?php if ($essential): ?>
                <h5>Essential Requirements</h5>
                <ol>
                    <?php foreach ($essential as $item): ?>
                        <li><?php echo preg_replace('/(\d)\+/', '$1<sup>+</sup>', htmlspecialchars($item)); ?></li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>

            <?php if ($preferable): ?>
                <h5>Preferable Requirements</h5>
                <ul>
                    <?php foreach ($preferable as $item): ?>
                        <li><?php echo htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <div class="apply-link-container">
            <a href="apply.php?ref=<?php echo urlencode($job['Job_Code']); ?>" target="_self" class="apply-btn">Apply for this Position</a>
        </div>
    </article>
    <?php endforeach; ?>
</main>

<?php 
require_once 'includes/footer.inc'; 
?>