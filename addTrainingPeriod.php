<?php
    // Page for an Admin to add a new Training Period and add Courses to it.
    // Author Alex Keehan
    session_cache_expire(30);
    session_start();
    ini_set("display_errors",1);
    error_reporting(E_ALL);

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    } 
    // Require admin privileges
    if ($accessLevel < 2) {
        header('Location: login.php');
        echo 'bad access level';
        die();
    }
    require_once('include/input-validation.php');
    require_once('database/dbEvents.php');
    require_once('database/dbCourses.php');
    require_once('database/dbTrainingPeriods.php');
    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate and retrieve course details from form input
        $courses = array();
        // Training period passed from selectTrainingPeriod.php
        // Hardcoded for now until training period gets dynamically passed from selectTrainingPeriod.php
        $semester = 'Spring';    //isset($_POST['training-period']) ? $_POST['training-period'] : false;
        $year = '2025';          //isset($_POST['yeardropdown']) ? $_POST['yeardropdown'] : false;


        //Grab row(s) from dbtrainingperiods that match the given semester & year
        $training_periods = get_training_periods_by_semester_and_year($semester, $year);
        // Grab the id for said row(s) to link the new course to its training period
        $periodId = $training_periods[0]['id'];
        
        foreach ($_POST['courses'] as $course) {
            $args = sanitize($course, null);

            $courseName = $args['new_course_name'];
            $abbrevName = $args['new_course_abbrev_name'];
            $startTime = $args['new_course_start_time'];
            $endTime = $args['new_course_end_time'];
            $date = $args['new_course_date'];
            $trainer = $args['new_course_trainer'];
            $description = $args['new_course_description'];
            $location = $args['new_course_location'];
            $capacity = intval($args['new_course_capacity']);

            // Check if any required fields are missing or invalid for each course
            if (empty($courseName) || empty($startTime) || empty($endTime) || empty($date) || empty($trainer) || empty($description) || empty($location) || /* || empty(staffId) || empty(eventId) || */ empty($periodId) || $capacity < 1 || $capacity > 20) {
                // Redirect back to the form with an error message
                header("Location: addTrainingPeriod.php?error=missing_fields");
                exit();
            }

            // Validate time range for each course
            if (!validate24hTimeRange($startTime, $endTime)) {
                // Redirect back to the form with an error message
                header("Location: addTrainingPeriod.php?error=bad_time_range");
                exit();
            }

            //  array with course details for each course
            $courseArgs = [
                $courseName,
                $abbrevName,
                $trainer,
                //staffId,
                //$eventId,
                $periodId,
                $date, 
                $startTime, 
                $endTime, 
                $description, 
                $location, 
                $capacity
            ];

            $courses[] = $courseArgs;
        }

        // Create the new courses
        foreach ($courses as $courseArgs) {
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
    
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc'); ?>
    <title>Empowerhouse VMS | Add Course</title>
</head>
<body>
    <?php require_once('header.php'); ?>
    <h1>Add Course</h1>
    <main class="date">
        <h2>New Course Form</h2>
        <?php
        // Display error message if provided in URL
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            if ($error === "missing_fields") {
                echo "<p class='error'>Please fill in all required fields for each course.</p>";
            } elseif ($error === "bad_time_range") {
                echo "<p class='error'>Invalid time range. Please ensure the start time comes before the end time for each course.</p>";
            } elseif ($error === "create_failed") {
                echo "<p class='error'>Failed to create one or more courses. Please try again later.</p>";
            }
        }
        ?>
        <form id="new-course-form" method="post">
            <fieldset>
                <legend>New Course</legend>
                <div id="courses-container">
                    <div class="course">
                        <label for="new_course_name">Course Name</label>
                        <input type="text" name="courses[0][new_course_name]" required placeholder="Enter course name">

                        <label for="new_course_abbrev_name">Abbreviated Name</label>
                        <input type="text" name="courses[0][new_course_abbrev_name]" placeholder="Enter abbreviated name">

                        <label for="new_course_date">Date</label>
                        <input type="date" name="courses[0][new_course_date]" min="<?php echo date('Y-m-d'); ?>" required>

                        <label for="new_course_start_time">Start Time</label>                    
                        <input type="time" name="courses[0][new_course_start_time]" required placeholder="Enter start time. Ex. 12:00 PM">

                        <label for="new_course_end_time">End Time</label>
                        <input type="time" name="courses[0][new_course_end_time]" required placeholder="Enter end time. Ex. 4:00 PM">

                        <label for="new_course_trainer">Taught By</label>
                        <input type="text" name="courses[0][new_course_trainer]" required placeholder="Enter trainer name"> 

                        <label for="new_course_description">Description</label>
                        <input type="text" name="courses[0][new_course_description]" required placeholder="Enter description">

                        <label for="new_course_location">Location</label>
                        <input type="text" name="courses[0][new_course_location]" required placeholder="Enter location">

                        <label for="new_course_capacity">Volunteer Slots</label>
                        <input type="text" name="courses[0][new_course_capacity]" pattern="([1-9])|([01][0-9])|(20)" required placeholder="Enter a number">

                        </select>
                    </div>
                </div>
                <button type="button" id="add-course-btn">Add Another Course</button>
            </fieldset>
            <button type="submit">Create Courses</button>
        </form>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addCourseBtn = document.getElementById('add-course-btn');
            const coursesContainer = document.getElementById('courses-container');
            let courseIndex = 1; 

            addCourseBtn.addEventListener('click', function () {
                const newCourseHtml = `
                    <div class="course">
                        <label for="new_course_name">Course Name</label>
                        <input type="text" name="courses[${courseIndex}][new_course_name]" required placeholder="Enter course name">

                        <label for="new_course_abbrev_name">Abbreviated Name</label>
                        <input type="text" name="courses[${courseIndex}][new_course_abbrev_name]" placeholder="Enter abbreviated name">

                        <label for="new_course_date">Date</label>
                        <input type="date" name="courses[${courseIndex}][new_course_date]" min="<?php echo date('Y-m-d'); ?>" required>

                        <label for="new_course_start_time">Start Time</label>                    
                        <input type="time" name="courses[${courseIndex}][new_course_start_time]" required placeholder="Enter start time. Ex. 12:00 PM">

                        <label for="new_course_end_time">End Time</label>
                        <input type="time" name="courses[${courseIndex}][new_course_end_time]" required placeholder="Enter end time. Ex. 4:00 PM">

                        <label for="new_course_trainer">Taught By</label>
                        <input type="text" name="courses[${courseIndex}][new_course_trainer]" required placeholder="Enter trainer name"> 

                        <label for="new_course_description">Description</label>
                        <input type="text" name="courses[${courseIndex}][new_course_description]" required placeholder="Enter description">

                        <label for="new_course_location">Location</label>
                        <input type="text" name="courses[${courseIndex}][new_course_location]" required placeholder="Enter location">

                        <label for="new_course_capacity">Volunteer Slots</label>
                        <input type="text" name="courses[${courseIndex}][new_course_capacity]" pattern="([1-9])|([01][0-9])|(20)" required placeholder="Enter a number">
                        </select>
                    </div>
                `;

                const courseDiv = document.createElement('div');
                courseDiv.classList.add('course');
                courseDiv.innerHTML = newCourseHtml;
                coursesContainer.appendChild(courseDiv);

                courseIndex++; // increment index for the next course fields
            });
        });
    </script>
</body>
</html>