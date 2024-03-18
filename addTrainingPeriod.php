<?php
    // Page for an Admin to add a new Training Period and add Courses to it.
    session_cache_expire(30);
    session_start();
    ini_set("display_errors",1);
    error_reporting(E_ALL);
    $selected_num_courses = False;

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

    if (isset($_POST['create-courses'])) {
        $args = sanitize($_POST, null);
        $required = array(
            "coursename",
            "date",
            "starttime",
            "endtime",
            "trainer",
            "description",
            "location",
            "capacity"
        );

        /*foreach ($args as $arg) {
            echo "Arg ", $arg, "\n";
        }

        foreach($required as $req) {
            echo "Required ", $req, "\n";
        }
        */

        $option = isset($_POST['training-periods']) ? $_POST['training-periods'] : false;   //training period that new courses will connect to

        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo 'bad form data';
            die();
        } else {
            //Create new Course and insert into dbCourses
        }
        
    }
    
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Empowerhouse VMS | Create Event</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Create Training Period</h1>
        <main>
            <h2>Choose Number Of Courses To Be Added</h2>
            <form id="num-courses" method="post">
                <input type="text" id="numcourses" name="numcourses" required placeholder="Enter Number Of Courses">
                <input type="submit" value="Submit">
            </form>
            <form id="add-course" method="post">
                <?php
                $selected_num_courses = True;
                $numcourses = isset($_POST['numcourses']) ? $_POST['numcourses'] : false;
                $i = 0;
                foreach ($courses as $course) { 
                    if ($i >= $numcourses) {
                        break;
                    }
                    $i++;
                    echo '<fieldset>
                    <label for="name">Course Name </label>                    
                    <input type="text" id="course-name" name="coursename" required placeholder="Enter Course Name">
                    <label for="name">Date </label>
                    <input type="date" id="date" name="date"'; 
                    if ($date){
                      echo 'value="' . $date . '"';
                    }
                    echo 'min="' .  date('Y-m-d') . '" required>                    
                    <input type="text" id="start-time" name="starttime" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter start time. Ex. 12:00 PM">
                    <label for="name">End Time </label>
                    <input type="text" id="end-time" name="endtime" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter end time. Ex. 4:00 PM">
                    <p id="date-range-error" class="error hidden">Start time must come before end time</p>
                    <label for="name">Taught By </label>
                    <input type="text" id="trainer" name="trainer" required placeholder="Enter trainer name"> 
                    <label for="name">Description </label>
                    <input type="text" id="description" name="description" required placeholder="Enter description">
                    <label for="name">Location </label>
                    <input type="text" id="location" name="location" required placeholder="Enter location">
                    <label for="name">Volunteer Slots</label>
                    <input type="text" id="capacity" name="capacity" pattern="([1-9])|([01][0-9])|(20)" required placeholder="Enter a number">
                    </fieldset>';
                }
                ?>
                <input type="submit" name="create-courses"<?php if($numcourses == 0) {?> style="display: none;" <?php } ?> value="Create Course(s)">
            </form>
        </main>
    </body>
</html>
