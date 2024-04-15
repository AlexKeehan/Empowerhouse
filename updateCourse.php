
<?php
/** 
 * Page to update data for a course from dbCourses 
 * @ Author Emily Lambert
 * @ Version April 5 2024
 **/

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

// Include necessary files
require_once('include/input-validation.php');
require_once('database/dbCourses.php');
require_once('database/dbEvents.php');
require_once('database/dbtrainingperiods.php');

//var_dump($_POST);

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

// Initialze and fetch variables 
$events = get_all_events();
$trainingPeriods = get_all_training_periods();
$courseID = null;
$courseDetails = null;
$error = '';
$allCourses = fetch_all_courses();

// Process form submission for selecting a course
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_course'])) {
    if (!empty($_POST['selected_course_id'])) {
        $courseID = $_POST['selected_course_id'];
        $courseDetails = retrieve_course($courseID);
    } else {
        $error = 'Please select a course to update.';
    }
}

// Process form submission for updating the course details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_update'])) {
    // Validate and retrieve course details from form input
    if (!isset($_POST['courses']) || empty($_POST['courses'])) {
        // Set error message
        $error = 'No courses provided.';
    } else {
        $courses = $_POST['courses'];

        foreach ($courses as $course) {
            // Sanitize input for each course
            $args = sanitize($course, null);

            // Extract course details from sanitized arguments
            $courseID = $args['course_id'];
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

            //var_dump($courseID, $courseName, $abbrevName, $startTime, $endTime, $date, $trainer, $description, $location, $capacity, $eventId, $periodId);

            // Check if course ID is given
            if (empty($courseID)) {
                $error = 'Course ID is missing.';
            } else {
                // Check if periodID is valid if given
                if (!empty($periodId)) {
                    $existingPeriod = get_training_period_by_id($periodId);
                    if (!$existingPeriod) {
                        $error = 'Invalid training period ID provided.';
                    }
                }

                // Array with updated course details
                $updatedCourseArgs = [
                    $courseID,
                    $courseName,
                    $abbrevName,
                    $trainer,  
                    $eventId, 
                    $periodId,
                    $date, 
                    $startTime, 
                    $endTime, 
                    $description, 
                    $location, 
                    $capacity
                ];

                // Update course in the database
                $result = update_course($courseID, $updatedCourseArgs);

                if (!$result) {
                    $error = 'Failed to update one or more courses. Please try again later.';
                }
            }
        }

        // Redirect to a calendar page upon successful update of course
        if (empty($error)) {
            header("Location: calendar.php?updateSuccess");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc'); ?>
    <title>Empowerhouse VMS | Update Course</title>
    <style>
    .required::after {
        content: "*";
        color: red;
    }
    </style>
</head>
<body>
    <?php require_once('header.php'); ?>
    <h1>Update Course</h1>
    <main class="date">
        <h2>Choose Course to Update</h2>
        <form id="select-course-form" method="post">
            <fieldset>
                <legend>Select Course</legend>
                <?php if (!empty($error)) : ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>
                <label for="selected_course_id">Choose a Course:</label>
                <select name="selected_course_id" id="selected_course_id">
                    <option value="">Select a Course</option>
                    <?php
                    // display available courses in the dropdown menu
                    foreach ($allCourses as $course) {
                        echo "<option value=\"" . $course['id'] . "\">" . $course['name'] . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="update_course">Update Course</button>
            </fieldset>
        </form>
        
        <?php if ($courseDetails) : ?>
        <h2>Update Course Details</h2>
        <form id="update-course-form" method="post">
            <fieldset>
                <legend>Course Details</legend>
                <?php if (!empty($error)) : ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>
                <div id="courses-container">
                    <div class="course">
                        <input type="hidden" name="courses[0][course_id]" value="<?php echo $courseID; ?>">
                        
                        <label for="new_course_name">Course Name <span class="required"></span></label>
                        <input type="text" name="courses[0][new_course_name]" value="<?php echo $courseDetails['name']; ?>" required placeholder="Enter course name">

                        <label for="new_course_abbrev_name">Abbreviated Name</label>
                        <input type="text" name="courses[0][new_course_abbrev_name]" value="<?php echo $courseDetails['abbrevName']; ?>" placeholder="Enter abbreviated name">

                        <label for="new_course_date">Date</label>
                        <input type="date" name="courses[0][new_course_date]" value="<?php echo $courseDetails['date']; ?>" min="<?php echo date('Y-m-d'); ?>" placeholder="Enter date">

                        <label for="new_course_start_time">Start Time</label>                    
                        <input type="time" name="courses[0][new_course_start_time]" value="<?php echo $courseDetails['startTime']; ?>" placeholder="Enter start time. Ex. 12:00 PM">

                        <label for="new_course_end_time">End Time</label>
                        <input type="time" name="courses[0][new_course_end_time]" value="<?php echo $courseDetails['endTime']; ?>" placeholder="Enter end time. Ex. 4:00 PM">

                        <label for="new_course_trainer">Taught By</label>
                        <input type="text" name="courses[0][new_course_trainer]" value="<?php echo $courseDetails['staffId']; ?>" placeholder="Enter trainer name"> 

                        <label for="new_course_description">Description</label>
                        <input type="text" name="courses[0][new_course_description]" value="<?php echo $courseDetails['description']; ?>" placeholder="Enter description">

                        <label for="new_course_location">Location</label>
                        <input type="text" name="courses[0][new_course_location]" value="<?php echo $courseDetails['location']; ?>" placeholder="Enter location">

                        <label for="new_course_capacity">Volunteer Slots</label>
                        <input type="number" name="courses[0][new_course_capacity]" value="<?php echo $courseDetails['capacity']; ?>" min="0" placeholder="Enter a number">
                        <!-- dropdown menus for periodID and eventID -->
                        <label for="event_id">Select Event</label>
                        <select name="courses[0][event_id]">
                            <option value="">None</option>
                            <?php foreach ($events as $event) {
                                $selected = ($event['id'] == $courseDetails['eventId']) ? 'selected' : '';
                                echo "<option value=\"" . $event['id'] . "\" $selected>" . $event['eventname'] . "</option>";
                            } ?>
                        </select>
                        <label for="period_id">Select Training Period</label>
                        <select name="courses[0][period_id]">
                            <option value="">None</option> 
                            <?php foreach ($trainingPeriods as $period) {
                                $selected = ($period['id'] == $courseDetails['periodId']) ? 'selected' : '';
                                echo "<option value=\"" . $period['id'] . "\" $selected>" . $period['semester'] . " " . $period['year'] . "</option>";
                            } ?>
                        </select>
                    </div>
                    <button type="submit" name="submit_update">Update Course</button>
                </div>
            </fieldset>
        </form>
        <?php endif; ?>
        <script>
            document.getElementById('selected_course_id').addEventListener('change', function() {
                var selectedCourseId = this.value;
                document.querySelector('select[name="selected_course_id"]').value = selectedCourseId;
            });
        </script>
    </main>
</body>
</html>


