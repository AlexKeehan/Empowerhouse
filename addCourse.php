<?php
/** 
 * Page to add multiple courses to dbCourses simultaneously with only course name required
 * @ Author Emily Lambert
 * @ Version April 5 2024
 **/
// Include necessary files
require_once('include/input-validation.php');
require_once('database/dbCourses.php');
require_once('database/dbEvents.php');
require_once('database/dbtrainingperiods.php');

// Check if user is logged in and has appropriate access level
session_cache_expire(30);
session_start();
$loggedIn = false;
$accessLevel = 0;
$userID = null;
if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $userID = $_SESSION['_id'];
} 
// Redirect if user does not have sufficient access level
if ($accessLevel < 2) {
    header('Location: login.php');
    die();
}

// Fetch available events and training periods for dropdown menu
$events = get_all_events();
$trainingPeriods = get_all_training_periods();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and retrieve course details from form input
    if (!isset($_POST['courses']) || empty($_POST['courses'])) {
        // Redirect back to the form with an error message
        header("Location: addCourse.php?error=no_courses_provided");
        exit();
    }

    $courses = $_POST['courses'];
    //echo "num of courses submitted: " . count($courses);

    foreach ($courses as $course) {
        // Sanitize input for each course
        $args = sanitize($course, null);

        // Extract course details from sanitized args
        $courseName = $args['new_course_name'];
        $abbrevName = $args['new_course_abbrev_name'];
        $startTime = $args['new_course_start_time'];
        $endTime = $args['new_course_end_time'];
        $date = $args['new_course_date'];
        $trainer = $args['new_course_trainer'];
        $description = $args['new_course_description'];
        $location = $args['new_course_location'];
        $capacity = intval($args['new_course_capacity']);
        $eventId = $args['event_id'];
        $periodId = $args['period_id'];

        // Check for required fields 
        if (empty($courseName)) {
            header("Location: addCourse.php?error=missing_course_name");
            exit();
        }

        // Check if periodID is valid if necessary
        if (!empty($periodId)) {
            $existingPeriod = get_training_period_by_id($periodId);
            if (!$existingPeriod) {
                header("Location: addCourse.php?error=invalid_period_id");
                exit();
            }
        }

        // Array with course details for each course
        $courseArgs = [
            $courseName,
            $abbrevName,
            $trainer,  
            $eventId, 
            $periodId, // added training periodId
            $date, 
            $startTime, 
            $endTime, 
            $description, 
            $location, 
            $capacity
        ];

        // Insert new course into the database
        $result = create_course($courseArgs);

        if (!$result) {
            // Redirect back to the form with an error message
            header("Location: addCourse.php?error=create_failed");
            exit();
        }
    }

    // Redirect to the calendar page upon successful creation of all courses
    header("Location: calendar.php?createSuccess");
    exit();
}

// Extract date from URL if provided
$date = null;
if (isset($_GET['date'])) {
    $date = $_GET['date'];
    $datePattern = '/[0-9]{4}-[0-9]{2}-[0-9]{2}/';
    $timeStamp = strtotime($date);
    if (!preg_match($datePattern, $date) || !$timeStamp) {
        header('Location: calendar.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc'); ?>
    <title>Empowerhouse VMS | Add Course</title>
    <style>
    .required::after {
        content: "*";
        color: red;
    }
</style>
</head>
<body>
    <?php require_once('header.php'); ?>
    <h1>Add Course</h1>
    <main class="date">
        <h2>New Course Form</h2>
        <?php
        // Display error messages  if provided in URL
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            if ($error === "missing_course_name") {
                echo "<p class='error'>Please fill in the course name for each course.</p>";
            } elseif ($error === "create_failed") {
                echo "<p class='error'>Failed to create one or more courses. Please try again later.</p>";
            } elseif ($error === "invalid_period_id") {
                echo "<p class='error'>Invalid training period ID provided.</p>";
            } elseif ($error === "no_courses_provided") {
                echo "<p class='error'>No courses provided in the form.</p>";
            }
        }
        ?>
        <form id="new-course-form" method="post">
            <fieldset>
                <legend>New Course</legend>
                <div id="courses-container">
                    <div class="course">
                        <label for="new_course_name">Course Name <span class="required"></span></label>
                        <input type="text" name="courses[0][new_course_name]" required placeholder="Enter course name">

                        <label for="new_course_abbrev_name">Abbreviated Name</label>
                        <input type="text" name="courses[0][new_course_abbrev_name]" placeholder="Enter abbreviated name">

                        <label for="new_course_date">Date</label>
                        <input type="date" name="courses[0][new_course_date]" min="<?php echo date('Y-m-d'); ?>" placeholder="Enter date">

                        <label for="new_course_start_time">Start Time</label>                    
                        <input type="time" name="courses[0][new_course_start_time]" placeholder="Enter start time. Ex. 12:00 PM">

                        <label for="new_course_end_time">End Time</label>
                        <input type="time" name="courses[0][new_course_end_time]" placeholder="Enter end time. Ex. 4:00 PM">

                        <label for="new_course_trainer">Taught By</label>
                        <input type="text" name="courses[0][new_course_trainer]" placeholder="Enter trainer name"> 

                        <label for="new_course_description">Description</label>
                        <input type="text" name="courses[0][new_course_description]" placeholder="Enter description">

                        <label for="new_course_location">Location</label>
                        <input type="text" name="courses[0][new_course_location]" placeholder="Enter location">

                        <label for="new_course_capacity">Volunteer Slots</label>
                        <input type="text" name="courses[0][new_course_capacity]" pattern="([1-9])|([01][0-9])|(20)" placeholder="Enter a number">
                        <!-- dropdown menus for periodID and eventID -->
                        <label for="event_id">Select Event</label>
                        <select name="courses[0][event_id]">
                            <option value="">None</option>
                            <?php foreach ($events as $event) {
                                echo "<option value=\"" . $event['id'] . "\">" . $event['eventname'] . "</option>";
                            } ?>
                        </select>
                        <label for="period_id">Select Training Period</label>
                        <select name="courses[0][period_id]">
                            <option value="">None</option> 
                            <?php foreach ($trainingPeriods as $period) {
                                echo "<option value=\"" . $period['id'] . "\">" . $period['semester'] . " " . $period['year'] . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <button id="add-course-btn" type="button">Add Another Course</button>
                <button type="submit">Submit</button>
            </fieldset>
        </form>
    </main>
    <script>
        // add/clone another course form when the "Add Another Course" button is pressed
        document.getElementById('add-course-btn').addEventListener('click', function () {
        var coursesContainer = document.getElementById('courses-container');
        var courses = coursesContainer.querySelectorAll('.course');

        var newCourse = courses[courses.length - 1].cloneNode(true);

        var inputs = newCourse.querySelectorAll('input, select');
        inputs.forEach(function(input) {
            var newName = input.name.replace(/\[\d+\]/, '[' + courses.length + ']');
            input.name = newName;
            input.value = ''; //clear input
        });
        coursesContainer.appendChild(newCourse);
    });
    </script>
</body>
</html>


