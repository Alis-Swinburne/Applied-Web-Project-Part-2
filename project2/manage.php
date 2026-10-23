<?php 
require_once 'includes/session.inc';
require_once 'settings.php';
require_once 'verify_access.php';

$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);
if (!$conn) {
    die("Unable to connect to database: " . mysqli_connect_error());
}

$has_access = verify_access($conn);

// Redirect non-admins BEFORE any HTML is output
if (!$has_access) {
    header("Location: index.php");
    exit();
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// CSRF token
if (empty($_SESSION['manage_csrf'])) {
    $_SESSION['manage_csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['manage_csrf'];

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8', false);
}

$valid_statuses = ['new', 'current', 'final'];
$message = '';
$message_type = 'success';

// ---- Actions (POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
        $message = "Invalid request. Please try again.";
        $message_type = 'error';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'delete_by_job') {
            $ref = trim($_POST['delete_job_ref'] ?? '');
            if (!preg_match('/^[a-zA-Z0-9]{6}$/', $ref)) {
                $message = "Job reference must be 6 alphanumeric characters.";
                $message_type = 'error';
            } else {
                $stmt = mysqli_prepare($conn, "DELETE FROM eoi WHERE job_ref = ?");
                mysqli_stmt_bind_param($stmt, "s", $ref);
                mysqli_stmt_execute($stmt);
                $deleted = mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);
                $message = "Deleted $deleted EOI(s) for job reference " . e($ref) . ".";
            }
        } elseif ($action === 'change_status') {
            $eoi_number = (int)($_POST['eoi_number'] ?? 0);
            $status = $_POST['status'] ?? '';
            if ($eoi_number > 0 && in_array($status, $valid_statuses, true)) {
                $stmt = mysqli_prepare($conn, "UPDATE eoi SET status = ? WHERE eoi_number = ?");
                mysqli_stmt_bind_param($stmt, "si", $status, $eoi_number);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $message = "EOI #$eoi_number status changed to " . e($status) . ".";
            } else {
                $message = "Invalid EOI or status.";
                $message_type = 'error';
            }
        }
    }
}

// ---- Search / filter / sort (GET) ----
$filter     = $_GET['filter'] ?? 'all';
$job_ref    = trim($_GET['job_ref'] ?? '');
$first_name = trim($_GET['first_name'] ?? '');
$last_name  = trim($_GET['last_name'] ?? '');

$sortable = [
    'eoi_number' => 'EOI Number',
    'job_ref'    => 'Job Reference',
    'first_name' => 'First Name',
    'last_name'  => 'Last Name',
    'status'     => 'Status',
    'state'      => 'State',
    'dob'        => 'Date of Birth'
];
$sort  = $_GET['sort'] ?? 'eoi_number';
if (!array_key_exists($sort, $sortable)) $sort = 'eoi_number';
$order = (($_GET['order'] ?? 'asc') === 'desc') ? 'DESC' : 'ASC';

$where = [];
$types = '';
$params = [];

if ($filter === 'job' && $job_ref !== '') {
    $where[]  = "job_ref = ?";
    $types   .= 's';
    $params[] = $job_ref;
} elseif ($filter === 'name') {
    if ($first_name !== '') {
        $where[]  = "first_name LIKE ?";
        $types   .= 's';
        $params[] = '%' . $first_name . '%';
    }
    if ($last_name !== '') {
        $where[]  = "last_name LIKE ?";
        $types   .= 's';
        $params[] = '%' . $last_name . '%';
    }
}

$sql = "SELECT * FROM eoi";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY `$sort` $order, eoi_number ASC";

$records = [];
$query_error = '';
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $records = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
} else {
    $query_error = "No EOI records yet (the eoi table is created when the first application is submitted).";
}

function pretty_gender($g) {
    $map = ['female' => 'Female', 'male' => 'Male', 'nonbinary' => 'Non-binary', 'prefernot' => 'Prefer not to say'];
    return $map[$g] ?? $g;
}
function skill_chips($skills) {
    $out = '';
    foreach (array_filter(array_map('trim', explode(',', (string)$skills))) as $skill) {
        $out .= '<span>' . e(ucwords(str_replace('_', ' ', $skill))) . '</span> ';
    }
    return $out;
}

$page_title = "Manage";
include 'includes/header.inc';
include 'includes/navigation.inc';
?>

<main>
    <?php if ($has_access): ?>
        <header>
            <h2>Manage</h2>
            <p>Welcome admin!</p>
        </header>

        <?php if ($message !== ''): ?>
            <p style="color: <?php echo $message_type === 'error' ? 'red' : 'green'; ?>;"><?php echo $message; ?></p>
        <?php endif; ?>

        <div>
            <!-- Search, filter and sort -->
            <section>
                <h3>Find EOIs</h3>
                <form method="get" action="manage.php">
                    <fieldset>
                        <legend>Show</legend>
                        <label><input type="radio" name="filter" value="all" <?php echo $filter === 'all' ? 'checked' : ''; ?>> All EOIs</label>
                        <label><input type="radio" name="filter" value="job" <?php echo $filter === 'job' ? 'checked' : ''; ?>> By job reference</label>
                        <label><input type="radio" name="filter" value="name" <?php echo $filter === 'name' ? 'checked' : ''; ?>> By applicant name</label>
                    </fieldset>

                    <div data-show="job">
                        <div>
                            <label for="job_ref">Job reference</label>
                            <input type="text" id="job_ref" name="job_ref" maxlength="6" value="<?php echo e($job_ref); ?>">
                        </div>
                    </div>

                    <div data-show="name">
                        <div>
                            <label for="first_name">First name</label>
                            <input type="text" id="first_name" name="first_name" maxlength="20" value="<?php echo e($first_name); ?>">
                        </div>
                        <div>
                            <label for="last_name">Last name</label>
                            <input type="text" id="last_name" name="last_name" maxlength="20" value="<?php echo e($last_name); ?>">
                        </div>
                    </div>

                    <div>
                        <div>
                            <label for="sort">Sort by</label>
                            <select id="sort" name="sort">
                                <?php foreach ($sortable as $col => $label): ?>
                                    <option value="<?php echo $col; ?>" <?php echo $sort === $col ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="order">Order</label>
                            <select id="order" name="order">
                                <option value="asc" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                                <option value="desc" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <button type="submit">Search</button>
                        <a href="manage.php">Reset</a>
                    </div>
                </form>
            </section>

            <!-- Delete all EOIs for a job reference -->
            <section>
                <h3>Delete by job reference</h3>
                <p>Removes every EOI submitted for one job. This cannot be undone.</p>
                <form method="post" action="manage.php" onsubmit="return confirm('Delete ALL EOIs for this job reference? This cannot be undone.');">
                    <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                    <input type="hidden" name="action" value="delete_by_job">
                    <div>
                        <label for="delete_job_ref">Job reference</label>
                        <input type="text" id="delete_job_ref" name="delete_job_ref" maxlength="6" required>
                    </div>
                    <button type="submit">Delete all</button>
                </form>
            </section>
        </div>

        <!-- Results -->
        <section>
            <h3>Results (<?php echo count($records); ?>)</h3>

            <?php if ($query_error !== ''): ?>
                <p><?php echo $query_error; ?></p>
            <?php elseif (count($records) === 0): ?>
                <p>No EOIs found.</p>
            <?php else: ?>
            <div>
            <table border="1">
                <thead>
                    <tr>
                        <th>EOI #</th>
                        <th>Job</th>
                        <th>Applicant</th>
                        <th>Details</th>
                        <th>Address</th>
                        <th>Skills</th>
                        <th>Other skills</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($records as $row): ?>
                    <tr>
                        <td>#<?php echo (int)$row['eoi_number']; ?></td>
                        <td><?php echo e($row['job_ref']); ?></td>
                        <td>
                            <strong><?php echo e($row['first_name'] . ' ' . $row['last_name']); ?></strong><br>
                            <small><?php echo e($row['email']); ?></small><br>
                            <small><?php echo e($row['phone']); ?></small>
                        </td>
                        <td>
                            DOB: <?php echo e($row['dob']); ?><br>
                            <?php echo e(pretty_gender($row['gender'])); ?>
                        </td>
                        <td>
                            <?php echo e($row['street']); ?><br>
                            <?php echo e($row['suburb'] . ', ' . strtoupper($row['state']) . ' ' . $row['postcode']); ?>
                        </td>
                        <td><?php echo skill_chips($row['skills']); ?></td>
                        <td><?php echo e($row['other_skills']); ?></td>
                        <td>
                            <strong><?php echo e($row['status']); ?></strong>
                            <form method="post" action="manage.php?<?php echo e($_SERVER['QUERY_STRING'] ?? ''); ?>">
                                <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                <input type="hidden" name="action" value="change_status">
                                <input type="hidden" name="eoi_number" value="<?php echo (int)$row['eoi_number']; ?>">
                                <select name="status">
                                    <?php foreach ($valid_statuses as $st): ?>
                                        <option value="<?php echo $st; ?>" <?php echo $row['status'] === $st ? 'selected' : ''; ?>><?php echo ucfirst($st); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>
        </section>

        <script>
        (function () {
            var form = document.querySelector('form');
            function update() {
                var chosen = form.querySelector('input[name="filter"]:checked');
                var value = chosen ? chosen.value : 'all';
                form.querySelectorAll('[data-show]').forEach(function (el) {
                    el.hidden = el.getAttribute('data-show').split(' ').indexOf(value) === -1;
                });
            }
            form.querySelectorAll('input[name="filter"]').forEach(function (r) {
                r.addEventListener('change', update);
            });
            update();
        })();
        </script>

    <?php else: ?>
        <?php 
        header("Location: index.php");
        exit();
        ?>
    <?php endif; ?>
</main>

<?php include 'includes/footer.inc'; ?>