<?php 
require_once 'includes/session.inc';
require_once 'settings.php';
require_once 'verify_access.php';

$conn = mysqli_connect($host, $db_user, $pwd, $sql_db);
if (!$conn) {
    die("Unable to connect to database: " . mysqli_connect_error());
}

$has_access = verify_access($conn);

// Redirect non-admins BEFORE any HTML is output (header() can't run once the page has started printing)
if (!$has_access) {
    header("Location: index.php");
    exit();
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// CSRF token so only forms from this page can delete/update records
if (empty($_SESSION['manage_csrf'])) {
    $_SESSION['manage_csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['manage_csrf'];

// Escape for display (false = don't double-encode text that process_eoi.php already escaped)
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8', false);
}

$valid_statuses = ['new', 'current', 'final'];
$message = '';
$message_type = 'success';

// ---- Actions (POST): delete by job reference, change status ----
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
$filter     = $_GET['filter'] ?? 'all';             // all | job | name
$job_ref    = trim($_GET['job_ref'] ?? '');
$first_name = trim($_GET['first_name'] ?? '');
$last_name  = trim($_GET['last_name'] ?? '');

// Whitelist of sortable columns (never put user input straight into ORDER BY)
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

// Display helpers (formatting only)
function pretty_gender($g) {
    $map = ['female' => 'Female', 'male' => 'Male', 'nonbinary' => 'Non-binary', 'prefernot' => 'Prefer not to say'];
    return $map[$g] ?? $g;
}
function skill_chips($skills) {
    $out = '';
    foreach (array_filter(array_map('trim', explode(',', (string)$skills))) as $skill) {
        $out .= '<span class="chip">' . e(ucwords(str_replace('_', ' ', $skill))) . '</span>';
    }
    return $out;
}

$page_title = "Manage";
include 'includes/header.inc';
include 'includes/navigation.inc';
?>

<main class="manage-page">
    <?php if ($has_access): ?>
        <header class="manage-header">
            <h1>Manage</h1>
            <p>Welcome admin!</p>
        </header>

        <?php if ($message !== ''): ?>
            <p class="manage-message <?php echo $message_type; ?>"><?php echo $message; ?></p>
        <?php endif; ?>

        <div class="manage-grid">
            <!-- Search, filter and sort -->
            <section class="manage-card manage-search">
                <h2>Find EOIs</h2>
                <form method="get" action="manage.php">
                    <fieldset>
                        <legend class="sr-only">Show</legend>
                        <div class="seg-group">
                            <label class="seg"><input type="radio" name="filter" value="all" <?php echo $filter === 'all' ? 'checked' : ''; ?>><span>All EOIs</span></label>
                            <label class="seg"><input type="radio" name="filter" value="job" <?php echo $filter === 'job' ? 'checked' : ''; ?>><span>By job reference</span></label>
                            <label class="seg"><input type="radio" name="filter" value="name" <?php echo $filter === 'name' ? 'checked' : ''; ?>><span>By applicant name</span></label>
                        </div>
                    </fieldset>

                    <div class="field-row" data-show="job">
                        <div class="field">
                            <label for="job_ref">Job reference</label>
                            <input type="text" id="job_ref" name="job_ref" maxlength="6" placeholder="e.g. DEV042" value="<?php echo e($job_ref); ?>">
                        </div>
                    </div>

                    <div class="field-row" data-show="name">
                        <div class="field">
                            <label for="first_name">First name</label>
                            <input type="text" id="first_name" name="first_name" maxlength="20" value="<?php echo e($first_name); ?>">
                        </div>
                        <div class="field">
                            <label for="last_name">Last name</label>
                            <input type="text" id="last_name" name="last_name" maxlength="20" value="<?php echo e($last_name); ?>">
                        </div>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label for="sort">Sort by</label>
                            <select id="sort" name="sort">
                                <?php foreach ($sortable as $col => $label): ?>
                                    <option value="<?php echo $col; ?>" <?php echo $sort === $col ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label for="order">Order</label>
                            <select id="order" name="order">
                                <option value="asc" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                                <option value="desc" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
                            </select>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn">Search</button>
                        <a class="link-reset" href="manage.php">Reset</a>
                    </div>
                </form>
            </section>

            <!-- Delete all EOIs for a job reference -->
            <section class="manage-card manage-delete">
                <h2>Delete by job reference</h2>
                <p class="hint">Removes every EOI submitted for one job. This cannot be undone.</p>
                <form method="post" action="manage.php"
                      onsubmit="return confirm('Delete ALL EOIs for this job reference? This cannot be undone.');">
                    <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                    <input type="hidden" name="action" value="delete_by_job">
                    <div class="field field-spaced">
                        <label for="delete_job_ref">Job reference</label>
                        <input type="text" id="delete_job_ref" name="delete_job_ref" maxlength="6" placeholder="e.g. SEC918" required>
                    </div>
                    <button type="submit" class="btn btn-danger">Delete all</button>
                </form>
            </section>
        </div>

        <!-- Results -->
        <section class="manage-card manage-results">
            <h2>Results <span class="count"><?php echo count($records); ?></span></h2>

            <?php if ($query_error !== ''): ?>
                <p class="empty"><?php echo $query_error; ?></p>
            <?php elseif (count($records) === 0): ?>
                <p class="empty">No EOIs found.</p>
            <?php else: ?>
            <div class="table-wrapper">
            <table>
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
                        <td class="cell-id">#<?php echo (int)$row['eoi_number']; ?></td>
                        <td><span class="job-tag"><?php echo e($row['job_ref']); ?></span></td>
                        <td class="applicant">
                            <strong><?php echo e($row['first_name'] . ' ' . $row['last_name']); ?></strong>
                            <small><?php echo e($row['email']); ?></small>
                            <small><?php echo e($row['phone']); ?></small>
                        </td>
                        <td>
                            <span class="sub">DOB: <?php echo e($row['dob']); ?></span>
                            <span class="sub"><?php echo e(pretty_gender($row['gender'])); ?></span>
                        </td>
                        <td>
                            <span class="sub"><?php echo e($row['street']); ?></span>
                            <span class="sub"><?php echo e($row['suburb'] . ', ' . strtoupper($row['state']) . ' ' . $row['postcode']); ?></span>
                        </td>
                        <td><?php echo skill_chips($row['skills']); ?></td>
                        <td><span class="sub"><?php echo e($row['other_skills']); ?></span></td>
                        <td>
                            <span class="badge badge-<?php echo e($row['status']); ?>"><?php echo e($row['status']); ?></span>
                            <form class="status-form" method="post" action="manage.php?<?php echo e($_SERVER['QUERY_STRING'] ?? ''); ?>">
                                <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                <input type="hidden" name="action" value="change_status">
                                <input type="hidden" name="eoi_number" value="<?php echo (int)$row['eoi_number']; ?>">
                                <select name="status" aria-label="Status for EOI <?php echo (int)$row['eoi_number']; ?>">
                                    <?php foreach ($valid_statuses as $st): ?>
                                        <option value="<?php echo $st; ?>" <?php echo $row['status'] === $st ? 'selected' : ''; ?>><?php echo ucfirst($st); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-small">Update</button>
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
        // Show only the search fields that apply to the chosen filter
        (function () {
            var form = document.querySelector('.manage-search form');
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
        // Immediately redirects back to index.php if user doesn't have admin access
        header("Location: index.php");
        exit();
        ?>
    <?php endif; ?>
</main>

<?php include 'includes/footer.inc'; ?>