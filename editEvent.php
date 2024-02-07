<?php
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
    $errors = '';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $args = sanitize($_POST, null);
        $required = array(
            "id", "eventname"
        );
        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo 'bad form data';
            die();
        } else {
            require_once('database/dbPersons.php');
            $id = $args['id'];
            $eventname = $args['eventname'];
            update_event($id,$eventname);
            foreach ($courses as $course){
            $validated = validate12hTimeRangeAndConvertTo24h($args[$course . "start-time"], $args[$course ."end-time"]);
            if (!$validated) {
                $errors .= '<p>The provided time range was invalid.</p>';
            }
            $staffId = $args[$course . 'staffId'];
            $description = $args[$course . 'description'];
            $location = $args[$course . 'location'];
            $startTime = $args[$course . 'start-time'] = $validated[0];
            $endTime = $args[$course . 'end-time'] = $validated[1];
            $date = $args[$course . 'date'] = validateDate($args[$course . 'date']);
            $capacity = intval($args[$course . "capacity"]);
            $assignedVolunteerCount = count(getvolunteers_byevent($id));
            $difference = $assignedVolunteerCount - $capacity;
            if ($capacity < $assignedVolunteerCount) {
                $errors .= "<p>There are currently $assignedVolunteerCount volunteers assigned to this event. The new capacity must not exceed this number. You must remove $difference volunteer(s) from the event to reduce the capacity to $capacity.</p>";
            }
            if (!$startTime || !$endTime || !$date || $capacity < 1 || $capacity >= 20){
                $errors .= '<p>Your request was missing arguments.</p>';
            }
     
            if (!$errors) {
                //Puts the arguements into an array and then pass them into the
              //database
                $courseargs = array(
                  "staffId" => $staffId,
                  "date" => $date,
                  "start-time" => $startTime,
                  "end-time" => $endTime,
                  "description" => $description,
                  "location" => $location,
                  "capacity" => $capacity
                );
                $success = update_course($course, $id, $courseargs);
               if (!$success){
                    echo "Oopsy!";
                    die();
                }
            }
            }
            if (!$errors) {
            header('Location: calendar.php?event-filter=' . $id . '&editSuccess');
            }
        }
    }
     
    if (!isset($_GET['id'])) {
        // uhoh
        die();
    }
    $args = sanitize($_GET);
    $id = $args['id'];
    $event = fetch_event_by_id($id);
    $editCourses = fetch_courses_by_eventid($id);
    if (!$event||!$editCourses) {
        echo "Event does not exist";
        die();
    }
    require_once('include/output.php');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Empowerhouse VMS | Edit Event</title>
    </head>
    <body
        <?php require_once('header.php') ?>
        <h1>Modify Event</h1>
        <main class="date">
        <?php if ($errors): ?>
            <div class="error-toast"><?php echo $errors ?></div>
        <?php endif ?>
            <h2>Event Details</h2>
            <form id="new-event-form" method="post">
                <label for="eventname">Event Name </label>
                <input type="hidden" name="id" value="<?php echo $id ?>"/> 
                <input type="text" id="eventname" name="eventname" value="<?php echo $event['eventname'] ?>" required placeholder="Enter name"> 
                <?php $i = 0;
                foreach($editCourses as $editCourse){ //Looping through the html for each course here 
                
                ?>
                <fieldset>
                <legend> <?php echo $editCourse['name']?> </legend>
                <label for="name">Trainer</label>
                <input type="text" id="staffId" name= "<?php echo $courses[$i] ?>staffId" value="<?php echo $editCourse['staffId'] ?>" maxlength="11"  required placeholder="Enter trainer's name">
                <label for="name">Date </label>
                <input type="date" id="date" name="<?php echo $courses[$i] ?>date" value="<?php echo $editCourse['date'] ?>" min="<?php echo date('Y-m-d'); ?>" required>
                <label for="name">Start Time </label>
                <input type="text" id="start-time" name="<?php echo $courses[$i] ?>start-time" value="<?php echo time24hto12h($editCourse['startTime']) ?>" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter start time. Ex. 12:00 PM">
                <label for="name">End Time </label>
                <input type="text" id="end-time" name="<?php echo $courses[$i] ?>end-time" value="<?php echo time24hto12h($editCourse['endTime']) ?>" pattern="([1-9]|10|11|12):[0-5][0-9] ?([aApP][mM])" required placeholder="Enter end time. Ex. 4:00 PM">
                <p id="date-range-error" class="error hidden">Start time must come before end time</p>
                <label for="name">Description </label>
                <input type="text" id="description" name="<?php echo $courses[$i] ?>description" value="<?php echo $editCourse['description'] ?>" required placeholder="Enter description">
                <label for="name">Location </label>
                <input type="text" id="location" name="<?php echo $courses[$i] ?>location" value="<?php echo $editCourse['location'] ?>" required placeholder="Enter location">
                <label for="name">Volunteer Slots</label>
                <input type="text" id="capacity" name="<?php echo $courses[$i] ?>capacity"  value="<?php echo $editCourse['capacity'] ?>"pattern="([1-9])|([01][0-9])|(30)" required placeholder="Enter a number up to 30">   
                </fieldset>
                <?php $i = $i+1;
                } ?>
                <input type="submit" value="Update Event">
                <a class="button cancel" href="event.php?id=<?php echo htmlspecialchars($_GET['id']) ?>" style="margin-top: .5rem">Cancel</a>
            </form>
        </main>
    </body>
</html>
