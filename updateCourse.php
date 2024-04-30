
<?php
/** 
 * Page to search for and update data for a course from dbCourses 
 * @ Author Emily Lambert
 * @ Version April 18 2024
 **/

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
require_once('include/input-validation.php');
require_once('database/dbCourses.php');
require_once('database/dbEvents.php');
require_once('database/dbTrainingPeriods.php');

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
$courses = fetch_all_courses();
$trainingPeriod = NULL;
// Process form submission for selecting a course
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_course'])) {
    if (!empty($_POST['selected_course_id'])) {
        $courseID = $_POST['selected_course_id'];
        $courseDetails = retrieve_course($courseID);
        //Check for existing training period
        if ($courseDetails['periodId'] != 0)
        {
            //Store training period id in a variable to use in the html form
            $trainingPeriod = $courseDetails['periodId'];
            //Query for the rest of the training period information
            $query_results = get_training_period_by_id($trainingPeriod);
            //Check if the query returned something
            if ($query_results)
            {
                //Store the name (semester & year) in a variable to print out in the html form
                $training_period_name = $query_results['semester'] . " " .  $query_results['year'];
            }     
        }
    } else {
        header("Location: updateCourse.php?error=no_course_selected");
        exit();
    }
}

// Process form submission for updating the course details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_update'])) {
    // Validate and retrieve course details from form input
    if (!isset($_POST['courses']) || empty($_POST['courses'])) {
        // Set error message
        header("Location: updateCourse.php?error=no_courses_provided");
        exit();
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
            //Check for existing training period
            if ($trainingPeriod != NULL)
            {
                $periodId = $trainingPeriod;
            }
            else
            {
                $periodId = $args['period_id'];
            }
            

            //var_dump($courseID, $courseName, $abbrevName, $startTime, $endTime, $date, $trainer, $description, $location, $capacity, $eventId, $periodId);

            // Check if course ID is given
            if (empty($courseID)) {
                header("Location: updateCourse.php?error=missing_course_name");
                exit();
            } else {
                // Check if periodID is valid if given
                if (!empty($periodId)) {
                    $existingPeriod = get_training_period_by_id($periodId);
                    if (!$existingPeriod) {
                        header("Location: updateCourse.php?error=invalid_period_id");
                        exit();
                    }
                }

                //Check if date is within training period
                if (!empty($date))
                {
                    $query_results = get_training_period_by_id($periodId);
                    $startDate = $query_results['startdate'];
                    $endDate = $query_results['enddate'];
                    if ($startDate != NULL && $endDate != NULL)
                    {
                        if (!($startDate <= $date && $endDate >= $date))
                        {
                            header("Location: updateCourse.php?error=date_outside_training_period");
                            exit();
                        }
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
                    header("Location: updateCourse.php?error=create_failed");
                    exit();
                }
            }
        }

        // Redirect to a calendar page upon successful update of course
        header("Location: calendar.php?editSuccess");
        exit();
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
        #course-search {
            margin-bottom: 10px;
        }
        .search-results {
            display: none;
            border: 1px solid #ccc;
            background-color: #fff;
            position: absolute;
            z-index: 1;
            width: 100%; 
            max-height: 200px;
            overflow-y: auto;
        }
        .search-results li {
            list-style-type: none;
            padding: 5px;
            cursor: pointer;
        }
        .search-results li:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <?php require_once('header.php'); ?>
    <h1>Update Course</h1>
    <?php
    // Display error messages  if provided in URL
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        if ($error === "missing_course_name") {
            echo "<p class='error'>Please fill in the course name for each course.</p>";
        } elseif ($error == "no_course_selected") {
            echo "<p class='error'>Please select a course</p>";
        } elseif ($error === "create_failed") {
            echo "<p class='error'>Failed to create one or more courses. Please try again later.</p>";
        } elseif ($error === "invalid_period_id") {
            echo "<p class='error'>Invalid training period ID provided.</p>";
        } elseif ($error === "no_courses_provided") {
            echo "<p class='error'>No courses provided in the form.</p>";
        } elseif ($error == "date_outside_training_period") {
            echo "<p class='error'>Date provided is outside the training period.</p>";
        }
    }
    ?>
    <main class="date">
        <h2>Select a Course to Update</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <fieldset>
                <legend>Course Selection</legend>
                <div style="position: relative;">
                    <input type="text" id="course-search" name="course_search" placeholder="Search for courses...">
                    <ul class="search-results" id="search-results"></ul>
                </div>
                <div id="selected-course">
                    <h3>Course to Update:</h3>
                </div>
                <button type="submit" name="update_course">Update Course</button>
            </fieldset>
        </form>
        <button onclick="window.location.href='index.php';">Back to Dashboard</button>

        <?php if ($courseDetails) : ?>
            <h2>Update Course Details</h2>
            <form id="update-course-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <fieldset>
                    <legend>Course Details</legend>
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
                                    echo "<option value=\"" . $event['id'] . "\">" . $event['eventname'] . "</option>";
                                } ?>
                            </select>
                            <?php
                            //Check for existing training period
                            //If so, then only display that training period
                            if ($trainingPeriod != 0)
                            {
                                echo"
                                <label for='period_id'>Select Training Period</label>
                                <select name='courses[0][period_id]'>
                                <option value='$trainingPeriod'>$training_period_name";
                            }
                            else
                            {
                                echo"
                                <label for='period_id'>Select Training Period</label>
                                <select name='courses[0][period_id]'>
                                    <option value=''>None</option>";
                                    foreach ($trainingPeriods as $period) {
                                        echo "<option value=\"" . $period['id'] . "\">" . $period['semester'] . " " . $period['year'] . "</option>";
                                    }
                            }
                            echo "</select>";
                            ?>
                        </div>
                        <button type="submit" name="submit_update">Update Course</button>
                    </div>
                </fieldset>
            </form>
        <?php endif; ?>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseSearch = document.getElementById('course-search');
            const searchResults = document.getElementById('search-results');
            const selectedCourse = document.getElementById('selected-course');

            // function to update selected course
            function updateSelectedCourse(course) {
                selectedCourse.innerHTML = '<h3>Course to Update:</h3>';
                const courseDetails = document.createElement('div');
                courseDetails.textContent = `${course.name} - ${course.abbrevName} | ${course.date} | ${course.startTime} - ${course.endTime} | ${course.description} | ${course.location} | ${course.capacity} Slots`;
                selectedCourse.appendChild(courseDetails);
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'selected_course_id';
                hiddenInput.value = course.id;
                selectedCourse.appendChild(hiddenInput);
            }

            // function to handle course selection
            function handleCourseSelection(course) {
                updateSelectedCourse(course);
                searchResults.style.display = 'none';
                courseSearch.value = '';
            }

            // function to display search results
            function displaySearchResults(filteredCourses) {
                searchResults.innerHTML = '';
                filteredCourses.forEach(course => {
                    const li = document.createElement('li');
                    li.textContent = `${course.name} - ${course.abbrevName} | ${course.date} | ${course.startTime} - ${course.endTime} | ${course.description} | ${course.location} | ${course.capacity} Slots`;
                    li.addEventListener('click', () => handleCourseSelection(course));
                    searchResults.appendChild(li);
                });
                searchResults.style.display = filteredCourses.length > 0 ? 'block' : 'none';
            }

            // function to filter courses based on search term
            function filterCourses(searchTerm) {
                const filteredCourses = <?php echo json_encode($courses); ?>;
                return filteredCourses
                    .filter(course => course.name.toLowerCase().includes(searchTerm.toLowerCase()))
                    .sort((a, b) => a.name.localeCompare(b.name)); 
            }

            // event listener for user input in search bar
            courseSearch.addEventListener('input', function() {
                const searchTerm = courseSearch.value.trim();
                const filteredCourses = filterCourses(searchTerm);
                displaySearchResults(filteredCourses);
            });

            // close dropdown menu when clicking away from it
            document.addEventListener('click', function(event) {
                if (!searchResults.contains(event.target) && event.target !== courseSearch) {
                    searchResults.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>


