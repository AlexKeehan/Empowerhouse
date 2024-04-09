<?php
session_cache_expire(30);
session_start();

date_default_timezone_set("America/New_York");

if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
    if (isset($_SESSION['change-password'])) {
        header('Location: changePassword.php');
    } else {
        header('Location: logout.php');
    }
    die();
}

include_once('database/dbPersons.php');
include_once('domain/Person.php');

if (isset($_SESSION['_id'])) {
    $person = retrieve_person($_SESSION['_id']);
}
$notRoot = $person->get_id() != 'vmsroot';

// Fetch attendees based on the selected course
$attendees = [];
if (isset($_POST['coursename'])) {
    $conn = connect(); // Assuming you have a function to establish a database connection
    
    // Find the course ID based on the selected course name
    $courseName = $_POST['coursename'];
    $sqlCourseID = "SELECT id FROM dbcourses WHERE name = '$courseName'";
    $resultCourseID = $conn->query($sqlCourseID);

    if ($resultCourseID->num_rows > 0) {
        $rowCourseID = $resultCourseID->fetch_assoc();
        $courseID = $rowCourseID['id'];

        // Find all attendees enrolled in the selected course
        $sqlAttendees = "SELECT p.first_name, p.last_name
                        FROM dbpersons p
                        INNER JOIN dbcoursesignup cs ON p.id = cs.person_id
                        WHERE cs.course_id = $courseID";
        $resultAttendees = $conn->query($sqlAttendees);

        if ($resultAttendees->num_rows > 0) {
            while($rowAttendees = $resultAttendees->fetch_assoc()) {
                $attendees[] = $rowAttendees['first_name'] . ' ' . $rowAttendees['last_name'];
            }
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>Empower House VMS | Verify</title>
    <style>
        /* Add some space between buttons */
        .button-spacing {
            margin-bottom: 10px;

        }
        form {
            margin-left: 1in; 
            margin-right: 1in;
        }
    </style>
</head>
<body>
    <?php require_once('header.php') ?>

    <form method="post" action="takeAttendance.php">
        <h3>Attendees</h3><br>
        <!-- Checkbox for each attendee -->
        <?php
            foreach ($attendees as $attendee) {
                echo "<input type='checkbox' name='attendee[]' value='$attendee'> $attendee<br>";
            }
    
        ?> <br>
        <!-- Submit button -->
        <input class="button-spacing" type="submit" value="Submit">
    </form>
    <!-- Back button -->
    <form method="post" action="takeAttendance.php" class="back-button">
        <input type="submit" value="Back">
    </form>
</body>
</html>
