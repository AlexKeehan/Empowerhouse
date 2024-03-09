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
    $done = False;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Empowerhouse VMS | Create Event</title>
    </head>
    <body>
        <?php require_once('header.php')
        ?>
        <h1>Select Training Period</h1>
        <main>
            <h2>Available Training Periods</h2>
            <form id="training-periods" method="post" action="addTrainingPeriod.php">
                <select name="Select Training Period">
                <option value="first">Jan 1 - Feb 28</option>
                <option value="second">May 1 - Jun 30</option>
                <option value="third">Sep 1 - Oct 31</option>
                </select>
            <input type="submit" name="Submit">
            </form>

            <?php
            if (isset($_POST['training_periods'])) {
                    echo "Selected training period is " .htmlspecialchars[$_POST['training_periods']];
            }
            ?>
        </main>
    </body>
</html>
