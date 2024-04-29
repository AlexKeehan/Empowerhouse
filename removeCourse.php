<?php
/** 
 * Page to search for and remove multiple courses from the dbCourses simultaneously 
 * @ Author Emily Lambert
 * @ Version April 17 2024
 **/

// Include necessary files
require_once('include/input-validation.php');
require_once('database/dbCourses.php');
require_once('database/dbEvents.php');
require_once('database/dbTrainingPeriods.php');

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

// Fetch all courses from the database
$courses = fetch_all_courses();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if course names are provided
    if (isset($_POST["course_names"])) {
        // Ask user to confirm they want the courses removed from the database
        $confirm_message = "Are you sure you want to remove the selected courses?";
        if (confirm($confirm_message)) {
            $course_names = $_POST["course_names"];
            $failed_courses = [];

            // Remove each course from the database
            foreach ($course_names as $course_name) {
                $success = remove_course_from_courses($course_name);
                if (!$success) {
                    $failed_courses[] = $course_name;
                }
            }

            if (empty($failed_courses)) {
                $message = "<p>All selected courses have been successfully removed!</p>";
                // Redirect to the calendar page upon successful removal
                header("Location: calendar.php?deleteSuccess");
                exit();
            } else {
                $message = "<p>Failed to remove the following courses: " . implode(", ", $failed_courses) . "</p>";
            }
        }
    } else {
        $message = "<p>Please select at least one course to remove</p>";
    }
}

// display the confirmation prompt
function confirm($message) {
    return isset($_POST['confirm']) && $_POST['confirm'] === 'yes';
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc'); ?>
    <title>Empowerhouse VMS | Remove Course</title>
    <style>
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
        #selected-courses {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php require_once('header.php'); ?>
    <h1>Remove Course</h1>
    <main class="date">
        <h2>Select Courses to Remove</h2>
        <?php if (isset($message)) echo $message; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <fieldset>
                <legend>Course Selection</legend>
                <div style="position: relative;">
                    <input type="text" id="course-search" name="course_search" placeholder="Search for courses...">
                    <ul class="search-results" id="search-results"></ul>
                </div>
                <div id="selected-courses">
                    <h3>Courses to Remove:</h3>
                </div>
                <button type="submit" name="confirm" value="yes">Remove Selected Courses</button>
                <button type="submit" name="confirm" value="no">Cancel</button>
            </fieldset>
        </form>
        <button onclick="window.location.href='index.php';">Back to Dashboard</button>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseSearch = document.getElementById('course-search');
            const searchResults = document.getElementById('search-results');
            const selectedCourses = document.getElementById('selected-courses');

            // filter courses by course name
            function filterCourses(searchTerm) {
                const filteredCourses = <?php echo json_encode($courses); ?>;
                return filteredCourses.filter(course => course.name.toLowerCase().includes(searchTerm.toLowerCase()));
            }

            function findSearchResults(filteredCourses) {
                // clear previous results
                searchResults.innerHTML = '';
                // find search results
                filteredCourses.forEach(course => {
                    const li = document.createElement('li');
                    li.textContent = `${course.name} - ${course.abbrevName} | ${course.date} | ${course.startTime} - ${course.endTime} | ${course.description} | ${course.location} | ${course.capacity} Slots`;
                    li.addEventListener('click', function() {
                        const selectedCourse = document.createElement('div');
                        selectedCourse.textContent = `${course.name} - ${course.abbrevName} | ${course.date} | ${course.startTime} - ${course.endTime} | ${course.description} | ${course.location} | ${course.capacity} Slots`;
                        selectedCourses.appendChild(selectedCourse);
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'course_names[]';
                        hiddenInput.value = course.name;
                        selectedCourse.appendChild(hiddenInput);
                        searchResults.style.display = 'none';
                        courseSearch.value = '';
                    });
                    searchResults.appendChild(li);
                });
                // display search results
                if (filteredCourses.length > 0) {
                    searchResults.style.display = 'block';
                } else {
                    searchResults.style.display = 'none';
                }
            }

            // event listener for user input in search bar
            courseSearch.addEventListener('input', function() {
                const searchTerm = courseSearch.value.trim();
                const filteredCourses = filterCourses(searchTerm);
                findSearchResults(filteredCourses);
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
