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

// Fetch course names and IDs from the dbcourses table
$courseData = [];
$conn = connect(); // Assume $db is your PDO or MySQLi connection object from dbinfo.php
$query = "SELECT id, name FROM dbcourses"; // Adjust based on your actual table schema
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $courseData[$row['id']] = $row['name'];
    }
}

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
        if ($_SESSION['access_level'] != 1) {
            echo "Error: You do not have the proper permission to submit.";
        } else {
            // Prepare SQL statement
            $sql = "INSERT INTO dbcoursesignup (course_id, person_id) VALUES (?, ?)";
            
            // Prepare and bind parameters
            $courseID = $_POST['course_id'];
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $courseID, $userID); // s for string
            
            // Execute the statement
            if ($stmt->execute()) {
                echo "New record inserted successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            // Close statement
            $stmt->close();
        }
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

            <h2>Sign Up for a Course</h2>
            <form method="post">
                <label for="course_id">Course</label>
                <select name="course_id" id="course_id" required>
                <option value="" disabled selected>Select</option>
                    <?php foreach ($courseData as $id => $name): ?>
                        <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" name="sign-up" id="sign-up" value="Sign Up">
            </form>
            <!-- Other forms adjusted for courses -->
            <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
        </main>
    </body>
</html>
