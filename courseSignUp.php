<?php
// Initiate session and database connection
session_cache_expire(30);
session_start();
require_once('database/dbinfo.php'); // Assuming this file handles database connection

$loggedIn = false;
$accessLevel = 0;
$userID = null;
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
}
if (!$loggedIn) {
    header('Location: login.php');
    die();
}

// Fetch course names from the dbpersons table
$courseNames = [];
$conn = connect(); // Assume $db is your PDO or MySQLi connection object from dbinfo.php
$query = "SELECT name FROM dbcourses"; // Adjust based on your actual table schema
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courseNames[] = $row['name'];
    }
}

$courses = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('include/input-validation.php');
    
    $args = sanitize($_POST);
    if (isset($args['submitName'])) {
        $courses = find_course($args['name']); // Adjust find_course to your actual function
        $search = 'Results for Search by Name: "' . htmlspecialchars($args['name']) . '"';
        $_SESSION['courses'] = $courses;
    } else if (isset($args['submitDateRange'])) {
        // Process date range search similarly, adjusted for courses
    } else if (isset($args['sign-up'])) {
        // Adjust for courses as necessary
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Empowerhouse VMS | Course Search</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Course Search</h1>
        <main class="search-form">
            <?php if (isset($courses)): ?>
                <h2><?= $search ?></h2>
                <!-- Course display logic -->
            <?php endif; ?>

            <h2>Search for a Course</h2>
            <form method="post">
                <label for="name">Name</label>
                <select name="name" id="name" required>
                    <?php foreach ($courseNames as $name): ?>
                        <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" name="submitName" id="submitName" value="Search by Name">
            </form>
            <!-- Other forms adjusted for courses -->
            <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
        </main>
    </body>
</html>
