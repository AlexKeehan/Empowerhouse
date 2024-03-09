<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $args = sanitize($_POST, null);
        $required = array(
            "eventname"
        );
        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo 'bad form data';
            die();
        } else {
            $id = create_event($args['eventname']);
            if(!$id){
                echo "Oopsy!";
                die();
            }
            $i = 0; 
            foreach ($courses as $course){         
                $validated = validate12hTimeRangeAndConvertTo24h($args[$course . "start-time"], $args[$course ."end-time"]);
                if (!$validated) {
                    echo 'bad time range';
                    die();
                }
                $startTime = $args[$course . 'start-time'] = $validated[0];
                $endTime = $args[$course . 'end-time'] = $validated[1];
                $date = $args[$course . 'date'] = validateDate($args[$course . "date"]);
                $capacity = intval($args[$course . "capacity"]);
                if (!$startTime || !$endTime || !$date || $capacity < 1 || $capacity > 20){
                    echo 'bad args';
                    die();
                }
                $courseArgs = [$course, $abvrcourse[$i], $args[$course . 'trainer'], $id, $date, $startTime, $endTime, $args[$course . 'description'], $args[$course . 'location'], $capacity];
                create_course($courseArgs);
                $i=$i+1;
            }
            require_once('include/output.php');
            
       /*     $name = htmlspecialchars_decode($args['name']);
            $startTime = time24hto12h($startTime);
            $endTime = time24hto12h($endTime);
            $date = date('l, F j, Y', strtotime($date));
            require_once('database/dbMessages.php');
            system_message_all_users_except($userID, "A new event was created!", "Exciting news!\r\n\r\nThe [$name](event: $id) event from $startTime to $endTime on $date was added!\r\nSign up today!");
*/ header("Location: calendar.php?event-filter=$id&createSuccess");
            die();
        }
    }

    //this RegEx checks for nnnn-nn-nn. Consider using a more sophisticated RegEx for the date to validate that the month is between 1-12 and the date is corect, according to the month
    $date = null;
    if (isset($_GET['date'])) {
        $date = $_GET['date'];
        $datePattern = '/[0-9]{4}-[0-9]{2}-[0-9]{2}/';
        $timeStamp = strtotime($date);
        if (!preg_match($datePattern, $date) || !$timeStamp) {
            header('Location: calendar.php');
            die();
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
        <h1>Create Event</h1>
        <main class="date">
            <h2>New Event Form</h2>
            <form id="new-event-form" method="post">
                <label for="name">Event Name </label>
                <input type="text" id="eventname" name="eventname" required placeholder="Enter name"> 
                <?php
                foreach ($courses as $course){ 
                    echo '<fieldset>
                    <legend>' . $course .  '</legend>
                    <label for="name">Date </label>
                    <input type="date" id="date" name="' . $course . 'date"'; 
                    if ($date){
                      echo 'value="' . $date . '"';
                    }
                    echo 'min="' .  date('Y-m-d') . '" required>
                    <label for="name">Start Time </label>                    
                    <input type="text" id="start-time" name="' . $course . 'start-time" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter start time. Ex. 12:00 PM">
                    <label for="name">End Time </label>
                    <input type="text" id="end-time" name="' . $course . 'end-time" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter end time. Ex. 4:00 PM">
                    <p id="date-range-error" class="error hidden">Start time must come before end time</p>
                    <label for="name">Taught By </label>
                    <input type="text" id="trainer" name="' . $course . 'trainer" required placeholder="Enter trainer name"> 
                    <label for="name">Description </label>
                    <input type="text" id="description" name="' . $course . 'description" required placeholder="Enter description">
                    <label for="name">Location </label>
                    <input type="text" id="location" name="' . $course . 'location" required placeholder="Enter location">
                    <label for="name">Volunteer Slots</label>
                    <input type="text" id="capacity" name="' . $course . 'capacity" pattern="([1-9])|([01][0-9])|(20)" required placeholder="Enter a number">
                    </fieldset>'; 
                }
                ?>

                <input type="submit" value="Create Event">
            </form>
                <?php if ($date): ?>
                    <a class="button cancel" href="calendar.php?month=<?php echo substr($date, 0, 7) ?>" style="margin-top: -.5rem">Return to Calendar</a>
                <?php else: ?>
                    <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
                <?php endif ?>
        </main>
    </body>
</html>
