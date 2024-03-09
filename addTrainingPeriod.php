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
        <main>
            <h2>New Event Form</h2>
            <form id="new-event-form" method="post">
                <label for="name">Event Name </label>
                <input type="text" id="eventname" name="eventname" required placeholder="Enter name"> 
                

                <input type="submit" value="Create Event">
            </form>
        </main>
    </body>
</html>
