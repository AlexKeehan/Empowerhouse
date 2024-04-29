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
require_once('database/dbMessages.php');

// Get date?
if (isset($_SESSION['_id'])) {
    $person = retrieve_person($_SESSION['_id']);
}
$notRoot = $person->get_id() != 'vmsroot';
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

    <form method="post">
        <h3>Attendees</h3><br>
        <!-- Checkbox for each attendee -->
        <?php 
            $conn = connect();
            // Check if the form was submitted
            $coursename = "";
            $courseID;
            //print_r($_POST);
            if (isset($_POST['coursename'])) {
                $coursename = $_POST['coursename'];
            }
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['coursename'])) {            
                // Prepare and execute the SQL statement to get course ID
                $stmt = $conn->prepare("SELECT id FROM dbcourses WHERE name = ?");
                $stmt->bind_param("s", $coursename);
                $stmt->execute();
                $result = $stmt->get_result();
            
                // Fetch the course ID
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $courseID = $row['id'];
            
                    // Prepare and execute SQL statement to get attendees for the course
                    $sqlAttendees = "SELECT p.id, p.first_name, p.last_name
                                    FROM dbpersons p
                                    INNER JOIN dbcoursesignup cs ON p.id = cs.person_id
                                    WHERE cs.course_id = ?";
                    $stmt2 = $conn->prepare($sqlAttendees);
                    $stmt2->bind_param("i", $courseID);
                    $stmt2->execute();
                    $result2 = $stmt2->get_result();
                    $_SESSION['courseID'] = $courseID;
                    // Generate checkboxes for unique attendees
                    while ($row = $result2->fetch_assoc()) {
                        $attendeeID = $row['id'];
                        $checked = isset($_POST['attendee']) && in_array($attendeeID, $_POST['attendee']) ? 1 : 0; // Check if attendee is checked
                        // Generate checkbox with attendance status
                        echo '<input type="checkbox" name="attendee[]" value="' . $attendeeID . '" ' . ($checked ? 'checked' : '') . '>';
                        echo $row['first_name'] . ' ' . $row['last_name'] . '<br>';
                    }
            
                    // Free result set
                    $stmt2->close();
                } else {
                    echo "No course found with the provided name.";
                }
            
                // Free result set
                $stmt->close();
            }
            //echo "aaaa ";
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
                //print_r($_POST);
                // need courseID again 
                //echo "bbbb ";
                if (isset($_POST['attendee'])) {
                    // Connect to the database
                    $conn = connect();
                    // Prepare SQL statement for inserting attendance
                    //echo "cccc ";
                    $stmt = $conn->prepare("INSERT INTO dbattendance (person_id, course_id, date, attendance) VALUES (?, ?, CURDATE(), ?)");
        
                    // Bind parameters and execute the statement for each selected attendee
                    foreach ($_POST['attendee'] as $attendeeID) {
                        $attendance = 1; // Assume checked by default
                        // If attendee is not checked, set attendance to 0

                        //Assuming that only one course is needed to complete training
                        $query = ("UPDATE dbPersons SET completedTraining = 'True', dateCompletedTraining=CURDATE() WHERE dbPersons.id='$attendeeID'");
                        $result = mysqli_query($conn,$query);
                        //echo "dddd ";
                        if (!in_array($attendeeID, $_POST['attendee'])) {
                            $attendance = 0;
                        }
                        $stmt->bind_param("sis", $attendeeID, $_SESSION['courseID'], $attendance);
                        $stmt->execute();
                        message_all_admins($attendeeID, "User Has Completed Training", "User Is Now Available To Volunteer");
                        send_message($attendeeID, 'vmsroot', "User Has Completed Training", "User Is Now Available To Volunteer");
                    }
        
                    // Close the statement and the database connection
                    $stmt->close();
                    $conn->close();
        
                    echo "<p>Attendance recorded successfully.</p>";
                } else {
                    echo "<p>No attendees selected.</p>";
                }
            }
            // Close the connection
        ?> <br>
        <!-- Submit button -->
        <input class="button-spacing" type="submit" name="submit" value="Submit">
    </form>
    <!-- Back button -->
    <form method="post" action="takeAttendance.php" class="back-button">
        <input type="submit" value="Back">
    </form>
</body>
</html>


<!-- $sqlAttendees = "SELECT p.id, p.first_name, p.last_name
                        FROM dbpersons p
                        INNER JOIN dbcoursesignup cs ON p.id = cs.person_id
                        WHERE cs.course_id = $courseID"; -->